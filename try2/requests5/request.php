<?php
if( empty($_POST["title"]) && empty($_POST["msg"]) && empty($_POST["imdb"]) ){
    $msg = "Please enter your request details correctly.";
    echo $msg;
}else{
    if( $user = selectDB("users","`keepalive` = '{$_COOKIE["tryq8flix2"]}'") ){
        $data = array(
            "title" => urlencode($_POST["title"]),
            "details" => urlencode($_POST["msg"]),
            "link" => urlencode($_POST["imdb"]),
            "userId" => $user[0]["id"]
        );
        if( insertDB("request",$data) ){
            $msg = 0;
            echo $msg;
        }
    }else{
        $msg = "Something wrong happened, Please try again.";
        echo $msg;
    }
}
?>