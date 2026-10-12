<?php
if(PHP_SAPI!=='cli') {http_response_code(404);exit;}
require __DIR__.'/../app/bootstrap.php';
if(in_array('--warm',$argv,true)) foreach(providers() as $provider) if($provider['enabled']) {
    try {$result=cachedProvider($provider['id'],'browse',['page'=>1,'search'=>''],true);echo $provider['name'].': '.count($result['data']['shows'])." items\n";}
    catch(AppError $error) {echo $provider['name'].': '.$error->errorCode."\n";}
}
foreach(query('SELECT * FROM refresh_jobs ORDER BY created_at LIMIT 20')->fetchAll() as $job) {
    try {cachedProvider((int)$job['provider_id'],$job['operation'],json_decode($job['args'],true),true);query('DELETE FROM refresh_jobs WHERE cache_key=?',[$job['cache_key']]);echo 'Refreshed provider '.$job['provider_id']."\n";}
    catch(AppError $error) {echo 'Provider '.$job['provider_id'].': '.$error->errorCode."\n";}
}
query('DELETE FROM sessions WHERE expires_at<UTC_TIMESTAMP()');
query('DELETE FROM password_resets WHERE expires_at<UTC_TIMESTAMP()');
query('DELETE FROM cache WHERE stale_until<UTC_TIMESTAMP()');
query('DELETE FROM rate_limits WHERE window_start<?',[time()-86400]);
