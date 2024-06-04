<?php
include ("../includes/config.php");
include ("../includes/checksouthead.php");
$id = $_POST["id"];

$sql= "SELECT `categoryId`
		FROM `favourites`
		WHERE
		`userId` LIKE '".$userID."'
		AND
		`categoryId` LIKE '".$id."'
		";
$result = $dbconnect->query($sql);
if ( $result->num_rows > 0 ){
	$sql = "DELETE FROM `favourites`
			WHERE
			`userId` LIKE '".$userID."'
			AND
			`categoryId` LIKE '".$id."'
			";
	$result = $dbconnect->query($sql);
}else{
	$sql = "INSERT INTO `favourites`
			(`userId`, `categoryId`)
			VALUES
			('".$userID."', '".$id."')
			";
	$result = $dbconnect->query($sql);
}

?>