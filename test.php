<?php
$cookieJar = tempnam('/tmp', 'cookie');

function makeRequest($url, $postData = null, $cookieJar) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_COOKIEJAR => $cookieJar,
        CURLOPT_COOKIEFILE => $cookieJar,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0',
        CURLOPT_HTTPHEADER => [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Accept-Language: en-US,en;q=0.5',
            'Accept-Encoding: gzip, deflate',  // Removed 'br' to avoid Brotli compression
            'Connection: keep-alive',
            'Upgrade-Insecure-Requests: 1',
            'Sec-Fetch-Dest: document',
            'Sec-Fetch-Mode: navigate',
            'Sec-Fetch-Site: none',
            'Sec-Fetch-User: ?1',
            'Cache-Control: max-age=0',
            "Referer: https://web5.topcinema.world/",
            'X-Requested-With: XMLHttpRequest',
        ],
        CURLOPT_ENCODING => '',  // This tells cURL to automatically handle compression
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

// First, visit the main page to set any necessary cookies
$mainPageUrl = 'https://web.topcinema.cam';
$mainPageResult = makeRequest($mainPageUrl, null, $cookieJar);

// Sleep for a few seconds to mimic human behavior
sleep(rand(3, 7));

// Now make the AJAX request
$ajaxUrl = 'https://web.topcinema.cam/wp-content/themes/movies2023/Ajaxat/Single/Server.php';
$postData = ['id' => '97124', 'i' => '1'];
$result = makeRequest($ajaxUrl, $postData, $cookieJar);

echo "Final URL after redirects: " . $result['info']['url'] . "\n\n";
echo "HTTP Status Code: " . $result['info']['http_code'] . "\n\n";
echo "Response Headers:\n" . $result['header'] . "\n\n";

echo "Response Body:\n";
var_dump($result['body']);

unlink($cookieJar);  // Clean up the temporary cookie file
/*
function makeRequest($url) {97124
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