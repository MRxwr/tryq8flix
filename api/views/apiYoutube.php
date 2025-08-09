<?php

if( isset($_GET['action']) && $_GET['action'] == 'Youtube' ){
    
    // Check if link is provided via POST
    if( !isset($_POST['link']) || empty($_POST['link']) ){
        echo dataError('YouTube link is required');
        exit;
    }
    
    $youtube_url = $_POST['link'];
    
    // Validate YouTube URL
    if( !isValidYouTubeUrl($youtube_url) ){
        echo dataError('Invalid YouTube URL');
        exit;
    }
    
    // Get video information and download links
    $video_info = getYouTubeVideoInfo($youtube_url);
    
    if( $video_info === false ){
        echo dataError('Failed to get video information. Video may be private, age-restricted, or unavailable.');
        exit;
    }
    
    echo dataOutput($video_info);
    
}else{
    echo dataError('Invalid request.');
}

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