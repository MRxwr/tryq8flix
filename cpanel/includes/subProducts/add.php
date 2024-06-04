<?php

require ("../config.php");

$productId = $_POST["productId"];
$color = $_POST["color"];
$colorEn = $_POST["colorEn"];
$size = $_POST["size"];
$quantity = $_POST["quantity"];
$code = $_POST["code"];
$price = $_POST["price"];
$cost = $_POST["cost"];

$sql = "INSERT INTO 
		`subproducts` 
		(`productId`, `color`, `colorEn`, `size`, `quantity`,`price`,`cost`) 
		VALUES 
		('".$productId."', '".$color."' , '".$colorEn."', '".$size."', '".$quantity."','".$price."','".$cost."'); 
		";
$result = $dbconnect->query($sql);

header("LOCATION: ../../add-sub-products.php?id=" . $_POST["productId"]);

//ALTER TABLE phrases AUTO_INCREMENT = 1

?>