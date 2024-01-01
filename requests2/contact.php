<?php
if( empty($_POST["title"]) && empty($_POST["msg"]) ){
    $msg = "Please enter your message details correctly.";
    echo $msg;
}else{
    if( $user = selectDB("users","`keepalive` = '{$_COOKIE["tryq8flix2"]}'") ){
        $data = array(
            "title" => urlencode($_POST["title"]),
            "message" => urlencode($_POST["msg"]),
            "userId" => $user[0]["id"]
        );
        if( insertDB("contact",$data) ){
            $msg = 0;
            echo $msg;
        }
    }else{
        $msg = "Something wrong happened, Please try again.";
        echo $msg;
    }
}
?>