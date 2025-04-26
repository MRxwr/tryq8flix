<?php 
if( isset($_GET["type"]) && !empty($_GET["type"]) && $_GET["type"] == "getServer" ){
    $ajaxUrl = "{$website3}/wp-content/themes/movies2023/Ajaxat/Single/Server.php";
    $data = json_decode($_POST["data"], true);
    $link = $data["link"];
    var_dump($_POST);die();
    unset($data["link"]);
    $postData = $data;
    echo $url = makeRequest($ajaxUrl, $postData, $link);
}else{
    echo "";
}
?>