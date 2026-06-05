<?php
if (!isset($_GET['action']) || empty($_GET['action'])) {
    echo dataError(array('msg' => 'action is required'));
    die();
}

if (empty($token)) {
    echo dataError(array('msg' => 'token is required'));
    die();
}

$action = $_GET['action'];
$url = isset($_REQUEST['url']) ? trim($_REQUEST['url']) : '';

if (empty($url)) {
    echo dataError(array('msg' => 'url is required'));
    die();
}

if (!filter_var($url, FILTER_VALIDATE_URL)) {
    echo dataError(array('msg' => 'invalid url'));
    die();
}

if (!isAllowedVideoUrl($url)) {
    echo dataError(array('msg' => 'Only X/Twitter, Instagram, TikTok, and YouTube links are supported'));
    die();
}

if (!in_array($action, array('info', 'link'), true)) {
    echo dataError(array('msg' => 'Invalid action'));
    die();
}

$result = runVideoDownloader($action, $url);
if ($result['ok']) {
    echo dataOutput($result['data']);
} else {
    echo dataError(array('msg' => $result['error']));
}
die();

function isAllowedVideoUrl($url)
{
    $host = strtolower(parse_url($url, PHP_URL_HOST) ?: '');
    if (empty($host)) {
        return false;
    }

    $allowedHosts = array(
        'twitter.com',
        'x.com',
        'www.twitter.com',
        'www.x.com',
        'instagram.com',
        'www.instagram.com',
        'tiktok.com',
        'www.tiktok.com',
        'vm.tiktok.com',
        'vt.tiktok.com',
        'youtube.com',
        'www.youtube.com',
        'youtu.be',
        'www.youtu.be',
        'm.youtube.com'
    );

    if (in_array($host, $allowedHosts, true)) {
        return true;
    }

    // Allow platform subdomains
    if (preg_match('/(^|\\.)instagram\\.com$/', $host)) {
        return true;
    }
    if (preg_match('/(^|\\.)tiktok\\.com$/', $host)) {
        return true;
    }
    if (preg_match('/(^|\\.)x\\.com$/', $host)) {
        return true;
    }
    if (preg_match('/(^|\\.)twitter\\.com$/', $host)) {
        return true;
    }    if (preg_match('/(^|\.)youtube\.com$/', $host)) {
        return true;
    }
    if (preg_match('/(^|\.)youtu\.be$/', $host)) {
        return true;
    }
    return false;
}

function runVideoDownloader($action, $url)
{
    $result = runPlatformFallback($action, $url);
    if ($result['ok']) {
        return $result;
    }

    return array('ok' => false, 'error' => 'No provider succeeded: ' . $result['error']);
}

function runPlatformFallback($action, $url)
{
    $host = strtolower(parse_url($url, PHP_URL_HOST) ?: '');

    if (strpos($host, 'x.com') !== false || strpos($host, 'twitter.com') !== false) {
        return runVXTwitterFallback($action, $url);
    }

    if (strpos($host, 'tiktok.com') !== false) {
        return runTikWmFallback($action, $url);
    }

    if (strpos($host, 'instagram.com') !== false) {
        return runInstagramFallback($action, $url);
    }

    if (strpos($host, 'youtube.com') !== false || strpos($host, 'youtu.be') !== false) {
        return runInvidiousFallback($action, $url);
    }

    return array('ok' => false, 'error' => 'No fallback available for this platform on this host');
}

function runTikWmFallback($action, $url)
{
    $apiUrl = 'https://www.tikwm.com/api/?url=' . rawurlencode($url);
    $json = fetchRemoteJson($apiUrl);
    if (!is_array($json) || !isset($json['code']) || intval($json['code']) !== 0 || empty($json['data']) || !is_array($json['data'])) {
        return array('ok' => false, 'error' => 'TikTok fallback API failed');
    }

    $data = $json['data'];
    $playUrl = !empty($data['play']) ? $data['play'] : (!empty($data['wmplay']) ? $data['wmplay'] : '');

    if ($action === 'link') {
        if (empty($playUrl) || !preg_match('/^https?:\/\//i', $playUrl)) {
            return array('ok' => false, 'error' => 'TikTok fallback found no playable URL');
        }

        $allUrls = array();
        if (!empty($data['play']) && preg_match('/^https?:\/\//i', $data['play'])) {
            $allUrls[] = $data['play'];
        }
        if (!empty($data['wmplay']) && preg_match('/^https?:\/\//i', $data['wmplay'])) {
            $allUrls[] = $data['wmplay'];
        }

        return array(
            'ok' => true,
            'data' => array(
                'stream_url' => $playUrl,
                'all_urls' => array_values(array_unique($allUrls)),
                'source' => 'tikwm-fallback'
            )
        );
    }

    $uploader = '';
    if (!empty($data['author']) && is_array($data['author'])) {
        $uploader = !empty($data['author']['nickname']) ? $data['author']['nickname'] : (!empty($data['author']['unique_id']) ? $data['author']['unique_id'] : '');
    }

    return array(
        'ok' => true,
        'data' => array(
            'id' => isset($data['id']) ? strval($data['id']) : '',
            'title' => isset($data['title']) ? trim($data['title']) : 'TikTok Video',
            'webpage_url' => $url,
            'uploader' => $uploader,
            'duration' => isset($data['duration']) ? intval($data['duration']) : 0,
            'thumbnail' => !empty($data['cover']) ? $data['cover'] : (!empty($data['origin_cover']) ? $data['origin_cover'] : ''),
            'ext' => 'mp4',
            'format' => 'fallback',
            'extractor' => 'tikwm-fallback'
        )
    );
}

function runInstagramFallback($action, $url)
{
    return runVxInstagramFallback($action, $url);
}

function runVxInstagramFallback($action, $url)
{
    $shortcode = extractInstagramShortcode($url);
    if (empty($shortcode)) {
        return array('ok' => false, 'error' => 'Instagram shortcode not found for vx fallback');
    }

    $vxUrl = 'https://www.vxinstagram.com/reels/' . rawurlencode($shortcode) . '/';
    $html = downloadRemoteFile($vxUrl);
    if (!is_string($html) || trim($html) === '') {
        return array('ok' => false, 'error' => 'VXInstagram page fetch failed');
    }

    $downloadUrl = '';
    $dom = str_get_html($html);
    if ($dom) {
        // Best source: direct media URL from meta tags (offload MP4).
        $ogVideo = $dom->find('meta[property=og:video]', 0);
        if ($ogVideo) {
            $candidate = trim((string)$ogVideo->getAttribute('content'));
            if (!empty($candidate)) {
                $downloadUrl = html_entity_decode($candidate, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }
        }

        if (empty($downloadUrl)) {
            $twStream = $dom->find('meta[name=twitter:player:stream]', 0);
            if ($twStream) {
                $candidate = trim((string)$twStream->getAttribute('content'));
                if (!empty($candidate)) {
                    $downloadUrl = html_entity_decode($candidate, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                }
            }
        }

        // Fallback: Download button link if meta tags are unavailable.
        foreach ($dom->find('a') as $anchor) {
            if (!empty($downloadUrl)) {
                break;
            }

            $classAttr = trim((string)$anchor->getAttribute('class'));
            if ($classAttr === '') {
                continue;
            }

            $classes = preg_split('/\s+/', $classAttr);
            if (!in_array('btn', $classes, true) || !in_array('btn-success', $classes, true)) {
                continue;
            }

            $href = trim((string)$anchor->getAttribute('href'));
            if ($href !== '') {
                $downloadUrl = html_entity_decode($href, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                break;
            }
        }

        $dom->clear();
        unset($dom);
    }

    if (empty($downloadUrl) || !preg_match('/^https?:\/\//i', $downloadUrl)) {
        return array('ok' => false, 'error' => 'VXInstagram download URL not found');
    }

    if ($action === 'link') {
        return array(
            'ok' => true,
            'data' => array(
                'stream_url' => $downloadUrl,
                'all_urls' => array($downloadUrl),
                'source' => 'vxinstagram-fallback'
            )
        );
    }

    $oembedUrl = 'https://www.instagram.com/api/v1/oembed/?url=' . rawurlencode($url);
    $oembed = fetchRemoteJson($oembedUrl);

    return array(
        'ok' => true,
        'data' => array(
            'id' => $shortcode,
            'title' => (is_array($oembed) && !empty($oembed['title'])) ? $oembed['title'] : 'Instagram Video',
            'webpage_url' => $url,
            'uploader' => (is_array($oembed) && !empty($oembed['author_name'])) ? $oembed['author_name'] : '',
            'duration' => 0,
            'thumbnail' => (is_array($oembed) && !empty($oembed['thumbnail_url'])) ? $oembed['thumbnail_url'] : '',
            'ext' => 'mp4',
            'format' => 'fallback',
            'extractor' => 'vxinstagram-fallback'
        )
    );
}

function extractInstagramShortcode($url)
{
    if (preg_match('#(?:www\.)?instagram\.com/(?:reel|reels|p|tv)/([A-Za-z0-9_-]+)#i', $url, $m)) {
        return $m[1];
    }
    return '';
}

function runVXTwitterFallback($action, $url)
{
    $tweetId = extractTwitterStatusId($url);
    if (empty($tweetId)) {
        return array('ok' => false, 'error' => 'Could not detect tweet ID for fallback');
    }

    $apiUrl = 'https://api.vxtwitter.com/Twitter/status/' . rawurlencode($tweetId);
    $json = fetchRemoteJson($apiUrl);
    if (!is_array($json)) {
        return array('ok' => false, 'error' => 'VX fallback API did not return valid JSON');
    }

    $mediaUrls = array();
    if (!empty($json['mediaURLs']) && is_array($json['mediaURLs'])) {
        foreach ($json['mediaURLs'] as $u) {
            if (is_string($u) && preg_match('/^https?:\/\//i', $u)) {
                $mediaUrls[] = $u;
            }
        }
    }

    if (empty($mediaUrls) && !empty($json['media_extended']) && is_array($json['media_extended'])) {
        foreach ($json['media_extended'] as $m) {
            if (!empty($m['url']) && is_string($m['url']) && preg_match('/^https?:\/\//i', $m['url'])) {
                $mediaUrls[] = $m['url'];
            }
        }
    }

    if ($action === 'link') {
        if (empty($mediaUrls)) {
            return array('ok' => false, 'error' => 'VX fallback found no media URL');
        }

        return array(
            'ok' => true,
            'data' => array(
                'stream_url' => $mediaUrls[0],
                'all_urls' => $mediaUrls,
                'source' => 'vxtwitter-fallback'
            )
        );
    }

    $duration = 0;
    if (!empty($json['media_extended'][0]['duration_millis'])) {
        $duration = intval(round(intval($json['media_extended'][0]['duration_millis']) / 1000));
    }

    return array(
        'ok' => true,
        'data' => array(
            'id' => isset($json['tweetID']) ? strval($json['tweetID']) : strval($tweetId),
            'title' => isset($json['text']) ? trim($json['text']) : 'Twitter Video',
            'webpage_url' => isset($json['tweetURL']) ? $json['tweetURL'] : $url,
            'uploader' => isset($json['user_name']) ? trim($json['user_name']) : (isset($json['user_screen_name']) ? trim($json['user_screen_name']) : ''),
            'duration' => $duration,
            'thumbnail' => !empty($json['media_extended'][0]['thumbnail_url']) ? $json['media_extended'][0]['thumbnail_url'] : '',
            'ext' => 'mp4',
            'format' => 'fallback',
            'extractor' => 'vxtwitter-fallback'
        )
    );
}

function extractTwitterStatusId($url)
{
    if (preg_match('/\/status\/(\d+)/i', $url, $m)) {
        return $m[1];
    }
    return '';
}

function extractYouTubeVideoId($url)
{
    // Handle youtube.com URLs
    if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([A-Za-z0-9_-]+)/i', $url, $m)) {
        return $m[1];
    }
    return '';
}

function runInvidiousFallback($action, $url)
{
    $videoId = extractYouTubeVideoId($url);
    if (empty($videoId)) {
        return array('ok' => false, 'error' => 'Could not extract YouTube video ID');
    }

    // List of Invidious instances to try (in order of preference)
    $instances = array(
        'https://inv.nadeko.net',
        'https://invidious.nerdvpn.de',
        'https://inv.thepixora.com',
        'https://yt.chocolatemoo53.com'
    );

    foreach ($instances as $instance) {
        // Fetch the watch page HTML
        $watchUrl = $instance . '/watch?v=' . rawurlencode($videoId);
        $html = downloadRemoteFileWithReferer($watchUrl, $instance);
        
        if (!is_string($html) || trim($html) === '') {
            continue;
        }

        $downloadUrl = '';
        $thumbnail = '';
        $title = 'YouTube Video';
        $author = '';
        $duration = 0;

        // Parse HTML to extract meta tags
        $dom = str_get_html($html);
        if ($dom) {
            // Extract video URL from og:video meta tag
            $ogVideo = $dom->find('meta[property=og:video]', 0);
            if ($ogVideo) {
                $candidate = trim((string)$ogVideo->getAttribute('content'));
                if (!empty($candidate) && preg_match('/^\//', $candidate)) {
                    // Relative URL, make it absolute
                    $downloadUrl = $instance . $candidate;
                } elseif (!empty($candidate)) {
                    $downloadUrl = $candidate;
                }
            }

            // Extract thumbnail from og:image
            $ogImage = $dom->find('meta[property=og:image]', 0);
            if ($ogImage) {
                $candidate = trim((string)$ogImage->getAttribute('content'));
                if (!empty($candidate)) {
                    if (preg_match('/^\//', $candidate)) {
                        $thumbnail = $instance . $candidate;
                    } else {
                        $thumbnail = $candidate;
                    }
                }
            }

            // Extract title from og:title
            $ogTitle = $dom->find('meta[property=og:title]', 0);
            if ($ogTitle) {
                $candidate = trim((string)$ogTitle->getAttribute('content'));
                if (!empty($candidate)) {
                    $title = $candidate;
                }
            }

            // Try to find duration (look for it in the HTML or calculate from video meta)
            // Invidious embeds data in the page, look for length in meta or duration attribute
            $metaTags = $dom->find('meta');
            foreach ($metaTags as $tag) {
                $property = strtolower($tag->getAttribute('property') ?: $tag->getAttribute('name') ?: '');
                if (strpos($property, 'duration') !== false) {
                    $durationCandidate = trim((string)$tag->getAttribute('content'));
                    if (!empty($durationCandidate) && is_numeric($durationCandidate)) {
                        $duration = intval($durationCandidate);
                        break;
                    }
                }
            }

            $dom->clear();
            unset($dom);
        }

        // Validate download URL
        if (empty($downloadUrl) || !preg_match('/^https?:\/\//i', $downloadUrl)) {
            continue; // Try next instance
        }

        if ($action === 'link') {
            return array(
                'ok' => true,
                'data' => array(
                    'stream_url' => $downloadUrl,
                    'all_urls' => array($downloadUrl),
                    'source' => 'invidious-fallback'
                )
            );
        }

        return array(
            'ok' => true,
            'data' => array(
                'id' => $videoId,
                'title' => $title,
                'webpage_url' => $url,
                'uploader' => $author,
                'duration' => $duration,
                'thumbnail' => $thumbnail,
                'ext' => 'mp4',
                'format' => 'fallback',
                'extractor' => 'invidious-fallback'
            )
        );
    }

    return array('ok' => false, 'error' => 'All Invidious instances failed for this YouTube video');
}

function fetchRemoteJson($url)
{
    $raw = downloadRemoteFile($url);
    if (!is_string($raw) || trim($raw) === '') {
        return null;
    }

    $decoded = json_decode($raw, true);
    return is_array($decoded) ? $decoded : null;
}

function fetchRemoteJsonWithReferer($url, $referer)
{
    $raw = downloadRemoteFileWithReferer($url, $referer);
    if (!is_string($raw) || trim($raw) === '') {
        return null;
    }

    $decoded = json_decode($raw, true);
    return is_array($decoded) ? $decoded : null;
}


function downloadRemoteFile($url)
{
    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept: application/json, text/plain, */*',
            'Accept-Language: en-US,en;q=0.9',
            'Accept-Encoding: gzip, deflate, br',
            'Cache-Control: no-cache',
            'Pragma: no-cache',
            'Sec-Fetch-Dest: empty',
            'Sec-Fetch-Mode: cors',
            'Sec-Fetch-Site: same-origin',
            'X-Requested-With: XMLHttpRequest'
        ));
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $data = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if (is_string($data) && $data !== '' && $httpCode >= 200 && $httpCode < 400) {
            return $data;
        }
    }

    if (ini_get('allow_url_fopen')) {
        $context = stream_context_create(array(
            'http' => array(
                'follow_location' => 1,
                'timeout' => 120,
                'user_agent' => 'Mozilla/5.0 TryQ8Flix Downloader'
            )
        ));
        $data = @file_get_contents($url, false, $context);
        if (is_string($data) && $data !== '') {
            return $data;
        }
    }

    return false;
}

function downloadRemoteFileWithReferer($url, $referer)
{
    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
        curl_setopt($ch, CURLOPT_REFERER, $referer);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept: application/json, text/plain, */*',
            'Accept-Language: en-US,en;q=0.9',
            'Accept-Encoding: gzip, deflate, br',
            'Cache-Control: no-cache',
            'Pragma: no-cache',
            'Sec-Fetch-Dest: empty',
            'Sec-Fetch-Mode: cors',
            'Sec-Fetch-Site: same-origin',
            'X-Requested-With: XMLHttpRequest',
            'Origin: ' . $referer
        ));
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $data = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if (is_string($data) && $data !== '' && $httpCode >= 200 && $httpCode < 400) {
            return $data;
        }
    }

    if (ini_get('allow_url_fopen')) {
        $context = stream_context_create(array(
            'http' => array(
                'follow_location' => 1,
                'timeout' => 120,
                'user_agent' => 'Mozilla/5.0 TryQ8Flix Downloader',
                'header' => "Referer: $referer\r\n"
            )
        ));
        $data = @file_get_contents($url, false, $context);
        if (is_string($data) && $data !== '') {
            return $data;
        }
    }

    return false;
}

