<?php
include_once ("config.php");
include_once("checksouthead.php");

$maincategory = $_GET["id"];
$removedmovie = $_GET["catid"];

$sql= "SELECT collections FROM category WHERE id = '$maincategory'";
$result = $dbconnect->query($sql);
$row = $result->fetch_assoc();
$collections = explode(",",$row["collections"]);
$removedmovieindex = array_search($removedmovie,$collections);
$removedmovie = "," . $collections[$removedmovieindex];
$collections = $row["collections"];
$collections = explode($removedmovie, $collections);
$newcollections = $collections[0] . $collections[1];
	
$sql = "UPDATE category SET collections = '$newcollections' WHERE id = '$maincategory'";
$result = $dbconnect->query($sql);

header ("Location: ../editcategory.php?id=$maincategory");

?>