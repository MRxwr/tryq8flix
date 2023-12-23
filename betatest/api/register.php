<?php
require('constants.php');

if( !isset($_POST['user']) OR $_POST['user'] == ""){
	$response['msg']="please enter user username.";
	echo json_encode($response);die();
}elseif( !preg_match("/^[a-zA-Z0-9 ]*$/",$_POST['user']) ){
	$response['msg']="Username should only contains numbers and letters.";
	echo json_encode($response);die();
}elseif( strstr($_POST['user']," ") ){
	$response['msg']="No spaces allowed in username.";
	echo json_encode($response);die();
}elseif( !isset($_POST['pass']) OR $_POST['pass'] == "" ){
	$response['msg']="Please enter your password.";
	echo json_encode($response);die();
}elseif( !preg_match("/^[a-zA-Z0-9 ]*$/",$_POST['pass']) ){
	$response['msg']="Password should only contains numbers and letters.";
	echo json_encode($response);die();
}elseif( !isset($_POST['email']) OR $_POST['email'] == "" ){
	$response['msg']="Please enter your email.";
	echo json_encode($response);die();
}elseif(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === false){
	$response['msg']="Please enter a valid email.";
	echo json_encode($response);die();
}
else
{
	$user = $_POST["user"];
	$testUser = strtolower($user);
	$email = $_POST["email"];
	$testEmail = strtolower($email);
	$pass = $_POST["pass"];
	$pass = md5($pass);
	$sql = "SELECT 
			`username`, `email`
			FROM 
			`users`
			WHERE 
			`username` LIKE '".$user."'
			OR
			`email` LIKE '".$email."'
			";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_assoc();
	if ( $result->num_rows == 0 )
	{
		$sql = "INSERT INTO
				`users`
				(`username`, `password`, `email`)
				VALUES
				('$user', '$pass', '$email')
				";
		if ($dbconnect->query($sql)){
			echo $id = mysqli_insert_id($dbconnect);
		}
		$row = $result->fetch_assoc();
		$sql = "SELECT
				`id`,`username`,`email`
				FROM
				`users`
				WHERE
				`id` LIKE '".$id."'
				";
		$result = $dbconnect->query($sql);
		$row = $result->fetch_assoc();
		
		$response['ok'] = true;
		$response['status']= $succeed;
		$response['msg']="Registration Successful.";
		$response['details']['id'] = $row["id"];
		$response['details']['username'] = $row["username"];
		$response['details']['email'] = $row["email"];
	}elseif(strtolower($row["email"]) == $testEmail){
		$response['ok'] = false;
		$response['status']= $error;
		$response['msg']="A user with this email already exist.";
	}elseif(strtolower($row["username"]) == $testUser){
		$response['ok'] = false;
		$response['status']= $error;
		$response['msg']="A user with this username already exist.";
	}
}

echo json_encode($response);

?>