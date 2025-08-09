<?php

if( isset($_GET['video_id']) && isset($_GET['itag']) ){
    
    $video_id = $_GET['video_id'];
    $itag = $_GET['itag'];
    
    // Validate video ID
    if( !preg_match('/^[a-zA-Z0-9_-]{11}$/', $video_id) ){
        echo dataError('Invalid video ID');
        exit;
    }
    
    // Since direct YouTube downloads are heavily restricted,
    // provide alternative methods instead
    $alternatives = getDownloadAlternatives($video_id, $itag);
    
    echo dataOutput($alternatives);
    exit;
    
}else{
    echo dataError('Video ID and itag are required');
}

// Function to provide download alternatives
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
        'message' => 'Due to YouTube\'s protection mechanisms, direct downloads are not available.',
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
            ),
            array(
                'method' => 'Mobile Apps',
                'description' => 'Use mobile applications (where legally available)',
                'note' => 'Check your local laws and YouTube\'s terms of service'
            )
        ),
        'legal_notice' => 'Please respect YouTube\'s Terms of Service and copyright laws. Only download videos you have permission to download.',
        'technical_note' => 'YouTube actively prevents unauthorized downloads through various protection mechanisms including signed URLs, geographic restrictions, and rate limiting.'
    );
}

    
    if ($download_url) {
        return array(
            'url' => $download_url,
            'filename' => sanitizeFilename("youtube_video_{$video_id}.mp4")
        );
    }else{
        return false;
    }
    
    

// Method 1: Extract from YouTube page
function extractFromYouTubePage($video_id, $itag) {
    $watch_url = "https://www.youtube.com/watch?v={$video_id}";
    
    $context = stream_context_create([
        'http' => [
            'timeout' => 15,
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'header' => "Accept-Language: en-US,en;q=0.9\r\n"
        ]
    ]);
    
    $html = @file_get_contents($watch_url, false, $context);
    if (!$html) {
        return false;
    }
    
    // Try to extract player response
    if (preg_match('/var ytInitialPlayerResponse = ({.+?});/', $html, $matches)) {
        $player_data = json_decode($matches[1], true);
        
        if (isset($player_data['streamingData']['formats'])) {
            foreach ($player_data['streamingData']['formats'] as $format) {
                if (isset($format['itag']) && $format['itag'] == $itag && isset($format['url'])) {
                    return $format['url'];
                }
            }
        }
        
        if (isset($player_data['streamingData']['adaptiveFormats'])) {
            foreach ($player_data['streamingData']['adaptiveFormats'] as $format) {
                if (isset($format['itag']) && $format['itag'] == $itag && isset($format['url'])) {
                    return $format['url'];
                }
            }
        }
    }
    
    return false;
}

// Method 2: Use third-party service
function getFromThirdPartyService($video_id, $itag) {
    // This is a placeholder for using services like SaveFrom, Y2mate, etc.
    // Note: Be careful about terms of service when using third-party services
    
    $services = array(
        "https://www.savefrom.net/mates/en/convert?url=https://www.youtube.com/watch?v={$video_id}",
        // Add more services as needed
    );
    
    foreach ($services as $service_url) {
        $context = stream_context_create([
            'http' => [
                'timeout' => 10,
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            ]
        ]);
        
        $response = @file_get_contents($service_url, false, $context);
        if ($response) {
            // Parse response and extract download links
            // This would need to be implemented based on the specific service
            // For now, return false
        }
    }
    
    return false;
}

// Method 3: Generate direct YouTube URL (basic implementation)
function generateDirectYouTubeUrl($video_id, $itag) {
    // This is a simplified approach that may not always work
    // YouTube frequently changes their URL structure
    
    $quality_map = array(
        '22' => 'hd720',
        '18' => 'medium', 
        '134' => 'small',
        '133' => 'tiny'
    );
    
    $quality = isset($quality_map[$itag]) ? $quality_map[$itag] : 'medium';
    
    // Try to construct a basic YouTube URL
    // Note: This is very basic and may not work reliably
    $base_url = "https://www.youtube.com/api/v1/videos/{$video_id}/streams";
    
    return false; // Disable this method for now as it's unreliable
}

// Function to stream video file
function streamVideoFile($download_info) {
    $url = $download_info['url'];
    $filename = $download_info['filename'];
    
    // Get video title for better filename
    $video_data = getVideoDataMethod2(extractVideoIdFromUrl($url));
    if ($video_data && !empty($video_data['title'])) {
        $filename = sanitizeFilename($video_data['title']) . '.mp4';
    }
    
    // Set headers for download
    header('Content-Description: File Transfer');
    header('Content-Type: video/mp4');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    
    // Get file size if possible
    $context = stream_context_create([
        'http' => [
            'method' => 'HEAD',
            'timeout' => 10,
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        ]
    ]);
    
    $headers = @get_headers($url, 1, $context);
    if ($headers && isset($headers['Content-Length'])) {
        $filesize = is_array($headers['Content-Length']) ? end($headers['Content-Length']) : $headers['Content-Length'];
        header('Content-Length: ' . $filesize);
    }
    
    // Stream the file
    $stream_context = stream_context_create([
        'http' => [
            'timeout' => 300,
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        ]
    ]);
    
    $handle = @fopen($url, 'rb', false, $stream_context);
    if ($handle === false) {
        echo dataError('Failed to open download stream');
        return;
    }
    
    // Output file content in chunks
    while (!feof($handle)) {
        $chunk = fread($handle, 8192);
        if ($chunk === false) {
            break;
        }
        echo $chunk;
        
        // Flush output to prevent memory issues
        if (ob_get_level()) {
            ob_flush();
        }
        flush();
    }
    
    fclose($handle);
}

// Helper function to extract video ID from URL
function extractVideoIdFromUrl($url) {
    $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/';
    preg_match($pattern, $url, $matches);
    return isset($matches[1]) ? $matches[1] : '';
}

// Function to sanitize filename
function sanitizeFilename($filename) {
    // Remove or replace invalid characters
    $filename = preg_replace('/[^a-zA-Z0-9\-_\.\s]/', '', $filename);
    $filename = preg_replace('/\s+/', ' ', $filename);
    $filename = trim($filename);
    
    // Limit length
    if (strlen($filename) > 100) {
        $filename = substr($filename, 0, 100);
    }
    
    return $filename ?: 'video';
}

// Include the same getVideoDataMethod2 function from apiYoutube.php
function getVideoDataMethod2($video_id) {
    if (empty($video_id)) return false;
    
    $watch_url = "https://www.youtube.com/watch?v={$video_id}";
    
    $context = stream_context_create([
        'http' => [
            'timeout' => 15,
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
        ]
    ]);
    
    $html = @file_get_contents($watch_url, false, $context);
    if (!$html) {
        return false;
    }
    
    $data = array();
    
    // Extract title
    if (preg_match('/<title>([^<]+)<\/title>/', $html, $matches)) {
        $data['title'] = html_entity_decode($matches[1]);
        $data['title'] = str_replace(' - YouTube', '', $data['title']);
    }
    
    return $data;
}

?>
