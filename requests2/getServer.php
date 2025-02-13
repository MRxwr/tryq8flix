<?php 

if( isset($_POST["type"]) && !empty($_POST["type"]) && $_POST["type"] == "get" ){
    echo $ajaxUrl = "{$website2}/wp-content/themes/movies2023/Ajaxat/Single/Server.php";
    $data = json_decode($_POST["data"], true);
    $link = str_replace("web2.topcinema.cam","web5.topcinema.world",$data["link"]);
    unset($data["link"]);
    $postData = $data;
    echo $url = makeRequest($ajaxUrl, $postData, $link);
    var_dump($url);
}else{
    echo "error";
}
?>