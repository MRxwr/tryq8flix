<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adaptive Video Player</title>
    <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css">
    <script src="https://cdn.plyr.io/3.7.8/plyr.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <style>
        :root {
            color-scheme: dark;
            --plyr-color-main: #e50914;
            --plyr-video-background: #090909;
            --plyr-font-family: Arial, sans-serif;
            --plyr-control-radius: 8px;
            --plyr-menu-background: #202020;
            --plyr-menu-color: #f5f5f5;
        }

        * { box-sizing: border-box; }
        html, body { width: 100%; height: 100%; margin: 0; background: #090909; }
        body { font-family: Arial, sans-serif; color: #f5f5f5; }
        .player-shell { position: relative; width: 100%; height: 100%; }
        .player-shell > video, .player-shell > .plyr { width: 100%; height: 100%; min-width: 0; }
        .plyr__video-wrapper { height: 100%; }
        .plyr video { object-fit: contain; }
        .plyr--video .plyr__controls {
            padding: 48px 24px 20px;
            background: linear-gradient(transparent, rgba(0, 0, 0, .9));
        }
        .plyr__control--overlaid {
            padding: 24px;
            box-shadow: 0 8px 40px rgba(229, 9, 20, .3);
        }
        .plyr__control--overlaid svg { width: 24px; height: 24px; }
        .player-message {
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 4;
            width: max-content;
            max-width: calc(100% - 32px);
            margin: 0;
            padding: 12px 18px;
            border: 1px solid #454545;
            border-radius: 10px;
            background: rgba(20, 20, 20, .95);
            text-align: center;
            font-size: 14px;
        }
        .player-message[hidden] { display: none; }
        #frame { display: block; width: 100%; height: 100%; border: 0; }
        @media (max-width: 600px) {
            .plyr--video .plyr__controls { padding: 36px 10px 12px; gap: 2px; }
            .plyr__controls .plyr__volume { min-width: 0; width: auto; }
            .plyr__volume input { display: none; }
            .plyr__controls [data-plyr="rewind"],
            .plyr__controls [data-plyr="fast-forward"],
            .plyr__controls [data-plyr="pip"],
            .plyr__controls [data-plyr="airplay"],
            .plyr__controls .plyr__time--duration { display: none; }
        }
        @media (max-width: 360px) {
            .plyr__controls .plyr__time--current,
            .plyr__controls .plyr__volume { display: none; }
        }
    </style>
</head>

<body>
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

$incomingLink = isset($_GET["link"]) && is_string($_GET["link"]) ? trim($_GET["link"]) : "";
$videoUrl = "";
$useVideoPlayer = false;

if($incomingLink !== "" && !str_contains($incomingLink, "vidvault.to")){
    $rawLink = $incomingLink;
    $encodedLink = fix_arabic_url($rawLink);
    $sourcePath = parse_url($encodedLink, PHP_URL_PATH);
    $isDirectVideo = is_string($sourcePath) && preg_match('/\.(mp4|m3u8)$/i', $sourcePath);
    $response = false;
    // Direct media must not be downloaded by PHP before playback starts.
    if (!$isDirectVideo) {
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "{$encodedLink}",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            "referer: {$website3}",
        ),
    ));
    $response = curl_exec($curl);
    if ($response === false) {
        error_log('Video source resolution failed: ' . curl_error($curl));
    }
    curl_close($curl);
    }
    
    // First check if the link itself is a direct video file
    if($isDirectVideo) {
        $videoUrl = $encodedLink;
        $useVideoPlayer = true;
    } 
    // Then try to extract video source from response
    else if($response) {
        // First try using the extractVideoSource function
        $extractedUrl = extractVideoSource($response);
        if($extractedUrl) {
            $videoUrl = fix_arabic_url($extractedUrl);
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

} elseif ($incomingLink !== "") {
    $videoUrl = $incomingLink;
    $useVideoPlayer = true;
}

if ($useVideoPlayer) {
    echo '<main class="player-shell" aria-label="Video player">
        <video id="videoPlayer" controls playsinline preload="metadata"></video>
        <p id="playerMessage" class="player-message" role="status" hidden></p>
    </main>';
} elseif ($incomingLink !== "") {
    echo '<iframe id="frame" title="Video provider" src="' . htmlspecialchars($incomingLink, ENT_QUOTES, 'UTF-8') . '" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
} else {
    echo '<p class="player-message" role="alert">No video link was provided.</p>';
}
?>

    <script>
    function showPlayerMessage(message) {
        var element = document.getElementById('playerMessage');
        if (element) {
            element.textContent = message;
            element.hidden = false;
        }
    }

    function setupVideoPlayer(videoElement, sourceUrl) {
        if (typeof Plyr === 'function') {
            new Plyr(videoElement, {
                controls: [
                    'play-large', 'rewind', 'play', 'fast-forward', 'progress',
                    'current-time', 'duration', 'mute', 'volume', 'settings',
                    'pip', 'airplay', 'fullscreen'
                ],
                settings: ['speed'],
                speed: { selected: 1, options: [0.5, 0.75, 1, 1.25, 1.5, 1.75, 2] },
                seekTime: 10,
                keyboard: { focused: true, global: false },
                tooltips: { controls: true, seek: true }
            });
        } else {
            showPlayerMessage('Styled controls could not load. Standard controls are available.');
        }

        videoElement.addEventListener('error', function() {
            showPlayerMessage('Unable to play this video. Try another server or check your connection.');
        });

        var sourcePath = new URL(sourceUrl, location.href).pathname.toLowerCase();
        if (sourcePath.endsWith('.m3u8')) {
            setupHlsPlayer(videoElement, sourceUrl);
        } else {
            videoElement.src = sourceUrl;
        }
    }

    function setupHlsPlayer(videoElement, sourceUrl) {
        if (typeof Hls !== 'undefined' && Hls.isSupported()) {
            var hls = new Hls();
            hls.loadSource(sourceUrl);
            hls.attachMedia(videoElement);
            hls.on(Hls.Events.ERROR, function(event, data) {
                if (data.fatal) {
                    showPlayerMessage('The stream could not load. Try another server or check your connection.');
                    hls.destroy();
                }
            });
            window.addEventListener('pagehide', function() { hls.destroy(); }, { once: true });
        } else if (videoElement.canPlayType('application/vnd.apple.mpegurl')) {
            videoElement.src = sourceUrl;
        } else {
            showPlayerMessage('HLS playback is unavailable. Try another browser or server.');
        }
    }

    function loadVideo(url) {
        var videoElement = document.getElementById('videoPlayer');
        setupVideoPlayer(videoElement, url);
    }
    <?php
        if($useVideoPlayer && !empty($videoUrl)) {
            echo "loadVideo(" . json_encode($videoUrl, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . ");";
        }
        ?>
    </script>
</body>

</html>