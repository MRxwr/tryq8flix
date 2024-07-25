<?php

function scrapeWecima($html) {
    $dom = str_get_html($html);
    if ($dom) {
        $data = [
            'movies' => []
        ];
        foreach ($dom->find('.Grid--WecimaPosts .GridItem') as $item) {
            $thumbDiv = $item->find('.Thumb--GridItem', 0);
            $link = $thumbDiv->find('a', 0);
            $bgSpan = $thumbDiv->find('.BG--GridItem', 0);
            $titleStrong = $thumbDiv->find('strong', 0);

            // Extract image URL from data-lazy-style attribute
            $imageUrl = '';
            if ($bgSpan) {
                preg_match('/url\((.*?)\)/', $bgSpan->getAttribute('data-lazy-style'), $matches);
                $imageUrl = isset($matches[1]) ? $matches[1] : '';
            }

            // Extract year from the title
            $year = '';
            $title = '';
            if ($titleStrong) {
                $title = $titleStrong->plaintext;
                preg_match('/\((\d{4})\)/', $title, $matches);
                $year = isset($matches[1]) ? $matches[1] : '';
                $title = trim(preg_replace('/\(\d{4}\)/', '', $title));
            }

            $jsonData = [
                'href' => $link ? $link->href : '',
                'image' => trim($imageUrl),
                'title' => $title,
                'year' => $year,
            ];
            $data['movies'][] = $jsonData;
        }
        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        return 'Error: Invalid DOM object.';
    }
}

// Usage
$html = file_get_contents('https://wecima.show'); // Replace with the actual URL
$movies = scrapeWecima($html);
echo $movies;

function makeRequest($url, $postData = null, $referer = null) {
    $ch = curl_init();
    $headers = [
        'Accept: */*',
        'Accept-Language: en-US,en;q=0.5',
        'Accept-Encoding: gzip, deflate',
        'X-Requested-With: XMLHttpRequest',
        'Connection: keep-alive',
        'Sec-Fetch-Dest: empty',
        'Sec-Fetch-Mode: cors',
        'Sec-Fetch-Site: same-origin',
    ];
    if ($referer) {
        $headers[] = 'Referer: ' . $referer;
    }
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0',
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_ENCODING => '',
    ]);
    if ($postData) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    }
    $response = curl_exec($ch);
    curl_close($ch);
    $link = extractLink($response);
    return $link;
}

function extractLink($html) {
    // Try to extract src from iframe
    if (preg_match('/<iframe.*?src="(.*?)"/', $html, $matches)) {
        return $matches[1];
    }
    // If no iframe, try to find any URL in the response
    if (preg_match('/https?:\/\/[^\s<>"]+/', $html, $matches)) {
        return $matches[0];
    }
    // If no URL found, return the entire response
    return "";
}
/*
$mainPageUrl = 'https://web.topcinema.cam/%d9%85%d8%b3%d9%84%d8%b3%d9%84-glee-%d8%a7%d9%84%d9%85%d9%88%d8%b3%d9%85-%d8%a7%d9%84%d8%ab%d8%a7%d9%86%d9%8a-%d8%a7%d9%84%d8%ad%d9%84%d9%82%d8%a9-22-%d9%88%d8%a7%d9%84%d8%a7%d8%ae%d9%8a%d8%b1%d8%a9-%d9%85%d8%aa%d8%b1%d8%ac%d9%85%d8%a9/watch/';
//$mainPageResult = makeRequest($mainPageUrl, null);

$ajaxUrl = 'https://web.topcinema.cam/wp-content/themes/movies2023/Ajaxat/Single/Server.php';
$postData = [
    'id' => '97124',
    'i' => '1',
];

echo $result = makeRequest($ajaxUrl, $postData, $mainPageUrl);

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