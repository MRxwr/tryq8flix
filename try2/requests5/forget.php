<?php
if( !isset($_POST["email"]) || empty($_POST["email"]) ){
    $msg = "Please enter email.";
    echo $msg;
}elseif( $user = selectDB("users","`email` LIKE '{$_POST["email"]}'") ){
	$newPass = rand("00000000","99999999");
    $newPassEnc = sha1($newPass);
    $data = array("password"=>$newPassEnc);
    if( updateDB("users",$data,"`email` = '{$_POST["email"]}'") ){
        $data = array(
            "site" => "TRYQ8FLiX - ",
            "subject" => "New password",
            "body" => "Use this new password [{$newPass}] to login with your username [{$user[0]["username"]}]",
            "to" => $_POST["email"]
        );
        sendMail($data);
        $msg = 0;
        echo $msg;
    }else{
        $msg = "Something wrong happened, Please try again.";
        echo $msg;
    }
}else{
    $msg = "No user with this email. Please try another.";
    echo $msg;
}
?>