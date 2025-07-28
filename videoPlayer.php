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
    echo "<script>console.log('🔧 PHP extractVideoSource: Starting extraction, HTML length:', " . strlen($html) . ");</script>";
    echo "<script>console.log('🔧 PHP extractVideoSource: HTML preview:', '" . addslashes(substr($html, 0, 300)) . "');</script>";
    
    $pattern = '/jwplayer\("vplayer"\)\.setup\({.*?sources:\s*\[{file:"(.*?)",/s';
    echo "<script>console.log('🔧 PHP extractVideoSource: Using pattern:', '" . addslashes($pattern) . "');</script>";
    
    if (preg_match($pattern, $html, $matches)) {
        echo "<script>console.log('🔧 PHP extractVideoSource: Pattern matched! Found:', '" . addslashes($matches[1]) . "');</script>";
        return $matches[1];
    } else {
        echo "<script>console.log('🔧 PHP extractVideoSource: Pattern did not match');</script>";
        
        // Try to find any jwplayer mentions
        if (strpos($html, 'jwplayer') !== false) {
            echo "<script>console.log('🔧 PHP extractVideoSource: jwplayer found in HTML, but pattern failed');</script>";
        } else {
            echo "<script>console.log('🔧 PHP extractVideoSource: No jwplayer found in HTML at all');</script>";
        }
        
        // Try to find any .m3u8 URLs
        if (preg_match('/https?:\/\/[^"\s]*\.m3u8[^"\s]*/', $html, $m3u8Matches)) {
            echo "<script>console.log('🔧 PHP extractVideoSource: Found m3u8 URL anyway:', '" . addslashes($m3u8Matches[0]) . "');</script>";
            return $m3u8Matches[0];
        }
        
        return null;
    }
}

function getUrlBase($url) {
    return strtok($url, '?');
}

if( isset($_GET["link"]) && !empty($_GET["link"]) ){
        echo "<script>console.log('🔧 PHP: Processing link parameter:', '" . addslashes($_GET["link"]) . "');</script>";
        
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
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        
        echo "<script>console.log('🔧 PHP: cURL response code:', $httpCode);</script>";
        echo "<script>console.log('🔧 PHP: Response length:', " . strlen($response) . ");</script>";
        
        $extractedSource = extractVideoSource($response);
        echo "<script>console.log('🔧 PHP: Extracted source:', '" . addslashes($extractedSource) . "');</script>";
        
        $_GET["link"] = $extractedSource;
        
        // crop after .m3u8
        if (strpos($_GET["link"], ".m3u8") !== false) {
            $originalLink = $_GET["link"];
            $_GET["link"] = substr($_GET["link"], 0, strpos($_GET["link"], ".m3u8")) . ".m3u8";
            echo "<script>console.log('🔧 PHP: m3u8 URL cleaned:', {original: '" . addslashes($originalLink) . "', cleaned: '" . addslashes($_GET["link"]) . "'});</script>";
        }
        
        echo "<script>console.log('🔧 PHP: Final processed link:', '" . addslashes($_GET["link"]) . "');</script>";
}else{
    echo "لا يوجد روابط متاحه للمشاهده حاليا، الرجاء المحاولة لاحقاً";
    echo "<script>console.log('❌ PHP: No link parameter provided or empty');</script>";
}
?>

<script>
        function setupVideoPlayer(videoElement, sourceUrl) {
            console.log('🎬 setupVideoPlayer called with:', { element: videoElement, url: sourceUrl });
            
            if (sourceUrl.includes('.m3u8')) {
                console.log('📺 Detected HLS (.m3u8) format, calling setupHlsPlayer');
                setupHlsPlayer(videoElement, sourceUrl);
            } else if (sourceUrl.includes('.mp4')) {
                console.log('📺 Detected MP4 format, calling setupMp4Player');
                setupMp4Player(videoElement, sourceUrl);
            } else {
                console.error('❌ Unsupported video format:', sourceUrl);
            }
        }

        function setupHlsPlayer(videoElement, sourceUrl) {
            console.log('🎭 setupHlsPlayer called with URL:', sourceUrl);
            console.log('🔍 Checking HLS support...');
            
            if (Hls.isSupported()) {
                console.log('✅ HLS.js is supported, creating HLS instance');
                var hls = new Hls();
                
                console.log('📡 Loading HLS source:', sourceUrl);
                hls.loadSource(sourceUrl);
                
                console.log('🔗 Attaching HLS to video element');
                hls.attachMedia(videoElement);
                
                hls.on(Hls.Events.MANIFEST_PARSED, function() {
                    console.log('📋 HLS manifest parsed successfully, starting playback');
                    videoElement.play().then(() => {
                        console.log('▶️ Video playback started successfully');
                    }).catch(err => {
                        console.error('❌ Error starting playback:', err);
                    });
                });
                
                hls.on(Hls.Events.ERROR, function(event, data) {
                    console.error('❌ HLS error:', { event, data });
                });
                
            } else if (videoElement.canPlayType('application/vnd.apple.mpegurl')) {
                console.log('✅ Native HLS support detected (Safari), using native playback');
                videoElement.src = sourceUrl;
                videoElement.addEventListener('loadedmetadata', function() {
                    console.log('📋 Video metadata loaded, starting playback');
                    videoElement.play().then(() => {
                        console.log('▶️ Native HLS playback started successfully');
                    }).catch(err => {
                        console.error('❌ Error starting native HLS playback:', err);
                    });
                });
            } else {
                console.error('❌ HLS is not supported in this browser');
            }
        }

        function setupMp4Player(videoElement, sourceUrl) {
            console.log('🎬 setupMp4Player called with URL:', sourceUrl);
            console.log('🔗 Setting video src to:', sourceUrl);
            
            videoElement.src = sourceUrl;
            videoElement.addEventListener('loadedmetadata', function() {
                console.log('📋 MP4 metadata loaded, starting playback');
                videoElement.play().then(() => {
                    console.log('▶️ MP4 playback started successfully');
                }).catch(err => {
                    console.error('❌ Error starting MP4 playback:', err);
                });
            });
            
            videoElement.addEventListener('error', function(e) {
                console.error('❌ Video element error:', e);
            });
        }

        function loadVideo(url) {
            console.log('🎥 loadVideo called with URL:', url);
            var videoElement = document.getElementById('videoPlayer');
            console.log('🎥 Video element found:', videoElement);
            setupVideoPlayer(videoElement, url);
        }

        // Function to extract video source from embed page HTML
        function extractVideoSourceFromHTML(html) {
            console.log('🔍 Starting video source extraction from HTML');
            console.log('📄 HTML content length:', html.length);
            console.log('📄 HTML preview (first 500 chars):', html.substring(0, 500));
            
            // Multiple patterns to try for different embed types
            const patterns = [
                // JWPlayer pattern
                { name: 'JWPlayer vplayer setup', regex: /jwplayer\("vplayer"\)\.setup\({.*?sources:\s*\[{file:"(.*?)",/s },
                { name: 'JWPlayer generic setup', regex: /jwplayer\([^)]+\)\.setup\({.*?file:\s*["'](.*?)["']/s },
                // Generic video source patterns
                { name: 'Video tag src', regex: /<video[^>]+src=["'](.*?)["']/i },
                { name: 'Src with m3u8', regex: /src:\s*["'](.*?\.m3u8.*?)["']/i },
                { name: 'Source with m3u8', regex: /source:\s*["'](.*?\.m3u8.*?)["']/i },
                { name: 'File property', regex: /"file":\s*["'](.*?)["']/i },
                { name: 'URL property', regex: /"url":\s*["'](.*?)["']/i },
                // HLS patterns
                { name: 'HLS m3u8 pattern', regex: /["'](https?:\/\/[^"']*\.m3u8[^"']*)["']/i },
                // MP4 patterns
                { name: 'MP4 pattern', regex: /["'](https?:\/\/[^"']*\.mp4[^"']*)["']/i }
            ];

            for (let i = 0; i < patterns.length; i++) {
                const pattern = patterns[i];
                console.log(`🔎 Trying pattern ${i + 1}/${patterns.length}: ${pattern.name}`);
                
                const match = html.match(pattern.regex);
                if (match && match[1]) {
                    console.log(`✅ Pattern matched! Raw URL found:`, match[1]);
                    
                    let url = match[1];
                    // Clean up the URL
                    const originalUrl = url;
                    url = url.replace(/\\"/g, '"').replace(/\\\//g, '/');
                    
                    if (originalUrl !== url) {
                        console.log('🧹 URL cleaned:', { original: originalUrl, cleaned: url });
                    }
                    
                    if (url.startsWith('http')) {
                        console.log(`🎯 Valid HTTP URL found with pattern "${pattern.name}":`, url);
                        return url;
                    } else {
                        console.log(`❌ URL doesn't start with http:`, url);
                    }
                } else {
                    console.log(`❌ Pattern "${pattern.name}" - no match`);
                }
            }
            
            console.log('❌ No video source found in any pattern');
            return null;
        }

        // Function to fetch embed page and extract video source
        async function fetchAndExtractVideo(embedUrl) {
            console.log('🚀 Starting fetchAndExtractVideo function');
            console.log('🔗 Embed URL:', embedUrl);
            
            const loadingMessage = document.getElementById('loadingMessage');
            console.log('📱 Loading message element:', loadingMessage);
            
            try {
                // Use a proxy approach - fetch through your own server
                const proxyUrl = window.location.origin + window.location.pathname + '?proxy_url=' + encodeURIComponent(embedUrl);
                console.log('🌐 Proxy URL constructed:', proxyUrl);
                console.log('🌐 Current location origin:', window.location.origin);
                console.log('🌐 Current pathname:', window.location.pathname);
                
                console.log('📡 Starting fetch request to proxy...');
                const response = await fetch(proxyUrl);
                console.log('📡 Fetch response received:', {
                    status: response.status,
                    statusText: response.statusText,
                    ok: response.ok,
                    headers: Object.fromEntries(response.headers.entries())
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                console.log('📄 Converting response to text...');
                const html = await response.text();
                console.log('📄 HTML received, length:', html.length);
                
                console.log('🎯 Calling extractVideoSourceFromHTML...');
                const videoSrc = extractVideoSourceFromHTML(html);
                console.log('🎯 Video source extraction result:', videoSrc);
                
                if (videoSrc) {
                    console.log('✅ Video source found! Processing...');
                    
                    if (loadingMessage) {
                        console.log('📱 Hiding loading message');
                        loadingMessage.style.display = 'none';
                    }
                    
                    // Clean up the video URL if it contains .m3u8
                    let cleanUrl = videoSrc;
                    console.log('🧹 Original video URL:', cleanUrl);
                    
                    if (cleanUrl.includes('.m3u8')) {
                        const originalCleanUrl = cleanUrl;
                        cleanUrl = cleanUrl.substring(0, cleanUrl.indexOf('.m3u8')) + '.m3u8';
                        console.log('🧹 Cleaned m3u8 URL:', { original: originalCleanUrl, cleaned: cleanUrl });
                    }
                    
                    console.log('🎥 Getting video element...');
                    const videoElement = document.getElementById('embedVideoPlayer');
                    console.log('🎥 Video element found:', videoElement);
                    
                    console.log('▶️ Setting up video player with URL:', cleanUrl);
                    setupVideoPlayer(videoElement, cleanUrl);
                    
                    console.log('🎊 Video setup complete! Final URL:', cleanUrl);
                } else {
                    throw new Error('Could not extract video source from embed page');
                }
            } catch (error) {
                console.error('❌ Error in fetchAndExtractVideo:', error);
                console.error('❌ Error stack:', error.stack);
                
                if (loadingMessage) {
                    console.log('📱 Updating loading message with error');
                    loadingMessage.innerHTML = 'Error loading video. Please try again later.';
                    loadingMessage.style.color = '#ff6b6b';
                }
            }
        }

        <?php
        if( isset($_GET["server"]) && $_GET["server"] == 1 ){
         echo "console.log('🔄 Server=1 detected, calling loadVideo');";
         echo "loadVideo('{$_GET['link']}');";
        } elseif (isset($_GET["server"]) && $_GET["server"] != 1 && isset($_GET["link"])) {
         echo "console.log('🔄 Server!=1 detected, calling fetchAndExtractVideo');";
         echo "console.log('🔗 Link parameter:', '{$_GET['link']}');";
         echo "fetchAndExtractVideo('{$_GET['link']}');";
        } else {
         echo "console.log('❌ No valid server/link parameters found');";
         echo "console.log('🔍 Current GET parameters:', " . json_encode($_GET) . ");";
        }
        ?>
    </script>
    </body>
</html>