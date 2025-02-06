<?php
if( $_GET["endpoint"] == "login" ){
    if( !isset($_POST["username"]) || empty($_POST["username"]) ){
        echo dataError(array("msg" => "Username is required"));die();
    }
    if( !isset($_POST["password"]) || empty($_POST["password"]) ){
        echo dataError(array("msg" => "Password is required"));die();
    }
    if( $user = selectDBNew("users",[strtolower($_POST["username"]),sha1($_POST["password"])],"`usernameSmall` LIKE ? AND `password` LIKE ?","") ){
        $token = md5(uniqid());
        updateDB("users",array("keepalive" => $token),"`id` = '{$user[0]["id"]}'");
        echo dataOutput(array("keepalive" => $token));die();
    }else{
        echo dataError(array("msg" => "Invalid Username or Password"));die();
    }
}elseif( $_GET["endpoint"] == "register" ){
    if( !isset($_POST["username"]) || empty($_POST["username"]) ){
        echo dataError(array("msg" => "Username is required"));die();
    }
    if( !isset($_POST["password"]) || empty($_POST["password"]) ){
        echo dataError(array("msg" => "Password is required"));die();
    }
    if( !isset($_POST["email"]) || empty($_POST["email"]) ){
        echo dataError(array("msg" => "Email is required"));die();
    }
    if( !isset($_POST["confirmPassword"]) || empty($_POST["confirmPassword"]) ){
        echo dataError(array("msg" => "Confirm Password is required"));die();
    }
    if( $_POST["password"] != $_POST["confirmPassword"] ){
        echo dataError(array("msg" => "Password and Confirm Password do not match"));die();
    }
    if( $user = selectDB("users","`usernameSmall` = '".strtolower($_POST["username"])."'") ){
        echo dataError(array("msg" => "Username already exists"));die();
    }elseif( $user = selectDB("users","`email` = '{$_POST["email"]}'") ){
        echo dataError(array("msg" => "Email already exists"));die();
    }else{
        $data = array(
            "username" => $_POST["username"],
            "usernameSmall" => strtolower($_POST["username"]),
            "password" => sha1($_POST["pass"]),
            "email" => $_POST["email"]
        );
        if( insertDB("users",$data) ){
            echo dataOutput(array("msg" => "User registered successfully"));die();
        }else{
            echo dataError(array("msg" => "Something went wrong, please try again"));die();
        }
    }
}elseif( $_GET["endpoint"] == "logout" ){
    if( empty($token) ){
        echo dataError(array("msg" => "token is required"));die();
    }
    if( $user = selectDB("users","`keepalive` = '{$token}'") ){
        $data = array("keepalive" => "");
        if( updateDB("users",$data,"`keepalive` = '{$token}'") ){
            echo dataOutput(array("msg" => "User logged out successfully"));die();
        }else{
            echo dataError(array("msg" => "Something went wrong, please try again"));die();
        }
    }else{
        echo dataError(array("msg" => "Invalid token"));die();
    }
}elseif( $_GET["endpoint"] == "forget" ){
    if( !isset($_POST["email"]) || empty($_POST["email"]) ){
        echo dataError(array("msg" => "Email is required"));die();
    }
    if( $user = selectDB("users","`email` = '{$_POST["email"]}'") ){
        $newPass = rand("00000000","99999999");
        $newPassEnc = sha1($newPass);
        $data = array("password"=>$newPassEnc);
        if( updateDB("users",$data,"`email` = '{$_POST["email"]}'") ){
            $data = array(
                "site" => "TRYQ8FLiX - ",
                "subject" => "New password",
                "body" => "Use this new password [{$newPass}] to login with your email [{$_POST["email"]}]",
                "to" => $_POST["email"]
            );
            sendMail($data);
            echo dataOutput(array("msg" => "New password sent to your email"));die();
        }else{
            echo dataError(array("msg" => "Something went wrong, please try again"));die();
        }
    }else{
        echo dataError(array("msg" => "Email not found"));die();
    }
}else{
    echo dataError(array("msg" => "404 endpoint Not Found"));die();
}
?>