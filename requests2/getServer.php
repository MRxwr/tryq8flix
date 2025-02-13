<?php 
function getTopCimaUrl($postData, $link) {
    GLOBAL $website2;

    // Convert the POST data to a URL-encoded string
    $postDataString = http_build_query($postData);

    // Build the URL (using https as observed in your headers)
    $url = "https://{$website2}/wp-content/themes/movies2023/Ajaxat/Single/Server.php";
    echo $url . "\n";

    // Prepare headers to match the browser's request
    $headers = array(
        "Accept: */*",
        "Accept-Encoding: gzip, deflate, br, zstd",
        "Accept-Language: en-US,en;q=0.9,ar;q=0.8",
        // Let cURL handle content-length automatically by commenting it out,
        // or if you need to set it manually, uncomment the next line:
        //"Content-Length: " . strlen($postDataString),
        "Content-Type: application/x-www-form-urlencoded; charset=UTF-8",
        "Cookie: _gid=GA1.2.1165536105.1739483465; _ga=GA1.1.1382703612.1739483465; _ga_6ZDPCTTMZN=GS1.1.1739483464.1.1.1739486025.0.0.0",
        "Origin: https://web5.topcinema.world",
        "Referer: {$link}",
        "Sec-Fetch-Dest: empty",
        "Sec-Fetch-Mode: cors",
        "Sec-Fetch-Site: same-origin",
        "User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1 Edg/133.0.0.0",
        "X-Requested-With: XMLHttpRequest"
    );

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '', // handle all encodings
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $postDataString,
        CURLOPT_HTTPHEADER => $headers,
        // Optional: Enable verbose logging for debugging; remove/comment when done
        CURLOPT_VERBOSE => true,
    ));

    $response = curl_exec($curl);

    if ($response === false) {
        echo 'Curl error: ' . curl_error($curl);
    }

    curl_close($curl);

    // Debug output if needed
    print_r($headers);
    print_r($postData);
    var_dump($response);
    die();

    return $response;
}


function extractLinkTopCima($html) {
    if (preg_match('/<iframe.*?src="(.*?)"/', $html, $matches)) {
        return $matches[1];
    }
    if (preg_match('/https?:\/\/[^\s<>"]+/', $html, $matches)) {
        return $matches[0];
    }
    return "";
}

if( isset($_POST["type"]) && !empty($_POST["type"]) && $_POST["type"] == "get" ){
    $data = json_decode($_POST["data"], true);
    $link = str_replace("web2.topcinema.cam","web5.topcinema.world",$data["link"]);
    unset($data["link"]);
    echo $url = (getTopCimaUrl($data, $link));
}else{
    echo "error";
}
?>