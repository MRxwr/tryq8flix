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

    if (isset($_GET["link"]) && !empty($_GET["link"])) {
        // Try to detect if the link is a direct video file
        $isDirectVideo = preg_match('/\\.(mp4|m3u8|webm|ogg)(\\?.*)?$/i', $_GET["link"]);
        $realVideoUrl = $_GET["link"];
        if (!$isDirectVideo) {
            // Try to fetch the page and extract a direct video file from <video> or common JS players
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $_GET["link"],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    isset($website3) ? "referer: {$website3}" : '',
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            // Try to extract <video src="...">
            if (preg_match('/<video[^>]*src=[\"\']([^\"\']+)[\"\']/i', $response, $matches)) {
                $realVideoUrl = $matches[1];
            } else {
                // Try to extract jwplayer sources
                $jwPattern = '/jwplayer\\([\"\']vplayer[\"\']\\)\\.setup\\(\\{.*?sources:\\s*\\[\\{file:[\"\'](.*?)[\"\']/s';
                if (preg_match($jwPattern, $response, $matches)) {
                    $realVideoUrl = $matches[1];
                } else {
                    // Try to extract any .mp4 or .m3u8 link in the HTML
                    if (preg_match('/https?:\\/\\/[^\"\'\s>]+\\.(mp4|m3u8)/i', $response, $matches)) {
                        $realVideoUrl = $matches[0];
                    } else {
                        $realVideoUrl = $_GET["link"];
                    }
                }
            }
        }
        // Now, if we have a direct video file, use the video player, else fallback to iframe
        if (preg_match('/\\.(mp4|m3u8|webm|ogg)(\\?.*)?$/i', $realVideoUrl)) {
            echo "<video id='videoPlayer' controls style='width:100%;height:100vh'></video>";
            echo "<script>loadVideo('" . addslashes($realVideoUrl) . "');</script>";
        } else {
            echo "<iframe id='frame' src='" . htmlspecialchars($_GET["link"]) . "' style='width:100%;height:100vh;border: none;overflow: hidden;' allowFullScreen></iframe>";
        }
    } else {
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
    </script>
    </body>
</html>