<?php
session_start();
include_once ("config.php");
include_once("includes/checksouthead.php");
if ( !isset($username) AND !in_array($username,$usernames) )
{
	header ("Location: ../category.php?msg=editpost");
}
else
{
	
	$id = $_POST["id"];
	
	$sql = "SELECT * FROM posts WHERE id='$id'";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_assoc();
	
	$catid = $_POST["catid"];
	$postdate = $_POST["postdate"];
	$title = $_POST["title"];
	$category = $_POST["category"];
	$poster = $_POST["poster"];
	
	$uptobox = $_POST["uptobox"];
	$youtube = $_POST["youtube"];
	$mycima = $_POST["mycima"];
	
	if( is_uploaded_file($_FILES['subtitle']['tmp_name']) )
	{
		//$directory = "../subs/";
		//$originalfile = $directory . round(microtime(true)). ".vtt";
		//move_uploaded_file($_FILES["subtitle"]["tmp_name"], $originalfile);
		
		header('Content-Type:text/plain; charset=utf-8');
		//uploading the file
		$directory = "../subs/";
		$originalfile = $directory . round(microtime(true));
		move_uploaded_file($_FILES["subtitle"]["tmp_name"], $originalfile);
		$fileoldname = round(microtime(true));
		rename($originalfile,$fileoldname);

		//converting srt to vtt
		$content = file_get_contents($fileoldname);
		$content = str_replace(",",".",$content);
		$content = "WEBVTT \n\n" . $content;
		//$getfileencoding = mb_detect_encoding($fileoldname);
		//$content = mb_convert_encoding($content,"utf-8"); // iconv($getfileencoding, "utf-8//TRANSLIT//IGNORE", $content);
		file_put_contents($fileoldname, $content);

		//saving the file into vtt extension
		$filenewname = $directory . date("d-m-y") . time() .  round(microtime(true)). ".vtt";
		rename($fileoldname,$filenewname);	
	}
	else
	{
		$filenewname = $row["subtitle"];
	}
	
	$videolink = $_POST["videolink"];
	$download = $_POST["download"];

	if ( $title != $row["title"] )
	{
		$sql = "UPDATE posts SET title='$title' WHERE id='$id'";
		$results = $dbconnect->query($sql);
	}
	if ( $postdate != $row["postdate"] )
	{
		$sql = "UPDATE posts SET postdate='$postdate' WHERE id='$id'";
		$results = $dbconnect->query($sql);
	}
	if ( $poster != $row["poster"] )
	{
		$sql = "UPDATE posts SET poster='$poster' WHERE id='$id'";
		$results = $dbconnect->query($sql);
	}
	if ( $videolink != $row["videolink"] )
	{
		$sql = "UPDATE posts SET videolink='$videolink' WHERE id='$id'";
		$results = $dbconnect->query($sql);
	}
	if ( $category != $row["category"] )
	{
		$sql = "UPDATE posts SET category='$category' WHERE id='$id'";
		$results = $dbconnect->query($sql);
	}
	if ( $download != $row["download"] )
	{
		$sql = "UPDATE posts SET download='$download' WHERE id='$id'";
		$results = $dbconnect->query($sql);
	}
	if ( $filenewname != $row["subtitle"] )
	{
		$sql = "UPDATE posts SET subtitle='$filenewname' WHERE id='$id'";
		$results = $dbconnect->query($sql);
	}
	if ( $catid != $row["catid"] )
	{
		$sql = "UPDATE posts SET catid='$catid' WHERE id='$id'";
		$results = $dbconnect->query($sql);
	}
	
	// ** updating video links ** \\
	$sql = "SELECT * FROM postlinks WHERE id='$id'";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_assoc();
	if ( $uptobox != $row["uptobox"] )
	{
		$sql = "UPDATE postlinks SET uptobox='$uptobox' WHERE id='$id'";
		$results = $dbconnect->query($sql);
	}
	if ( $catid != $row["youtube"] )
	{
		$sql = "UPDATE postlinks SET youtube='$youtube' WHERE id='$id'";
		$results = $dbconnect->query($sql);
	}
	if ( $catid != $row["mycima"] )
	{
		$sql = "UPDATE postlinks SET mycima='$mycima' WHERE id='$id'";
		$results = $dbconnect->query($sql);
	}
	
	
	
	header ("Location: ../category.php?id=$catid");
}

?>