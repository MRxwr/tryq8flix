<?php
include_once ("config.php");
include_once ("checksouthead.php");
if ( !isset($username) AND !in_array($username,$usernames) )
{
	header ("Location: ../index.php");
}
else
{
	include_once ("config.php");
	$username = $_GET["username"];
	$title = $_GET["title"];
	$RequestId = $_GET["id"];

	$sql = "UPDATE `requests`
			SET
			`status` = 'Done'
			WHERE
			`id` LIKE '".$RequestId."'
			";
	$result = $dbconnect->query($sql);
	
	$sql = "SELECT `id`
			FROM `users`
			WHERE
			`username` LIKE '".$username."'
			";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_assoc();
	$userid = $row["id"];
	
	$type = "request";
	$title = $_GET["cattitle"];
	$titleid = "search.php?search=" . str_replace(" ","+",$title);
	$status = "unseen";
	
	$sql = "INSERT INTO `notificationnew`
			(`userid`, `type`, `title`, `titleid`, `status`)
			VALUES
			('".$userid."', '".$type."', '".$title."', '".$titleid."', '".$status."')
			";
	$result = $dbconnect->query($sql);

	header ("Location: ../viewrequests.php");
}

?>