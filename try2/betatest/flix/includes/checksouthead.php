<?php
if ( isset ( $_COOKIE["Q8FLiX"] ) )
{
	session_start ();
	include_once ("config.php");
	$usernames = array('admin','MRNSR90');
	$svdva = $_COOKIE["Q8FLiX"];
	$sql = "SELECT *
			FROM `users`
			WHERE
			`keepalive` LIKE '%".$svdva."%'
			";
	$result = $dbconnect->query($sql);
	if ( $result->num_rows == 1 ){
		$row = $result->fetch_assoc();
		$username = $row["username"];
		$userID = $row["id"];
		$uptoboxToken = $row["uptoboxToken"];
		$_SESSION['username'] = $username;	
	}
	else
	{
		goto jump;
	}
}
elseif ( !isset ( $_COOKIE["Q8FLiX"] ) )
{
	jump:
	header("Location: ../logout.php");
}

/*$allowedIds = array('1','2','8');
if ( !in_array($userID,$allowedIds) ){
	header("Location: maintenance.php");
}*/