<?php

require ("../config.php");

$arphrase = $_POST["arphr"];
$enphrase = $_POST["enphr"];
$id = $_GET["id"];

$sql = "UPDATE phrases SET `phr-ar`='$arphrase', `phr-en`='$enphrase' WHERE `id`='$id'";
$result = $dbconnect->query($sql);

header("LOCATION: ../../phrases.php");

?>