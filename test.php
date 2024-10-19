<?php
require("templates/simple_html_dom.php");
require("admin/includes/config.php");
require("admin/includes/functions.php");

function scrapeInstagramPost($url) {
    // Fetch the HTML content of the Instagram post
    $html = file_get_contents($url);

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