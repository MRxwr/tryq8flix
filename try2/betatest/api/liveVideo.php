<?php
require ('../includes/config.php');
include ("../includes/checksouthead.php");
date_default_timezone_set('Asia/Kuwait');

if ( isset($_POST["currentTime"]) ){
	$sql = "UPDATE `watchedvideos`
			SET 
			`time`= '".$_POST["currentTime"]."'
			WHERE
			`userId` LIKE '".$_POST["userId"]."'
			AND
			`videoId` LIKE '".$_POST["postId"]."'
			";
	$result = $dbconnect->query($sql);
	echo $_POST["currentTime"];
}

if ( isset($_POST["playFrom"]) ){
	$sql = "SELECT `time`
			FROM `watchedvideos`
			WHERE
			`userId` LIKE '".$_POST["userId"]."'
			AND
			`videoId` LIKE '".$_POST["postId"]."'
			";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_assoc();
	echo $row["time"];
}


?>