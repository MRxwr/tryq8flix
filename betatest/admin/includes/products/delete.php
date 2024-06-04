<?php

require ("../config.php");

$id = $_GET["id"];

$sql = "DELETE FROM `products` WHERE `id`='$id'";
$result = $dbconnect->query($sql);

header("LOCATION: ../../product.php");

?>