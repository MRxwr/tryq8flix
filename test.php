<?php
require("templates/simple_html_dom.php");
require("admin/includes/config.php");
require("admin/includes/functions.php");

function scrapeInstagramPost($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');

    $html = curl_exec($ch);

    if (curl_errno($ch)) {
        return json_encode(['error' => 'cURL error: ' . curl_error($ch)]);
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode != 200) {
        return json_encode(['error' => "HTTP error: $httpCode"]);
    }

    // Regular expression to match the og:title meta tag
    $pattern = '/<meta property="og:title" content="(.*?)"/';

    // Extract the content of the og:title meta tag
    if (preg_match($pattern, $html, $matches)) {
        $content = html_entity_decode($matches[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Split the content into title and description
        $parts = explode(' : ', $content, 2);
        $title = $parts[0];
        $description = isset($parts[1]) ? $parts[1] : '';

        // Extract hashtags
        preg_match_all('/#(\w+)/', $description, $hashtag_matches);
        $hashtags = $hashtag_matches[1];

        // Remove hashtags from description
        $description = preg_replace('/#\w+\s?/', '', $description);

        // Create a JSON object
        $json_object = [
            'title' => $title,
            'description' => trim($description),
            'hashtags' => $hashtags
        ];

        return json_encode($json_object, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    return json_encode(['error' => 'Failed to extract og:title content']);
}

// Usage example
$instagram_post_url = 'https://www.instagram.com/trendylegend_kw/p/C-RThToIKXc/';
$result = scrapeInstagramPost($instagram_post_url);
echo $result;
?>