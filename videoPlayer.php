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
    // Try to find common video patterns in scripts or tags
    $patterns = [
        '/file["\']?\s*[:=]\s*["\'](https?:[^"\'\s]+\.(?:m3u8|mp4)[^"\'\s]*)["\']/', // JSON/JS file:
        '/["\']?url["\']?\s*:\s*["\'](https?:[^"\'\s]+\.(?:m3u8|mp4)[^"\'\s]*)["\']/', // JSON/JS url:
        '/<source[^>]+src=["\'](https?:[^"\']+\.(?:m3u8|mp4)[^"\']*)["\']/i',        // <source> tag
        '/video_url["\']?\s*:\s*["\'](https?:[^"\'\s]+)["\']/'                       // common video_url variable
    ];

    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $html, $matches)) {
            return $matches[1];
        }
    }

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
    $rawLink = $_GET["link"];
    $encodedLink = fix_arabic_url($rawLink);
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "{$encodedLink}",
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
    curl_close($curl);
    
    // First check if the link itself is a direct video file
    if(strpos($encodedLink, ".mp4") !== false || strpos($encodedLink, ".m3u8") !== false) {
        $videoUrl = $encodedLink;
        $useVideoPlayer = true;
    } 
    // Then try to extract video source from response
    else if($response) {
        // First try using the extractVideoSource function
        $extractedUrl = extractVideoSource($response);
        if($extractedUrl) {
            $videoUrl = fix_arabic_url($extractedUrl);
            // Crop after .m3u8 if present
            if (strpos($videoUrl, ".m3u8") !== false) {
                $videoUrl = substr($videoUrl, 0, strpos($videoUrl, ".m3u8")) . ".m3u8";
            }
            $useVideoPlayer = true;
        }
        // If that fails, search for .mp4 or .m3u8 in the response
        else {
            // Pattern 1: Look for video.mp4 pattern (for updown.icu and similar sites)
            if(preg_match('/https?:\/\/[^"\'\s]+\/video\.mp4/i', $response, $matches)) {
                $videoUrl = $matches[0];
                $useVideoPlayer = true;
            }
            // Pattern 2: General MP4 pattern
            else if(preg_match('/https?:\/\/[^"\'\s<>]+\.mp4[^"\'\s<>]*/i', $response, $matches)) {
                $videoUrl = $matches[0];
                $useVideoPlayer = true;
            }
            // Pattern 3: M3U8 pattern
            else if(preg_match('/https?:\/\/[^"\'\s<>]+\.m3u8[^"\'\s<>]*/i', $response, $matches)) {
                $videoUrl = $matches[0];
                $useVideoPlayer = true;
            }
            // Pattern 4: Look for source with type="video/mp4"
            else if(preg_match('/source\s+src=["\'](https?:\/\/[^"\']+)["\'](?:[^>]*type=["\'](video\/mp4|application\/x-mpegURL)["\']|[^>]*)/i', $response, $matches)) {
                $videoUrl = $matches[1];
                $useVideoPlayer = true;
            }
            // Pattern 5: Look for file: pattern (common in many players)
            else if(preg_match('/file["\']?\s*:\s*["\']([^"\']+\.(?:mp4|m3u8))["\']/', $response, $matches)) {
                $videoUrl = $matches[1];
                // If it's a relative URL, make it absolute
                if(strpos($videoUrl, 'http') !== 0) {
                    $parsedUrl = parse_url($_GET["link"]);
                    $baseUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
                    $videoUrl = $baseUrl . ($videoUrl[0] == '/' ? '' : '/') . $videoUrl;
                }
                $useVideoPlayer = true;
            } else {
                // Try broader pattern for any MP4 URL - last resort
                if(preg_match_all('/https?:\/\/[^"\'\s]+\.mp4/i', $response, $allMatches)) {
                    $videoUrl = $allMatches[0][0]; // Use the first match
                    $useVideoPlayer = true;
                }
            }
        }
    }

    if($useVideoPlayer && !empty($videoUrl)) {
        // Use native video tag with attributes that force Android/Xiaomi browsers to use their native player UI
        echo "<video id='videoPlayer' controls autoplay playsinline webkit-playsinline 
              x5-playsinline x5-video-player-type='h5' x5-video-player-fullscreen='true'
              style='width:100%;height:100vh;background:#000;display:block;'></video>";
    } else {
        echo "<iframe id='frame' src='{$_GET["link"]}' style='width:100%;height:100vh;border: none;overflow: hidden;' allowFullScreen referrerpolicy='no-referrer'></iframe>";
    }
}else{
    echo "لا يوجد روابط متاحه للمشاهده حاليا، الرجاء المحاولة لاحقاً";
}
?>

    <script>
        function setupVideoPlayer(videoElement, sourceUrl) {
            if (sourceUrl.includes('.m3u8')) {
                setupHlsPlayer(videoElement, sourceUrl);
            } else if (sourceUrl.includes('.mp4')) {
                setupMp4Player(videoElement, sourceUrl);
            } else {
                console.error('Unsupported video format');
            }
        }

        function setupHlsPlayer(videoElement, sourceUrl) {
            if (Hls.isSupported()) {
                var hls = new Hls();
                hls.loadSource(sourceUrl);
                hls.attachMedia(videoElement);
                hls.on(Hls.Events.MANIFEST_PARSED, function() {
                    videoElement.play();
                });
            } else if (videoElement.canPlayType('application/vnd.apple.mpegurl')) {
                videoElement.src = sourceUrl;
                videoElement.addEventListener('loadedmetadata', function() {
                    videoElement.play();
                });
            } else {
                console.error('HLS is not supported in this browser');
            }
        }

        function setupMp4Player(videoElement, sourceUrl) {
            videoElement.src = sourceUrl;
            videoElement.addEventListener('loadedmetadata', function() {
                videoElement.play();
            });
        }

        function loadVideo(url) {
            var videoElement = document.getElementById('videoPlayer');
            setupVideoPlayer(videoElement, url);
        }
        <?php
        if($useVideoPlayer && !empty($videoUrl)) {
            echo "loadVideo('" . $videoUrl . "');";
        }
        ?>
    </script>
    </body>
</html>