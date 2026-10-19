<?php
// Standalone diagnostic page. No credentials, scraping, or stream bypass.
$providers = [
 'VidKing' => 'https://www.vidking.net/embed/movie/%s?autoPlay=true',
 'VidSrc CC' => 'https://vidsrc.cc/v2/embed/movie/%s?autoPlay=true',
 'VidSrc ME' => 'https://vidsrc.me/embed/movie/%s?autoPlay=true',
 'Videasy' => 'https://player.videasy.net/movie/%s?autoPlay=true',
 'Vidcore' => 'https://vidcore.net/movie/%s?autoPlay=true',
 'Vaplayer' => 'https://vaplayer.ru/embed/movie/%s?autoPlay=true',
 'ZXC' => 'https://zxcstream.xyz/player/movie/%s?autoplay=true',
 'Mapple' => 'https://mapple.uk/watch/movie/%s?autoPlay=true'
];
$id = isset($_GET['id']) && preg_match('/^[0-9]{1,12}$/', (string)$_GET['id']) ? (string)$_GET['id'] : '550';
$provider = isset($_GET['provider']) && isset($providers[$_GET['provider']]) ? $_GET['provider'] : 'VidKing';
$embed = sprintf($providers[$provider], $id);

if (isset($_GET['extract']) && $_GET['extract'] === '1') {
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    if (!function_exists('curl_init')) {
        echo json_encode(['ok'=>false,'message'=>'PHP cURL is not installed','sources'=>[]]); exit;
    }
    // PlayTorrio VidSrc HTTP API strategy: only for VidSrc ME.
    // This is a third-party endpoint, not an official VidSrc API.
    if ($provider === 'VidSrc ME') {
        $apiUrl = 'https://data.vidsrcme.ru/api.php?'.http_build_query([
            'type'=>'movie', 'tmdb'=>$id, 'stream_urls'=>''
        ]);
        $api = curl_init($apiUrl);
        curl_setopt_array($api, [
            CURLOPT_RETURNTRANSFER=>true, CURLOPT_FOLLOWLOCATION=>false,
            CURLOPT_CONNECTTIMEOUT=>5, CURLOPT_TIMEOUT=>8,
            CURLOPT_SSL_VERIFYPEER=>true, CURLOPT_SSL_VERIFYHOST=>2,
            CURLOPT_PROTOCOLS=>CURLPROTO_HTTPS,
            CURLOPT_USERAGENT=>'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
            CURLOPT_HTTPHEADER=>['Accept: application/json','Referer: https://cloudorchestranova.com/'],
        ]);
        $apiBody = curl_exec($api);
        $apiError = curl_error($api);
        $apiErrno = curl_errno($api);
        $apiStatus = (int)curl_getinfo($api, CURLINFO_HTTP_CODE);
        curl_close($api);
        $apiSources = [];
        if ($apiBody !== false && $apiStatus === 200 && strlen($apiBody) <= 2000000) {
            $apiJson = json_decode($apiBody, true);
            $urls = $apiJson['data']['stream_urls'] ?? null;
            if (($apiJson['status_code'] ?? null) == 200 && is_array($urls)) {
                foreach ($urls as $url) {
                    if (!is_string($url) || !filter_var($url, FILTER_VALIDATE_URL)) continue;
                    $scheme = strtolower((string)parse_url($url, PHP_URL_SCHEME));
                    if ($scheme !== 'https') continue;
                    $urlPath = (string)parse_url($url, PHP_URL_PATH);
                    if (!preg_match('~\\.(m3u8|mp4)$~i', $urlPath)) continue;
                    $apiSources[$url] = ['url'=>$url,'format'=>preg_match('~\\.m3u8$~i',$urlPath)?'hls':'mp4'];
                    if (count($apiSources) >= 12) break;
                }
            }
        }
        if ($apiSources) {
            echo json_encode([
                'ok'=>true,'provider'=>$provider,'method'=>'playtorrio-vidsrc-api',
                'sources'=>array_values($apiSources),
                'message'=>'Found stream URLs via PlayTorrio VidSrc API strategy. Playback may require provider-specific headers.'
            ], JSON_UNESCAPED_SLASHES); exit;
        }
        // Preserve fallback diagnostics if API is unavailable or returns no media.
        $apiDiagnostic = 'VidSrc API HTTP '.$apiStatus.($apiError ? ' (cURL '.$apiErrno.': '.substr($apiError, 0, 240).')' : '').'; no usable direct URLs. ';
    }
    // Follow a few HTTPS redirects manually, only within the provider's original host
    // (including its www/non-www equivalent). Avoid arbitrary redirects/SSRF.
    $currentUrl = $embed;
    $originalHost = strtolower((string)parse_url($embed, PHP_URL_HOST));
    $allowedHost = preg_replace('/^www\\./', '', $originalHost);
    $body = false; $err = ''; $status = 0; $type = '';
    for ($hop = 0; $hop <= 5; $hop++) {
        $ch = curl_init($currentUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER=>true, CURLOPT_HEADER=>true,
            CURLOPT_FOLLOWLOCATION=>false,
            CURLOPT_CONNECTTIMEOUT=>7, CURLOPT_TIMEOUT=>15,
            CURLOPT_MAXREDIRS=>0, CURLOPT_SSL_VERIFYPEER=>true,
            CURLOPT_SSL_VERIFYHOST=>2, CURLOPT_USERAGENT=>'Mozilla/5.0 (compatible; TryQ8FlixPlaybackTest/1.0)',
            CURLOPT_HTTPHEADER=>['Accept: text/html,application/xhtml+xml'],
            CURLOPT_ENCODING=>'',
            CURLOPT_PROTOCOLS=>CURLPROTO_HTTPS,
        ]);
        $raw = curl_exec($ch);
        $err = curl_error($ch);
        $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $type = (string)curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        $headerSize = (int)curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);
        if ($raw === false) { $body = false; break; }
        $headers = substr($raw, 0, $headerSize);
        $body = substr($raw, $headerSize);
        if (!in_array($status, [301,302,303,307,308], true)) break;
        if (!preg_match('/^Location:\\s*(.+)$/im', $headers, $match)) {
            $err = 'Redirect without Location header'; break;
        }
        $location = trim($match[1]);
        if (str_starts_with($location, '//')) $location = 'https:' . $location;
        if (str_starts_with($location, '/')) {
            $parts = parse_url($currentUrl);
            $location = 'https://' . $parts['host'] . $location;
        } elseif (!preg_match('~^https?://~i', $location)) {
            $base = substr($currentUrl, 0, strrpos($currentUrl, '/') + 1);
            $location = $base . $location;
        }
        $nextHost = strtolower((string)parse_url($location, PHP_URL_HOST));
        $nextScheme = strtolower((string)parse_url($location, PHP_URL_SCHEME));
        $nextPort = parse_url($location, PHP_URL_PORT);
        if ($nextScheme !== 'https' || preg_replace('/^www\\./', '', $nextHost) !== $allowedHost || ($nextPort !== null && $nextPort !== 443) || parse_url($location, PHP_URL_USER) !== null) {
            $err = 'Redirect to '.substr($nextHost, 0, 120).' blocked (outside provider host)';
            break;
        }
        if ($hop === 5) { $err = 'Too many redirects'; break; }
        $currentUrl = $location;
    }
    if ($body === false || $status < 200 || $status >= 300) {
        echo json_encode(['ok'=>false,'message'=>($apiDiagnostic ?? '').'Provider HTTP '.$status.($err ? ': '.$err : '').'; cannot inspect embed response','sources'=>[]]); exit;
    }
    if (strlen($body) > 2000000) $body = substr($body, 0, 2000000);
    $sources = [];
    $addSource = static function($candidate) use (&$sources) {
        $candidate = html_entity_decode(str_replace(['\\/','\\u0026','\\u003d'], ['/','&','='], trim($candidate)), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        if (!filter_var($candidate, FILTER_VALIDATE_URL)) return;
        $scheme = strtolower((string)parse_url($candidate, PHP_URL_SCHEME));
        if (!in_array($scheme, ['http','https'], true)) return;
        $path = (string)parse_url($candidate, PHP_URL_PATH);
        if (!preg_match('~\\.(m3u8|mp4)(?:$)~i', $path)) return;
        if (count($sources) >= 12) return;
        $sources[$candidate] = ['url'=>$candidate,'format'=>preg_match('~\\.m3u8$~i',$path)?'hls':'mp4'];
    };
    if (preg_match('~^(video/mp4|application/vnd\\.apple\\.mpegurl|application/x-mpegurl)~i', $type)) $addSource($embed);
    preg_match_all('~https?://[^\\s"\'<>\\\\]{4,2000}~i', $body, $found);
    foreach ($found[0] as $candidate) $addSource($candidate);
    preg_match_all('~(?:src|file|source|url)\\s*[:=]\\s*["\'](https?:[^"\']+)["\']~i', $body, $found2);
    foreach ($found2[1] as $candidate) $addSource($candidate);
    $sources = array_values($sources);
    echo json_encode(['ok'=>count($sources)>0,'provider'=>$provider,'http_status'=>$status,'sources'=>$sources,'message'=>count($sources)>0?'Found publicly exposed media URLs in the provider response':($apiDiagnostic ?? '').'No direct MP4/HLS URL exposed in initial HTML. This provider may require JavaScript, cookies, or a different authorized integration.'], JSON_UNESCAPED_SLASHES); exit;
}

?><!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>TryQ8Flix Playback Lab</title>
<style>body{margin:0;background:#0b1020;color:#edf2ff;font:16px system-ui,sans-serif}main{max-width:1100px;margin:auto;padding:24px}h1{margin-bottom:5px}p,small{color:#b5c0d8}form,.panel{background:#172039;border:1px solid #2c3b59;border-radius:14px;padding:18px;margin:16px 0}label{display:block;margin:10px 0}input,select,button,a.action{box-sizing:border-box;background:#263655;color:white;border:1px solid #536a94;border-radius:8px;padding:12px;font:inherit}input,select{width:100%}button,a.action{cursor:pointer;display:inline-block;text-decoration:none;margin:5px 4px 5px 0}button:focus,a:focus,input:focus,select:focus{outline:3px solid #6eb7ff}iframe,video{width:100%;aspect-ratio:16/9;background:black;border:0;border-radius:10px}.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(270px,1fr));gap:14px}.muted{overflow-wrap:anywhere}code{color:#b4e1ff}</style></head>
<body><main><h1>TryQ8Flix — Playback Lab</h1><p>Test embed behavior versus a direct video URL on desktop, mobile, or Xiaomi Browser. The diagnostic attempts to detect publicly available direct URLs; it does not bypass protected playback.</p>
<form method="get"><label>TMDB movie ID<input name="id" inputmode="numeric" pattern="[0-9]{1,12}" value="<?= htmlspecialchars($id, ENT_QUOTES) ?>"></label><label>Streaming provider<select name="provider"><?php foreach($providers as $name=>$pattern): ?><option value="<?= htmlspecialchars($name,ENT_QUOTES) ?>" <?= $provider===$name?'selected':'' ?>><?= htmlspecialchars($name) ?></option><?php endforeach; ?></select></label><button type="submit">Load embed</button></form>
<div class="panel"><h2>Embed test: <?= htmlspecialchars($provider) ?></h2><p class="muted"><code><?= htmlspecialchars($embed) ?></code></p><a class="action" href="<?= htmlspecialchars($embed,ENT_QUOTES) ?>" target="_blank" rel="noopener noreferrer">Open in external browser</a><button id="copy" type="button">Copy embed URL</button><iframe src="<?= htmlspecialchars($embed,ENT_QUOTES) ?>" allow="autoplay; fullscreen; encrypted-media; picture-in-picture" allowfullscreen referrerpolicy="strict-origin-when-cross-origin" title="Provider playback test"></iframe><small>Some providers block iframe embedding or require a browser session. Use the external browser button to compare.</small></div>
<div class="panel"><h2>Direct stream test</h2><p id="extract-status" aria-live="polite">Checking the selected provider for a direct stream…</p><label>Detected streams<select id="detected"><option value="">Searching…</option></select></label><button type="button" id="retry-extract">Retry extraction</button><p>Paste an MP4 or HLS URL that you own or are authorized to play. A working URL here can later be tested with Media3 on Android TV.</p><label>Direct URL<input id="direct" type="url" placeholder="https://example.com/video.mp4"></label><button id="play" type="button">Play direct URL</button><button id="clear" type="button">Clear</button><video id="video" controls playsinline></video><p id="status" aria-live="polite"></p><small>Native browser HLS support varies. A failed HLS test here does not mean Android Media3 cannot play it. No headers, cookies, or DRM credentials are extracted.</small></div>
<div class="panel"><h2>Test checklist</h2><div class="grid"><label><input type="checkbox"> Embed loads</label><label><input type="checkbox"> Xiaomi detects qualities</label><label><input type="checkbox"> Remote navigation works</label><label><input type="checkbox"> Fullscreen works</label><label><input type="checkbox"> Back returns correctly</label><label><input type="checkbox"> Direct URL plays</label></div></div>
</main><script>
const embed=<?= json_encode($embed,JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT) ?>;
document.getElementById('copy').onclick=()=>navigator.clipboard.writeText(embed).catch(()=>window.prompt('Copy URL',embed));
const video=document.getElementById('video'), status=document.getElementById('status');
const extractStatus=document.getElementById('extract-status'), detected=document.getElementById('detected');
async function extract(){
 extractStatus.textContent='Checking the provider response for direct media links…';
 detected.innerHTML='<option value="">Searching…</option>';
 try{
  const u=new URL(window.location.href);u.searchParams.set('extract','1');
  const res=await fetch(u.toString(),{cache:'no-store'});
  if(!res.ok)throw new Error('HTTP '+res.status);
  const data=await res.json();detected.innerHTML='';
  if(!data.sources?.length){detected.add(new Option('No direct streams detected',''));extractStatus.textContent=data.message||'No streams found';return}
  data.sources.forEach((s,i)=>detected.add(new Option((i+1)+'. '+s.format.toUpperCase()+' — '+s.url.slice(0,100),s.url)));
  document.getElementById('direct').value=data.sources[0].url;
  extractStatus.textContent=data.sources.length+' direct URL(s) found. First URL filled automatically; select another to switch.';
 }catch(e){detected.innerHTML='<option value="">Extraction request failed</option>';extractStatus.textContent='Extraction failed: '+e.message}
}
detected.onchange=()=>{if(detected.value)document.getElementById('direct').value=detected.value};
document.getElementById('retry-extract').onclick=extract;
extract();
document.getElementById('play').onclick=()=>{const url=document.getElementById('direct').value.trim();try{const u=new URL(url);if(!['https:','http:'].includes(u.protocol))throw Error();video.src=u.href;video.load();video.play().then(()=>status.textContent='Playback started').catch(e=>status.textContent='Playback failed: '+e.message)}catch(e){status.textContent='Enter a valid HTTP(S) video URL'}};
document.getElementById('clear').onclick=()=>{video.pause();video.removeAttribute('src');video.load();document.getElementById('direct').value='';status.textContent=''};
video.addEventListener('error',()=>status.textContent='Video error. Check format, CORS, URL expiry and permissions.');
</script></body></html>