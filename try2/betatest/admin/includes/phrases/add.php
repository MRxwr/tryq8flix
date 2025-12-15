<?php

require ("../config.php");

$arphrase = $_POST["arphr"];
$enphrase = $_POST["enphr"];

$sql = "INSERT INTO `phrases` (`id`, `phr-ar`, `phr-en`) VALUES (NULL, '$arphrase', '$enphrase')";
$result = $dbconnect->query($sql);

header("LOCATION: ../../phrases.php");

//ALTER TABLE phrases AUTO_INCREMENT = 1

?>