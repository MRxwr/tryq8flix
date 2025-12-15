<?php

include_once ("config.php");
include_once("checksouthead.php");

$catid = $_GET["catid"];

$sql= "SELECT id FROM users WHERE username LIKE '$username'";
$result = $dbconnect->query($sql);
$row = $result->fetch_assoc();
$userid = $row["id"];

$sql= "SELECT id FROM favolist ORDER BY id DESC LIMIT 1";
$result = $dbconnect->query($sql);
$row = $result->fetch_assoc();
$id = $row["id"]+1;

$sql= "SELECT catids FROM favolist WHERE userid = '$userid'";
$result = $dbconnect->query($sql);
if ( $result->num_rows < 1)
{
	$row = $result->fetch_assoc();
	$catids = "," . $catid;

	$sql = "INSERT INTO favolist (id, userid, catids) VALUES ('$id','$userid','$catids')";
	$result = $dbconnect->query($sql);
}
else
{
	$row = $result->fetch_assoc();
	$catids = $row["catids"];
	$catids = explode(",",$catids);
	if ( !in_array ($catid,$catids) )
	{
		$catids = "," . $catid . $row["catids"];
	}
	$sql = "UPDATE favolist SET catids = '$catids' WHERE userid = '$userid'";
	$result = $dbconnect->query($sql);
}

header ("Location: ../category.php?id=$catid");

?>