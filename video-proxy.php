<?php
// video-proxy.php - A specialized proxy for video files
// Use this to stream videos through your server

if (!isset($_GET['url'])) {
    exit("Missing URL parameter");
}

$url = $_GET['url'];

// Basic URL validation
if (!filter_var($url, FILTER_VALIDATE_URL)) {
    exit("Invalid URL format");
}

// Get the file information
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_NOBODY, true); // HEAD request to get headers only
curl_setopt($ch, CURLOPT_REFERER, $url);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36');

$response = curl_exec($ch);
$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
$fileSize = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
curl_close($ch);

// Check for range requests (used in video streaming)
$range = '';
if (isset($_SERVER['HTTP_RANGE'])) {
    $range = $_SERVER['HTTP_RANGE'];
    header('HTTP/1.1 206 Partial Content');
} else {
    header('HTTP/1.1 200 OK');
}

// Set the appropriate headers
header('Content-Type: ' . $contentType);
header('Accept-Ranges: bytes');
if ($fileSize > 0) {
    header('Content-Length: ' . $fileSize);
}
header('Cache-Control: public, max-age=86400');

// Handle range requests
if ($range) {
    $rangeHeader = 'Range: ' . $range;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [$rangeHeader]);
} else {
    $ch = curl_init($url);
}

// Stream the content
curl_setopt($ch, CURLOPT_RETURNTRANSFER, false); // Important for streaming
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_REFERER, $url);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36');
curl_setopt($ch, CURLOPT_WRITEFUNCTION, function($curl, $data) {
    echo $data;
    return strlen($data);
});

curl_exec($ch);
curl_close($ch);
exit;
