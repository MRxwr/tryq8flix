<?php
/**
 * video-downloader-v2.php
 * Handles downloading a video from a remote URL to the local server, 
 * then serving it to the user.
 */

require_once("admin/includes/config.php");
require_once("admin/includes/functions/index.php");

// 1. Basic check
if (!isset($_GET['url']) || empty($_GET['url'])) {
    die("Missing URL parameter.");
}

$remoteUrl = $_GET['url'];

// 2. Security / Validation
if (!filter_var($remoteUrl, FILTER_VALIDATE_URL)) {
    die("Invalid URL format.");
}

// 3. Define local storage
$storageDir = 'downloads/temp_videos/';
if (!is_dir($storageDir)) {
    mkdir($storageDir, 0777, true);
}

// Create a unique filename based on the URL hash
$ext = pathinfo(parse_url($remoteUrl, PHP_URL_PATH), PATHINFO_EXTENSION);
if (!$ext || strlen($ext) > 4) $ext = 'mp4';
$filename = md5($remoteUrl) . '.' . $ext;
$localPath = $storageDir . $filename;

// 4. Download to server if not exists (or always download for fresh copy)
// For this implementation, we check if it exists to avoid redundant downloads within a session.
if (!file_exists($localPath) || (time() - filemtime($localPath) > 3600)) {
    $fp = fopen($localPath, 'w+');
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $remoteUrl);
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 300); // 5 mins timeout
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
    
    $success = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    fclose($fp);

    if (!$success || $httpCode >= 400) {
        if (file_exists($localPath)) unlink($localPath);
        die("Failed to download video to server. (HTTP $httpCode)");
    }
}

// 5. Serve to user
if (file_exists($localPath)) {
    $cleanFilename = isset($_GET['title']) ? preg_replace('/[^a-zA-Z0-9_\-]/', '_', $_GET['title']) . '.mp4' : 'video.mp4';
    
    header('Content-Description: File Transfer');
    header('Content-Type: video/mp4');
    header('Content-Disposition: attachment; filename="' . $cleanFilename . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($localPath));
    
    // Clear buffer and stream file
    ob_clean();
    flush();
    readfile($localPath);
    
    // Optional: Delete after serving? 
    // Usually better to keep for a while or clean via cron.
    exit;
} else {
    die("File not found on server.");
}
