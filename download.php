<?php

$videoUrl = $_GET['url'];

$ch = curl_init($videoUrl);

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HEADER => true,
    CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'] ?? 'Mozilla/5.0',
]);

$result = curl_exec($ch);

echo '<pre>';
echo 'HTTP CODE: ' . curl_getinfo($ch, CURLINFO_HTTP_CODE) . "\n";
echo 'CONTENT TYPE: ' . curl_getinfo($ch, CURLINFO_CONTENT_TYPE) . "\n";
echo 'ERROR: ' . curl_error($ch) . "\n";
echo '</pre>';