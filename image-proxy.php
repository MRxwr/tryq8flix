<?php
// image-proxy.php
// Usage: image-proxy.php?url=<image_url>

if (!isset($_GET['url'])) {
    http_response_code(400);
    echo 'Missing url parameter.';
    exit;
}

function fix_arabic_url($url) {
    if (empty($url)) return $url;
    return preg_replace_callback('/[^\x21-\x7e]/', function($match) {
        return rawurlencode($match[0]);
    }, $url);
}

$url = fix_arabic_url($_GET['url']);

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
    $contentType = $meta['Content-Type'];
    
    // Safety check: if content type is not an image, try to guess from extension
    if (strpos($contentType, 'image/') !== 0) {
        $ext = pathinfo($cacheFile, PATHINFO_EXTENSION);
        $mimes = [
            'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png',
            'gif' => 'image/gif', 'webp' => 'image/webp', 'svg' => 'image/svg+xml'
        ];
        if (isset($mimes[$ext])) {
            $contentType = $mimes[$ext];
        }
    }

    header('Access-Control-Allow-Origin: *');
    header('Content-Type: ' . $contentType);
    header('Cache-Control: public, max-age=300');
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
$mimes = [
    'image/jpeg' => 'jpg',
    'image/jpg' => 'jpg',
    'image/png' => 'png',
    'image/gif' => 'gif',
    'image/webp' => 'webp',
    'image/svg+xml' => 'svg',
    'image/x-icon' => 'ico'
];

$ext = 'bin';
if (isset($mimes[strtolower($contentType)])) {
    $ext = $mimes[strtolower($contentType)];
} else {
    // Try to get extension from URL if content-type is generic
    $urlPath = parse_url($url, PHP_URL_PATH);
    $urlExt = pathinfo($urlPath, PATHINFO_EXTENSION);
    if ($urlExt && strlen($urlExt) <= 4) {
        $ext = $urlExt;
        // Also fix the content type if we found an extension
        $invMimes = array_flip($mimes);
        if (isset($invMimes[$ext])) {
            $contentType = $invMimes[$ext];
        }
    }
}

$cacheFile = $cacheFileBase . '.' . $ext;

// Get header size and extract the body
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$body = substr($response, $headerSize);

// Final check: if we still have a non-image content-type but data looks like image
if (strpos($contentType, 'image/') !== 0 && !empty($body)) {
    if (strpos($body, "\xff\xd8") === 0) { $contentType = 'image/jpeg'; $newExt = 'jpg'; }
    elseif (strpos($body, "\x89PNG") === 0) { $contentType = 'image/png'; $newExt = 'png'; }
    elseif (strpos($body, "GIF8") === 0) { $contentType = 'image/gif'; $newExt = 'gif'; }
    elseif (strpos($body, "RIFF") === 0 && strpos($body, "WEBP") === 8) { $contentType = 'image/webp'; $newExt = 'webp'; }
    
    if (isset($newExt)) {
        $ext = $newExt;
        $cacheFile = $cacheFileBase . '.' . $ext;
    }
}

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
