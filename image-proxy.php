<?php
// image-proxy.php
// Usage: image-proxy.php?url=<image_url>

if (!isset($_GET['url'])) {
    http_response_code(400);
    echo 'Missing url parameter.';
    exit;
}

$url = $_GET['url'];

// Validate URL (basic check)
if (!filter_var(urldecode($url), FILTER_VALIDATE_URL)) {
    http_response_code(400);
    echo 'Invalid URL.';
    exit;
}

// Get image headers to determine content type
$headers = @get_headers($url, 1);
if (!$headers || strpos($headers[0], '200') === false) {
    http_response_code(404);
    echo 'Image not found.';
    exit;
}

$contentType = isset($headers['Content-Type']) ? $headers['Content-Type'] : 'image/jpeg';
header('Content-Type: ' . $contentType);

// Optionally cache for 1 day
header('Cache-Control: public, max-age=86400');

// Output the image
readfile($url);
exit;
