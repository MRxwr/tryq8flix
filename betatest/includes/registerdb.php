<?php
session_start ();
if ( !isset ($_SESSION["username"]) )
{
	include_once ("config.php");
	
	$username = $_POST["username"];

	if ( strstr($username," ") )
	{
		header("Location: ../register.php?error=6");
		exit();	
	}
	if ( strlen($username) < 3 )
	{
		header("Location: ../register.php?error=7");
		exit();
	}
	if ( !preg_match("/^[a-zA-Z0-9 ]*$/",$username) )
	{
		header("Location: ../register.php?error=9");
		exit();
	}

	$password = $_POST["password"];
	$password1 = $_POST["password1"];
	$passwordlen = strlen($password);
	$email = $_POST["email"];
	$email1 = $_POST["email1"];
	$avatar = "";
	$description = "";
	
	if ( $password != $password1 )
	{
		header("Location: ../register.php?error=1");
		exit();
	}
	if ( !preg_match("/^[a-zA-Z0-9 ]*$/",$password) )
	{
		header("Location: ../register.php?error=10");
		exit();
	}
	if ( $passwordlen < 7 )
	{
		header("Location: ../register.php?error=8");
		exit();
	}
	elseif ( $email != $email1 )
	{
		header("Location: ../register.php?error=2");
		exit();
	}
	elseif ( filter_var($email, FILTER_VALIDATE_EMAIL) === false )
	{
		header("Location: ../register.php?error=5");
		exit();
	}
	
	$sql = "SELECT username FROM users WHERE username like '$username'";
	$result = $dbconnect->query($sql);
	if ( $result->num_rows > 0 )
	{
	header("Location: ../register.php?error=3");
	exit();
	}
	
	$sql = "SELECT email FROM users WHERE email like '$email'";
	$result = $dbconnect->query($sql);
	if ( $result->num_rows > 0 )
	{
	header("Location: ../register.php?error=4");
	exit();
	}
	
	$sql = "SELECT id FROM users ORDER BY id DESC LIMIT 1";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_assoc();
	$id = $row["id"]+1;
	
	$password = md5 ($password);
	
	$sql = "INSERT INTO users (id, username, password, email, avatar, description) VALUES ('$id', '$username', '$password', '$email', '$avatar', '$description')";
	if ( $dbconnect->query($sql) )
	{
	header("Location: ../login.php?msg=klasdfjs");
	}
	else
	{
		echo "Error: " . $sql . "<br>" . $dbconnect->error;
	}
}
else
{
	header("Location: ../index.php?error=5");
	exit();
}
?>