<?php
require __DIR__.'/../app/bootstrap.php';
if(config('environment')!=='development' || config('origin')!=='http://127.0.0.1:8788')throw new RuntimeException('API tests only run against isolated localhost development.');
$testIp='127.0.0.'.random_int(20,240);
$run=bin2hex(random_bytes(5));$users=[];$titles=[];$passed=0;$failed=0;$prefix='test_'.$run;
function requestApi(string $endpoint,array $query=[],?array $body=null,array $headers=[],?string $cookie=null): array {
    global $testIp;
    $url=appUrl('api/index.php').'?'.http_build_query(['endpoint'=>$endpoint]+$query);$curl=curl_init($url);$received=[];
    curl_setopt_array($curl,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_INTERFACE=>$testIp,CURLOPT_TIMEOUT=>30,CURLOPT_HTTPHEADER=>array_merge(array_filter($headers,static fn($header)=>str_starts_with(strtolower($header),'origin:')) ? [] : ['Origin: http://127.0.0.1:8788'],$headers),CURLOPT_HEADERFUNCTION=>static function($curl,$line)use(&$received){$received[]=$line;return strlen($line);}]);
    if($body!==null){curl_setopt($curl,CURLOPT_POST,true);curl_setopt($curl,CURLOPT_POSTFIELDS,http_build_query($body));}
    if($cookie)curl_setopt($curl,CURLOPT_COOKIE,$cookie);
    $response=curl_exec($curl);if($response===false)throw new RuntimeException(curl_error($curl));$status=curl_getinfo($curl,CURLINFO_HTTP_CODE);curl_close($curl);
    $json=json_decode($response,true);return ['status'=>$status,'json'=>$json,'headers'=>$received,'raw'=>$response];
}
function checkApi(string $name,callable $test): void{global $passed,$failed;try{$test();echo 'PASS '.$name."\n";$passed++;}catch(Throwable $error){echo 'FAIL '.$name.': '.$error->getMessage()."\n";$failed++;}}
function assertApi(bool $condition,string $message='Assertion failed'): void{if(!$condition)throw new RuntimeException($message);}
function cookieFrom(array $response): string {foreach($response['headers'] as $header)if(preg_match('/^Set-Cookie: (q8flix_v2=[^;]+)/i',$header,$match))return $match[1];throw new RuntimeException('No session cookie');}
try {
    checkApi('signed-out requests return real 401',fn()=>assertApi(requestApi('Main')['status']===401));
    checkApi('forged bearer tokens rejected',fn()=>assertApi(requestApi('Home',['server'=>14],null,['Authorization: Bearer '.str_repeat('a',64)])['status']===401));
    $password='V2-Test-Password-'.$run;
    $a=requestApi('User',['action'=>'register'],['username'=>$prefix.'_a','email'=>$prefix.'_a@example.invalid','password'=>$password,'confirmPassword'=>$password]);
    $b=requestApi('User',['action'=>'register'],['username'=>$prefix.'_b','email'=>$prefix.'_b@example.invalid','password'=>$password,'confirmPassword'=>$password]);
    foreach([$a,$b] as $response)if(isset($response['json']['data']['user']['id']))$users[]=$response['json']['data']['user']['id'];
    checkApi('registration creates session without exposing token',function()use($a,$b){assertApi($a['status']===200 && $b['status']===200,$a['raw']);assertApi(!isset($a['json']['data']['keepalive']));$headers=implode('',$a['headers']);assertApi(str_contains($headers,'HttpOnly') && str_contains($headers,'SameSite=Lax'));assertApi(!str_contains($headers,'Domain='));});
    $cookieA=cookieFrom($a);$cookieB=cookieFrom($b);$csrfA=$a['json']['data']['csrf'];$csrfB=$b['json']['data']['csrf'];
    $fixture=normalizeItem(['href'=>'900'.(string)random_int(100000,999999),'title'=>'API Fixture '.$run,'media_type'=>'movie','year'=>2024],14);storeItem($fixture);$titles[]=$fixture['id'];
    $title=$fixture['id'];$source=$fixture['source']['id'];
    checkApi('mutation without CSRF rejected',fn()=>assertApi(requestApi('Favorites',['action'=>'add'],['title_id'=>$title],[],$cookieA)['status']===403));
    checkApi('foreign-Origin mutation rejected',fn()=>assertApi(requestApi('Favorites',['action'=>'add'],['title_id'=>$title],['Origin: https://evil.example','X-CSRF-Token: '.$csrfA],$cookieA)['status']===403));
    checkApi('mutations reject GET',fn()=>assertApi(requestApi('Favorites',['action'=>'add','title_id'=>$title],null,[],$cookieA)['status']===405));
    checkApi('favorites isolated between accounts',function()use($title,$cookieA,$cookieB,$csrfA,$csrfB){assertApi(requestApi('Favorites',['action'=>'add'],['title_id'=>$title],['X-CSRF-Token: '.$csrfA],$cookieA)['status']===200);$aa=requestApi('Favorites',[],null,[],$cookieA);$bb=requestApi('Favorites',[],null,[],$cookieB);assertApi(count($aa['json']['data']['favorites'])===1);assertApi(count($bb['json']['data']['favorites'])===0);requestApi('Favorites',['action'=>'remove'],['title_id'=>$title],['X-CSRF-Token: '.$csrfB],$cookieB);assertApi(count(requestApi('Favorites',[],null,[],$cookieA)['json']['data']['favorites'])===1);});
    checkApi('progress scoped and preserved when reopening',function()use($source,$cookieA,$cookieB,$csrfA){$body=['source'=>$source,'position'=>'61','duration'=>'121'];$progress=requestApi('History',['action'=>'progress'],$body,['X-CSRF-Token: '.$csrfA],$cookieA);assertApi($progress['status']===200,$progress['raw']);requestApi('History',['action'=>'opened'],['source'=>$source],['X-CSRF-Token: '.$csrfA],$cookieA);$history=requestApi('History',[],null,[],$cookieA)['json']['data']['history'];assertApi($history[0]['position_seconds']===60);assertApi($history[0]['tracking']==='direct');assertApi(requestApi('History',[],null,[],$cookieB)['json']['data']['history']===[]);});
    checkApi('user cannot access provider administration',fn()=>assertApi(requestApi('Health',[],null,[],$cookieA)['status']===403));
    checkApi('SQL-shaped username does not authenticate',fn()=>assertApi(requestApi('User',['action'=>'login'],['username'=>"' OR 1=1 --",'password'=>$password])['status']===401));
    checkApi('route traversal is not included',fn()=>assertApi(requestApi('../app/bootstrap',[],null,[],$cookieA)['status']===404));
    checkApi('open proxy disabled',function()use($cookieA){$curl=curl_init(appUrl('video-proxy.php?url=http://127.0.0.1/'));curl_setopt_array($curl,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_COOKIE=>$cookieA]);curl_exec($curl);$status=curl_getinfo($curl,CURLINFO_HTTP_CODE);curl_close($curl);assertApi($status===410);});
    checkApi('private config blocked by dev router',function(){$curl=curl_init(appUrl('config/local.php'));curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);$out=curl_exec($curl);$status=curl_getinfo($curl,CURLINFO_HTTP_CODE);curl_close($curl);assertApi($status===404 && $out==='Not found');});
    checkApi('reset request does not change password',function()use($prefix,$cookieA,$users){$before=query('SELECT password_hash FROM users WHERE id=?',[$users[0]])->fetchColumn();$reset=requestApi('User',['action'=>'forget'],['email'=>$prefix.'_a@example.invalid']);assertApi($reset['status']===200,$reset['raw']);assertApi(query('SELECT password_hash FROM users WHERE id=?',[$users[0]])->fetchColumn()===$before);});
    checkApi('reset token is single-use and revokes sessions',function()use($prefix,$cookieA,$password){$token=null;foreach(glob(V2_ROOT.'/storage/mail/*.txt') ?: [] as $file){$text=file_get_contents($file);if(str_contains($text,$prefix.'_a@example.invalid')){preg_match('/token=([a-f0-9]{64})/',$text,$match);$token=$match[1];unlink($file);}}assertApi($token!==null);$body=['token'=>$token,'password'=>$password.'-reset','confirmPassword'=>$password.'-reset'];assertApi(requestApi('User',['action'=>'reset'],$body)['status']===200);assertApi(requestApi('User',['action'=>'reset'],$body)['status']===400);assertApi(requestApi('Main',[],null,[],$cookieA)['status']===401);});
    checkApi('logout revokes server-side session',function()use($cookieB,$csrfB){assertApi(requestApi('User',['action'=>'logout'],[],['X-CSRF-Token: '.$csrfB],$cookieB)['status']===200);assertApi(requestApi('Main',[],null,[],$cookieB)['status']===401);});
} finally {
    foreach($users as $id)query('DELETE FROM users WHERE id=?',[$id]);
    foreach($titles as $id)query('DELETE FROM titles WHERE id=?',[$id]);
    foreach(['auth-register','auth-login','auth-forget','auth-reset'] as $scope) query('DELETE FROM rate_limits WHERE bucket=?',[hash('sha256',$scope.'|'.$testIp)]);
    foreach($users as $id) query('DELETE FROM rate_limits WHERE bucket=?',[hash('sha256','api-user-'.$id.'|'.$testIp)]);

}
echo "$passed API checks passed, $failed failures.\n";exit($failed ? 1 : 0);
