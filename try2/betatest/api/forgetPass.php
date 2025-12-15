<?php
require('constants.php');

if( isset($_POST['email']) AND !empty($_POST['email']) AND !isset($_POST["verifyCode"]) ){
	$fpass = md5(rand(11111111,99999999));
	$sql = "UPDATE `users`
			SET 
			`forgetpassword` = '$fpass' 
			WHERE 
			`email` LIKE '".$_POST['email']."'
			";
	$result = $dbconnect->query($sql);
	$response['ok'] = true;
	$response['status']= $succeed;
	$response['msg']="An email has been sent to you with verifying code.";
	echo json_encode($response);
	die();
}

if( isset($_POST['email']) AND !empty($_POST['email']) AND isset($_POST["verifyCode"]) AND !empty($_POST["verifyCode"]) ){
	$sql = "SELECT 
			*
			FROM
			`users`
			WHERE 
			`email` LIKE '".$_POST['email']."'
			AND
			`forgetpassword` LIKE '".$_POST["verifyCode"]."' 
			";
	$result = $dbconnect->query($sql);
	if ( $result->num_rows > 0 ){
		$response['ok'] = true;
		$response['status']= $succeed;
		$response['msg']="Veriyfing code is correct, please proceed to change your password...";
		echo json_encode($response);
		die();
	}else{
		$response['msg']="Verifying code is invalid.";
		echo json_encode($response);
		die();
	}
}

if( isset($_POST['email']) AND !empty($_POST['email']) AND isset($_POST["newPass"]) AND !empty($_POST["newPass"]) ){
	if ( !preg_match("/^[a-zA-Z0-9 ]*$/",$_POST['newPass']) ){
		$response['msg']="Password should only contains numbers and letters.";
		echo json_encode($response);
		die();
	}else{
		$sql = "UPDATE `users`
				SET 
				`password` = '".md5($_POST["newPass"])."' 
				WHERE 
				`email` LIKE '".$_POST['email']."'
				";
		$result = $dbconnect->query($sql);
		$response['ok'] = true;
		$response['status']= $succeed;
		$response['msg']="Your password has been updated. please proceed with login.";
		echo json_encode($response);
		die();
	}
}

$response['msg']="Please enter info correctly.";
echo json_encode($response);
die();

?>