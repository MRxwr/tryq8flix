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
	$pass = md5($pass);
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
		$coockiecode = $row["keepalive"];
		$coockiecode = explode(',',$coockiecode);
		$GenerateNewCC = md5(rand());
		if ( sizeof($coockiecode) <= 3 ){
			$coockiecodenew = array();
			if (!isset($coockiecode[2])){
				$coockiecodenew[1] = $GenerateNewCC ;
				}else{
					$coockiecodenew[0] = $coockiecode[1];
					}
			if (!isset ($coockiecode[1])){
				$coockiecodenew[0] = $GenerateNewCC ;
				}else{
					$coockiecodenew[1] = $coockiecode[2];
					}
			if (!isset ($coockiecode[0])){
				$coockiecodenew[2] = $GenerateNewCC ;
				}else{
					$coockiecodenew[2] = $GenerateNewCC;
					}
		}
		$coockiecode = $coockiecodenew[0] . "," . $coockiecodenew[1] . "," . $coockiecodenew[2];
		$sql = "UPDATE `users`
				SET 
				`keepalive` = '$coockiecode' 
				WHERE 
				`username` LIKE '".$user."'
				AND
				`password` LIKE '".$pass."'
				";
		$result = $dbconnect->query($sql);
		$response['ok'] = true;
		$response['status']= $succeed;
		$response['msg']="Login Successful.";
		$response['details']['id'] = $row["id"];
		$response['details']['username'] = $row["username"];
		$response['details']['newCookies'] = $GenerateNewCC;
	}else{
		$response['ok'] = false;
		$response['status']= $error;
		$response['msg']="Please enter your info correctly";
	}
}

echo json_encode($response);

?>