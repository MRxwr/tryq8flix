<?php
if( !isset($_POST["pass"]) || empty($_POST["pass"]) ){
    $msg = "Please enter password.";
    echo $msg;
}elseif( !isset($_POST["rePass"]) || empty($_POST["rePass"]) ){
    $msg = "Please enter confrim password.";
    echo $msg;
}elseif( $_POST["pass"] != $_POST["rePass"] ){
    $msg = "Passwords is not a match, Please enter them correctly";
    echo $msg;
}elseif( !isset($_POST["token"]) || empty($_POST["token"]) ){
    $msg = "Please enter uptobox token.";
    echo $msg;
}else{
    if( isset($_FILES['logo']['tmp_name']) && is_uploaded_file($_FILES['logo']['tmp_name']) ){
		$directory = "../logos/";
		$originalfile = $directory . date("d-m-y") . time() .  rand(111111,999999) . ".png";
		move_uploaded_file($_FILES["logo"]["tmp_name"], $originalfile);
		$filenewname = str_replace("../logos/",'',$originalfile);
	}else{
        $filenewname = "";
    }
    $data = array(
        "avatar" => $filenewname,
        "password" => sha1($_POST["pass"]),
        "uptoboxToken" => urlencode($_POST["token"])
    );
    if( $user = checkLogin() ){
        if( updateDB("users",$data,"`id` = '{$user["id"]}'") ){
            $msg = 0;
            echo $msg;
        }else{
            $msg = "Something wrong happened, Please try again.";
            echo $msg;
        }
    }else{
        $msg = "Please login to continue editing your profile.";
        echo $msg;
    }
    
}
?>