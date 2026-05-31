<?php
// image-proxy.php
// Usage: image-proxy.php?url=<image_url>

if (!isset($_GET['url'])) {
    http_response_code(400);
    echo 'Missing url parameter.';
    exit;
}

$url = $_GET['url']; // Keep the URL as-is, don't decode it
/*
// Initialize cURL session
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
// Add Referer header based on the target URL
$parsedUrl = parse_url($url);
$referer = $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . '/';
curl_setopt($ch, CURLOPT_REFERER, $referer);
curl_setopt($ch, CURLOPT_HEADER, 1);

// Execute cURL session and get the response
$response = curl_exec($ch);
*/
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

// Check if the request was successful
if (curl_errno($ch)) {
    http_response_code(404);
    echo 'Error fetching image: ' . curl_error($ch);
    curl_close($ch);
    exit;
}

// Get the status code
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
if ($httpcode != 200) {
    http_response_code($httpcode);
    echo 'Image not found. Status code: ' . $httpcode;
    curl_close($ch);
    exit;
}

// Get content type
$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

// Get header size and extract the body
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$body = substr($response, $headerSize);

// Set response headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: ' . $contentType);
header('Cache-Control: public, max-age=86400'); // Cache for 1 day

// Output the image
echo $body;

// Close cURL session
curl_close($ch);
exit;
