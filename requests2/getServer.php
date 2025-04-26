<?php 
if( isset($_GET["type"]) && !empty($_GET["type"]) && $_GET["type"] == "getServer" ){
    $ajaxUrl = 'https://web5.topcinema.world/wp-content/themes/movies2023/Ajaxat/Single/Server.php';
    $data = json_decode($_POST["data"], true);
    $link = $data["link"];
    unset($data["link"]);
    $postData = $data;
    echo $url = makeRequest($ajaxUrl, $postData, $link);
}else{
    echo "";
}
?>