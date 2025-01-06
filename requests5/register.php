<?php
if( !isset($_POST["username"]) || empty($_POST["username"]) || validateInput($_POST["username"]) === false ){
    $msg = "Please enter username.";
    echo $msg;
}elseif( !isset($_POST["email"]) || empty($_POST["email"]) || !filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) ){
    $msg = "Please enter email.";
    echo $msg;
}elseif( !isset($_POST["pass"]) || empty($_POST["pass"]) || validateInput($_POST["pass"]) === false){
    $msg = "Please enter password.";
    echo $msg;
}elseif( !isset($_POST["rePass"]) || empty($_POST["rePass"]) || validateInput($_POST["rePass"]) === false){
    $msg = "Please enter confrim password.";
    echo $msg;
}elseif( $_POST["pass"] != $_POST["rePass"] ){
    $msg = "Passwords is not a match, Please enter them correctly";
    echo $msg;
}else{
    if( $user = selectDB("users","`username` = '{$_POST["username"]}'") ){
        $msg = "The username [{$_POST["username"]}] is taken, please try another.";
        echo $msg;
    }elseif( $user = selectDB("users","`email` = '{$_POST["email"]}'") ){
        $msg = "A user with [{$_POST["email"]}] already registerd. Please try to login.";
        echo $msg;
    }else{
        $data = array(
            "username" => $_POST["username"],
            "usernameSmall" => strtolower($_POST["username"]),
            "password" => sha1($_POST["pass"]),
            "email" => $_POST["email"]
        );
        if( insertDB("users",$data) ){
            $msg = 0;
            echo $msg;
        }else{
            $msg = "Something wrong happened, Please try again.";
            echo $msg;
        }
    }
}
?>