<?php
include_once ("config.php");
include_once("checksouthead.php");

$catid = $_GET["catid"];

$sql= "SELECT id FROM users WHERE username LIKE '$username'";
$result = $dbconnect->query($sql);
$row = $result->fetch_assoc();
$userid = $row["id"];

$sql= "SELECT catids FROM favolist WHERE userid = '$userid'";
$result = $dbconnect->query($sql);
if ( $result->num_rows > 0)
{
	$row = $result->fetch_assoc();
	$catids = $row["catids"];
	$catids = explode(",",$catids);
	echo "<br>";
	$favoindex = array_search($catid,$catids);
	$unfovao = "," . $catids[$favoindex];
	$catids = $row["catids"];
	$catids = explode($unfovao,$catids);
	$catids = $catids[0] . $catids[1];
	
	$sql = "UPDATE favolist SET catids = '$catids' WHERE userid = '$userid'";
	$result = $dbconnect->query($sql);
}

if ( isset($_GET["profile"]) )
{
	header ("Location: ../profile.php?username=$username&msg=deletepost");
}
else
{
	header ("Location: ../category.php?id=$catid");
}

	

?>