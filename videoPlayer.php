<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adaptive Video Player</title>
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
</head>
<body style="background-color: #1A1A1A;margin: auto;">
    <?php 
    if( isset($_GET["link"]) && !empty($_GET["link"]) ){
        //search link fpr topcenima in the url
        if( strpos($_GET["link"], "topcinema") !== false ){
            $html = curlCall("{$_GET["link"]}");
            $dom = str_get_html($html);
            $data = [
                'shows' => []
            ];
            if ($dom) {
                foreach ($dom->find('.server--item') as $server) {
                    $id = $server->getAttribute('data-id');
                    $i = $server->getAttribute('data-server');
                    $jsonData = [
                        'id' => $id,
                        'i' => $i,
                        'link' => "{$_GET["link"]}",
                    ];
                    $data['shows'][] = $jsonData;
                }
                $servers = json_encode($data['shows'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            } else {
                echo 'Error: Invalid DOM object.';
                $servers = json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            }
            $servers = json_decode($servers, true);
            $ajaxUrl = "{$website2}/wp-content/themes/movies2023/Ajaxat/Single/Server.php";
            $url = makeRequest($ajaxUrl, $servers[2], "{$_GET["link"]}");
            $_GET["link"] = extractVideoSource($url);
            if (strpos($_GET["link"], ".m3u8") !== false) {
                $_GET["link"] = substr($_GET["link"], 0, strpos($_GET["link"], ".m3u8")) . ".m3u8";
            }
        }else{
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
        }
    }else{
        echo "لا يوجد روابط متاحه للمشاهده حاليا، الرجاء المحاولة لاحقاً";
    }
    
    if (isset($_GET["server"]) && $_GET["server"] != 1 ){
        echo "<iframe id='frame' src='{$_GET["link"]}' style='width:100%;height:100vh;border: none;overflow: hidden;'allowFullScreen></iframe>"; 
    }else{
        echo "<video id='videoPlayer' controls style='width:100%;height:100vh'></video>";
    }
    ?>
<?php 
require("admin/includes/config.php");
require("admin/includes/functions.php");
require("templates/simple_html_dom.php");

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