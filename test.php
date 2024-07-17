<?php

function testProxy($url, $proxy) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
    curl_setopt($ch, CURLOPT_PROXY, $proxy);
    curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_VERBOSE, true);

    $verbose = fopen('php://temp', 'w+');
    curl_setopt($ch, CURLOPT_STDERR, $verbose);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    $errno = curl_errno($ch);

    rewind($verbose);
    $verboseLog = stream_get_contents($verbose);

    curl_close($ch);

    return [
        'http_code' => $httpCode,
        'error' => $error,
        'errno' => $errno,
        'verbose_log' => $verboseLog,
        'response' => $response
    ];
}

// Test proxy
$url = 'https://shvip.cam/'; // Using http to avoid SSL issues
$proxy = '15.236.106.236:3128'; // Replace with your proxy

echo "Testing proxy connection to $url using proxy $proxy\n\n";

$result = testProxy($url, $proxy);

echo "HTTP Code: " . $result['http_code'] . "\n";
echo "Error: " . $result['error'] . "\n";
echo "Error Number: " . $result['errno'] . "\n";
echo "Verbose Log:\n" . $result['verbose_log'] . "\n";

if (!empty($result['response'])) {
    echo "Response Preview:\n" . substr($result['response'], 0, 500) . "...\n";
} else {
    echo "No response received.\n";
}

?>