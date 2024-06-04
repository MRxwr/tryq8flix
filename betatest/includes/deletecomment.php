<?php

$commentid = $_GET["commentid"];
$catid = $_GET["catid"];
$postid = $_GET["postid"];

include_once ("config.php");

$sql = "DELETE FROM comments WHERE commentid = '$commentid'";
$results = $dbconnect->query($sql);

header ("Location: ../watch.php?catid=$catid&postid=$postid");

?>