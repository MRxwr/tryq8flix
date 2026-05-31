<?php
// image-proxy.php
// Usage: image-proxy.php?url=<image_url>

if (!isset($_GET['url'])) {
    http_response_code(400);
    echo 'Missing url parameter.';
    exit;
}

$url = $_GET['url'];

$cacheDir = 'temp_images/';
if (!is_dir($cacheDir)) {
    mkdir($cacheDir, 0777, true);
}

// Cleanup old files (older than 5 mins)
$now = time();
if ($handle = opendir($cacheDir)) {
    while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != "..") {
            $filePath = $cacheDir . $file;
            if ($now - filemtime($filePath) > 300) {
                unlink($filePath);
            }
        }
    }
    closedir($handle);
}

$cacheFileName = md5($url);
$cacheFileBase = $cacheDir . $cacheFileName;

// Find existing cache file regardless of extension
$existingFiles = glob($cacheFileBase . '.*');
$cacheFile = null;
$cacheMeta = $cacheFileBase . '.json';

if (!empty($existingFiles)) {
    foreach ($existingFiles as $file) {
        if (strpos($file, '.json') === false) {
            $cacheFile = $file;
            break;
        }
    }
}

// Serve from cache if it exists and is less than 5 minutes old
if ($cacheFile && file_exists($cacheFile) && file_exists($cacheMeta) && ($now - filemtime($cacheFile) < 300)) {
    $meta = json_decode(file_get_contents($cacheMeta), true);
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: ' . $meta['Content-Type']);
    header('Cache-Control: public, max-age=300');
    // Suggest a filename for the browser
    $ext = pathinfo($cacheFile, PATHINFO_EXTENSION);
    header('Content-Disposition: inline; filename="' . $cacheFileName . '.' . $ext . '"');
    readfile($cacheFile);
    exit;
}

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
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_HEADER => true
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

// Map common content types to extensions
$extensions = [
    'image/jpeg' => 'jpg',
    'image/jpg' => 'jpg',
    'image/png' => 'png',
    'image/gif' => 'gif',
    'image/webp' => 'webp',
    'image/svg+xml' => 'svg',
    'image/x-icon' => 'ico'
];

$ext = 'bin';
if (isset($extensions[strtolower($contentType)])) {
    $ext = $extensions[strtolower($contentType)];
} else {
    // Try to get extension from URL if content-type is generic
    $urlPath = parse_url($url, PHP_URL_PATH);
    $urlExt = pathinfo($urlPath, PATHINFO_EXTENSION);
    if ($urlExt && strlen($urlExt) <= 4) {
        $ext = $urlExt;
    }
}

$cacheFile = $cacheFileBase . '.' . $ext;

// Get header size and extract the body
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$body = substr($response, $headerSize);

// Save to cache
file_put_contents($cacheFile, $body);
file_put_contents($cacheMeta, json_encode(['Content-Type' => $contentType]));

// Set response headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: ' . $contentType);
header('Content-Disposition: inline; filename="' . $cacheFileName . '.' . $ext . '"');
header('Cache-Control: public, max-age=300'); // Cache for 5 mins

// Output the image
echo $body;

// Close cURL session
curl_close($ch);
exit;
