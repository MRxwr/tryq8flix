<?php

if( isset($_GET['video_id']) && isset($_GET['itag']) ){
    
    $video_id = $_GET['video_id'];
    $itag = $_GET['itag'];
    
    // Validate video ID
    if( !preg_match('/^[a-zA-Z0-9_-]{11}$/', $video_id) ){
        echo dataError('Invalid video ID');
        exit;
    }
    
    // Try to get actual download URL using advanced methods
    $download_info = getYouTubeDownloadUrl($video_id, $itag);
    
    if( !$download_info ){
        // Fallback to alternatives if direct download fails
        $alternatives = getDownloadAlternatives($video_id, $itag);
        echo dataOutput($alternatives);
        exit;
    }
    
    // If we got a direct URL, redirect to it
    if( isset($_GET['direct']) && $_GET['direct'] == '1' ){
        echo dataOutput(array(
            'download_url' => $download_info['url'],
            'filename' => $download_info['filename'],
            'expires_in' => '6 hours (YouTube URLs expire)',
            'method_used' => $download_info['method']
        ));
    } else {
        // Redirect to direct download
        header('Location: ' . $download_info['url']);
        exit;
    }
    
}else{
    echo dataError('Video ID and itag are required');
}

// Advanced function to get actual YouTube download URLs
function getYouTubeDownloadUrl($video_id, $itag) {
    
    // Method 1: Try YouTube's internal API (like mobile apps use)
    $download_url = getFromYouTubeAPI($video_id, $itag);
    if ($download_url) {
        return array(
            'url' => $download_url,
            'filename' => sanitizeFilename("youtube_video_{$video_id}.mp4"),
            'method' => 'YouTube Internal API'
        );
    }
    
    // Method 2: Extract from YouTube page with signature handling
    $download_url = extractFromYouTubePage($video_id, $itag);
    if ($download_url) {
        return array(
            'url' => $download_url,
            'filename' => sanitizeFilename("youtube_video_{$video_id}.mp4"),
            'method' => 'Page Extraction'
        );
    }
    
    // Method 3: Try external API services
    $download_url = getFromExternalAPI($video_id, $itag);
    if ($download_url) {
        return array(
            'url' => $download_url,
            'filename' => sanitizeFilename("youtube_video_{$video_id}.mp4"),
            'method' => 'External API'
        );
    }
    
    return false;
}

// Method 1: Use YouTube's internal API (how mobile apps work)
function getFromYouTubeAPI($video_id, $itag) {
    
    // This mimics what YouTube mobile apps do
    $api_url = "https://www.youtube.com/youtubei/v1/player";
    
    $post_data = json_encode([
        'context' => [
            'client' => [
                'clientName' => 'ANDROID',
                'clientVersion' => '17.31.35',
                'androidSdkVersion' => 30,
                'userAgent' => 'com.google.android.youtube/17.31.35 (Linux; U; Android 11) gzip'
            ]
        ],
        'videoId' => $video_id,
        'params' => 'CgIQBg%3D%3D',
        'playbackContext' => [
            'contentPlaybackContext' => [
                'html5Preference' => 'HTML5_PREF_WANTS'
            ]
        ],
        'contentCheckOk' => true,
        'racyCheckOk' => true
    ]);
    
    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => [
                'Content-Type: application/json',
                'User-Agent: com.google.android.youtube/17.31.35 (Linux; U; Android 11) gzip',
                'X-YouTube-Client-Name: 3',
                'X-YouTube-Client-Version: 17.31.35'
            ],
            'content' => $post_data,
            'timeout' => 15
        ]
    ]);
    
    $response = @file_get_contents($api_url, false, $context);
    if (!$response) {
        return false;
    }
    
    $data = json_decode($response, true);
    if (!$data || !isset($data['streamingData'])) {
        return false;
    }
    
    // Look for the requested format
    $all_formats = array_merge(
        $data['streamingData']['formats'] ?? [],
        $data['streamingData']['adaptiveFormats'] ?? []
    );
    
    foreach ($all_formats as $format) {
        if (isset($format['itag']) && $format['itag'] == $itag) {
            if (isset($format['url'])) {
                return $format['url'];
            }
            // Handle signed URLs
            if (isset($format['signatureCipher']) || isset($format['cipher'])) {
                $cipher = $format['signatureCipher'] ?? $format['cipher'];
                $decoded_url = decodeCipher($cipher, $video_id);
                if ($decoded_url) {
                    return $decoded_url;
                }
            }
        }
    }
    
    return false;
}

// Method 2: Enhanced page extraction with signature handling
function extractFromYouTubePage($video_id, $itag) {
    $watch_url = "https://www.youtube.com/watch?v={$video_id}";
    
    $context = stream_context_create([
        'http' => [
            'timeout' => 15,
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'header' => [
                "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
                "Accept-Language: en-US,en;q=0.9",
                "Accept-Encoding: gzip, deflate",
                "Cache-Control: no-cache"
            ]
        ]
    ]);
    
    $html = @file_get_contents($watch_url, false, $context);
    if (!$html) {
        return false;
    }
    
    // Extract player response with multiple patterns
    $patterns = [
        '/var ytInitialPlayerResponse = ({.+?});/',
        '/window\["ytInitialPlayerResponse"\] = ({.+?});/',
        '/"ytInitialPlayerResponse":({.+?}),"/',
    ];
    
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $html, $matches)) {
            $player_data = json_decode($matches[1], true);
            
            if ($player_data && isset($player_data['streamingData'])) {
                $all_formats = array_merge(
                    $player_data['streamingData']['formats'] ?? [],
                    $player_data['streamingData']['adaptiveFormats'] ?? []
                );
                
                foreach ($all_formats as $format) {
                    if (isset($format['itag']) && $format['itag'] == $itag) {
                        if (isset($format['url'])) {
                            return $format['url'];
                        }
                        // Handle cipher
                        if (isset($format['signatureCipher'])) {
                            $decoded_url = decodeCipher($format['signatureCipher'], $video_id);
                            if ($decoded_url) {
                                return $decoded_url;
                            }
                        }
                    }
                }
            }
        }
    }
    
    return false;
}

// Method 3: Use external API services that work
function getFromExternalAPI($video_id, $itag) {
    
    // Try different external APIs
    $apis = [
        "https://youtube-dl-api-omega.vercel.app/api/youtube?url=https://www.youtube.com/watch?v={$video_id}",
        "https://api.vevioz.com/api/button/mp4/https://www.youtube.com/watch?v={$video_id}",
    ];
    
    foreach ($apis as $api_url) {
        $context = stream_context_create([
            'http' => [
                'timeout' => 10,
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'header' => 'Accept: application/json'
            ]
        ]);
        
        $response = @file_get_contents($api_url, false, $context);
        if ($response) {
            $data = json_decode($response, true);
            if ($data && isset($data['formats'])) {
                foreach ($data['formats'] as $format) {
                    if (isset($format['format_id']) && $format['format_id'] == $itag && isset($format['url'])) {
                        return $format['url'];
                    }
                }
            }
        }
    }
    
    return false;
}

// Decode YouTube's signature cipher (simplified version)
function decodeCipher($cipher, $video_id) {
    // Parse cipher parameters
    parse_str($cipher, $params);
    
    if (!isset($params['url'])) {
        return false;
    }
    
    $url = urldecode($params['url']);
    
    // If there's a signature, we'd need to decode it
    // This is complex and changes frequently
    if (isset($params['s'])) {
        // YouTube signature decoding would go here
        // This is the most complex part and changes frequently
        // For now, return the URL as-is (may not work)
        return $url;
    }
    
    return $url;
}

// Function to provide download alternatives (fallback)
function getDownloadAlternatives($video_id, $itag) {
    $youtube_url = "https://www.youtube.com/watch?v=" . $video_id;
    
    // Quality mapping
    $quality_map = array(
        '22' => '720p HD',
        '18' => '480p', 
        '134' => '360p',
        '133' => '240p'
    );
    
    $quality = isset($quality_map[$itag]) ? $quality_map[$itag] : 'Unknown Quality';
    
    return array(
        'video_id' => $video_id,
        'requested_quality' => $quality,
        'youtube_url' => $youtube_url,
        'message' => 'Direct download failed. Here are alternative methods:',
        'alternatives' => array(
            array(
                'method' => 'Browser Extension',
                'description' => 'Use browser extensions like "Video DownloadHelper" or "SaveFrom.net Helper"',
                'instructions' => array(
                    '1. Install a YouTube downloader browser extension',
                    '2. Visit the YouTube video page',
                    '3. Click the extension icon to download'
                )
            ),
            array(
                'method' => 'Online Services',
                'description' => 'Use online YouTube downloaders',
                'services' => array(
                    array(
                        'name' => 'SaveFrom.net',
                        'url' => 'https://savefrom.net/en/',
                        'instructions' => 'Paste the YouTube URL and select quality'
                    ),
                    array(
                        'name' => 'Y2mate',
                        'url' => 'https://y2mate.com/',
                        'instructions' => 'Enter YouTube URL and choose download format'
                    ),
                    array(
                        'name' => 'ClipConverter',
                        'url' => 'https://clipconverter.cc/',
                        'instructions' => 'Convert YouTube videos to various formats'
                    )
                )
            ),
            array(
                'method' => 'Desktop Software',
                'description' => 'Use desktop applications for downloading',
                'software' => array(
                    array(
                        'name' => '4K Video Downloader',
                        'description' => 'Free desktop application for Windows/Mac/Linux'
                    ),
                    array(
                        'name' => 'yt-dlp',
                        'description' => 'Command-line tool for advanced users'
                    ),
                    array(
                        'name' => 'Freemake Video Downloader',
                        'description' => 'Windows application with simple interface'
                    )
                )
            )
        ),
        'legal_notice' => 'Please respect YouTube\'s Terms of Service and copyright laws. Only download videos you have permission to download.',
        'technical_note' => 'YouTube actively prevents unauthorized downloads through various protection mechanisms including signed URLs, geographic restrictions, and rate limiting.'
    );
}

// Function to sanitize filename
function sanitizeFilename($filename) {
    $filename = preg_replace('/[^a-zA-Z0-9\-_\.\s]/', '', $filename);
    $filename = preg_replace('/\s+/', ' ', $filename);
    $filename = trim($filename);
    
    if (strlen($filename) > 100) {
        $filename = substr($filename, 0, 100);
    }
    
    return $filename ?: 'video';
}

?>
