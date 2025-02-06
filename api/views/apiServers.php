<?php
if( isset($_GET["action"]) && !empty($_GET["action"]) ){
    if( empty($token) ){
        echo dataError(array("msg" => "token is required"));die();
    }
    if( $_GET["action"] == "list" ){
        if( !isset($_GET["href"]) || empty($_GET["href"]) ){
            echo dataError(array("msg" => "href is required"));die();
        }
        $url = $_GET["href"];
        $data = scrapeWecimaServers($url);
        echo dataOutput($data);die();
    }elseif( $_GET["action"] == "view" ){
        
    }else{
        echo dataError(array("msg" => "404 action Not Found"));die();
    }
}else{
    echo dataError(array("msg" => "404 action Not Found"));die();
}
?>