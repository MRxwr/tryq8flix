<?php

$videoUrl = $_GET['url'] ?? '';

$head = curl_init($videoUrl);
curl_setopt_array($head, [
    CURLOPT_NOBODY => true,
    CURLOPT_HEADER => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
]);

$headers = curl_exec($head);
curl_close($head);

$contentLength = null;

if (preg_match('/Content-Length:\s*(\d+)/i', $headers, $m)) {
    $contentLength = $m[1];
}

header('Content-Type: video/mp4');
header('Content-Disposition: attachment; filename="video.mp4"');

if ($contentLength) {
    header('Content-Length: ' . $contentLength);
}

$stream = curl_init($videoUrl);

curl_setopt_array($stream, [
    CURLOPT_RETURNTRANSFER => false,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'] ?? 'Mozilla/5.0',
]);

curl_setopt($stream, CURLOPT_WRITEFUNCTION, function ($ch, $chunk) {
    echo $chunk;
    flush();
    return strlen($chunk);
});

curl_exec($stream);
curl_close($stream);