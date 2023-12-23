<?php

require ("../config.php");

if ( isset($_GET["b"]) )
{
	if ( $_GET["b"] == "new" )
	{
		$bannerType = "new";
	}
	elseif ( $_GET["b"] == "best" )
	{
		$bannerType = "best";
	}
	elseif ( $_GET["b"] == "build" )
	{
		$bannerType = "build";
	}
	else
	{
		$bannerType = "main";
	}
}

$url = $_POST["url"];
$title = $_POST["title"];
$id = $_GET["id"];

if( is_uploaded_file($_FILES['logo']['tmp_name']) )
{
	$directory = "../../../logos/";
	$originalfile = $directory . date("d-m-y") . time() .  round(microtime(true)). ".png";
	move_uploaded_file($_FILES["logo"]["tmp_name"], $originalfile);
	$filenewname = str_replace("../../../logos/",'',$originalfile);
}
else
{
	$sql = "SELECT imageurl 
			FROM banners 
			WHERE `id` LIKE '".$id."'";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_assoc();
	$filenewname = $row["imageurl"];
}

$sql = "UPDATE banners SET `url`='$url', `imageurl`='$filenewname', `title`='$title' WHERE `id`='$id'";
$result = $dbconnect->query($sql);

header("LOCATION: ../../banners.php?b=$bannerType");

//ALTER TABLE phrases AUTO_INCREMENT = 1

?>