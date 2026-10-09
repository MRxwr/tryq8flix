<?php
declare(strict_types=1);

function sessionToken(): string
{
    $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    $token = preg_match('/^Bearer ([a-f0-9]{64})$/D', $header, $match) ? $match[1] : ($_COOKIE['q8flix_v2'] ?? '');
    return is_string($token) && preg_match('/^[a-f0-9]{64}$/D', $token) ? $token : '';
}

function currentUser(): ?array
{
    $token = sessionToken();
    if ($token === '') return null;
    return query('SELECT u.id,u.username,u.email,u.avatar,u.role,s.csrf FROM sessions s JOIN users u ON u.id=s.user_id WHERE s.token_hash=? AND s.expires_at>UTC_TIMESTAMP() AND u.active=1', [hash('sha256', $token)])->fetch() ?: null;
}

function requireUser(bool $admin = false): array
{
    $user = currentUser();
    if (!$user) throw new AppError('unauthorized', 'Please sign in.', 401);
    if ($admin && $user['role'] !== 'admin') throw new AppError('forbidden', 'Administrator access is required.', 403);
    return $user;
}

function assertSameOrigin(): void
{
    $expected = parse_url((string)config('origin'));
    $expectedOrigin = $expected['scheme'] . '://' . $expected['host'] . (isset($expected['port']) ? ':' . $expected['port'] : '');
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    if ($origin !== '' && !hash_equals($expectedOrigin, $origin)) throw new AppError('foreign_origin', 'Request origin is not allowed.', 403);
    if (($_SERVER['HTTP_SEC_FETCH_SITE'] ?? '') === 'cross-site') throw new AppError('foreign_origin', 'Cross-site request denied.', 403);
}

function mutation(?array $user = null): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') throw new AppError('method_not_allowed', 'Use POST for this action.', 405);
    assertSameOrigin();
    if ($user) {
        $csrf = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!is_string($csrf) || !hash_equals($user['csrf'], $csrf)) throw new AppError('csrf', 'Refresh the page and try again.', 403);
    }
}

function rateLimit(string $scope, int $limit, int $seconds): void
{
    $key = hash('sha256', $scope . '|' . ($_SERVER['REMOTE_ADDR'] ?? 'cli'));
    $window = intdiv(time(), $seconds) * $seconds;
    query('INSERT INTO rate_limits(bucket,hits,window_start) VALUES(?,1,?) ON DUPLICATE KEY UPDATE hits=IF(window_start=VALUES(window_start),hits+1,1),window_start=VALUES(window_start)', [$key,$window]);
    $hits = (int)query('SELECT hits FROM rate_limits WHERE bucket=?', [$key])->fetchColumn();
    if ($hits > $limit) {
        header('Retry-After: ' . max(1, $window + $seconds - time()));
        throw new AppError('rate_limited', 'Too many requests. Please try again later.', 429);
    }
}

function setSessionCookie(string $token, int $expires): void
{
    setcookie('q8flix_v2', $token, ['expires'=>$expires, 'path'=>basePath() . '/', 'secure'=>parse_url((string)config('origin'), PHP_URL_SCHEME)==='https', 'httponly'=>true, 'samesite'=>'Lax']);
}

function createSession(int $userId, bool $remember = false): array
{
    $token = bin2hex(random_bytes(32));
    $csrf = bin2hex(random_bytes(32));
    $expires = time() + ($remember ? 30*86400 : 12*3600);
    query('INSERT INTO sessions(token_hash,user_id,csrf,expires_at) VALUES(?,?,?,?)', [hash('sha256',$token),$userId,$csrf,gmdate('Y-m-d H:i:s',$expires)]);
    setSessionCookie($token, $remember ? $expires : 0);
    // Web clients receive only public session data; the token stays in an HttpOnly cookie.
    return ['csrf'=>$csrf,'user'=>query('SELECT id,username,email,avatar,role FROM users WHERE id=?',[$userId])->fetch()];
}

function authAction(string $action): array
{
    if ($action === 'profile') {
        $user = requireUser();
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') return $user;
        mutation($user);
        $email = mb_strtolower(textInput('email'));
        if (!filter_var($email,FILTER_VALIDATE_EMAIL)) throw new AppError('invalid_email','Enter a valid email address.');
        try { query('UPDATE users SET email=? WHERE id=?',[$email,$user['id']]); }
        catch (PDOException $error) { if ($error->getCode()==='23000') throw new AppError('email_taken','That email is already used.',409); throw $error; }
        return ['msg'=>'Profile updated.'];
    }
    if (in_array($action,['logout','delete','change'],true)) {
        $user = requireUser(); mutation($user);
        if ($action==='logout') {
            query('DELETE FROM sessions WHERE token_hash=?',[hash('sha256',sessionToken())]); setSessionCookie('',time()-3600);
        } elseif ($action==='delete') {
            query('DELETE FROM users WHERE id=?',[$user['id']]); setSessionCookie('',time()-3600);
        } else {
            $old = textInput('currentPassword',1024);
            $hash = query('SELECT password_hash FROM users WHERE id=?',[$user['id']])->fetchColumn();
            if (!password_verify($old,$hash)) throw new AppError('invalid_password','Current password is incorrect.',403);
            $password = validatedPassword();
            db()->beginTransaction();
            try { query('UPDATE users SET password_hash=? WHERE id=?',[password_hash($password,PASSWORD_DEFAULT),$user['id']]); query('DELETE FROM sessions WHERE user_id=?',[$user['id']]); db()->commit(); }
            catch (Throwable $error) { db()->rollBack(); throw $error; }
            setSessionCookie('',time()-3600);
        }
        return ['msg'=>$action==='change' ? 'Password changed. Please sign in again.' : 'Done.'];
    }
    mutation(); rateLimit('auth-' . $action, $action==='login' ? 10 : 5, 900);
    if ($action==='register') {
        $username=textInput('username',80); $email=mb_strtolower(textInput('email')); $password=validatedPassword();
        if (!preg_match('/^[\p{L}\p{N}_ .-]{3,80}$/u',$username)) throw new AppError('invalid_username','Use 3–80 letters, numbers, spaces, or underscores.');
        if (!filter_var($email,FILTER_VALIDATE_EMAIL)) throw new AppError('invalid_email','Enter a valid email.');
        try { query('INSERT INTO users(username,username_small,email,password_hash) VALUES(?,?,?,?)',[$username,mb_strtolower($username),$email,password_hash($password,PASSWORD_DEFAULT)]); }
        catch (PDOException $error) { if ($error->getCode()==='23000') throw new AppError('account_exists','Username or email already used.',409); throw $error; }
        return createSession((int)db()->lastInsertId());
    }
    if ($action==='login') {
        $name=mb_strtolower(textInput('username',255)); $password=textInput('password',1024);
        $user=query('SELECT * FROM users WHERE (username_small=? OR email=?) AND active=1',[$name,$name])->fetch();
        $dummy='$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi';
        $valid=password_verify($password,$user['password_hash'] ?? $dummy);
        if (!$user || !$valid) throw new AppError('invalid_login','Invalid username or password.',401);
        if (password_needs_rehash($user['password_hash'],PASSWORD_DEFAULT)) query('UPDATE users SET password_hash=? WHERE id=?',[password_hash($password,PASSWORD_DEFAULT),$user['id']]);
        return createSession((int)$user['id'],input('remember')==='1');
    }
    if ($action==='forget') {
        $email=mb_strtolower(textInput('email'));
        if (!filter_var($email,FILTER_VALIDATE_EMAIL)) throw new AppError('invalid_email','Enter a valid email.');
        $user=query('SELECT id FROM users WHERE email=? AND active=1',[$email])->fetch();
        if ($user) {
            $token=bin2hex(random_bytes(32));
            query('DELETE FROM password_resets WHERE user_id=?',[$user['id']]);
            query('INSERT INTO password_resets(token_hash,user_id,expires_at) VALUES(?,?,?)',[hash('sha256',$token),$user['id'],gmdate('Y-m-d H:i:s',time()+1800)]);
            deliverReset($email,appUrl('index.php?v=Reset&token='.$token));
        }
        return ['msg'=>'If that account exists, reset instructions have been sent.'];
    }
    if ($action==='reset') {
        $token=textInput('token',64); $password=validatedPassword();
        db()->beginTransaction();
        try {
            $reset=query('SELECT user_id FROM password_resets WHERE token_hash=? AND expires_at>UTC_TIMESTAMP() FOR UPDATE',[hash('sha256',$token)])->fetch();
            if (!$reset) throw new AppError('invalid_reset','This reset link is invalid or expired.');
            query('UPDATE users SET password_hash=? WHERE id=?',[password_hash($password,PASSWORD_DEFAULT),$reset['user_id']]);
            query('DELETE FROM sessions WHERE user_id=?',[$reset['user_id']]);
            query('DELETE FROM password_resets WHERE user_id=?',[$reset['user_id']]); db()->commit();
        } catch (Throwable $error) { db()->rollBack(); throw $error; }
        return ['msg'=>'Password reset. You can now sign in.'];
    }
    throw new AppError('unknown_action','Unknown account action.',404);
}

function validatedPassword(): string
{
    $password=textInput('password',1024);
    if (strlen($password)<12 || strlen($password)>72) throw new AppError('weak_password','Use a password between 12 and 72 bytes.');
    if ($password!==textInput('confirmPassword',1024)) throw new AppError('password_mismatch','Passwords do not match.');
    return $password;
}

function deliverReset(string $email,string $url): void
{
    if (config('environment')==='development') {
        $dir=V2_ROOT.'/storage/mail'; if(!is_dir($dir)) mkdir($dir,0700,true);
        file_put_contents($dir.'/'.bin2hex(random_bytes(8)).'.txt',"To: {$email}\nReset: {$url}\n",LOCK_EX);
        return;
    }
    $from=(string)config('mail_from');
    if (!filter_var($from,FILTER_VALIDATE_EMAIL) || !mail($email,'Reset your Q8Flix V2 password',"Reset within 30 minutes: {$url}","From: {$from}")) throw new AppError('mail_unavailable','Email delivery is unavailable. Please contact support.',503);
}
