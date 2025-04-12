<?php
if( isset($_GET["endpoint"]) && $_GET["endpoint"] == "submitRoom" ){
    if( !isset($_POST["code"]) || empty($_POST["code"]) ){
        echo dataError(array("msg" => "Room code is required"));die();
    }
    if( !isset($_POST["type"]) || empty($_POST["type"]) ){
        echo dataError(array("msg" => "Room type is required"));die();
    }
    if( !isset($_POST["roomData"]) || empty($_POST["roomData"]) ){
        echo dataError(array("msg" => "Room data is required"));die();
    }
    if( $user = selectDB("users","`keepalive` = '{$token}'") ){
        $user = $user[0];
        if( insertDB("qas_rooms",$_POST) ){
            echo dataOutput(array("msg" => "Room saved successfully"));die();
        }else{
            echo dataError(array("msg" => "Could not save room"));die();
        }
    }else{
        echo dataError(array("msg" => "Invalid token"));die();
    }
}else{
    echo dataError(array("msg" => "Invalid endpoint"));die();
}
?>