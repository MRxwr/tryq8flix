<?php

require ("../config.php");

$id = $_GET["id"];

$sql = "DELETE FROM `phrases` WHERE `id`='$id'";
$result = $dbconnect->query($sql);

header("LOCATION: ../../phrases.php");

?>