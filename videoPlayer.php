<?php 
require("admin/includes/config.php");
require("admin/includes/functions.php");

function extractVideoSource($html) {
    $pattern = '/jwplayer\("vplayer"\)\.setup\({.*?sources:\s*\[{file:"(.*?)",/s';
    if (preg_match($pattern, $html, $matches)) {
        return $matches[1];
    }
    return null;
}

if( isset($_GET["link"]) && !empty($_GET["link"]) ){
    $curl = curl_init();
    curl_setopt_array($curl, array(
    CURLOPT_URL => "{$_GET["link"]}",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => array(
        "referer: {$website3}",
    ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    $_GET["link"] = extractVideoSource($response);
    echo "<video id='video' controls style='width:100%;height:300px;margin-top: 30px;'><source src='{$_GET["link"]}' type='video/mp4'></video>";
}else{
    echo "لا يوجد روابط متاحه للمشاهده حاليا، الرجاء المحاولة لاحقاً";
}
?>