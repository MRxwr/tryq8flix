<?php
session_start();
include_once ("config.php");
include_once("includes/checksouthead.php");
if ( !isset($username) AND !in_array($username,$usernames) )
{
	header ("Location: ../index.php?error=category");
}
else
{
	include_once ("config.php");
	$_POST = str_replace("'",'',$_POST);
	$title = $_POST["title"];
	$rating = $_POST["rating"];
	$imdbrating = $_POST["imdbrating"];
	$duration = $_POST["duration"];
	$genre = $_POST["genre"];
	$releasedate = $_POST["releasedate"];
	$postdate = $_POST["postdate"];
	$language = $_POST["language"];
	$notes = $_POST["notes"];
	$country = $_POST["country"];
	$channel = $_POST["channel"];
	//$channel = str_replace("'","",$channel);
	$poster = $_POST["poster"];
	$header = $_POST["header"];
	$description = $_POST["description"];
	//$description = str_replace("'","?singlequtation?",$description);
	$trailer = $_POST["trailer"];
	$type = $_POST["type"];
	$posttime = $_POST["posttime"];
	
	$sql= "SELECT id FROM category WHERE title like '$title' and type like '$type'";
	$result = $dbconnect->query($sql);
	if ( $result->num_rows > 0 )
	{
		$row = $result->fetch_assoc();
		$id = $row["id"];
		goto jump;
	}
	
	$sql= "SELECT id FROM category ORDER BY id DESC LIMIT 1";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_assoc();
	$id = $row["id"]+1;
	
	$sql = "INSERT INTO category 
			(id,type, title, rating, imdbrating, duration, genre, releasedate, posttime, postdate, language, country, channel, poster, header, description, trailer, notes) 
			VALUES
			('$id','$type', '$title', '$rating', '$imdbrating', '$duration', '$genre', '$releasedate', '$posttime', '$postdate', '$language', '$country', '$channel', '$poster', '$header', '$description', '$trailer','$notes')
			";
	$result = $dbconnect->query($sql);
	
	header ("Location: ../latest.php");
	
	jump:
	header ("Location: ../category.php?id=$id");
}
?>