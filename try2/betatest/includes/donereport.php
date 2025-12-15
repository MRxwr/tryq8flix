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
	include_once ("config.php");
	$username = $_SESSION["username"];
	if ( $username = "admin")
	{
		$reportid = $_GET["reportid"];

		$sql = "UPDATE `reports`
				SET
				`status` = 'Done'
				WHERE
				`id` = '".$reportid."'
				";
		$result = $dbconnect->query($sql);

		$sql = "SELECT r.*, c.title
				FROM `reports` AS r
				JOIN `category` AS c
				ON
				r.catid = c.id
				WHERE
				r.id LIKE '".$reportid."'
				";
		$result = $dbconnect->query($sql);
		$row = $result->fetch_assoc();
		$postid = $row["postid"];
		$userid = $row["userid"];
		$catid = $row["catid"];
		$title = $row["title"];

		$type = "report";
		$titleid = "category.php?id=$catid";
		$status = "unseen";

		$sql = "INSERT INTO `notificationnew`
				(`userid`, `type`, `title`, `titleid`, `status`)
				VALUES
				('".$userid."', '".$type."', '".$title."', '".$titleid."', '".$status."')
				";
		$result = $dbconnect->query($sql);

		header ("Location: ../viewreports.php");
	}
	
}

?>