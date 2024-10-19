<?php
require("templates/simple_html_dom.php");
require("admin/includes/config.php");
require("admin/includes/functions.php");

function scrapeInstagramPost($url) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
        CURLOPT_HTTPHEADER => [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Accept-Language: en-US,en;q=0.5',
            'Connection: keep-alive',
            'Upgrade-Insecure-Requests: 1',
        ],
        CURLOPT_ENCODING => '',
        CURLOPT_TIMEOUT => 30,
    ]);

    $response = curl_exec($ch);
    $info = curl_getinfo($ch);

    if (curl_errno($ch)) {
        return json_encode(['error' => 'cURL error: ' . curl_error($ch)]);
    }

    curl_close($ch);

    echo "HTTP Status Code: " . $info['http_code'] . "\n";
    echo "Content Type: " . $info['content_type'] . "\n";
    echo "Total Time: " . $info['total_time'] . " seconds\n\n";

    if ($info['http_code'] != 200) {
        return json_encode(['error' => "HTTP error: " . $info['http_code']]);
    }

    echo "First 1000 characters of the response:\n";
    echo substr($response, 0, 1000) . "\n\n";

    echo "Last 1000 characters of the response:\n";
    echo substr($response, -1000) . "\n\n";

    // Extract all meta tags
    preg_match_all('/<meta[^>]+>/i', $response, $matches);

    echo "Meta tags found:\n";
    if (!empty($matches[0])) {
        foreach ($matches[0] as $meta_tag) {
            echo $meta_tag . "\n";
        }
    } else {
        echo "No meta tags found.\n";
    }

    // Try to find og:title specifically
    preg_match('/<meta property="og:title" content="(.*?)"/i', $response, $og_title_match);

    if (!empty($og_title_match)) {
        $og_title = html_entity_decode($og_title_match[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        echo "\nog:title content: " . $og_title . "\n";

        // Process the og:title content as before...
        $parts = explode(' : ', $og_title, 2);
        $title = $parts[0];
        $description = isset($parts[1]) ? $parts[1] : '';

        preg_match_all('/#(\w+)/', $description, $hashtag_matches);
        $hashtags = $hashtag_matches[1];

        $description = preg_replace('/#\w+\s?/', '', $description);

        $json_object = [
            'title' => $title,
            'description' => trim($description),
            'hashtags' => $hashtags
        ];

        return json_encode($json_object, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    } else {
        return json_encode(['error' => 'Failed to extract og:title content']);
    }
}

// Usage with the provided Instagram post URL
$instagram_post_url = 'https://www.instagram.com/trendylegend_kw/p/C-RThToIKXc/';
$result = scrapeInstagramPost($instagram_post_url);
echo $result;

?>