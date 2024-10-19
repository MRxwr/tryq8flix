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
    echo "Total Time: " . $info['total_time'] . " seconds\n";
    echo "First 1000 characters of response:\n" . substr($response, 0, 1000) . "\n\n";

    if ($info['http_code'] != 200) {
        return json_encode(['error' => "HTTP error: " . $info['http_code']]);
    }

    if (strpos($info['content_type'], 'application/json') !== false) {
        return json_encode(['error' => "Received JSON response instead of HTML. Instagram might be blocking the request."]);
    }

    // Rest of the function remains the same...
    // (The part that extracts og:title and processes it)

    return json_encode(['error' => 'Failed to extract og:title content']);
}

// Usage
$instagram_post_url = 'https://www.instagram.com/trendylegend_kw/p/C-RThToIKXc/';
$result = scrapeInstagramPost($instagram_post_url);
echo $result;

?>