<?php 

$website2 = "https://web5.topcinema.world";

function getTopCimaUrl($postData, $link) {
    global $website2;
    
    // Build the URL from $website2
    $url = $website2 . "/wp-content/themes/movies2023/Ajaxat/Single/Server.php";
    
    $curl = curl_init();
    
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '', // Handle all supported encodings
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        // You can pass an array for POSTFIELDS (cURL will encode it automatically)
        CURLOPT_POSTFIELDS => $postData,
        CURLOPT_HTTPHEADER => array(
            'Referer: ' . $link,
            'x-requested-with: XMLHttpRequest'
        ),
    ));
    
    $response = curl_exec($curl);
    
    if ($response === false) {
        echo 'Curl error: ' . curl_error($curl);
    }
    
    curl_close($curl);
    
    return $response;
}

// Example usage:
$postData = array(
    'id' => '142569',
    'i'  => '1'
);
$link = "https://web5.topcinema.world/%d9%85%d8%b3%d9%84%d8%b3%d9%84-%d8%b2%d9%85%d9%8a%d9%84%d8%aa%d9%8a-%d9%81%d9%8a-%d8%a7%d9%84%d8%ba%d8%b1%d9%81%d8%a9-%d8%a7%d9%84%d9%85%d8%b2%d8%b9%d8%ac%d8%a9-my-chilling-roommate-%d8%a7%d9%84%d8%ad%d9%84%d9%82%d8%a9-10-%d9%88%d8%a7%d9%84%d8%a7%d8%ae%d9%8a%d8%b1%d8%a9-%d9%85%d8%aa%d8%b1%d8%ac%d9%85%d8%a9/watch/";

echo getTopCimaUrl($postData, $link);


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