<?php
// proxy.php - A simple web proxy to access content through your server
// Usage: proxy.php?url=https://example.com

// Include config and functions
require_once("admin/includes/config.php");
require_once("admin/includes/functions/index.php");

// Security check - only logged in users can access this proxy
$user = checkLogin();
if (empty($user["id"])) {
    exit("Access denied. Please log in first.");
}

// Get the target URL
if (!isset($_GET['url'])) {
    include 'proxy-form.html';
    exit;
}

$url = $_GET['url'];

// Basic URL validation
if (!filter_var($url, FILTER_VALIDATE_URL)) {
    exit("Invalid URL format");
}

// Initialize cURL
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36');
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_REFERER, $url); // Add referer for sites that check it
curl_setopt($ch, CURLOPT_ENCODING, ''); // Accept all encodings

// Execute the request
$response = curl_exec($ch);

// Check for errors
if (curl_errno($ch)) {
    exit('Error fetching content: ' . curl_error($ch));
}

// Get response info
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);

// Close cURL
curl_close($ch);

// Check HTTP status
if ($httpCode != 200) {
    exit("Error: Site returned HTTP code $httpCode");
}

// Extract headers and body
$headers = substr($response, 0, $headerSize);
$body = substr($response, $headerSize);

// Only process HTML content
if (strpos($contentType, 'text/html') !== false) {
    // Base URL for resolving relative paths
    $parsedUrl = parse_url($url);
    $baseUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
    if (isset($parsedUrl['port'])) $baseUrl .= ':' . $parsedUrl['port'];
    
    // Replace relative URLs with absolute ones through the proxy
    $body = preg_replace_callback(
        '/(src|href|action)=(["\'])((?!http|https|\/\/|data:|javascript:|mailto:|tel:)[^"\']+)(["\'])/i',
        function($matches) use ($baseUrl, $url) {
            $attribute = $matches[1];
            $quoteType = $matches[2];
            $path = $matches[3];
            
            // Handle relative URLs
            if (substr($path, 0, 1) == '/') {
                $absoluteUrl = $baseUrl . $path;
            } else {
                $dir = dirname($url);
                $absoluteUrl = $dir . '/' . $path;
            }
            
            return "$attribute=$quoteType" . "proxy.php?url=" . urlencode($absoluteUrl) . "$quoteType";
        },
        $body
    );
    
    // Replace absolute URLs
    $body = preg_replace_callback(
        '/(src|href|action)=(["\'])(https?:\/\/[^"\']+)(["\'])/i',
        function($matches) {
            $attribute = $matches[1];
            $quoteType = $matches[2];
            $url = $matches[3];
            
            return "$attribute=$quoteType" . "proxy.php?url=" . urlencode($url) . "$quoteType";
        },
        $body
    );
    
    // Handle CSS background images and other inline URLs
    $body = preg_replace_callback(
        '/url\(["\']?((?!data:)[^)]+)["\']?\)/i',
        function($matches) use ($baseUrl, $url) {
            $path = $matches[1];
            
            // Skip data URLs
            if (strpos($path, 'data:') === 0) {
                return "url(" . $path . ")";
            }
            
            // Handle relative vs absolute URLs
            if (strpos($path, 'http') === 0) {
                $absoluteUrl = $path;
            } else if (substr($path, 0, 1) == '/') {
                $absoluteUrl = $baseUrl . $path;
            } else {
                $dir = dirname($url);
                $absoluteUrl = $dir . '/' . $path;
            }
            
            return "url(image-proxy.php?url=" . urlencode($absoluteUrl) . ")";
        },
        $body
    );
    
    // Add a base target to open links in the proxy
    $body = str_replace('<head>', '<head><base target="_self">', $body);
    
    // Try to fix video tags to go through the proxy
    $body = preg_replace_callback(
        '/<video[^>]*>(.*?)<\/video>/is',
        function($matches) {
            $videoTag = $matches[0];
            // Rewrite video source attributes
            $videoTag = preg_replace_callback(
                '/src=(["\'])(https?:\/\/[^"\']+)(["\'])/i',
                function($srcMatches) {
                    return 'src=' . $srcMatches[1] . 'video-proxy.php?url=' . urlencode($srcMatches[2]) . $srcMatches[3];
                },
                $videoTag
            );
            // Also handle source tags inside video
            $videoTag = preg_replace_callback(
                '/<source[^>]*src=(["\'])(https?:\/\/[^"\']+)(["\'])/i',
                function($srcMatches) {
                    return '<source src=' . $srcMatches[1] . 'video-proxy.php?url=' . urlencode($srcMatches[2]) . $srcMatches[3];
                },
                $videoTag
            );
            return $videoTag;
        },
        $body
    );
    
    // Try to fix iframe embeds (like YouTube)
    $body = preg_replace_callback(
        '/<iframe[^>]*src=(["\'])(https?:\/\/[^"\']+)(["\'])[^>]*><\/iframe>/i',
        function($matches) {
            $iframeSrc = $matches[2];
            // Don't proxy the iframe if it's already a proxy URL
            if (strpos($iframeSrc, 'proxy.php') !== false) {
                return $matches[0];
            }
            return str_replace(
                'src=' . $matches[1] . $iframeSrc . $matches[3],
                'src=' . $matches[1] . 'proxy.php?url=' . urlencode($iframeSrc) . $matches[3],
                $matches[0]
            );
        },
        $body
    );
    
    // Add proxy frame for context
    echo '<!DOCTYPE html>
<html>
<head>
    <title>Web Proxy</title>
    <style>
        body { margin: 0; padding: 0; font-family: Arial, sans-serif; }
        #proxy-bar { background: #333; color: white; padding: 10px; display: flex; justify-content: space-between; align-items: center; }
        #proxy-bar a { color: #fff; text-decoration: none; }
        #proxy-bar a:hover { text-decoration: underline; }
        #proxy-content { width: 100%; height: calc(100vh - 50px); border: none; }
        #url-input { width: 60%; padding: 5px; }
        #go-btn { padding: 5px 10px; }
    </style>
</head>
<body>
    <div id="proxy-bar">
        <div>
            <form method="get" action="proxy.php">
                <input type="text" id="url-input" name="url" value="' . htmlspecialchars($url) . '">
                <input type="submit" id="go-btn" value="Go">
            </form>
        </div>
        <div>
            <a href="proxy.php">New Proxy</a> | 
            <a href="' . htmlspecialchars($url) . '" target="_blank">Open Original</a>
        </div>
    </div>
    <iframe id="proxy-content" sandbox="allow-same-origin allow-scripts allow-forms allow-popups" srcdoc="' . htmlspecialchars($body) . '"></iframe>
</body>
</html>';
} else {
    // For non-HTML content, pass through the content type and body
    header("Content-Type: $contentType");
    
    // Handle streaming media
    if (strpos($contentType, 'video/') !== false || 
        strpos($contentType, 'audio/') !== false || 
        strpos($contentType, 'application/octet-stream') !== false) {
        
        // For large files, consider range requests
        if (isset($_SERVER['HTTP_RANGE'])) {
            // Pass through the range header to the remote server
            // This requires a more complex implementation for proper support
            header("HTTP/1.1 206 Partial Content");
            header("Accept-Ranges: bytes");
        }
    }
    
    echo $body;
}
?>
