<?php
require __DIR__.'/../app/bootstrap.php';
$passed=0;$failed=0;
function test(string $name,callable $callback): void {global $passed,$failed;try{$callback();echo 'PASS '.$name."\n";$passed++;}catch(Throwable $error){echo 'FAIL '.$name.': '.$error->getMessage()."\n";$failed++;}}
function expect(bool $value,string $message='Assertion failed'): void {if(!$value)throw new RuntimeException($message);}
function rejected(callable $callback): void {try{$callback();}catch(AppError){return;}throw new RuntimeException('Unsafe input was accepted');}
test('private and reserved addresses rejected',function(){foreach(['127.0.0.1','10.0.0.1','192.168.1.2','169.254.169.254','0.0.0.0','::1','::ffff:127.0.0.1','fc00::1','fe80::1'] as $ip)expect(!publicIp($ip),$ip);expect(publicIp('8.8.8.8'));});
test('only approved hosts, ports and schemes',function(){foreach(['file:///etc/passwd','gopher://api.themoviedb.org/','https://api.themoviedb.org.evil.test/','https://user:pass@api.themoviedb.org/','https://api.themoviedb.org:3306/','http://127.0.0.1/'] as $url)rejected(fn()=>validateRemoteUrl($url));expect(validateRemoteUrl('https://api.themoviedb.org/3/movie/1')[0]==='api.themoviedb.org');});
test('relative URLs resolved correctly',function(){expect(absoluteUrl('../video?a=1','https://example.com/show/season/')==='https://example.com/show/video?a=1');expect(absoluteUrl('?serv=2','https://example.com/player.php?serv=1')==='https://example.com/player.php?serv=2');expect(absoluteUrl('//example.com/video','https://example.com/')==='https://example.com/video');});
test('Arabic titles and digits normalized',fn()=>expect(normalizedTitle('إختبَار ٢٠٢٤')==='اختبار 2024'));
test('stable IDs ignore artwork and separate media types',function(){$base=['href'=>'123','title'=>'Example','image'=>'https://image.tmdb.org/a.jpg'];$first=normalizeItem($base,14);$second=normalizeItem(array_replace($base,['image'=>'https://image.tmdb.org/b.jpg']),14);expect($first['id']===$second['id']);expect($first['id']!==normalizeItem($base,13)['id']);expect($first['rating']===null);});
test('poster URLs reject executable schemes',fn()=>expect(imageUrl('javascript:alert(1)')===''));
test('unclassified empty lists are not movies',function(){$data=normalizeDetails(['seasons'=>[],'episodes'=>[]],['kind'=>'scraper'],'https://example.com/');expect($data['media_type']==='unknown');expect($data['retrieval_status']==='unclassified');});
test('episode identity is independent of source',function(){$a=['title_id'=>'canonical','href'=>'https://provider-a.test/'];$b=['title_id'=>'canonical','href'=>'https://provider-b.test/'];expect(itemIdentity($a,'https://provider-a.test/ep',1,2)===itemIdentity($b,'https://provider-b.test/ep',1,2));});
test('signed query parameters remain intact',function(){$streams=normalizeStreams([['link'=>'https://www.vidking.net/video.m3u8?token=abc&expires=123']],'https://www.vidking.net/');expect($streams[0]['link']==='https://www.vidking.net/video.m3u8?token=abc&expires=123');expect($streams[0]['type']==='hls');});
test('provider stream hosts must be approved',fn()=>expect(normalizeStreams([['link'=>'https://evil.test/embed']],'https://www.vidking.net/')===[]));
test('original app files unchanged',function(){$manifest=json_decode(file_get_contents(V2_ROOT.'/storage/original-hashes.json'),true);foreach($manifest as $file=>$hash)expect(hash_file('sha256',V2_ROOT.'/../'.$file)===$hash,$file.' changed');});
test('copied scraper has no public proxy dependency at runtime',function(){
    $script=<<<'PHP'
<?php
require 'try2/templates/simple_html_dom.php';
eval('namespace V2Legacy; function file_get_contents($url){return \\file_get_contents("tests/fixtures/tuktuk.html");}');
require 'admin/includes/functions/tuktuk.php';
$items=V2Legacy\tuktukHome('https://example.com/');
if(count($items)!==1 || $items[0]['title']!=='فيلم اختبار ٢٠٢٤' || $items[0]['href']!=='/movie/test-2024/')exit(1);
echo 'ok';
PHP;
    $process=proc_open([PHP_BINARY],[['pipe','r'],['pipe','w'],['pipe','w']],$pipes,V2_ROOT);fwrite($pipes[0],$script);fclose($pipes[0]);$out=stream_get_contents($pipes[1]);fclose($pipes[1]);$err=stream_get_contents($pipes[2]);fclose($pipes[2]);$status=proc_close($process);expect($status===0 && $out==='ok',$err.$out);
});
$count=0;
foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator(V2_ROOT,FilesystemIterator::SKIP_DOTS)) as $file){if($file->getExtension()!=='php' || str_contains($file->getPathname(),'node_modules'))continue;$out=[];$status=0;exec(escapeshellarg(PHP_BINARY).' -l '.escapeshellarg($file->getPathname()).' 2>&1',$out,$status);$count++;if($status){echo implode("\n",$out)."\n";$failed++;}}
echo "\n$passed tests passed, $failed failures; $count PHP files syntax checked.\n";exit($failed ? 1 : 0);
