<?php
include ("../includes/config.php");
include ("../includes/checksouthead.php");
$id = $_GET["id"];
$sql= "SELECT `id`
		FROM `users`
		WHERE
		`username` LIKE '$username'
		";
$result = $dbconnect->query($sql);
$row = $result->fetch_assoc();
$userid = $row["id"];

$sql= "SELECT `categoryId`
		FROM `favourites`
		WHERE
		`userId` LIKE '".$userid."'
		AND
		`categoryId` LIKE '".$id."'
		";
$result = $dbconnect->query($sql);
if ( $result->num_rows > 0 ){
	$favoOn = 1;
}

if ( isset($_GET["q"]) )
{
	if ( isset($favoOn) ){
		$sql = "DELETE FROM `favourites`
				WHERE
				`userId` LIKE '".$userid."'
				AND
				`categoryId` LIKE '".$id."'
				";
		$result = $dbconnect->query($sql);
		$favoOn = 0;
	}else{
		$sql = "INSERT INTO `favourites`
				(`userId`, `categoryId`)
				VALUES
				('".$userid."', '".$id."')
				";
		$result = $dbconnect->query($sql);
		$categoryId = $id;
		$favoOn = 1;
	}
}else{
	$categoryId = $_GET["id"];
}

	
if ( isset($favoOn) AND $favoOn == 1 )
{
	echo "<div align='center' style='width: 100%;'>
	<a onclick='showUser(". $id .")'><img src='images/favoon1.png' style='width: 45px; height: 45px;'></a>
	<div style='font-size: 10px; padding-bottom:5px;'>Unfavorite</div>
	</div>";
}
else
{
	echo "<div align='center'  style='width: 100%;'>
	<a onclick='showUser(". $id .")'><img src='images/favooff1.png' style='width: 45px; height: 45px;'></a>
	<div style='font-size: 10px; padding-bottom:5px;'>Favorite</div>
	</div>";
}

?>