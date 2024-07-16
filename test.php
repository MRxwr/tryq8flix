<?php

function fetchRawHtml($url, $proxy) {
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // Don't follow redirects automatically
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_PROXY, $proxy);
    curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
        'Accept-Language: en-US,en;q=0.5',
        'Cache-Control: no-cache',
        'Pragma: no-cache',
        'Upgrade-Insecure-Requests: 1'
    ]);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36");
    $response = curl_exec($ch);
    curl_close($ch);
    
    return $response;
}

// Usage
$urls = [
    "https://egydead.space/home",
    "https://shvip.cam",
];
$proxy = "15.236.106.236:3128";

foreach ($urls as $url) {
    echo "Trying URL: $url\n";
    $rawHtml = fetchRawHtml($url, $proxy);
    
    if ($rawHtml !== false) {
        echo "Retrieved content:\n\n";
        echo $rawHtml . "\n\n";
        //file_put_contents('rawhtml_' . md5($url) . '.txt', $rawHtml);
        //echo "Content saved to 'rawhtml_" . md5($url) . ".txt'\n\n";
    } else {
        echo "Failed to retrieve content from $url\n\n";
    }
}

?>