<?php

require ("../config.php");

$id = $_GET["id"];

$sql = "DELETE FROM `categories` WHERE `id`='$id'";
$result = $dbconnect->query($sql);

header("LOCATION: ../../categories.php");

?>