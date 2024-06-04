<?php

include_once ("config.php");
include_once("checksouthead.php");

if ( !isset ( $_SESSION["username"] ))
{
	header("Location: ../index.php");
}

else
{
	$username = $_SESSION["username"];
	$id = $_GET["noidsub"];
	
	$sql= "SELECT id FROM users WHERE username LIKE '$username'";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_assoc();
	$userid = $row["id"];
	
	$sql= "UPDATE notification SET status = 'Seen' WHERE id LIKE '$id' AND userid LIKE '$userid'";
	$result = $dbconnect->query($sql);
	
	if ( !isset($_GET["ntf"]) )
	{
		header("Location: ../index.php");
	}
	else
	{
		if ( $_GET["ntf"] == "set" )
		{
			header("Location: ../notifications.php");
		}
	}
	
}

?>