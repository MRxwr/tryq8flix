<?php
require('constants.php');

if( !isset($_POST['user']) OR $_POST['user'] == ""){
	$response['msg']="please enter user username.";
	echo json_encode($response);die();
}elseif( !isset($_POST['pass']) OR $_POST['pass'] == "" ){
	$response['msg']="Please enter your password.";
	echo json_encode($response);die();
}elseif( !preg_match("/^[a-zA-Z0-9 ]*$/",$_POST['pass']) ){
	$response['msg']="Password should only contains numbers and letters.";
	echo json_encode($response);die();
}elseif( !preg_match("/^[a-zA-Z0-9 ]*$/",$_POST['user']) ){
	$response['msg']="Username should only contains numbers and letters.";
	echo json_encode($response);die();
}else{
	$user = $_POST["user"];
	$pass = $_POST["pass"];
	$pass = sha1($pass);
	$sql = "SELECT 
			* 
			FROM 
			`users`
			WHERE 
			`username` LIKE '".$user."' 
			AND 
			`password` LIKE '".$pass."'
			";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_assoc();
	if ( $result->num_rows == 1 ){
		$coockiecode = md5(rand());
		$sql = "UPDATE `users`
				SET 
				`keepalive` = '{$coockiecode}' 
				WHERE 
				`id` = {$row["id"]}
				";
		$result = $dbconnect->query($sql);
		$response['ok'] = true;
		$response['status']= $succeed;
		$response['msg']="Login Successful.";
		$response['details']['token'] = $coockiecode;
	}else{
		$response['ok'] = false;
		$response['status']= $error;
		$response['msg']="Please enter your info correctly";
	}
}

echo json_encode($response);
?>