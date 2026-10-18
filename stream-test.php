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
?><!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>TryQ8Flix Playback Lab</title>
<style>body{margin:0;background:#0b1020;color:#edf2ff;font:16px system-ui,sans-serif}main{max-width:1100px;margin:auto;padding:24px}h1{margin-bottom:5px}p,small{color:#b5c0d8}form,.panel{background:#172039;border:1px solid #2c3b59;border-radius:14px;padding:18px;margin:16px 0}label{display:block;margin:10px 0}input,select,button,a.action{box-sizing:border-box;background:#263655;color:white;border:1px solid #536a94;border-radius:8px;padding:12px;font:inherit}input,select{width:100%}button,a.action{cursor:pointer;display:inline-block;text-decoration:none;margin:5px 4px 5px 0}button:focus,a:focus,input:focus,select:focus{outline:3px solid #6eb7ff}iframe,video{width:100%;aspect-ratio:16/9;background:black;border:0;border-radius:10px}.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(270px,1fr));gap:14px}.muted{overflow-wrap:anywhere}code{color:#b4e1ff}</style></head>
<body><main><h1>TryQ8Flix — Playback Lab</h1><p>Test embed behavior versus a direct video URL on desktop, mobile, or Xiaomi Browser. This page does not extract streams or bypass protected playback.</p>
<form method="get"><label>TMDB movie ID<input name="id" inputmode="numeric" pattern="[0-9]{1,12}" value="<?= htmlspecialchars($id, ENT_QUOTES) ?>"></label><label>Streaming provider<select name="provider"><?php foreach($providers as $name=>$pattern): ?><option value="<?= htmlspecialchars($name,ENT_QUOTES) ?>" <?= $provider===$name?'selected':'' ?>><?= htmlspecialchars($name) ?></option><?php endforeach; ?></select></label><button type="submit">Load embed</button></form>
<div class="panel"><h2>Embed test: <?= htmlspecialchars($provider) ?></h2><p class="muted"><code><?= htmlspecialchars($embed) ?></code></p><a class="action" href="<?= htmlspecialchars($embed,ENT_QUOTES) ?>" target="_blank" rel="noopener noreferrer">Open in external browser</a><button id="copy" type="button">Copy embed URL</button><iframe src="<?= htmlspecialchars($embed,ENT_QUOTES) ?>" allow="autoplay; fullscreen; encrypted-media; picture-in-picture" allowfullscreen referrerpolicy="strict-origin-when-cross-origin" title="Provider playback test"></iframe><small>Some providers block iframe embedding or require a browser session. Use the external browser button to compare.</small></div>
<div class="panel"><h2>Direct stream test</h2><p>Paste an MP4 or HLS URL that you own or are authorized to play. A working URL here can later be tested with Media3 on Android TV.</p><label>Direct URL<input id="direct" type="url" placeholder="https://example.com/video.mp4"></label><button id="play" type="button">Play direct URL</button><button id="clear" type="button">Clear</button><video id="video" controls playsinline></video><p id="status" aria-live="polite"></p><small>Native browser HLS support varies. A failed HLS test here does not mean Android Media3 cannot play it. No headers, cookies, or DRM credentials are extracted.</small></div>
<div class="panel"><h2>Test checklist</h2><div class="grid"><label><input type="checkbox"> Embed loads</label><label><input type="checkbox"> Xiaomi detects qualities</label><label><input type="checkbox"> Remote navigation works</label><label><input type="checkbox"> Fullscreen works</label><label><input type="checkbox"> Back returns correctly</label><label><input type="checkbox"> Direct URL plays</label></div></div>
</main><script>
const embed=<?= json_encode($embed,JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT) ?>;
document.getElementById('copy').onclick=()=>navigator.clipboard.writeText(embed).catch(()=>window.prompt('Copy URL',embed));
const video=document.getElementById('video'), status=document.getElementById('status');
document.getElementById('play').onclick=()=>{const url=document.getElementById('direct').value.trim();try{const u=new URL(url);if(!['https:','http:'].includes(u.protocol))throw Error();video.src=u.href;video.load();video.play().then(()=>status.textContent='Playback started').catch(e=>status.textContent='Playback failed: '+e.message)}catch(e){status.textContent='Enter a valid HTTP(S) video URL'}};
document.getElementById('clear').onclick=()=>{video.pause();video.removeAttribute('src');video.load();document.getElementById('direct').value='';status.textContent=''};
video.addEventListener('error',()=>status.textContent='Video error. Check format, CORS, URL expiry and permissions.');
</script></body></html>