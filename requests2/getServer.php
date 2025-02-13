<?php 
function getTopCimaUrl($postData, $link){
    GLOBAL $website2;
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
    CURLOPT_HTTPHEADER => array(
        "Referer: {$link}",
        'x-requested-with: XMLHttpRequest',
        'content-type: application/x-www-form-urlencoded',
        //'content-length: 13',
    ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
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
    echo $url = extractLinkTopCima(getTopCimaUrl($data, $link));
}else{
    echo "error";
}
?>