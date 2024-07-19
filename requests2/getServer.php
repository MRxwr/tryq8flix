<?php 
if( isset($_GET["type"]) && !empty($_GET["type"]) && $_GET["type"] == "getServer" ){
    $ajaxUrl = 'https://web.topcinema.cam/wp-content/themes/movies2023/Ajaxat/Single/Server.php';
    $data = json_decode($_POST["id"], true);
    $link = $data["link"];
    unset($data["link"]);
    $postData = $data;
    return $url = makeRequest($ajaxUrl, $postData, $link);
}else{
    return "";
}
?>