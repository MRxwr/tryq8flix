<?php
$cookieJar = tempnam('/tmp', 'cookie');

function makeRequest($url, $postData = null, $cookieJar) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => true, // This line is added to get the headers
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_COOKIEJAR => $cookieJar,
        CURLOPT_COOKIEFILE => $cookieJar,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0',
        CURLOPT_HTTPHEADER => [
            'Accept: */*',
            'Accept-Encoding: gzip, deflate, br',
            'Accept-Language: en-US,en;q=0.5',
            'Connection: keep-alive',
            'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
            'Host: web5.topcinema.world',
            'Origin: https://web5.topcinema.world',
            'Referer: https://web5.topcinema.world/%d9%85%d8%b3%d9%84%d8%b3%d9%84-glee-%d8%a7%d9%84%d9%85%d9%88%d8%b3%d9%85-%d8%a7%d9%84%d8%ab%d8%a7%d9%86%d9%8a-%d8%a7%d9%84%d8%ad%d9%84%d9%82%d8%a9-22-%d9%88%d8%a7%d9%84%d8%a7%d8%ae%d9%8a%d8%b1%d8%a9-%d9%85%d8%aa%d8%b1%d8%ac%d9%85%d8%a9/watch/',
            'Sec-Fetch-Dest: empty',
            'Sec-Fetch-Mode: cors',
            'Sec-Fetch-Site: same-origin',
            'X-Requested-With: XMLHttpRequest',
        ],
    ]);

    if ($postData) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    }

    $response = curl_exec($ch);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $header = substr($response, 0, $headerSize);
    $body = substr($response, $headerSize);
    $info = curl_getinfo($ch);
    curl_close($ch);

    return ['header' => $header, 'body' => $body, 'info' => $info];
}

function decodeResponse($body, $contentEncoding) {
    if (strpos($contentEncoding, 'gzip') !== false) {
        return gzdecode($body);
    } elseif (strpos($contentEncoding, 'deflate') !== false) {
        return gzinflate($body);
    } elseif (strpos($contentEncoding, 'br') !== false && function_exists('brotli_uncompress')) {
        return brotli_uncompress($body);
    }
    return $body;
}

$ajaxUrl = 'https://web5.topcinema.world/wp-content/themes/movies2023/Ajaxat/Single/Server.php';
$postData = ['id' => '97124', 'i' => '1'];
$result = makeRequest($ajaxUrl, $postData, $cookieJar);

echo "HTTP Status Code: " . $result['info']['http_code'] . "\n\n";
echo "Response Headers:\n" . $result['header'] . "\n\n";

$contentEncoding = '';
if (preg_match('/Content-Encoding: (.+)/', $result['header'], $matches)) {
    $contentEncoding = $matches[1];
}

$decodedBody = decodeResponse($result['body'], $contentEncoding);

echo "Decoded Response Body:\n";
var_dump($decodedBody);

echo "\nRaw Response Body:\n";
var_dump($result['body']);

unlink($cookieJar);  // C
/*
function makeRequest($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    //curl_setopt($ch, CURLOPT_PROXY, $proxy);
    //curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    // Set headers to mimic the browser request
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Host: shvip.cam',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0',
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/png,image/svg+xml,;q=0.8',
        'Accept-Language: en-US,en;q=0.5',
        'Accept-Encoding: gzip, deflate, br, zstd',
        'Referer: https://google.com',
        'Connection: keep-alive',
        'Upgrade-Insecure-Requests: 1',
        'Sec-Fetch-Dest: document',
        'Sec-Fetch-Mode: navigate',
        'Sec-Fetch-Site: same-origin',
        'Sec-Fetch-User: ?1',
        'Sec-GPC: 1',
        'Priority: u=0, i'
    ]);

    // Set cookies
    //curl_setopt($ch, CURLOPT_COOKIE, 'cf_clearance=3BwF3j7yPM8f3OT5hauw8Bh8nwAL_mK0YD0zYcl7_kg-1721167758-1.0.1.1-eHiuVVC73Y8p4TKxzMxdJDS4EJmiVtwQWwRLGaG4I9Z5_EKo9wQWbVRwmognJyVC78lAPa_unbSaMGmZ_wHl9g; XSRF-TOKEN=eyJpdiI6IjVqdUV0ZGt1Y0U0cURHeUcwNlNFWFE9PSIsInZhbHVlIjoiUXQ2bUdKNUJ0M2o5TDU5VGVsclMzOVBPQW4xdmRYZEZPSzBsSVp3WWZtdmtBU1I3ZjdNMUgrTDhoTTNqS2RmdzhId3oyNGY3cWtDWkNvcXdXZXc5d3hjN0QzMFEySkhaOHJuRExZaFQySXoxY2JMNGlRTW1tUTluMmRhY0x2dXoiLCJtYWMiOiIzYThjMmUwZDUyOTNkMDM4Mzk5YjljOTZlNDQ2NDE5YmRmNzAyNzUwM2E5Mzc0ZDZlMWNkMzhiMmFlOTNjZjJjIiwidGFnIjoiIn0%3D; laravel_session=eyJpdiI6Im4wRXU3ZDl6akVYcVQ3MVVobGVSeEE9PSIsInZhbHVlIjoiMUJsQ2hwMW1hdUY1YjBGNFVGanNQd2Jma3R1aFF5eG5uM05qV05LMG1jNVlzODVlS3p6Q09pakwrZEtjNXM3L3IwTHYra0s2eWlOWUpnOGtFdFMwRU5aVjVzTGtjR2NUZmFNWjA2TnVJVnk0WGhEdUl1RkJ5MDJBYWpVaDltdCsiLCJtYWMiOiI4ZDkzNGMzYWUzZTc1MTcyNGJiYzQ0MWE4ODFlZjIwNTI5MDUwOGY0M2Q5YWUwMTBhMzM0MjAzZGM2ZDIzZmM1IiwidGFnIjoiIn0%3D');

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

$url = 'https://shvip.cam/';
//$proxy = '15.236.106.236:3128';  // Your proxy

$result = file_get_contents($url);

echo "HTTP Code: " . $result . "\n";
if ($result['error']) {
    echo "Error: " . $result . "\n";
} else {
    echo "Response Preview:\n" . $result . "...\n";
}
*/
?>