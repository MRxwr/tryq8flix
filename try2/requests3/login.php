<?php
if( !isset($_POST["username"]) || empty($_POST["username"]) ){
    $msg = "Please enter username.";
    echo $msg;
}elseif( !isset($_POST["pass"]) || empty($_POST["pass"]) ){
    $msg = "Please enter password.";
    echo $msg;
}else{
    $password = sha1($_POST["pass"]);
    if( $user = selectDB("users","`usernameSmall` = '".strtolower($_POST["username"])."' AND `password` != '{$password}'") ){
        $msg = "Wrong password, Please check again.";
        echo $msg;
    }else{
        $cookie = md5($_POST["username"]."+".$_POST["pass"]);
        setcookie( "tryq8flix2", $cookie, time() + (86400 * 30) ,"/");
        $data = array("keepalive"=>$cookie);
        if( updateDB("users",$data,"`usernameSmall` = '".strtolower($_POST["username"])."' AND `password` = '{$password}'") ){
            $msg = 0;
            echo $msg;
        }else{
            $msg = "Something wrong happened, Please try again.";
            echo $msg;
        }
    }
}
?>