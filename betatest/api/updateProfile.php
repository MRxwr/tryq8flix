<?php
require('constants.php');

if( !isset($_POST['email']) OR $_POST['email'] == ""){
	$response['msg']="please enter user email.";
	echo json_encode($response);die();
}elseif( filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === false ){
	$response['msg']="Please type in your email correctly.";
	echo json_encode($response);die();
}elseif( !isset($_POST['pass']) OR $_POST['pass'] == "" ){
	$response['msg']="Please enter your password.";
	echo json_encode($response);die();
}elseif( !preg_match("/^[a-zA-Z0-9 ]*$/",$_POST['pass']) ){
	$response['msg']="Password should only contains numbers and letters.";
	echo json_encode($response);die();
}elseif( !preg_match("/^[a-zA-Z0-9 ]*$/",$_POST['descr']) ){
	$response['msg']="description should only contains letters and numbers";
	echo json_encode($response);die();
}elseif( !isset($_POST['descr']) OR $_POST['descr'] == "" ){
	$response['msg']="Please enter your description.";
	echo json_encode($response);die();
}else{
	if( is_uploaded_file($_FILES['avatar']['tmp_name']) ){
	$directory = "../avatar/";
	$originalfile = $directory . date("d-m-y") . time() . rand(111111,999999) . ".png";
	move_uploaded_file($_FILES["avatar"]["tmp_name"], $originalfile);
	$avatar = "avatar/" . $originalfile;
	}

	$email = $_POST["email"];
	$pass = $_POST["pass"];
	$pass = md5($pass);
	$descr = $_POST["descr"];
	$sql = "UPDATE 
			`users`
			SET 
			`description` = '".$descr."',
			`password` = '".$pass."'";
			
			if ( isset($avatar) ){
				$sql .= ",`avatar` = '".$avatar."'";
			}
			$sql .= "WHERE `email` LIKE '".$email."' ";
	$result = $dbconnect->query($sql);
	
	$sql = "SELECT
			*
			FROM
			`users`
			WHERE
			`email` LIKE '".$email."'";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_assoc();
	if ( $result->num_rows == 1 ){
		$response['ok'] = true;
		$response['status']= $succeed;
		$response['msg']="Update Successful.";
		$response['details']['id'] = $row["id"];
		$response['details']['username'] = $row["username"];
		$response['details']['email'] = $row["email"];
	}else{
		$response['ok'] = false;
		$response['status']= $error;
		$response['msg']="Please enter your info correctly";
	}
}

echo json_encode($response);

?>