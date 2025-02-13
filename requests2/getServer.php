<?php 
var_dump($_POST);
if( isset($_POST["type"]) && !empty($_POST["type"]) && $_POST["type"] == "getServer" ){
    $ajaxUrl = 'https://web.topcinema.cam/wp-content/themes/movies2023/Ajaxat/Single/Server.php';
    $data = json_decode($_POST["data"], true);
    $link = $data["link"];
    unset($data["link"]);
    $postData = $data;
    echo $url = makeRequest($ajaxUrl, $postData, $link);
}else{
    echo "";
}
?>