<?php

function makeRequest($url, $proxy) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_PROXY, $proxy);
    curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    // Set headers to mimic the browser request
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Host: shvip.cam',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0',
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/png,image/svg+xml,*/*;q=0.8',
        'Accept-Language: en-US,en;q=0.5',
        'Accept-Encoding: gzip, deflate, br, zstd',
        'Referer: https://shvip.cam/watch/%D9%85%D8%B3%D9%84%D8%B3%D9%84-my-adventures-with-superman-%D8%A7%D9%84%D9%85%D9%88%D8%B3%D9%85-%D8%A7%D9%84%D8%A7%D9%88%D9%84-%D8%A7%D9%84%D8%AD%D9%84%D9%82%D8%A9-1-%D9%85%D8%AA%D8%B1%D8%AC%D9%85%D8%A9',
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
    curl_setopt($ch, CURLOPT_COOKIE, 'cf_clearance=3BwF3j7yPM8f3OT5hauw8Bh8nwAL_mK0YD0zYcl7_kg-1721167758-1.0.1.1-eHiuVVC73Y8p4TKxzMxdJDS4EJmiVtwQWwRLGaG4I9Z5_EKo9wQWbVRwmognJyVC78lAPa_unbSaMGmZ_wHl9g; XSRF-TOKEN=eyJpdiI6IjVqdUV0ZGt1Y0U0cURHeUcwNlNFWFE9PSIsInZhbHVlIjoiUXQ2bUdKNUJ0M2o5TDU5VGVsclMzOVBPQW4xdmRYZEZPSzBsSVp3WWZtdmtBU1I3ZjdNMUgrTDhoTTNqS2RmdzhId3oyNGY3cWtDWkNvcXdXZXc5d3hjN0QzMFEySkhaOHJuRExZaFQySXoxY2JMNGlRTW1tUTluMmRhY0x2dXoiLCJtYWMiOiIzYThjMmUwZDUyOTNkMDM4Mzk5YjljOTZlNDQ2NDE5YmRmNzAyNzUwM2E5Mzc0ZDZlMWNkMzhiMmFlOTNjZjJjIiwidGFnIjoiIn0%3D; laravel_session=eyJpdiI6Im4wRXU3ZDl6akVYcVQ3MVVobGVSeEE9PSIsInZhbHVlIjoiMUJsQ2hwMW1hdUY1YjBGNFVGanNQd2Jma3R1aFF5eG5uM05qV05LMG1jNVlzODVlS3p6Q09pakwrZEtjNXM3L3IwTHYra0s2eWlOWUpnOGtFdFMwRU5aVjVzTGtjR2NUZmFNWjA2TnVJVnk0WGhEdUl1RkJ5MDJBYWpVaDltdCsiLCJtYWMiOiI4ZDkzNGMzYWUzZTc1MTcyNGJiYzQ0MWE4ODFlZjIwNTI5MDUwOGY0M2Q5YWUwMTBhMzM0MjAzZGM2ZDIzZmM1IiwidGFnIjoiIn0%3D');

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
$proxy = '45.8.21.156:80';  // Your proxy

$result = makeRequest($url, $proxy);

echo "HTTP Code: " . $result['http_code'] . "\n";
if ($result['error']) {
    echo "Error: " . $result['error'] . "\n";
} else {
    echo "Response Preview:\n" . $result['response'] . "...\n";
}

?>