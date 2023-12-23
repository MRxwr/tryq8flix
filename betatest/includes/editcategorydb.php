<?php
include_once ("config.php");
include_once("checksouthead.php");
if ( !isset($username) AND !in_array($username,$usernames) )
{
	$id = $_POST["id"];
	header ("Location: ../category.php?msg=editcategory&id=$id");
}
else
{
	
	$id = $_POST["id"];
	$title = $_POST["title"];
	$rating = $_POST["rating"];
	$imdbrating = $_POST["imdbrating"];
	$duration = $_POST["duration"];
	$genre = $_POST["genre"];
	$releasedate = $_POST["releasedate"];
	$postdate = $_POST["postdate"];
	$language = $_POST["language"];
	$country = $_POST["country"];
	$notes = $_POST["notes"];
	$channel = $_POST["channel"];
	$poster = $_POST["poster"];
	$header = $_POST["header"];
	$description = $_POST["description"];
	$trailer = $_POST["trailer"];
	$description = str_replace("'","?singlequtation?",$description);
	$type = $_POST["type"];
	$collections = $_POST["collections"];
	$array = explode(PHP_EOL, $collections);
	
	$i = 0 ;
	while ( $i < sizeof($array))
	{
		$array[$i] = trim(preg_replace('/\s+/', '', $array[$i]));
		$i = $i + 1;
	}
	
	$sql = "SELECT * FROM category WHERE id='$id'";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_assoc();
	$savedcollections = explode(",",$row["collections"]);
	$newcollections = $row["collections"];

	$i = 0;
	while ( $i < sizeof($array))
	{
		$newarray = explode("id=", $array[$i]);
		if ( !in_array ($newarray[1],$savedcollections) )
		{
			$newcollections = "," . $newarray[1] . $newcollections;
		}
		$i = $i + 1;
	}
	
	if ( isset($newcollections) )
	{
		$sql = "UPDATE category SET collections='$newcollections' WHERE id='$id'";
		$results = $dbconnect->query($sql);
	}

	if ( $title != $row["title"] )
	{
		$sql = "UPDATE category SET title='$title' WHERE id='$id'";
		$results = $dbconnect->query($sql);
		
		$sql = "UPDATE posts SET category='$title' WHERE catid='$id'";
		$results = $dbconnect->query($sql);
	}
	if ( $title != $row["rating"] )
	{
		$sql = "UPDATE category SET rating='$rating' WHERE id='$id'";
		$results = $dbconnect->query($sql);
	}
	if ( $title != $row["imdbrating"] )
	{
		$sql = "UPDATE category SET imdbrating='$imdbrating' WHERE id='$id'";
		$results = $dbconnect->query($sql);
	}
	if ( $title != $row["duration"] )
	{
		$sql = "UPDATE category SET duration='$duration' WHERE id='$id'";
		$results = $dbconnect->query($sql);
	}
	if ( $notes != $row["notes"] )
	{
		$sql = "UPDATE category SET notes='$notes' WHERE id='$id'";
		$results = $dbconnect->query($sql);
	}
	if ( $title != $row["genre"] )
	{
		$sql = "UPDATE category SET genre='$genre' WHERE id='$id'";
		$results = $dbconnect->query($sql);
	}
	if ( $type != $row["type"] )
	{
		$sql = "UPDATE category SET type='$type' WHERE id='$id'";
		$results = $dbconnect->query($sql);

		$sql = "UPDATE posts SET type='$type' WHERE catid='$id'";
		$results = $dbconnect->query($sql);
	}
	if ( $title != $row["releasedate"] )
	{
		$sql = "UPDATE category SET releasedate='$releasedate' WHERE id='$id'";
		$results = $dbconnect->query($sql);
	}
	if ( $title != $row["postdate"] )
	{
		$sql = "UPDATE category SET postdate='$postdate' WHERE id='$id'";
		$results = $dbconnect->query($sql);
	}
	if ( $title != $row["language"] )
	{
		$sql = "UPDATE category SET language='$language' WHERE id='$id'";
		$results = $dbconnect->query($sql);
	}
	if ( $title != $row["trailer"] )
	{
		$sql = "UPDATE category SET trailer='$trailer' WHERE id='$id'";
		$results = $dbconnect->query($sql);
	}
	if ( $title != $row["description"] )
	{
		$sql = "UPDATE category SET description='$description' WHERE id='$id'";
		$results = $dbconnect->query($sql);
	}
	if ( $poster != $row["poster"] )
	{
		$sql = "UPDATE category SET poster='$poster' WHERE id='$id'";
		$results = $dbconnect->query($sql);
	}
	if ( $title != $row["header"] )
	{
		$sql = "UPDATE category SET header='$header' WHERE id='$id'";
		$results = $dbconnect->query($sql);
	}
	if ( $title != $row["channel"] )
	{
		$sql = "UPDATE category SET channel='$channel' WHERE id='$id'";
		$results = $dbconnect->query($sql);
	}
	if ( $title != $row["country"] )
	{
		$sql = "UPDATE category SET country='$country' WHERE id='$id'";
		$results = $dbconnect->query($sql);
	}
	
	header ("Location: ../category.php?error=categorydone&id=$id");
}

?>