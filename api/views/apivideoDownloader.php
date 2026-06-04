<?php
if (!isset($_GET['action']) || empty($_GET['action'])) {
    echo dataError(array('msg' => 'action is required'));
    die();
}

if (empty($token)) {
    echo dataError(array('msg' => 'token is required'));
    die();
}

$action = $_GET['action'];
$url = isset($_REQUEST['url']) ? trim($_REQUEST['url']) : '';

if (empty($url)) {
    echo dataError(array('msg' => 'url is required'));
    die();
}

if (!filter_var($url, FILTER_VALIDATE_URL)) {
    echo dataError(array('msg' => 'invalid url'));
    die();
}

if (!isAllowedVideoUrl($url)) {
    echo dataError(array('msg' => 'Only X/Twitter, Instagram and TikTok links are supported'));
    die();
}

if (!in_array($action, array('info', 'link'), true)) {
    echo dataError(array('msg' => 'Invalid action'));
    die();
}

$result = runVideoDownloader($action, $url);
if ($result['ok']) {
    echo dataOutput($result['data']);
} else {
    echo dataError(array('msg' => $result['error']));
}
die();

function isAllowedVideoUrl($url)
{
    $host = strtolower(parse_url($url, PHP_URL_HOST) ?: '');
    if (empty($host)) {
        return false;
    }

    $allowedHosts = array(
        'twitter.com',
        'x.com',
        'www.twitter.com',
        'www.x.com',
        'instagram.com',
        'www.instagram.com',
        'tiktok.com',
        'www.tiktok.com',
        'vm.tiktok.com',
        'vt.tiktok.com'
    );

    if (in_array($host, $allowedHosts, true)) {
        return true;
    }

    // Allow platform subdomains
    if (preg_match('/(^|\\.)instagram\\.com$/', $host)) {
        return true;
    }
    if (preg_match('/(^|\\.)tiktok\\.com$/', $host)) {
        return true;
    }
    if (preg_match('/(^|\\.)x\\.com$/', $host)) {
        return true;
    }
    if (preg_match('/(^|\\.)twitter\\.com$/', $host)) {
        return true;
    }

    return false;
}

function runVideoDownloader($action, $url)
{
    if (!function_exists('shell_exec')) {
        return array('ok' => false, 'error' => 'Server does not allow shell execution');
    }

    $bin = ensureYtDlpBinary();
    if (!$bin['ok']) {
        $fallback = runPlatformFallback($action, $url);
        if ($fallback['ok']) {
            return $fallback;
        }
        return array('ok' => false, 'error' => $bin['error'] . ' | fallback failed: ' . $fallback['error']);
    }
    $ytDlp = $bin['path'];

    $safeUrl = escapeshellarg($url);
    $safeBin = escapeshellarg($ytDlp);

    if ($action === 'info') {
        $command = $safeBin . ' --no-warnings --no-playlist -J ' . $safeUrl . ' 2>&1';
        $raw = shell_exec($command);
        if (!is_string($raw) || trim($raw) === '') {
            $fallback = runPlatformFallback($action, $url);
            if ($fallback['ok']) {
                return $fallback;
            }
            return array('ok' => false, 'error' => 'Empty response from yt-dlp');
        }

        $decoded = json_decode(trim($raw), true);
        if (!is_array($decoded)) {
            $fallback = runPlatformFallback($action, $url);
            if ($fallback['ok']) {
                return $fallback;
            }
            return array('ok' => false, 'error' => 'Invalid downloader response: ' . trim($raw));
        }

        return array(
            'ok' => true,
            'data' => array(
                'id' => isset($decoded['id']) ? $decoded['id'] : '',
                'title' => isset($decoded['title']) ? $decoded['title'] : '',
                'webpage_url' => isset($decoded['webpage_url']) ? $decoded['webpage_url'] : $url,
                'uploader' => !empty($decoded['uploader']) ? $decoded['uploader'] : (!empty($decoded['channel']) ? $decoded['channel'] : ''),
                'duration' => isset($decoded['duration']) ? intval($decoded['duration']) : 0,
                'thumbnail' => isset($decoded['thumbnail']) ? $decoded['thumbnail'] : '',
                'ext' => isset($decoded['ext']) ? $decoded['ext'] : '',
                'format' => isset($decoded['format']) ? $decoded['format'] : '',
                'extractor' => !empty($decoded['extractor_key']) ? $decoded['extractor_key'] : (isset($decoded['extractor']) ? $decoded['extractor'] : '')
            )
        );
    }

    if ($action === 'link') {
        $command = $safeBin . ' --no-warnings --no-playlist -f mp4/best -g ' . $safeUrl . ' 2>&1';
        $raw = shell_exec($command);
        if (!is_string($raw) || trim($raw) === '') {
            $fallback = runPlatformFallback($action, $url);
            if ($fallback['ok']) {
                return $fallback;
            }
            return array('ok' => false, 'error' => 'Could not get stream url from yt-dlp');
        }

        $lines = preg_split('/\r\n|\r|\n/', trim($raw));
        $urls = array();
        foreach ($lines as $line) {
            $line = trim($line);
            if (!empty($line) && preg_match('/^https?:\/\//i', $line)) {
                $urls[] = $line;
            }
        }

        if (empty($urls)) {
            $fallback = runPlatformFallback($action, $url);
            if ($fallback['ok']) {
                return $fallback;
            }
            return array('ok' => false, 'error' => 'No direct stream URL found: ' . trim($raw));
        }

        return array(
            'ok' => true,
            'data' => array(
                'stream_url' => $urls[0],
                'all_urls' => $urls
            )
        );
    }

    return array('ok' => false, 'error' => 'Unsupported action');
}

function runPlatformFallback($action, $url)
{
    $host = strtolower(parse_url($url, PHP_URL_HOST) ?: '');

    if (strpos($host, 'x.com') !== false || strpos($host, 'twitter.com') !== false) {
        return runVXTwitterFallback($action, $url);
    }

    return array('ok' => false, 'error' => 'No fallback available for this platform on this host');
}

function runVXTwitterFallback($action, $url)
{
    $tweetId = extractTwitterStatusId($url);
    if (empty($tweetId)) {
        return array('ok' => false, 'error' => 'Could not detect tweet ID for fallback');
    }

    $apiUrl = 'https://api.vxtwitter.com/Twitter/status/' . rawurlencode($tweetId);
    $json = fetchRemoteJson($apiUrl);
    if (!is_array($json)) {
        return array('ok' => false, 'error' => 'VX fallback API did not return valid JSON');
    }

    $mediaUrls = array();
    if (!empty($json['mediaURLs']) && is_array($json['mediaURLs'])) {
        foreach ($json['mediaURLs'] as $u) {
            if (is_string($u) && preg_match('/^https?:\/\//i', $u)) {
                $mediaUrls[] = $u;
            }
        }
    }

    if (empty($mediaUrls) && !empty($json['media_extended']) && is_array($json['media_extended'])) {
        foreach ($json['media_extended'] as $m) {
            if (!empty($m['url']) && is_string($m['url']) && preg_match('/^https?:\/\//i', $m['url'])) {
                $mediaUrls[] = $m['url'];
            }
        }
    }

    if ($action === 'link') {
        if (empty($mediaUrls)) {
            return array('ok' => false, 'error' => 'VX fallback found no media URL');
        }

        return array(
            'ok' => true,
            'data' => array(
                'stream_url' => $mediaUrls[0],
                'all_urls' => $mediaUrls,
                'source' => 'vxtwitter-fallback'
            )
        );
    }

    $duration = 0;
    if (!empty($json['media_extended'][0]['duration_millis'])) {
        $duration = intval(round(intval($json['media_extended'][0]['duration_millis']) / 1000));
    }

    return array(
        'ok' => true,
        'data' => array(
            'id' => isset($json['tweetID']) ? strval($json['tweetID']) : strval($tweetId),
            'title' => isset($json['text']) ? trim($json['text']) : 'Twitter Video',
            'webpage_url' => isset($json['tweetURL']) ? $json['tweetURL'] : $url,
            'uploader' => isset($json['user_name']) ? trim($json['user_name']) : (isset($json['user_screen_name']) ? trim($json['user_screen_name']) : ''),
            'duration' => $duration,
            'thumbnail' => !empty($json['media_extended'][0]['thumbnail_url']) ? $json['media_extended'][0]['thumbnail_url'] : '',
            'ext' => 'mp4',
            'format' => 'fallback',
            'extractor' => 'vxtwitter-fallback'
        )
    );
}

function extractTwitterStatusId($url)
{
    if (preg_match('/\/status\/(\d+)/i', $url, $m)) {
        return $m[1];
    }
    return '';
}

function fetchRemoteJson($url)
{
    $raw = downloadRemoteFile($url);
    if (!is_string($raw) || trim($raw) === '') {
        return null;
    }

    $decoded = json_decode($raw, true);
    return is_array($decoded) ? $decoded : null;
}

function ensureYtDlpBinary()
{
    $projectRoot = realpath(__DIR__ . '/../../');
    if ($projectRoot === false) {
        return array('ok' => false, 'error' => 'Unable to resolve project path');
    }

    $isWindows = (stripos(PHP_OS, 'WIN') === 0);
    $binName = $isWindows ? 'yt-dlp.exe' : 'yt-dlp';
    $candidateDirs = array();

    // On shared hosting, executables are often blocked in public_html; /tmp is usually executable.
    $tmpDir = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'tryq8flix-bin';
    $candidateDirs[] = $tmpDir;
    $candidateDirs[] = $projectRoot . DIRECTORY_SEPARATOR . 'bin';

    $attemptErrors = array();

    foreach ($candidateDirs as $binDir) {
        $binPath = $binDir . DIRECTORY_SEPARATOR . $binName;

        if (is_file($binPath)) {
            if (!$isWindows) {
                @chmod($binPath, 0755);
            }
            if (isUsableYtDlpBinary($binPath)) {
                return array('ok' => true, 'path' => $binPath);
            }
            @unlink($binPath);
        }

        if (!is_dir($binDir) && !@mkdir($binDir, 0755, true)) {
            $attemptErrors[] = 'mkdir-failed: ' . $binDir;
            continue;
        }

        $install = installYtDlpBinary($binPath, $isWindows);
        if (!$install['ok']) {
            $attemptErrors[] = $install['error'];
            continue;
        }

        if (isUsableYtDlpBinary($binPath)) {
            return array('ok' => true, 'path' => $binPath);
        }

        $attemptErrors[] = 'installed-but-not-executable: ' . $binPath;
    }

    return array('ok' => false, 'error' => 'yt-dlp failed in all locations: ' . implode(' | ', array_slice($attemptErrors, 0, 5)));
}

function installYtDlpBinary($binPath, $isWindows)
{
    $errors = array();

    $downloadCandidates = $isWindows
        ? array(
            'https://github.com/yt-dlp/yt-dlp/releases/latest/download/yt-dlp.exe'
        )
        : array(
            // Prefer standalone Linux binary first to avoid Python-version issues.
            'https://github.com/yt-dlp/yt-dlp/releases/latest/download/yt-dlp_linux',
            // Fallback to generic file if standalone binary is unavailable.
            'https://github.com/yt-dlp/yt-dlp/releases/latest/download/yt-dlp'
        );

    foreach ($downloadCandidates as $downloadUrl) {
        $binaryData = downloadRemoteFile($downloadUrl);
        if ($binaryData === false || strlen($binaryData) < 1024) {
            $errors[] = 'php-download-failed: ' . $downloadUrl;
            if (downloadRemoteFileWithShell($downloadUrl, $binPath)) {
                if (!$isWindows) {
                    @chmod($binPath, 0755);
                }

                if (isUsableYtDlpBinary($binPath)) {
                    return array('ok' => true, 'path' => $binPath);
                }

                $errors[] = 'shell-download-invalid-binary: ' . $downloadUrl;
                @unlink($binPath);
            } else {
                $errors[] = 'shell-download-failed: ' . $downloadUrl;
            }
            continue;
        }

        if (@file_put_contents($binPath, $binaryData) === false) {
            $errors[] = 'write-failed: ' . $downloadUrl;
            continue;
        }

        if (!$isWindows) {
            @chmod($binPath, 0755);
        }

        if (isUsableYtDlpBinary($binPath)) {
            return array('ok' => true, 'path' => $binPath);
        }

        @unlink($binPath);
        $errors[] = 'downloaded-but-not-usable: ' . $downloadUrl;
    }

    $detail = empty($errors) ? '' : (' [' . implode(' | ', array_slice($errors, 0, 4)) . ']');
    return array('ok' => false, 'error' => 'Failed to download a working yt-dlp binary for this host' . $detail);
}

function isUsableYtDlpBinary($binPath)
{
    if (!is_file($binPath)) {
        return false;
    }

    $safeBin = escapeshellarg($binPath);
    $raw = shell_exec($safeBin . ' --version 2>&1');
    if (!is_string($raw) || trim($raw) === '') {
        return false;
    }

    $rawLower = strtolower($raw);
    if (strpos($rawLower, 'traceback') !== false) {
        return false;
    }
    if (strpos($rawLower, 'unsupported version of python') !== false) {
        return false;
    }
    if (strpos($rawLower, 'command not found') !== false) {
        return false;
    }
    if (strpos($rawLower, 'permission denied') !== false) {
        return false;
    }

    if (preg_match('/\d{4}\.\d{2}\.\d{2}/', trim($raw))) {
        return true;
    }

    // Some builds may not print date-style version; check help output as fallback.
    $helpRaw = shell_exec($safeBin . ' --help 2>&1');
    if (is_string($helpRaw) && stripos($helpRaw, 'yt-dlp [OPTIONS] URL') !== false) {
        return true;
    }

    return false;
}

function downloadRemoteFile($url)
{
    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 TryQ8Flix Downloader');
        $data = curl_exec($ch);
        curl_close($ch);
        if (is_string($data) && $data !== '') {
            return $data;
        }
    }

    if (ini_get('allow_url_fopen')) {
        $context = stream_context_create(array(
            'http' => array(
                'follow_location' => 1,
                'timeout' => 120,
                'user_agent' => 'Mozilla/5.0 TryQ8Flix Downloader'
            )
        ));
        $data = @file_get_contents($url, false, $context);
        if (is_string($data) && $data !== '') {
            return $data;
        }
    }

    return false;
}

function downloadRemoteFileWithShell($url, $targetPath)
{
    if (!function_exists('shell_exec')) {
        return false;
    }

    $safeUrl = escapeshellarg($url);
    $safeTarget = escapeshellarg($targetPath);

    // Prefer curl if available.
    $curlCmd = 'curl -L --fail --connect-timeout 20 --max-time 180 -A "Mozilla/5.0 TryQ8Flix Downloader" -o ' . $safeTarget . ' ' . $safeUrl . ' 2>&1';
    $curlOut = shell_exec($curlCmd);
    if (is_file($targetPath) && filesize($targetPath) > 1024) {
        return true;
    }

    // Fallback to wget.
    $wgetCmd = 'wget -O ' . $safeTarget . ' --timeout=180 --user-agent="Mozilla/5.0 TryQ8Flix Downloader" ' . $safeUrl . ' 2>&1';
    $wgetOut = shell_exec($wgetCmd);
    if (is_file($targetPath) && filesize($targetPath) > 1024) {
        return true;
    }

    if (is_file($targetPath) && filesize($targetPath) <= 1024) {
        @unlink($targetPath);
    }

    // Avoid unused-variable optimizations and keep debug outputs available if needed later.
    $null = $curlOut;
    $null = $wgetOut;

    return false;
}
