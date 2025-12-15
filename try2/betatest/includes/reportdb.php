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
$catid = $_POST["catid"];
$postid = $_POST["postid"];
$description = $_POST["desctiption"];
$date = $_POST["date"];
$time = $_POST["time"];
$issue = $_POST["issue"];
	
$sql= "SELECT id FROM users WHERE username = '$username'";
$result = $dbconnect->query($sql);
$row = $result->fetch_assoc();
$userid = $row["id"];

$sql = "INSERT INTO reports (userid, catid, postid, issue, description, time, status) VALUES ('$userid', '$catid', '$postid', '$issue','$description', '$time', 'Processing...')";
$result = $dbconnect->query($sql);
	
header ("Location: ../report.php?msg=done&catid=$catid&postid=$postid");
}

?>