<?php
// image-proxy.php
// Usage: image-proxy.php?url=<image_url>

if (!isset($_GET['url'])) {
    http_response_code(400);
    echo 'Missing url parameter.';
    exit;
}

$originalUrl = $_GET['url'];
$decodedUrl = urldecode($originalUrl);

// Validate URL (basic check)
if (!filter_var($decodedUrl, FILTER_VALIDATE_URL)) {
    http_response_code(400);
    echo 'Invalid URL.';
    exit;
}

// Re-encode only the path if it contains non-ASCII
$parts = parse_url($decodedUrl);
if (isset($parts['path'])) {
    $encodedPath = preg_replace_callback('/[^A-Za-z0-9_\-\.~\/]/u', function ($matches) {
        return rawurlencode($matches[0]);
    }, $parts['path']);
    $rebuiltUrl = $parts['scheme'] . '://' . $parts['host'];
    if (isset($parts['port'])) $rebuiltUrl .= ':' . $parts['port'];
    $rebuiltUrl .= $encodedPath;
    if (isset($parts['query'])) $rebuiltUrl .= '?' . $parts['query'];
    if (isset($parts['fragment'])) $rebuiltUrl .= '#' . $parts['fragment'];
} else {
    $rebuiltUrl = $decodedUrl;
}

// Get image headers to determine content type
$headers = @get_headers($rebuiltUrl, 1);
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
readfile($rebuiltUrl);
exit;
