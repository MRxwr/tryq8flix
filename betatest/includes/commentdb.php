<?php

include_once ("config.php");

$id = $_POST["catid"];
$postid = $_POST["postid"];
$username = $_POST["username"];
$date = $_POST["date"];
$time = $_POST["time"];
$comment = $_POST["comment"];
$useravatar =  $_POST["useravatar"];
	
	$sql = "SELECT commentid FROM comments ORDER BY commentid DESC LIMIT 1";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_assoc();
	$commentid = $row["commentid"]+1;

$comment = str_replace("'","?singlequtation?",$comment);

$result = $dbconnect->query("INSERT INTO comments (commentid, postid, id, comment,reply, username, useravatar, date, time ) VALUES ('$commentid', '$postid', '$id', '$comment', '0', '$username', '$useravatar', '$date', '$time')");

header ("Location: ../watch.php?catid=$id&postid=$postid");

?>