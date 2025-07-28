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
        echo "<iframe id='frame' src='{$_GET["link"]}' style='width:100%;height:100vh;border: none;overflow: hidden;'allowFullScreen sandbox='allow-same-origin allow-scripts allow-popups allow-presentation allow-top-navigation'></iframe>"; 
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
        
        // Function to hide all anchor tags inside iframe
        function hideIframeAnchors() {
            const iframe = document.getElementById('frame');
            if (!iframe) return;
            
            console.log('🔒 Starting to hide iframe anchor tags...');
            
            function hideAnchors() {
                try {
                    const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                    const anchors = iframeDoc.querySelectorAll('a');
                    
                    console.log('🔒 Found', anchors.length, 'anchor tags in iframe');
                    
                    anchors.forEach((anchor, index) => {
                        // Method 1: Set display to none
                        anchor.style.display = 'none';
                        
                        // Method 2: Remove href to prevent navigation
                        if (anchor.href) {
                            anchor.removeAttribute('href');
                        }
                        
                        // Method 3: Prevent click events
                        anchor.addEventListener('click', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            console.log('🚫 Blocked click on anchor:', anchor);
                            return false;
                        });
                        
                        console.log('🔒 Hidden anchor', index + 1, ':', anchor.textContent?.substring(0, 50));
                    });
                    
                    console.log('✅ Successfully hidden all anchor tags in iframe');
                } catch (error) {
                    console.log('❌ Cannot access iframe content (cross-origin):', error.message);
                    
                    // Fallback: Inject CSS to hide anchors
                    try {
                        const style = iframe.contentDocument.createElement('style');
                        style.textContent = 'a { display: none !important; pointer-events: none !important; }';
                        iframe.contentDocument.head.appendChild(style);
                        console.log('✅ Injected CSS to hide anchors as fallback');
                    } catch (cssError) {
                        console.log('❌ CSS injection also failed:', cssError.message);
                    }
                }
            }
            
            // Try to hide anchors immediately
            hideAnchors();
            
            // Also try after iframe loads
            iframe.addEventListener('load', function() {
                console.log('🔄 Iframe loaded, hiding anchors again...');
                setTimeout(hideAnchors, 100); // Small delay to ensure content is ready
                setTimeout(hideAnchors, 500); // Another attempt after 500ms
                setTimeout(hideAnchors, 1000); // Final attempt after 1s
            });
            
            // Monitor for new content and hide anchors periodically
            const anchorHidingInterval = setInterval(hideAnchors, 2000); // Check every 2 seconds
            
            // Clean up interval when page unloads
            window.addEventListener('beforeunload', function() {
                clearInterval(anchorHidingInterval);
            });
        }
        
        <?php
        if( isset($_GET["server"]) && $_GET["server"] == 1 ){
         echo "loadVideo('{$_GET['link']}');";
        } else {
         echo "// Start hiding iframe anchors after page loads";
         echo "setTimeout(hideIframeAnchors, 500);";
        }
        ?>
    </script>
    </body>
</html>