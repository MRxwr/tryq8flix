<?php
// image-proxy.php
// Usage: image-proxy.php?url=<image_url>

if (!isset($_GET['url'])) {
    http_response_code(400);
    echo 'Missing url parameter.';
    exit;
}

$url = $_GET['url'];

// Initialize cURL session to use CodeBeautify as a proxy
$ch = curl_init();

curl_setopt_array($ch, array(
    CURLOPT_URL => 'https://www.codebeautify.com/URLService',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => array('path' => $url),
    CURLOPT_HTTPHEADER => array(
        'Origin: https://codebeautify.org',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
    ),
    CURLOPT_SSL_VERIFYPEER => false
));

// Execute cURL session and get the response
$response = curl_exec($ch);

// Check for cURL errors
if (curl_errno($ch)) {
    http_response_code(500);
    echo 'Error fetching image: ' . curl_error($ch);
    curl_close($ch);
    exit;
}

// Map common extensions to content types
$ext = strtolower(pathinfo($url, PATHINFO_EXTENSION));
$mime_types = [
    'jpg'  => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png'  => 'image/png',
    'gif'  => 'image/gif',
    'webp' => 'image/webp'
];

$content_type = $mime_types[$ext] ?? 'image/jpeg';
header("Content-Type: $content_type");
header("Access-Control-Allow-Origin: *");
header("Cache-Control: public, max-age=86400");

echo $response;
exit;
