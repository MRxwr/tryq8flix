<?php
session_start ();
include_once ("config.php");
include_once("includes/checksouthead.php");
if ( !isset($username) AND !in_array($username,$usernames) )
{
	header ("Location: ../index.php");
}
else
{
$username = $_SESSION["username"];
$title = $_POST["title"];
$imdblink = $_POST["imdblink"];
$description = $_POST["desctiption"];
$date = $_POST["date"];
$time = $_POST["time"];

$sql = "INSERT INTO requests (username, title, imdblink, description, status, time) VALUES ('$username', '$title', '$imdblink', '$description','Processing...', '$time')";
$result = $dbconnect->query($sql);
	
header ("Location: ../request.php?msg=done");
}

?>