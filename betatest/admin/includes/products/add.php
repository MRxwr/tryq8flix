<?php

require ("../config.php");

$artitle = $_POST["arTitle"];
$entitle = $_POST["enTitle"];
$arDetails = $_POST["arDetails"];
$enDetails = $_POST["enDetails"];
$enSubText = $_POST["enSubText"];
$arSubText = $_POST["arSubText"];
$categoryId = $_POST["categoryId"];
$brandId = $_POST["brandId"];
$price = $_POST["price"];
$discount = $_POST["discount"];
$videoLink = $_POST["videoLink"];
$quantity = $_POST["quantity"];
$size = "";
$color = "";
$rating = "";

$sql = "INSERT INTO `products`
		(`id`, `categoryId`, `brandId`, `arTitle`, `enTitle`, `date`, `arDetails`, `enDetails`, `enSubText`, `arSubText`, `price`, `discount`, `video`, `rating`, `color`, `size`, `quantity`, `views`) 
		VALUES 
		(NULL,'$categoryId','$brandId','$artitle','$entitle',NULL,'$arDetails','$enDetails','$enSubText','$arSubText','$price','$discount','$videoLink','0','$color','$size','$quantity','0')";
$result = $dbconnect->query($sql);

$sql = "SELECT `id`
		FROM `products` 
		WHERE `enTitle` LIKE '$entitle' 
		AND `arTitle` LIKE '$artitle'
		";
$result = $dbconnect->query($sql);
$row = $result->fetch_assoc();
$productID = $row["id"];

$i = 0;
while ( $i < sizeof($_FILES['logo']['tmp_name']) )
{
	if( is_uploaded_file($_FILES['logo']['tmp_name'][$i]) )
	{
		$directory = "../../../logos/";
		$originalfile = $directory . date("d-m-y") . time() .  rand(111111,999999). ".png";
		move_uploaded_file($_FILES["logo"]["tmp_name"][$i], $originalfile);
		$filenewname = str_replace("../../../logos/",'',$originalfile);
		$sql = "INSERT INTO `images`(`id`, `productId`, `imageurl`) VALUES (NULL,'$productID','$filenewname')";
		$result = $dbconnect->query($sql);
	}
	$i++;
}
header("LOCATION: ../../product.php");

//ALTER TABLE phrases AUTO_INCREMENT = 1

?>