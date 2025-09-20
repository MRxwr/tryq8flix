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

// Live streaming links array
$liveStreams = [
    [
        'id' => 1,
        'name' => 'أبو ظبي الرياضية 1',
        'url' => 'https://admdn1.cdn.mangomolo.com/adsports1/smil:adsports1.stream.smil/chunklist_b4000000_t64MTA4MHA=.m3u8',
        'category' => 'sports',
        'language' => 'ar',
        'logo' => 'https://i.imgur.com/placeholder1.png'
    ],
    [
        'id' => 2,
        'name' => 'أبو ظبي الرياضية 2',
        'url' => 'https://admdn5.cdn.mangomolo.com/adsports2/smil:adsports2.stream.smil/chunklist_b4000000_t64MTA4MHA=.m3u8',
        'category' => 'sports',
        'language' => 'ar',
        'logo' => 'https://i.imgur.com/placeholder2.png'
    ],
    [
        'id' => 3,
        'name' => 'الكويت الرياضية',
        'url' => 'https://kwtspta.cdn.mangomolo.com/sp/smil:sp.stream.smil/chunklist_b2500000_t64MTA4MHA=.m3u8',
        'category' => 'sports',
        'language' => 'ar',
        'logo' => 'https://i.imgur.com/placeholder3.png'
    ],
    [
        'id' => 4,
        'name' => 'الكويت الرياضية بلس',
        'url' => 'https://kwtsplta.cdn.mangomolo.com/spl/smil:spl.stream.smil/chunklist_b2500000_t64MTA4MHA=.m3u8',
        'category' => 'sports',
        'language' => 'ar',
        'logo' => 'https://i.imgur.com/placeholder4.png'
    ],
    [
        'id' => 5,
        'name' => 'دبي الرياضية 1',
        'url' => 'https://dmitnthvll.cdn.mangomolo.com/dubaisports/smil:dubaisports.smil/chunklist_b1600000.m3u8',
        'category' => 'sports',
        'language' => 'ar',
        'logo' => 'https://i.imgur.com/placeholder5.png'
    ],
    [
        'id' => 6,
        'name' => 'دبي الرياضية 2',
        'url' => 'https://dmitwlvvll.cdn.mangomolo.com/dubaisportshd/smil:dubaisportshd.smil/chunklist_b1600000.m3u8',
        'category' => 'sports',
        'language' => 'ar',
        'logo' => 'https://i.imgur.com/placeholder6.png'
    ],
    [
        'id' => 7,
        'name' => 'دبي الرياضية 3',
        'url' => 'https://dmitwlvvll.cdn.mangomolo.com/dubaisportshd5/smil:dubaisportshd5.smil/chunklist_b1600000.m3u8',
        'category' => 'sports',
        'language' => 'ar',
        'logo' => 'https://i.imgur.com/placeholder7.png'
    ],
    [
        'id' => 8,
        'name' => 'قناة الشارقة الرياضية',
        'url' => 'https://svs.itworkscdn.net/smc4sportslive/smc4.smil/chunklist_b1600000.m3u8',
        'category' => 'sports',
        'language' => 'ar',
        'logo' => 'https://i.imgur.com/placeholder8.png'
    ],
    [
        'id' => 9,
        'name' => 'الإمارات الرياضية',
        'url' => 'https://bcovlive-a.akamaihd.net/0764b2cc4e2c4ec0b0efc23cf1e11a56/ap-south-1/6313884884001/profile_0/chunklist.m3u8',
        'category' => 'sports',
        'language' => 'ar',
        'logo' => 'https://i.imgur.com/placeholder9.png'
    ],
    [
        'id' => 10,
        'name' => 'السعودية الرياضية',
        'url' => 'https://edge.taghtia.com/sa/6.m3u8',
        'category' => 'sports',
        'language' => 'ar',
        'logo' => 'https://i.imgur.com/placeholder10.png'
    ],
    [
        'id' => 11,
        'name' => 'الرياضة السعودية',
        'url' => 'https://edge.taghtia.com/sa/3.m3u8',
        'category' => 'sports',
        'language' => 'ar',
        'logo' => 'https://i.imgur.com/placeholder11.png'
    ],
    [
        'id' => 12,
        'name' => 'بحرين الرياضية',
        'url' => 'https://5c7b683162943.streamlock.net/live/ngrp:bahrainsports_all/chunklist_b1524000.m3u8',
        'category' => 'sports',
        'language' => 'ar',
        'logo' => 'https://i.imgur.com/placeholder12.png'
    ],
    [
        'id' => 13,
        'name' => 'الأردن الرياضية',
        'url' => 'https://jrtvsports-live.ercdn.net/jrtvsports/jrtvsports.m3u8',
        'category' => 'sports',
        'language' => 'ar',
        'logo' => 'https://i.imgur.com/placeholder13.png'
    ],
    [
        'id' => 14,
        'name' => 'قطر الرياضية 1',
        'url' => 'https://qatarsports1-live.akamaized.net/hls/live/2086576/QatarSports1/master.m3u8',
        'category' => 'sports',
        'language' => 'ar',
        'logo' => 'https://i.imgur.com/placeholder14.png'
    ],
    [
        'id' => 15,
        'name' => 'قطر الرياضية 2',
        'url' => 'https://qatarsports2-live.akamaized.net/hls/live/2086577/QatarSports2/master.m3u8',
        'category' => 'sports',
        'language' => 'ar',
        'logo' => 'https://i.imgur.com/placeholder15.png'
    ]
];

// Get selected stream
$selectedStream = null;
if (isset($_GET['stream'])) {
    $streamId = intval($_GET['stream']);
    foreach ($liveStreams as $stream) {
        if ($stream['id'] == $streamId) {
            $selectedStream = $stream;
            break;
        }
    }
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
                <h1 class="text-center mb-3">
                    <i class="bi bi-broadcast me-2"></i>البث المباشر للقنوات الرياضية
                </h1>
                <p class="text-center text-muted">اختر القناة التي تريد مشاهدتها</p>
            </div>
        </div>

        <!-- Search and Filter -->
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

        <!-- Streams Grid -->
        <div class="grid-container">
            <div class="row" id="streamsGrid">
                <?php foreach ($liveStreams as $stream): ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4 stream-item" 
                     data-category="<?php echo $stream['category']; ?>" 
                     data-name="<?php echo strtolower($stream['name']); ?>">
                    <div class="card stream-card h-100" onclick="playStream(<?php echo $stream['id']; ?>, '<?php echo htmlspecialchars($stream['url']); ?>', '<?php echo htmlspecialchars($stream['name']); ?>')">
                        <div class="position-relative">
                            <div class="live-indicator">مباشر</div>
                            <div class="stream-info text-center">
                                <div class="mb-3">
                                    <i class="bi bi-tv-fill" style="font-size: 48px; color: #e50914;"></i>
                                </div>
                                <div class="stream-name"><?php echo $stream['name']; ?></div>
                                <div class="stream-category"><?php echo $stream['category']; ?></div>
                                <div class="mt-3">
                                    <button class="btn btn-primary btn-sm">
                                        <i class="bi bi-play-fill me-1"></i>مشاهدة
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
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
            
            // Handle player events
            player.ready(function() {
                console.log('Player is ready');
            });
            
            player.on('error', function(e) {
                console.error('Player error:', e);
                alert('حدث خطأ في تشغيل البث. يرجى المحاولة مرة أخرى.');
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
        
        // Play stream function
        function playStream(streamId, streamUrl, streamName) {
            console.log('Playing stream:', streamName, streamUrl);
            
            // Show loading modal
            $('#loadingModal').modal('show');
            
            // Initialize player if not already done
            if (!player) {
                initPlayer();
            }
            
            // Set source and play
            player.src({
                src: streamUrl,
                type: 'application/x-mpegURL'
            });
            
            // Show player container
            $('#playerContainer').show();
            
            // Enter fullscreen
            setTimeout(() => {
                if (player.requestFullscreen) {
                    player.requestFullscreen();
                }
                player.play().catch(e => {
                    console.error('Play error:', e);
                    $('#loadingModal').modal('hide');
                    alert('حدث خطأ في تشغيل البث. يرجى المحاولة مرة أخرى.');
                    closePlayer();
                });
            }, 1000);
        }
        
        // Close player function
        function closePlayer() {
            if (player) {
                player.pause();
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            }
            $('#playerContainer').hide();
            $('#loadingModal').modal('hide');
        }
        
        // Search functionality
        $('#searchInput').on('input', function() {
            const searchTerm = $(this).val().toLowerCase();
            filterStreams();
        });
        
        // Category filter
        $('#categoryFilter').on('change', function() {
            filterStreams();
        });
        
        // Filter streams function
        function filterStreams() {
            const searchTerm = $('#searchInput').val().toLowerCase();
            const selectedCategory = $('#categoryFilter').val();
            
            $('.stream-item').each(function() {
                const streamName = $(this).data('name');
                const streamCategory = $(this).data('category');
                
                let showItem = true;
                
                // Filter by search term
                if (searchTerm && !streamName.includes(searchTerm)) {
                    showItem = false;
                }
                
                // Filter by category
                if (selectedCategory && streamCategory !== selectedCategory) {
                    showItem = false;
                }
                
                $(this).toggle(showItem);
            });
        }
        
        // Handle fullscreen changes
        document.addEventListener('fullscreenchange', function() {
            if (!document.fullscreenElement) {
                closePlayer();
            }
        });
        
        // Handle escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePlayer();
            }
        });
        
        // Initialize on page load
        $(document).ready(function() {
            console.log('Live Matches page loaded');
            
            // Auto-play if stream parameter is provided
            <?php if ($selectedStream): ?>
            setTimeout(() => {
                playStream(
                    <?php echo $selectedStream['id']; ?>, 
                    '<?php echo htmlspecialchars($selectedStream['url']); ?>', 
                    '<?php echo htmlspecialchars($selectedStream['name']); ?>'
                );
            }, 1000);
            <?php endif; ?>
        });
    </script>
</body>
</html>
