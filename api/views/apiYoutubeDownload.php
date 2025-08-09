<?php

if( isset($_GET['video_id']) && isset($_GET['itag']) ){
    
    $video_id = $_GET['video_id'];
    $itag = $_GET['itag'];
    
    // Validate video ID
    if( !preg_match('/^[a-zA-Z0-9_-]{11}$/', $video_id) ){
        echo dataError('Invalid video ID');
        exit;
    }
    
    // Get the actual download URL
    $download_info = getYouTubeDownloadUrl($video_id, $itag);
    
    if( !$download_info ){
        echo dataError('Unable to generate download link. Video may be private or restricted.');
        exit;
    }
    
    // Redirect to the actual download URL or stream it
    if( isset($_GET['direct']) && $_GET['direct'] == '1' ){
        // Return the direct URL as JSON
        echo dataOutput(array(
            'download_url' => $download_info['url'],
            'filename' => $download_info['filename']
        ));
    } else {
        // Stream the file
        streamVideoFile($download_info);
    }
    
}else{
    echo dataError('Video ID and itag are required');
}

// Function to get YouTube download URL using various methods
function getYouTubeDownloadUrl($video_id, $itag) {
    // Method 1: Try to extract from YouTube page
    $download_url = extractFromYouTubePage($video_id, $itag);
    
    if (!$download_url) {
        // Method 2: Use third-party services as fallback
        $download_url = getFromThirdPartyService($video_id, $itag);
    }
    
    if (!$download_url) {
        // Method 3: Generate direct YouTube URL (may not always work)
        $download_url = generateDirectYouTubeUrl($video_id, $itag);
    }
    
    if ($download_url) {
        return array(
            'url' => $download_url,
            'filename' => sanitizeFilename("youtube_video_{$video_id}.mp4")
        );
    }
    
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
