<?php

$id = $_GET["id"];
$catid = $_GET["catid"];

include_once ("config.php");

$sql = "DELETE FROM posts WHERE id = '$id'";
$results = $dbconnect->query($sql);

$sql = "DELETE FROM postlinks WHERE id = '$id'";
$results = $dbconnect->query($sql);

header ("Location: ../category.php?id=$catid&msg=deletepost");

?>