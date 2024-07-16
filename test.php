<?php

function fetchWithCustomProxy($url, $proxy) {
    $ch = curl_init();
    
    // Set the URL and proxy
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_PROXY, $proxy);
    curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
    
    // Basic cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    
    // SSL Options
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    
    // Set headers to mimic a real browser
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
        'Accept-Language: en-US,en;q=0.5',
        'Accept-Encoding: gzip, deflate, br',
        'Connection: keep-alive',
        'Upgrade-Insecure-Requests: 1',
        'Cache-Control: max-age=0',
    ]);
    
    // Enable compression
    curl_setopt($ch, CURLOPT_ENCODING, '');
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    
    curl_close($ch);
    
    return [
        'http_code' => $httpCode,
        'error' => $error,
        'response' => $response
    ];
}

// List of proxies to try (preferably including some from Kuwait)
$proxies = [
    '15.236.106.236:3128', // The proxy that worked before
    // Add more proxies here, especially if you can find any from Kuwait
];

$url = "https://shvip.cam/";//"https://egydead.space/home";

foreach ($proxies as $proxy) {
    echo "Trying proxy: $proxy\n";
    $result = fetchWithCustomProxy($url, $proxy);
    
    echo "HTTP Code: " . $result['http_code'] . "\n";
    if ($result['error']) {
        echo "Error: " . $result['error'] . "\n";
    } else {
        echo "Response Preview:\n";
        echo substr($result['response'], 0, 500) . "...\n";
    }
    echo "\n";
    
    // If we get a successful response, break the loop
    if ($result['http_code'] == 200 && !empty($result['response'])) {
        break;
    }
}

?>