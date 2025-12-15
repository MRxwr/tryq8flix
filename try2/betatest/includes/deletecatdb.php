<?php

$id = $_GET["id"];

include_once ("config.php");

$sql = "DELETE FROM category WHERE id = '$id'";
$results = $dbconnect->query($sql);

$sql = "SELECT * FROM `posts` 
		WHERE catid LIKE ".$id;
$results = $dbconnect->query($sql);
while ( $row = $results->fetch_assoc() )
{
	$ids[] = $row["id"];
}

$i=0;
while ( $i < sizeof($ids) )
{
	$sql = "DELETE FROM postlinks WHERE id = " . $ids[$i];
	$results = $dbconnect->query($sql);
	$i++;
}

$sql = "DELETE FROM posts WHERE catid = '$id'";
$results = $dbconnect->query($sql);

$sql = "DELETE FROM favourites WHERE categoryId = '$id'";
$results = $dbconnect->query($sql);

header ("Location: ../latest.php?error=deletecat");

?>