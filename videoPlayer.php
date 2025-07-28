<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="frame-src 'self' <?php echo isset($_GET['link']) ? parse_url($_GET['link'], PHP_URL_HOST) : ''; ?>">
    <title>Adaptive Video Player</title>
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <script>
        // Store the original URL of the iframe
        let originalIframeUrl = '';
        
        // Function to create a protective overlay to capture clicks
        function createProtectiveOverlay() {
            const iframe = document.getElementById('frame');
            if (!iframe) return;
            
            // Store original URL
            originalIframeUrl = iframe.src;
            
            // Create an invisible overlay that will capture all clicks
            const overlay = document.createElement('div');
            overlay.style.position = 'absolute';
            overlay.style.top = '0';
            overlay.style.left = '0';
            overlay.style.width = '100%';
            overlay.style.height = '100%';
            overlay.style.zIndex = '1000';
            overlay.style.backgroundColor = 'transparent';
            overlay.style.pointerEvents = 'none'; // Allow clicks to pass through initially
            
            // Add the overlay to the iframe's parent
            iframe.parentNode.style.position = 'relative';
            iframe.parentNode.appendChild(overlay);
            
            // Only block clicks after the video player has loaded
            setTimeout(() => {
                overlay.style.pointerEvents = 'auto'; // Now block clicks
                
                // Allow click events only for play/pause and fullscreen controls
                overlay.addEventListener('click', function(e) {
                    // Get click coordinates
                    const rect = overlay.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;
                    
                    // Define controls area (usually bottom middle of player)
                    // You may need to adjust these values based on the player
                    const controlsY = rect.height * 0.9; // Bottom 10% of player
                    const controlsXLeft = rect.width * 0.3;
                    const controlsXRight = rect.width * 0.7;
                    
                    // If click is in the controls area, pass it through
                    if (y > controlsY && x > controlsXLeft && x < controlsXRight) {
                        overlay.style.pointerEvents = 'none';
                        setTimeout(() => {
                            overlay.style.pointerEvents = 'auto';
                        }, 500); // Re-enable click blocking after a short delay
                    }
                    
                    e.stopPropagation();
                });
            }, 3000); // Wait for 3 seconds to allow the video player to initialize
        }
        
        // Monitor and prevent iframe navigation
        function monitorIframe() {
            const iframe = document.getElementById('frame');
            if (!iframe) return;
            
            // Check iframe source periodically
            setInterval(function() {
                try {
                    // Reset if navigation occurs
                    if (iframe.contentWindow.location.href !== originalIframeUrl) {
                        console.log('Iframe navigation detected, resetting to original URL');
                        iframe.src = originalIframeUrl;
                    }
                } catch (e) {
                    // Cross-origin errors will happen if the iframe loads content from another domain
                    // This is actually expected for security reasons
                }
            }, 1000);
            
            // Try to access the iframe content and prevent redirects
            try {
                iframe.onload = function() {
                    try {
                        iframe.contentWindow.onbeforeunload = function() {
                            return false; // Try to prevent unload
                        };
                    } catch (e) {
                        // Cross-origin access will fail
                    }
                };
            } catch (e) {
                // Cross-origin access will fail
            }
        }
        
        // Initialize everything when the page loads
        window.onload = function() {
            if (document.getElementById('frame')) {
                createProtectiveOverlay();
                monitorIframe();
            }
        };
    </script>
</head>
<body style="background-color: #1A1A1A;margin: auto;">
    <?php 
    if (isset($_GET["server"]) && $_GET["server"] != 1 ){
        echo "<iframe id='frame' src='{$_GET["link"]}' style='width:100%;height:100vh;border: none;overflow: hidden;' sandbox='allow-scripts allow-same-origin' allowFullScreen></iframe>"; 
    }else{
        echo "<video id='videoPlayer' controls style='width:100%;height:100vh'></video>";
    }
    ?>
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
        <?php
        if( isset($_GET["server"]) && $_GET["server"] == 1 ){
         echo "loadVideo('{$_GET['link']}');";
        }
        ?>
    </script>
    </body>
</html>