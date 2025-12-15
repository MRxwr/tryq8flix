<?php

require ("../config.php");

$id = $_GET["id"];
$arTitle = $_POST["arTitle"];
$enTitle = $_POST["enTitle"];
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
$rating = "0";

$i = 0;
while ( $i < sizeof($_FILES['logo']['tmp_name']) )
{
	if( is_uploaded_file($_FILES['logo']['tmp_name'][$i]) )
	{
		$directory = "../../../logos/";
		$originalfile = $directory . date("d-m-y") . time() .  rand(111111,999999). ".png";
		move_uploaded_file($_FILES["logo"]["tmp_name"][$i], $originalfile);
		$filenewname = str_replace("../../../logos/",'',$originalfile);
		$sql = "INSERT INTO `images`(`id`, `productId`, `imageurl`) VALUES (NULL,'$id','$filenewname')";
		$result = $dbconnect->query($sql);
	}
	$i++;
}

$sql = "UPDATE `products` SET `categoryId`='$categoryId',`brandId`='$brandId',`arTitle`='$arTitle',`enTitle`='$enTitle',`arDetails`='$arDetails',`enDetails`='$enDetails',`enSubText`='$enSubText',`arSubText`='$arSubText',`price`='$price',`discount`='$discount',`video`='$videoLink',`rating`='$rating',`color`='$color',`size`='$size',`quantity`='$quantity' WHERE `id`= '$id'";
$result = $dbconnect->query($sql);

header("LOCATION: ../../product.php");

?>