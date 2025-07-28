<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adaptive Video Player</title>
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
</head>
<body style="background-color: #1A1A1A;margin: auto;">
<?php 
require("admin/includes/config.php");
require("admin/includes/functions.php");

function extractVideoSource($html) {
    $pattern = '/jwplayer\("vplayer"\)\.setup\({.*?sources:\s*\[{file:"(.*?)",/s';
    if (preg_match($pattern, $html, $matches)) {
        return $matches[1];
    }
    return null;
}

function getUrlBase($url) {
    return strtok($url, '?');
}

$videoUrl = "";
$useVideoPlayer = false;

if( isset($_GET["link"]) && !empty($_GET["link"]) ){
    echo "<script>console.log('Starting with URL: " . addslashes($_GET["link"]) . "');</script>";
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "{$_GET["link"]}",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            "referer: {$website3}",
        ),
    ));
    $response = curl_exec($curl);
    $curl_info = curl_getinfo($curl);
    echo "<script>console.log('cURL response length: " . strlen($response) . " bytes');</script>";
    echo "<script>console.log('cURL status code: " . $curl_info['http_code'] . "');</script>";
    curl_close($curl);
    
    // Save response for debugging
    file_put_contents('response_debug.txt', $response);
    
    // First check if the link itself is a direct video file
    if(strpos($_GET["link"], ".mp4") !== false || strpos($_GET["link"], ".m3u8") !== false) {
        $videoUrl = $_GET["link"];
        $useVideoPlayer = true;
        echo "<script>console.log('Direct video URL detected: " . addslashes($videoUrl) . "');</script>";
    } 
    // Then try to extract video source from response
    else if($response) {
        echo "<script>console.log('Searching for video in response...');</script>";
        
        // First try using the extractVideoSource function
        $extractedUrl = extractVideoSource($response);
        if($extractedUrl) {
            $videoUrl = $extractedUrl;
            echo "<script>console.log('JW Player video URL found: " . addslashes($videoUrl) . "');</script>";
            // Crop after .m3u8 if present
            if (strpos($videoUrl, ".m3u8") !== false) {
                $videoUrl = substr($videoUrl, 0, strpos($videoUrl, ".m3u8")) . ".m3u8";
                echo "<script>console.log('Cleaned M3U8 URL: " . addslashes($videoUrl) . "');</script>";
            }
            $useVideoPlayer = true;
        }
        // If that fails, search for .mp4 or .m3u8 in the response
        else {
            echo "<script>console.log('JW Player extraction failed, trying regex patterns');</script>";
            
            // Try different patterns to find video URLs
            
            // Pattern 1: Look for video.mp4 pattern (for updown.icu and similar sites)
            if(preg_match('/https?:\/\/[^"\'\s]+\/video\.mp4/i', $response, $matches)) {
                $videoUrl = $matches[0];
                $useVideoPlayer = true;
                echo "<script>console.log('Pattern 1 (video.mp4) match found: " . addslashes($videoUrl) . "');</script>";
            }
            // Pattern 2: General MP4 pattern
            else if(preg_match('/https?:\/\/[^"\'\s<>]+\.mp4[^"\'\s<>]*/i', $response, $matches)) {
                $videoUrl = $matches[0];
                $useVideoPlayer = true;
                echo "<script>console.log('Pattern 2 (general MP4) match found: " . addslashes($videoUrl) . "');</script>";
            }
            // Pattern 3: M3U8 pattern
            else if(preg_match('/https?:\/\/[^"\'\s<>]+\.m3u8[^"\'\s<>]*/i', $response, $matches)) {
                $videoUrl = $matches[0];
                $useVideoPlayer = true;
                echo "<script>console.log('Pattern 3 (M3U8) match found: " . addslashes($videoUrl) . "');</script>";
            }
            // Pattern 4: Look for source with type="video/mp4"
            else if(preg_match('/source\s+src=["\'](https?:\/\/[^"\']+)["\'](?:[^>]*type=["\'](video\/mp4|application\/x-mpegURL)["\']|[^>]*)/i', $response, $matches)) {
                $videoUrl = $matches[1];
                $useVideoPlayer = true;
                echo "<script>console.log('Pattern 4 (source tag) match found: " . addslashes($videoUrl) . "');</script>";
            }
            // Pattern 5: Look for file: pattern (common in many players)
            else if(preg_match('/file["\']?\s*:\s*["\']([^"\']+\.(?:mp4|m3u8))["\']/', $response, $matches)) {
                $videoUrl = $matches[1];
                echo "<script>console.log('Pattern 5 (file:) raw match found: " . addslashes($videoUrl) . "');</script>";
                // If it's a relative URL, make it absolute
                if(strpos($videoUrl, 'http') !== 0) {
                    $parsedUrl = parse_url($_GET["link"]);
                    $baseUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
                    $videoUrl = $baseUrl . ($videoUrl[0] == '/' ? '' : '/') . $videoUrl;
                    echo "<script>console.log('Converted to absolute URL: " . addslashes($videoUrl) . "');</script>";
                }
                $useVideoPlayer = true;
            } else {
                echo "<script>console.log('No video URL patterns matched in the response');</script>";
                // Try broader pattern for any MP4 URL - last resort
                if(preg_match_all('/https?:\/\/[^"\'\s]+\.mp4/i', $response, $allMatches)) {
                    echo "<script>console.log('Found " . count($allMatches[0]) . " potential MP4 URLs:');</script>";
                    foreach($allMatches[0] as $index => $match) {
                        echo "<script>console.log('MP4 URL #" . ($index+1) . ": " . addslashes($match) . "');</script>";
                    }
                    $videoUrl = $allMatches[0][0]; // Use the first match
                    $useVideoPlayer = true;
                    echo "<script>console.log('Using first MP4 URL: " . addslashes($videoUrl) . "');</script>";
                } else {
                    echo "<script>console.log('No MP4 URLs found in broader search');</script>";
                }
            }
        }
    }

    // Decide which element to display
    if($useVideoPlayer) {
        echo "<script>console.log('Using video player with URL: " . addslashes($videoUrl) . "');</script>";
        echo "<video id='videoPlayer' controls style='width:100%;height:100vh'></video>";
        echo "<div style='position:fixed; bottom:10px; left:10px; background:rgba(0,0,0,0.7); color:white; padding:5px; font-size:12px; z-index:9999;'>Using Video Player: " . htmlspecialchars($videoUrl) . "</div>";
    } else {
        echo "<script>console.log('Using iframe with URL: " . addslashes($_GET["link"]) . "');</script>";
        echo "<iframe id='frame' src='{$_GET["link"]}' style='width:100%;height:100vh;border: none;overflow: hidden;'allowFullScreen></iframe>"; 
        echo "<div style='position:fixed; bottom:10px; left:10px; background:rgba(0,0,0,0.7); color:white; padding:5px; font-size:12px; z-index:9999;'>Using iframe: No direct video URL found</div>";
    }
}else{
    echo "لا يوجد روابط متاحه للمشاهده حاليا، الرجاء المحاولة لاحقاً";
}
?>

<script>
        function setupVideoPlayer(videoElement, sourceUrl) {
            console.log('Setting up video player for: ' + sourceUrl);
            if (sourceUrl.includes('.m3u8')) {
                console.log('Detected HLS stream (.m3u8)');
                setupHlsPlayer(videoElement, sourceUrl);
            } else if (sourceUrl.includes('.mp4')) {
                console.log('Detected MP4 video');
                setupMp4Player(videoElement, sourceUrl);
            } else {
                console.error('Unsupported video format for URL: ' + sourceUrl);
            }
        }

        function setupHlsPlayer(videoElement, sourceUrl) {
            console.log('Setting up HLS player');
            if (Hls.isSupported()) {
                console.log('HLS.js is supported in this browser');
                var hls = new Hls();
                hls.loadSource(sourceUrl);
                hls.attachMedia(videoElement);
                hls.on(Hls.Events.MANIFEST_PARSED, function() {
                    console.log('HLS manifest parsed, playing video');
                    videoElement.play();
                });
                hls.on(Hls.Events.ERROR, function(event, data) {
                    console.error('HLS error:', data);
                });
            } else if (videoElement.canPlayType('application/vnd.apple.mpegurl')) {
                console.log('Using native HLS support');
                videoElement.src = sourceUrl;
                videoElement.addEventListener('loadedmetadata', function() {
                    console.log('Video metadata loaded, playing');
                    videoElement.play();
                });
            } else {
                console.error('HLS is not supported in this browser');
            }
        }

        function setupMp4Player(videoElement, sourceUrl) {
            console.log('Setting up MP4 player');
            videoElement.src = sourceUrl;
            videoElement.addEventListener('loadedmetadata', function() {
                console.log('Video metadata loaded, playing');
                videoElement.play();
            });
            videoElement.addEventListener('error', function(e) {
                console.error('Video error: ', e);
                console.error('Error code: ' + videoElement.error.code);
            });
        }

        function loadVideo(url) {
            console.log('Loading video with URL: ' + url);
            var videoElement = document.getElementById('videoPlayer');
            if (videoElement) {
                setupVideoPlayer(videoElement, url);
            } else {
                console.error('Video element not found in the DOM');
            }
        }
        <?php
        if($useVideoPlayer && !empty($videoUrl)) {
            echo "loadVideo('" . $videoUrl . "');";
        }
        ?>
    </script>
    </body>
</html>