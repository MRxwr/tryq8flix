<?php
if( isset($_GET["action"]) && !empty($_GET["action"]) ){
    if( empty($token) ){
        echo dataError(array("msg" => "token is required"));die();
    }
    if( $_GET["action"] == "list" ){
        $data = array();
        echo dataOutput($data);die();
    }elseif( $_GET["action"] == "view" ){
        if( isset($_GET["server"]) && !empty($_GET["server"]) ){
            if( $_GET["server"] == 1 ){
                $url = $website3;
                if( isset($_GET["page"]) && !empty($_GET["page"]) ){
                    $url .= "/page/{$_GET["page"]}";
                }
                $data = scrapeWecima("{$url}");
                $data = json_decode($data, true);
                echo dataOutput($data);die();
            }else{
                echo dataError(array("msg" => "Invalid Server"));die();
            }
        }else{
            echo dataError(array("msg" => "server is required"));die();
        }
    }else{
        echo dataError(array("msg" => "404 action Not Found"));die();
    }
}else{
    echo dataError(array("msg" => "404 action Not Found"));die();
}
?>