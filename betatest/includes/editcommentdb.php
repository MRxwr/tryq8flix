<?php
session_start();
if ( !isset ($_SESSION["username"]) )
{
	header ("Location: ../category.php?msg=editpost");
}
else
{
	include_once ("config.php");
	
	$commentid = $_POST["commentid"];
	$postid = $_POST["postid"];
	$catid = $_POST["catid"];
	$comment = $_POST["comment"];
	$comment = str_replace("'","?singlequtation?",$comment);
	
	$sql = "SELECT * FROM comments WHERE commentid='$commentid'";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_assoc();

	if ( $comment != $row["comment"] )
	{
		$sql = "UPDATE comments SET comment='$comment' WHERE commentid='$commentid'";
		$results = $dbconnect->query($sql);
	}
	
	header ("Location: ../watch.php?id=$catid&postid=$postid");
}

?>