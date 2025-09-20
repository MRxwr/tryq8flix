<?php
// Prevent browser caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");
require("admin/includes/config.php");
require("admin/includes/functions.php");
$profileData = checkLogin();
$x  = randomLetter();
$xValue = md5(time());

// Get match URL from parameter
$matchUrl = isset($_GET['match']) ? $_GET['match'] : '';
$liveStreams = [];

// If match URL is provided, fetch live streams from API
if (!empty($matchUrl)) {
    // Include necessary files for the API
    require_once("templates/simple_html_dom.php");
    
    // Backup original GET parameters
    $originalGet = $_GET;
    
    // Set up API parameters
    $_GET['action'] = 'match';
    $_GET['match'] = $matchUrl;
    
    // Capture the output from the API
    ob_start();
    include 'api/views/apiLive.php';
    $apiResponse = ob_get_clean();
    
    // Restore original GET parameters
    $_GET = $originalGet;
    
    if ($apiResponse) {
        $apiData = json_decode($apiResponse, true);
        
        if (isset($apiData['data']) && is_array($apiData['data'])) {
            $liveStreams = $apiData['data'];
        } elseif (isset($apiData['ok']) && $apiData['ok'] && isset($apiData['data'])) {
            // Handle the exact format from your API response
            $liveStreams = $apiData['data'];
        } else {
            // Log the API response for debugging
            error_log("API Response: " . $apiResponse);
        }
    } else {
        // Log the failed API call
        error_log("Failed to get API response");
    }
} else {
    // Default static streams if no match URL provided
    $liveStreams = [
        [
            'live' => 'https://admdn1.cdn.mangomolo.com/adsports1/smil:adsports1.stream.smil/chunklist_b4000000_t64MTA4MHA=.m3u8',
            'serv' => 1,
            'name' => 'أبو ظبي الرياضية 1'
        ],
        [
            'live' => 'https://admdn5.cdn.mangomolo.com/adsports2/smil:adsports2.stream.smil/chunklist_b4000000_t64MTA4MHA=.m3u8',
            'serv' => 2,
            'name' => 'أبو ظبي الرياضية 2'
        ],
        [
            'live' => 'https://kwtspta.cdn.mangomolo.com/sp/smil:sp.stream.smil/chunklist_b2500000_t64MTA4MHA=.m3u8',
            'serv' => 3,
            'name' => 'الكويت الرياضية'
        ]
    ];
}

// Get selected server
$selectedServer = isset($_GET['server']) ? intval($_GET['server']) : 1;
$currentStream = null;

// Find the current stream based on selected server
foreach ($liveStreams as $stream) {
    if ($stream['serv'] == $selectedServer) {
        $currentStream = $stream;
        break;
    }
}

// If no stream found for selected server, use first available
if (!$currentStream && !empty($liveStreams)) {
    $currentStream = $liveStreams[0];
    $selectedServer = $currentStream['serv'];
}
?>
<!doctype html>
<html lang="ar" style="direction:rtl">

<head>
    <title>البث المباشر - TRYQ8FLiX 2.0</title>
    
    <meta charset="utf-8">
    <meta name="theme-color" content="black" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="origin" name="referrer">
    <meta name="description" content="مشاهدة القنوات الرياضية المباشرة">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.3/font/bootstrap-icons.css">
    <link rel="manifest" href="manifest.json">
    <link rel="shortcut icon" href="https://i.imgur.com/6CBCStr.png" type="image/x-icon">
    <link rel="apple-touch-icon" href="https://i.imgur.com/6CBCStr.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css?<?php echo randomLetter() . "=" . md5(time()) ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    
    <!-- Video.js CSS -->
    <link href="https://vjs.zencdn.net/8.6.1/video-js.css" rel="stylesheet">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.5/dist/umd/popper.min.js" integrity="sha384-Xe+8cL9oJa6tN/veChSP7q+mnSPaj5Bcu9mPX5F5xIGE0DVittaqT5lorf0EI7Vk" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.min.js" integrity="sha384-kjU+l4N0Yf4ZOJErLsIcvOU2qSb74wXpOhqTvwVx3OElZRweTnQ6d31fXEoRD1Jy" crossorigin="anonymous"></script>
    
    <!-- Video.js JavaScript -->
    <script src="https://vjs.zencdn.net/8.6.1/video.min.js"></script>
    <!-- HLS support for Video.js -->
    <script src="https://cdn.jsdelivr.net/npm/@videojs/http-streaming@3.0.2/dist/videojs-http-streaming.min.js"></script>

    <style>
        body {
            background-color: #101010;
            color: white;
        }
        
        .card {
            background-color: #211f20;
            color: white;
            border: 1px solid #333;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
        }
        
        .stream-card {
            cursor: pointer;
            min-height: 120px;
        }
        
        .stream-logo {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 50%;
        }
        
        .navbar {
            background-color: #211f20 !important;
            border-bottom: 1px solid #333;
        }
        
        .navbar-brand {
            color: white !important;
            font-weight: bold;
        }
        
        .btn-primary {
            background-color: #e50914;
            border-color: #e50914;
        }
        
        .btn-primary:hover {
            background-color: #f40612;
            border-color: #f40612;
        }
        
        .player-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: black;
            z-index: 9999;
            display: none;
        }
        
        .video-js {
            width: 100% !important;
            height: 100% !important;
        }
        
        .close-player {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 10000;
            background: rgba(0,0,0,0.7);
            border: none;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
        }
        
        .close-player:hover {
            background: rgba(0,0,0,0.9);
        }
        
        .category-filter {
            margin-bottom: 20px;
        }
        
        .search-box {
            margin-bottom: 20px;
        }
        
        .stream-info {
            padding: 15px;
        }
        
        .stream-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .stream-category {
            font-size: 12px;
            color: #999;
            text-transform: uppercase;
        }
        
        .live-indicator {
            background: #e50914;
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            position: absolute;
            top: 10px;
            left: 10px;
        }
        
        .grid-container {
            padding: 20px 0;
        }
        
        .server-selector {
            margin-bottom: 30px;
        }
        
        .server-buttons {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
        }
        
        .server-btn {
            min-width: 120px;
            transition: all 0.3s ease;
        }
        
        .server-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        
        .current-stream-info .card {
            background: linear-gradient(135deg, #211f20 0%, #333 100%);
            border: 1px solid #e50914;
        }
        
        .stream-iframe-container {
            position: relative;
            width: 100%;
            height: 0;
            padding-bottom: 56.25%; /* 16:9 aspect ratio */
            background: #000;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .stream-iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
        }
        
        .stream-status {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(0,0,0,0.8);
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            z-index: 10;
        }
        
        @media (max-width: 768px) {
            .server-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .server-btn {
                width: 200px;
            }
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-play-circle-fill me-2"></i>TRYQ8FLiX
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">الرئيسية</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="liveMatches.php">البث المباشر</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <?php if (!empty($matchUrl)): ?>
                <h1 class="text-center mb-3">
                    <i class="bi bi-broadcast me-2"></i>مشاهدة المباراة مباشر
                </h1>
                <p class="text-center text-muted">اختر السيرفر المناسب لك</p>
                <?php else: ?>
                <h1 class="text-center mb-3">
                    <i class="bi bi-broadcast me-2"></i>البث المباشر للقنوات الرياضية
                </h1>
                <p class="text-center text-muted">اختر القناة التي تريد مشاهدتها</p>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($liveStreams)): ?>
        <!-- Server Selector -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="server-selector text-center">
                    <h5 class="mb-3">السيرفرات المتاحة:</h5>
                    <div class="server-buttons">
                        <?php foreach ($liveStreams as $stream): ?>
                        <button class="btn <?php echo ($stream['serv'] == $selectedServer) ? 'btn-primary' : 'btn-outline-primary'; ?> me-2 mb-2 server-btn" 
                                onclick="switchServer(<?php echo $stream['serv']; ?>)">
                            <i class="bi bi-server me-1"></i>سيرفر <?php echo $stream['serv']; ?>
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Stream Display -->
        <?php if ($currentStream): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="current-stream-info text-center">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="bi bi-play-circle-fill text-primary me-2"></i>
                                السيرفر الحالي: <?php echo $currentStream['serv']; ?>
                            </h5>
                            <p class="text-muted small">الرابط: <?php echo substr($currentStream['live'], 0, 50) . '...'; ?></p>
                            <div class="mt-3">
                                <button class="btn btn-success btn-lg" onclick="playCurrentStream()">
                                    <i class="bi bi-play-fill me-2"></i>بدء المشاهدة
                                </button>
                                <button class="btn btn-info btn-lg ms-2" onclick="openInNewTab()">
                                    <i class="bi bi-box-arrow-up-right me-2"></i>فتح في نافذة جديدة
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <!-- Debug info when no current stream -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-warning">
                    <h6>معلومات التشخيص:</h6>
                    <p><strong>عدد السيرفرات:</strong> <?php echo count($liveStreams); ?></p>
                    <p><strong>السيرفر المطلوب:</strong> <?php echo $selectedServer; ?></p>
                    <?php if (!empty($liveStreams)): ?>
                    <p><strong>السيرفرات المتاحة:</strong> 
                    <?php foreach ($liveStreams as $stream): ?>
                        <?php echo $stream['serv']; ?>,
                    <?php endforeach; ?>
                    </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if (empty($matchUrl)): ?>
        <!-- Search and Filter (only for static streams) -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="search-box">
                    <input type="text" id="searchInput" class="form-control" placeholder="البحث عن قناة...">
                </div>
            </div>
            <div class="col-md-6">
                <div class="category-filter">
                    <select id="categoryFilter" class="form-select">
                        <option value="">جميع الفئات</option>
                        <option value="sports">رياضية</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Static Streams Grid (only when no match URL) -->
        <div class="grid-container">
            <div class="row" id="streamsGrid">
                <div class="col-12 text-center">
                    <p class="text-muted">لا توجد قنوات متاحة حالياً</p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php else: ?>
        <!-- No streams available -->
        <div class="row">
            <div class="col-12 text-center">
                <div class="alert alert-warning">
                    <h4><i class="bi bi-exclamation-triangle me-2"></i>لا توجد بث متاح</h4>
                    <p>عذراً، لا توجد روابط بث متاحة حالياً.</p>
                    <?php if (!empty($matchUrl)): ?>
                    <div class="mt-3">
                        <h6>معلومات التشخيص:</h6>
                        <p><strong>رابط المباراة:</strong> <?php echo htmlspecialchars($matchUrl); ?></p>
                        <p><strong>استجابة API:</strong> <?php echo isset($apiResponse) ? (strlen($apiResponse) > 500 ? substr($apiResponse, 0, 500) . '...' : htmlspecialchars($apiResponse)) : 'لا توجد استجابة'; ?></p>
                        <p><strong>عدد السيرفرات المستخرجة:</strong> <?php echo count($liveStreams); ?></p>
                        <?php if (!empty($liveStreams)): ?>
                        <p><strong>السيرفرات:</strong> 
                        <?php foreach ($liveStreams as $stream): ?>
                            <?php echo 'Server ' . $stream['serv'] . ' (' . substr($stream['live'], 0, 50) . '...), '; ?>
                        <?php endforeach; ?>
                        </p>
                        <?php endif; ?>
                        <button class="btn btn-secondary" onclick="retryAPI()">
                            <i class="bi bi-arrow-clockwise me-2"></i>إعادة المحاولة
                        </button>
                    </div>
                    <?php endif; ?>
                    <a href="index.php" class="btn btn-primary">
                        <i class="bi bi-house me-2"></i>العودة للرئيسية
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Full Screen Player -->
    <div class="player-container" id="playerContainer">
        <button class="close-player" onclick="closePlayer()">
            <i class="bi bi-x-lg"></i>
        </button>
        <video
            id="livePlayer"
            class="video-js vjs-default-skin"
            controls
            preload="auto"
            data-setup='{"fluid": true, "responsive": true}'>
            <p class="vjs-no-js">
                To view this video please enable JavaScript, and consider upgrading to a web browser that
                <a href="https://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>.
            </p>
        </video>
    </div>

    <!-- Loading Modal -->
    <div class="modal fade" id="loadingModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-white">
                <div class="modal-body text-center p-4">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <h5>جاري تحميل البث...</h5>
                    <p class="text-muted">يرجى الانتظار</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        let player = null;
        let currentStreamUrl = '<?php echo isset($currentStream['live']) ? addslashes($currentStream['live']) : ''; ?>';
        let currentServer = <?php echo $selectedServer; ?>;
        let matchUrl = '<?php echo addslashes($matchUrl); ?>';
        
        // Switch server function
        function switchServer(serverNum) {
            const baseUrl = window.location.href.split('?')[0];
            let newUrl = baseUrl + '?server=' + serverNum;
            
            if (matchUrl) {
                newUrl += '&match=' + encodeURIComponent(matchUrl);
            }
            
            window.location.href = newUrl;
        }
        
        // Play current stream in fullscreen
        function playCurrentStream() {
            if (!currentStreamUrl) {
                alert('لا يوجد رابط بث متاح');
                return;
            }
            
            $('#loadingModal').modal('show');
            
            // Check if the URL is an iframe embed or direct stream
            if (currentStreamUrl.includes('embed') || currentStreamUrl.includes('.php')) {
                // It's an iframe URL
                playIframeStream(currentStreamUrl);
            } else {
                // It's a direct stream URL (m3u8, etc.)
                playDirectStream(currentStreamUrl);
            }
        }
        
        // Play iframe stream
        function playIframeStream(url) {
            const iframe = document.createElement('iframe');
            iframe.src = url;
            iframe.className = 'stream-iframe';
            iframe.allowFullscreen = true;
            iframe.allow = 'autoplay; encrypted-media; fullscreen';
            
            // Create fullscreen container
            const container = document.createElement('div');
            container.className = 'player-container';
            container.style.display = 'block';
            container.innerHTML = `
                <button class="close-player" onclick="closePlayer()">
                    <i class="bi bi-x-lg"></i>
                </button>
                <div class="stream-status">سيرفر ${currentServer} - مباشر</div>
            `;
            container.appendChild(iframe);
            
            document.body.appendChild(container);
            $('#loadingModal').modal('hide');
            
            // Try to go fullscreen
            setTimeout(() => {
                if (container.requestFullscreen) {
                    container.requestFullscreen();
                }
            }, 1000);
        }
        
        // Play direct stream (m3u8, etc.)
        function playDirectStream(url) {
            if (!player) {
                initPlayer();
            }
            
            player.src({
                src: url,
                type: getStreamType(url)
            });
            
            $('#playerContainer').show();
            
            setTimeout(() => {
                const playerContainer = document.getElementById('playerContainer');
                if (playerContainer.requestFullscreen) {
                    playerContainer.requestFullscreen();
                }
                player.play().catch(e => {
                    console.error('Play error:', e);
                    $('#loadingModal').modal('hide');
                    alert('حدث خطأ في تشغيل البث. يرجى تجربة سيرفر آخر.');
                    closePlayer();
                });
            }, 1000);
        }
        
        // Get stream type based on URL
        function getStreamType(url) {
            if (url.includes('.m3u8')) {
                return 'application/x-mpegURL';
            } else if (url.includes('.mp4')) {
                return 'video/mp4';
            } else {
                return 'application/x-mpegURL'; // Default to HLS
            }
        }
        
        // Open stream in new tab
        function openInNewTab() {
            if (!currentStreamUrl) {
                alert('لا يوجد رابط بث متاح');
                return;
            }
            
            window.open(currentStreamUrl, '_blank');
        }
        
        // Retry API call
        function retryAPI() {
            window.location.reload();
        }
        
        // Initialize Video.js player
        function initPlayer() {
            if (player) {
                player.dispose();
            }
            
            player = videojs('livePlayer', {
                controls: true,
                fluid: true,
                responsive: true,
                playbackRates: [0.5, 1, 1.25, 1.5, 2],
                poster: '',
                techOrder: ['html5'],
                html5: {
                    vhs: {
                        overrideNative: true
                    }
                }
            });
            
            player.ready(function() {
                console.log('Player is ready');
            });
            
            player.on('error', function(e) {
                console.error('Player error:', e);
                alert('حدث خطأ في تشغيل البث. يرجى تجربة سيرفر آخر.');
                closePlayer();
            });
            
            player.on('loadstart', function() {
                console.log('Stream loading started');
            });
            
            player.on('canplay', function() {
                console.log('Stream can play');
                $('#loadingModal').modal('hide');
            });
        }
        
        // Close player function
        function closePlayer() {
            // Remove any fullscreen iframe containers
            const existingContainers = document.querySelectorAll('.player-container');
            existingContainers.forEach(container => {
                if (container.id !== 'playerContainer') {
                    container.remove();
                }
            });
            
            if (player) {
                player.pause();
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            }
            $('#playerContainer').hide();
            $('#loadingModal').modal('hide');
        }
        
        // Handle fullscreen changes
        document.addEventListener('fullscreenchange', function() {
            if (!document.fullscreenElement) {
                // Don't auto-close, let user decide
            }
        });
        
        // Handle escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePlayer();
            }
        });
        
        // Auto-play if current stream is available
        $(document).ready(function() {
            console.log('Live Matches page loaded');
            console.log('Current stream URL:', currentStreamUrl);
            console.log('Current server:', currentServer);
            console.log('Match URL:', matchUrl);
            
            // Update server buttons state
            $('.server-btn').removeClass('btn-primary').addClass('btn-outline-primary');
            $(`.server-btn:contains("سيرفر ${currentServer}")`).removeClass('btn-outline-primary').addClass('btn-primary');
        });
    </script>
</body>
</html>
