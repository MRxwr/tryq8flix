<?php
session_start ();
include_once ("config.php");

$username = $_POST["username"];
$password = $_POST["password"];
$password = sha1($password);



$sql = "SELECT
		*
		FROM
		`adminstration`
		WHERE
		`username` LIKE '$username'
		AND
		`password` LIKE '$password'
		";
$result = $dbconnect->query($sql);
$row = $result->fetch_assoc();
$coockiecode = $row["keepMeAlive"];
$coockiecode = explode(',',$coockiecode);
$GenerateNewCC = md5(rand());
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
	$sql = "UPDATE
			`adminstration`
			SET
			`keepMeAlive` = '$coockiecode'
			WHERE
			`username` LIKE '$username'
			AND
			`password` LIKE '$password'
			";
	$result = $dbconnect->query($sql);
	$_SESSION["TRYQ8FliXAdmin"] = $email;
	header("Location: ../index.php");
	setcookie("TRYQ8FliXAdmin", $GenerateNewCC, time() + (86400*30 ), "/");
	exit();
}
else
{
	echo "try again";
	header("Location: ../login.php?error=wrong");
	exit();
}

?>