<?php
$path=rawurldecode(parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH) ?: '/');
if(preg_match('#^/(?:app|config|storage|bin|tests|admin|try2|views|api/views)(?:/|$)#i',$path) || str_contains($path,'..') || str_contains($path,'\\') || preg_match('#/(?:\.|router\.php)#',$path)) {http_response_code(404);echo 'Not found';return true;}
$allowed=['/','/index.php','/api/index.php','/image-proxy.php','/video-proxy.php','/videoPlayer.php','/manifest.json','/sw.js','/offline.html'];
if(!in_array($path,$allowed,true) && !preg_match('#^/(?:assets|logos)/[a-zA-Z0-9_.-]+\.(?:css|js|svg|png|jpg|webp)$#D',$path)) {http_response_code(404);echo 'Not found';return true;}
return false;
