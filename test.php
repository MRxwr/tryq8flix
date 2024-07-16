<?php

function checkWithProxy($url, $proxy) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36");
    curl_setopt($ch, CURLOPT_PROXY, $proxy);
    curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    
    curl_close($ch);
    
    echo "URL: $url\n";
    echo "Proxy: $proxy\n";
    echo "HTTP Code: $httpCode\n";
    if ($error) {
        echo "Error: $error\n";
    }
    echo "Response Preview:\n";
    echo substr($response, 0, 500) . "...\n\n";
}

$url = "https://shvip.cam/";
$proxies = [
    "15.236.106.236:3128",  // The proxy that worked before
    "162.245.85.220:80",  // Replace with actual proxies
];

foreach ($proxies as $proxy) {
    checkWithProxy($url, $proxy);
}

?>