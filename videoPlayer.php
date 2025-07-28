<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adaptive Video Player</title>
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
</head>
<body style="background-color: #1A1A1A;margin: auto;">
    <?php 
    if (isset($_GET["server"]) && $_GET["server"] != 1 ){
        echo "<video id='embedVideoPlayer' controls style='width:100%;height:100vh'></video>"; 
        echo "<div id='loadingMessage' style='color: white; text-align: center; margin-top: 20px;'>Loading video...</div>";
    }else{
        echo "<video id='videoPlayer' controls style='width:100%;height:100vh'></video>";
    }
    ?>
<?php 
require("admin/includes/config.php");
require("admin/includes/functions.php");

// Handle proxy requests for fetching embed pages
if (isset($_GET['proxy_url']) && !empty($_GET['proxy_url'])) {
    $proxyUrl = $_GET['proxy_url'];
    
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $proxyUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36",
            "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8",
            "Accept-Language: en-US,en;q=0.5",
            "Accept-Encoding: gzip, deflate",
            "Connection: keep-alive",
            "Upgrade-Insecure-Requests: 1",
        ),
    ));
    
    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    
    if ($response !== false && $httpCode == 200) {
        header('Content-Type: text/html; charset=utf-8');
        echo $response;
    } else {
        http_response_code(500);
        echo "Error fetching the embed page";
    }
    exit;
}

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

if( isset($_GET["link"]) && !empty($_GET["link"]) ){
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
        curl_close($curl);
        $_GET["link"] = extractVideoSource($response);
        // crop after .m3u8
        if (strpos($_GET["link"], ".m3u8") !== false) {
            $_GET["link"] = substr($_GET["link"], 0, strpos($_GET["link"], ".m3u8")) . ".m3u8";
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

        // Function to extract video source from embed page HTML
        function extractVideoSourceFromHTML(html) {
            // Multiple patterns to try for different embed types
            const patterns = [
                // JWPlayer pattern
                /jwplayer\("vplayer"\)\.setup\({.*?sources:\s*\[{file:"(.*?)",/s,
                /jwplayer\([^)]+\)\.setup\({.*?file:\s*["'](.*?)["']/s,
                // Generic video source patterns
                /<video[^>]+src=["'](.*?)["']/i,
                /src:\s*["'](.*?\.m3u8.*?)["']/i,
                /source:\s*["'](.*?\.m3u8.*?)["']/i,
                /"file":\s*["'](.*?)["']/i,
                /"url":\s*["'](.*?)["']/i,
                // HLS patterns
                /["'](https?:\/\/[^"']*\.m3u8[^"']*)["']/i,
                // MP4 patterns
                /["'](https?:\/\/[^"']*\.mp4[^"']*)["']/i
            ];

            for (let pattern of patterns) {
                const match = html.match(pattern);
                if (match && match[1]) {
                    let url = match[1];
                    // Clean up the URL
                    url = url.replace(/\\"/g, '"').replace(/\\\//g, '/');
                    if (url.startsWith('http')) {
                        return url;
                    }
                }
            }
            return null;
        }

        // Function to fetch embed page and extract video source
        async function fetchAndExtractVideo(embedUrl) {
            const loadingMessage = document.getElementById('loadingMessage');
            
            try {
                // Use a proxy approach - fetch through your own server
                const proxyUrl = window.location.origin + window.location.pathname + '?proxy_url=' + encodeURIComponent(embedUrl);
                
                const response = await fetch(proxyUrl);
                if (!response.ok) {
                    throw new Error('Failed to fetch embed page');
                }
                
                const html = await response.text();
                const videoSrc = extractVideoSourceFromHTML(html);
                
                if (videoSrc) {
                    if (loadingMessage) loadingMessage.style.display = 'none';
                    
                    // Clean up the video URL if it contains .m3u8
                    let cleanUrl = videoSrc;
                    if (cleanUrl.includes('.m3u8')) {
                        cleanUrl = cleanUrl.substring(0, cleanUrl.indexOf('.m3u8')) + '.m3u8';
                    }
                    
                    const videoElement = document.getElementById('embedVideoPlayer');
                    setupVideoPlayer(videoElement, cleanUrl);
                    
                    console.log('Extracted video source:', cleanUrl);
                } else {
                    throw new Error('Could not extract video source from embed page');
                }
            } catch (error) {
                console.error('Error fetching video:', error);
                if (loadingMessage) {
                    loadingMessage.innerHTML = 'Error loading video. Please try again later.';
                    loadingMessage.style.color = '#ff6b6b';
                }
            }
        }

        <?php
        if( isset($_GET["server"]) && $_GET["server"] == 1 ){
         echo "loadVideo('{$_GET['link']}');";
        } elseif (isset($_GET["server"]) && $_GET["server"] != 1 && isset($_GET["link"])) {
         echo "fetchAndExtractVideo('{$_GET['link']}');";
        }
        ?>
    </script>
    </body>
</html>