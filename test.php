<?php
include_once('admin/includes/config.php');
include_once('admin/includes/functions.php');

$file_id = isset($_GET["id"]) ? trim($_GET["id"]) : 'yr0vg9ubunty';

echo "File ID: " . htmlspecialchars($file_id) . "<br><br>";

$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://updown.cam/' . $file_id,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => false,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => array('op' => 'download2','id' => $file_id),
  CURLOPT_HTTPHEADER => array(
    'Cookie: affiliate=JS1hsUeoifu8xhVytIUUq8LrYGsfANNIvLIMeAZNz3rEsbsVHI5HICg5ykyMumHrREYjIPCsvFX2CDbo2ErlrY3BRx3H6lD%2Fqok%3D'
  ),
  CURLOPT_HEADER => true,
));

$response = curl_exec($curl);
$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
$headers = substr($response, 0, $header_size);
$body = substr($response, $header_size);

// Check if there's a Location header (redirect to actual video URL)
if (preg_match('/Location:\s*(.+)/i', $headers, $matches)) {
    $video_url = trim($matches[1]);
    echo "<strong>Video URL (from redirect):</strong><br>";
    echo htmlspecialchars($video_url) . "<br><br>";
    echo "<a href='" . htmlspecialchars($video_url) . "' target='_blank'>Open Video</a>";
} else {
    // If no redirect, parse HTML to find video link
    $dom = str_get_html($body);
    if ($dom) {
        // Look for video source tag
        $video = $dom->find('video source', 0);
        if ($video && $video->hasAttribute('src')) {
            $video_url = $video->getAttribute('src');
            echo "<strong>Video URL (from source tag):</strong><br>";
            echo htmlspecialchars($video_url) . "<br><br>";
            echo "<a href='" . htmlspecialchars($video_url) . "' target='_blank'>Open Video</a>";
        } else {
            // Look for direct download link
            $link = $dom->find('a[href*=".mp4"], a[href*=".mkv"], a[href*=".avi"]', 0);
            if ($link) {
                $video_url = $link->href;
                echo "<strong>Video URL (from download link):</strong><br>";
                echo htmlspecialchars($video_url) . "<br><br>";
                echo "<a href='" . htmlspecialchars($video_url) . "' target='_blank'>Open Video</a>";
            } else {
                echo "<strong>Could not find video URL.</strong><br><br>";
                echo "<strong>Headers:</strong><br><pre>" . htmlspecialchars($headers) . "</pre><br>";
                echo "<strong>Body (first 2000 chars):</strong><br><pre>" . htmlspecialchars(substr($body, 0, 2000)) . "</pre>";
            }
        }
        $dom->clear();
        unset($dom);
    } else {
        echo "<strong>Could not parse HTML.</strong><br><br>";
        echo "<strong>Headers:</strong><br><pre>" . htmlspecialchars($headers) . "</pre><br>";
        echo "<strong>Body (first 2000 chars):</strong><br><pre>" . htmlspecialchars(substr($body, 0, 2000)) . "</pre>";
    }
}

curl_close($curl);

?>