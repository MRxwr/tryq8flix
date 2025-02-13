<?php 
function getTopCimaUrl($postData, $link){
    GLOBAL $website2;
    $headers = array(
        'Accept: */*',
        'Accept-Language: en-US,en;q=0.5',
        'Accept-Encoding: gzip, deflate',
        'Connection: keep-alive',
        'Sec-Fetch-Dest: empty',
        'Sec-Fetch-Mode: cors',
        'Sec-Fetch-Site: same-origin',
        "Referer: {$link}",
        'x-requested-with: XMLHttpRequest',
        'content-type: application/x-www-form-urlencoded',
        'content-length: 13',
        'user-agent: Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1 Edg/133.0.0.0',
        'origin: https://web5.topcinema.world'
    );
    $curl = curl_init();
    curl_setopt_array($curl, array(
    CURLOPT_URL => "{$website2}/wp-content/themes/movies2023/Ajaxat/Single/Server.php",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => $postData,
    CURLOPT_HTTPHEADER => $headers,
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    print_r($response)
    print_r($response);die();
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