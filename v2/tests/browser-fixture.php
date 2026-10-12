<?php
if(PHP_SAPI!=='cli')exit;
require __DIR__.'/../app/bootstrap.php';
if(config('environment')!=='development' || config('origin')!=='http://127.0.0.1:8788')throw new RuntimeException('Local development only.');
$item=normalizeItem(['href'=>'999999991','title'=>'V2 Playback Test (temporary fixture)','media_type'=>'movie','year'=>2024,'overview'=>'Temporary verification fixture. MP4 uses the Blender Sintel trailer; HLS uses the public hls.js test stream. Not a catalog recommendation.'],14);
$source=$item['source']['id'];
$detailsKey=hash('sha256','14:details:'.json_encode(['href'=>'999999991','source_id'=>$source]));
$streamsKey=hash('sha256','14:streams:'.json_encode(['href'=>'999999991','source_id'=>$source]));
if(($argv[1] ?? '')==='--remove') {
    query('DELETE FROM cache WHERE cache_key IN (?,?)',[$detailsKey,$streamsKey]);
    query('DELETE FROM titles WHERE id=?',[$item['id']]);
    echo "Browser playback fixture removed.\n";exit;
}
storeItem($item);
$details=['media_type'=>'movie','seasons'=>[],'episodes'=>[],'overview'=>$item['overview'],'retrieval_status'=>'complete','source_id'=>$source];
$streams=['streams'=>[['name'=>'Sintel MP4 test','type'=>'mp4','availability'=>'test_fixture','link'=>'https://media.w3.org/2010/05/sintel/trailer.mp4'],['name'=>'hls.js HLS test','type'=>'hls','availability'=>'test_fixture','link'=>'https://test-streams.mux.dev/x36xhzz/x36xhzz.m3u8']]];
foreach([$detailsKey=>$details,$streamsKey=>$streams] as $key=>$data)query('INSERT INTO cache(cache_key,payload,expires_at,stale_until) VALUES(?,?,?,?) ON DUPLICATE KEY UPDATE payload=VALUES(payload),expires_at=VALUES(expires_at),stale_until=VALUES(stale_until)',[$key,json_encode($data),gmdate('Y-m-d H:i:s',time()+3600),gmdate('Y-m-d H:i:s',time()+3600)]);
echo appUrl('index.php?v=More&id='.$item['id'])."\n";
