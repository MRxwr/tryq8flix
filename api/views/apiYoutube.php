<?php

if( isset($_GET['action']) && $_GET['action'] == 'Youtube' ){
    
    // Enable error reporting for debugging
    error_reporting(E_ALL);
    
    // Check if link is provided via POST
    if( !isset($_POST['link']) || empty($_POST['link']) ){
        echo dataError('YouTube link is required');
        exit;
    }
    
    $youtube_url = trim($_POST['link']);
    
    // Validate YouTube URL
    if( !isValidYouTubeUrl($youtube_url) ){
        echo dataError('Invalid YouTube URL format. Please provide a valid YouTube URL.');
        exit;
    }
    
    // Get video information and download links
    try {
        $video_info = getYouTubeVideoInfo($youtube_url);
        
        if( $video_info === false ){
            echo dataError('Failed to extract video information. The video may be private, age-restricted, deleted, or temporarily unavailable.');
            exit;
        }
        
        echo dataOutput($video_info);
        
    } catch (Exception $e) {
        echo dataError('Error processing request: ' . $e->getMessage());
        exit;
    }
    
}else{
    echo dataError('Invalid request. Missing action parameter.');
}

// Function to validate YouTube URL
function isValidYouTubeUrl($url) {
    $patterns = array(
        '/^(?:https?:\/\/)?(?:www\.)?youtube\.com\/watch\?v=([a-zA-Z0-9_-]{11})/',
        '/^(?:https?:\/\/)?(?:www\.)?youtu\.be\/([a-zA-Z0-9_-]{11})/',
        '/^(?:https?:\/\/)?(?:www\.)?youtube\.com\/embed\/([a-zA-Z0-9_-]{11})/'
    );
    
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $url)) {
            return true;
        }
    }
    return false;
}

// Function to extract YouTube video ID from URL
function extractYouTubeVideoId($url) {
    $patterns = array(
        '/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/'
    );
    
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $url, $matches)) {
            return $matches[1];
        }
    }
    return false;
}

// Function to get YouTube video information using web scraping
function getYouTubeVideoInfo($url) {
    $video_id = extractYouTubeVideoId($url);
    if (!$video_id) {
        return false;
    }
    
    $video_data = false;
    $last_error = '';
    
    // Try Method 1: oEmbed API
    try {
        $video_data = getVideoDataMethod1($video_id);
        if ($video_data && !empty($video_data['title'])) {
            // Success with Method 1
        } else {
            $video_data = false;
        }
    } catch (Exception $e) {
        $last_error = 'oEmbed failed: ' . $e->getMessage();
    }
    
    // Try Method 2: Direct page scraping if Method 1 failed
    if (!$video_data) {
        try {
            $video_data = getVideoDataMethod2($video_id);
            if (!$video_data || empty($video_data['title'])) {
                $video_data = false;
            }
        } catch (Exception $e) {
            $last_error .= ' | Page scraping failed: ' . $e->getMessage();
        }
    }
    
    // If both methods failed, try a basic approach
    if (!$video_data) {
        $video_data = array(
            'title' => 'YouTube Video - ' . $video_id,
            'author' => 'Unknown',
            'duration' => 0,
            'view_count' => 0,
            'description' => 'Video information could not be extracted, but download links are still available.'
        );
    }
    
    // Extract download links
    $formats = extractDownloadFormats($video_id, $video_data);
    
    $result = array(
        'video_id' => $video_id,
        'title' => $video_data['title'] ?? 'Unknown Title',
        'duration' => formatDuration($video_data['duration'] ?? 0),
        'thumbnail' => "https://img.youtube.com/vi/{$video_id}/maxresdefault.jpg",
        'uploader' => $video_data['author'] ?? 'Unknown',
        'view_count' => (int)($video_data['view_count'] ?? 0),
        'description' => substr($video_data['description'] ?? 'No description available', 0, 500),
        'formats' => $formats,
        'debug_info' => $last_error ? $last_error : 'Video information extracted successfully'
    );
    
    return $result;
}

// Method 1: Use YouTube's oEmbed API
function getVideoDataMethod1($video_id) {
    $oembed_url = "https://www.youtube.com/oembed?url=https://www.youtube.com/watch?v={$video_id}&format=json";
    
    // Check if allow_url_fopen is enabled
    if (!ini_get('allow_url_fopen')) {
        throw new Exception('allow_url_fopen is disabled');
    }
    
    $context = stream_context_create([
        'http' => [
            'timeout' => 10,
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'header' => "Accept: application/json\r\nAccept-Language: en-US,en;q=0.9\r\n"
        ]
    ]);
    
    $response = @file_get_contents($oembed_url, false, $context);
    if (!$response) {
        throw new Exception('Failed to fetch oEmbed data');
    }
    
    $data = json_decode($response, true);
    if (!$data || json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON response from oEmbed');
    }
    
    return array(
        'title' => $data['title'] ?? '',
        'author' => $data['author_name'] ?? '',
        'thumbnail' => $data['thumbnail_url'] ?? ''
    );
}

// Method 2: Parse YouTube page directly  
function getVideoDataMethod2($video_id) {
    $watch_url = "https://www.youtube.com/watch?v={$video_id}";
    
    if (!ini_get('allow_url_fopen')) {
        throw new Exception('allow_url_fopen is disabled');
    }
    
    $context = stream_context_create([
        'http' => [
            'timeout' => 15,
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'header' => "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\nAccept-Language: en-US,en;q=0.9\r\n"
        ]
    ]);
    
    $html = @file_get_contents($watch_url, false, $context);
    if (!$html) {
        throw new Exception('Failed to fetch YouTube page');
    }
    
    $data = array();
    
    // Extract title from page title
    if (preg_match('/<title>([^<]+)<\/title>/i', $html, $matches)) {
        $title = html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8');
        $title = str_replace(' - YouTube', '', $title);
        $data['title'] = trim($title);
    }
    
    // Try to extract more data from JSON in the page
    $json_patterns = array(
        '/var ytInitialPlayerResponse = ({.+?});/',
        '/window\["ytInitialPlayerResponse"\] = ({.+?});/',
    );
    
    foreach ($json_patterns as $pattern) {
        if (preg_match($pattern, $html, $matches)) {
            $json_data = json_decode($matches[1], true);
            if ($json_data && isset($json_data['videoDetails'])) {
                $video_details = $json_data['videoDetails'];
                $data['title'] = $video_details['title'] ?? $data['title'] ?? '';
                $data['author'] = $video_details['author'] ?? '';
                $data['duration'] = (int)($video_details['lengthSeconds'] ?? 0);
                $data['view_count'] = (int)($video_details['viewCount'] ?? 0);
                $data['description'] = $video_details['shortDescription'] ?? '';
                break;
            }
        }
    }
    
    // If we couldn't extract a title, throw an error
    if (empty($data['title'])) {
        throw new Exception('Could not extract video title from page');
    }
    
    return $data;
}

// Function to extract download formats
function extractDownloadFormats($video_id, $video_data) {
    $formats = array();
    
    // Generate different quality options
    $quality_options = array(
        array('quality' => '720p HD', 'itag' => 22, 'resolution' => '1280x720'),
        array('quality' => '480p', 'itag' => 18, 'resolution' => '854x480'),
        array('quality' => '360p', 'itag' => 134, 'resolution' => '640x360'),
        array('quality' => '240p', 'itag' => 133, 'resolution' => '426x240')
    );
    
    foreach ($quality_options as $info) {
        $formats[] = array(
            'format_id' => $info['itag'],
            'quality' => $info['quality'],
            'resolution' => $info['resolution'],
            'filesize' => 'Unknown',
            'ext' => 'mp4',
            'download_url' => generateDownloadUrl($video_id, $info['itag'])
        );
    }
    
    return $formats;
}

// Function to generate download URL using internal proxy
function generateDownloadUrl($video_id, $itag) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $script_path = dirname($_SERVER['REQUEST_URI']);
    
    return $protocol . '://' . $host . $script_path . '?endpoint=YoutubeDownload&video_id=' . urlencode($video_id) . '&itag=' . urlencode($itag);
}

// Function to format duration from seconds to HH:MM:SS
function formatDuration($seconds) {
    $seconds = (int)$seconds;
    if ($seconds <= 0) return '00:00';
    
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $secs = $seconds % 60;
    
    if ($hours > 0) {
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
    } else {
        return sprintf('%02d:%02d', $minutes, $secs);
    }
}

?>

// Function to validate YouTube URL
function isValidYouTubeUrl($url) {
    $pattern = '/^(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/';
    return preg_match($pattern, $url);
}

// Function to extract YouTube video ID from URL
function extractYouTubeVideoId($url) {
    $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/';
    preg_match($pattern, $url, $matches);
    return isset($matches[1]) ? $matches[1] : false;
}

// Function to get YouTube video information using web scraping
function getYouTubeVideoInfo($url) {
    $video_id = extractYouTubeVideoId($url);
    if (!$video_id) {
        return false;
    }
    
    // Try multiple methods to get video information
    $video_data = getVideoDataMethod1($video_id);
    if (!$video_data) {
        $video_data = getVideoDataMethod2($video_id);
    }
    
    if (!$video_data) {
        return false;
    }
    
    // Extract download links
    $formats = extractDownloadFormats($video_id, $video_data);
    
    $result = array(
        'video_id' => $video_id,
        'title' => $video_data['title'] ?? 'Unknown Title',
        'duration' => formatDuration($video_data['duration'] ?? 0),
        'thumbnail' => "https://img.youtube.com/vi/{$video_id}/maxresdefault.jpg",
        'uploader' => $video_data['author'] ?? 'Unknown',
        'view_count' => $video_data['view_count'] ?? 0,
        'description' => substr($video_data['description'] ?? '', 0, 500),
        'formats' => $formats
    );
    
    return $result;
}

// Method 1: Use YouTube's oEmbed API
function getVideoDataMethod1($video_id) {
    $oembed_url = "https://www.youtube.com/oembed?url=https://www.youtube.com/watch?v={$video_id}&format=json";
    
    $context = stream_context_create([
        'http' => [
            'timeout' => 10,
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        ]
    ]);
    
    $response = @file_get_contents($oembed_url, false, $context);
    if (!$response) {
        return false;
    }
    
    $data = json_decode($response, true);
    if (!$data) {
        return false;
    }
    
    return array(
        'title' => $data['title'] ?? '',
        'author' => $data['author_name'] ?? '',
        'thumbnail' => $data['thumbnail_url'] ?? ''
    );
}

// Method 2: Parse YouTube page directly
function getVideoDataMethod2($video_id) {
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
    
    // Extract JSON data from page
    if (preg_match('/var ytInitialPlayerResponse = ({.+?});/', $html, $matches)) {
        $json_data = json_decode($matches[1], true);
        if ($json_data) {
            $video_details = $json_data['videoDetails'] ?? array();
            $data['title'] = $video_details['title'] ?? $data['title'] ?? '';
            $data['author'] = $video_details['author'] ?? '';
            $data['duration'] = $video_details['lengthSeconds'] ?? 0;
            $data['view_count'] = $video_details['viewCount'] ?? 0;
            $data['description'] = $video_details['shortDescription'] ?? '';
        }
    }
    
    return $data;
}

// Function to extract download formats
function extractDownloadFormats($video_id, $video_data) {
    $formats = array();
    
    // Generate different quality options with proxy URLs
    $quality_options = array(
        'hd720' => array('quality' => '720p HD', 'itag' => 22, 'resolution' => '1280x720'),
        'medium' => array('quality' => '480p', 'itag' => 18, 'resolution' => '854x480'),
        'small' => array('quality' => '360p', 'itag' => 134, 'resolution' => '640x360'),
        'tiny' => array('quality' => '240p', 'itag' => 133, 'resolution' => '426x240')
    );
    
    foreach ($quality_options as $key => $info) {
        $formats[] = array(
            'format_id' => $info['itag'],
            'quality' => $info['quality'],
            'resolution' => $info['resolution'],
            'filesize' => 'Unknown',
            'ext' => 'mp4',
            'download_url' => generateDownloadUrl($video_id, $info['itag'])
        );
    }
    
    return $formats;
}

// Function to generate download URL using internal proxy
function generateDownloadUrl($video_id, $itag) {
    $base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
    $api_path = dirname($_SERVER['REQUEST_URI']);
    
    return $base_url . $api_path . "?endpoint=YoutubeDownload&video_id=" . urlencode($video_id) . "&itag=" . urlencode($itag);
}

// Function to format duration from seconds to HH:MM:SS
function formatDuration($seconds) {
    if ($seconds <= 0) return '00:00';
    
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $seconds = $seconds % 60;
    
    if ($hours > 0) {
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    } else {
        return sprintf('%02d:%02d', $minutes, $seconds);
    }
}

?>