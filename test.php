<?php
function makeRequest($url, $useProxy = false, $proxy = '') {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');

    if ($useProxy) {
        curl_setopt($ch, CURLOPT_PROXY, $proxy);
        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
    }

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

// Test 1: Local PHP info
echo "Test 1: Local PHP Info\n";
phpinfo();
echo "\n\n";

// Test 2: Direct request to a known good site
echo "Test 2: Direct Request to Known Good Site\n";
$result = makeRequest('https://shvip.cam/');
echo "HTTP Code: " . $result['http_code'] . "\n";
echo "Response Preview:\n" . substr($result['response'], 0, 500) . "...\n\n";

// Test 3: Proxied request to a known good site
echo "Test 3: Proxied Request to Known Good Site\n";
$proxy = '15.236.106.236:3128'; // Replace with your proxy
$result = makeRequest('https://shvip.cam/', true, $proxy);
echo "HTTP Code: " . $result['http_code'] . "\n";
echo "Response Preview:\n" . substr($result['response'], 0, 500) . "...\n\n";

// Test 4: Direct request to the target site
echo "Test 4: Direct Request to Target Site\n";
$result = makeRequest('https://egydead.space/home');
echo "HTTP Code: " . $result['http_code'] . "\n";
echo "Response Preview:\n" . substr($result['response'], 0, 500) . "...\n\n";

// Test 3: Proxied request to a known good site
echo "Test 3: Proxied Request to Known Good Site\n";
$proxy = '15.236.106.236:3128'; // Replace with your proxy
$result = makeRequest('https://egydead.space/home', true, $proxy);
echo "HTTP Code: " . $result['http_code'] . "\n";
echo "Response Preview:\n" . substr($result['response'], 0, 500) . "...\n\n";

?>