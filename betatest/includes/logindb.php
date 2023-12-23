<?php
session_start ();
include_once ("config.php");

$username = $_POST["username"];
$password = $_POST["password"];
$password = sha1($password);



$sql = "SELECT * FROM users WHERE username like '$username' AND password like '$password'";
$result = $dbconnect->query($sql);
$row = $result->fetch_assoc();
$coockiecode = $row["keepalive"];
$coockiecode = explode(',',$coockiecode);
$GenerateNewCC = sha1(md5(rand()));
	if ( sizeof($coockiecode) <= 3 )
	{
		$coockiecodenew = array();
		if ( !isset ($coockiecode[2]) ) { $coockiecodenew[1] = $GenerateNewCC ; } else { $coockiecodenew[0] = $coockiecode[1]; }
		if ( !isset ($coockiecode[1]) ) { $coockiecodenew[0] = $GenerateNewCC ; } else { $coockiecodenew[1] = $coockiecode[2]; }
		if ( !isset ($coockiecode[0]) ) { $coockiecodenew[2] = $GenerateNewCC ; } else { $coockiecodenew[2] = $GenerateNewCC; }
	}

$coockiecode = $coockiecodenew[0] . "," . $coockiecodenew[1] . "," . $coockiecodenew[2] ;

if ( $result->num_rows == 1 )
{
	$sql = "UPDATE users SET keepalive = '$coockiecode' WHERE username like '$username' AND password like '$password'";
	$result = $dbconnect->query($sql);
	$_SESSION["username"] = $username;
	header("Location: ../index.php");
	//setcookie("Q8FLiX", $GenerateNewCC, time() - (86400*30 ), "/");
	setcookie("Q8FLiX", $GenerateNewCC, time() + (86400*30 ), "/");
	exit();
}
else
{
	echo "try again";
	header("Location: ../login.php?error=wrong");
	exit();
}

?>