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

if( is_uploaded_file($_FILES['logo']['tmp_name']) )
{
	$directory = "../../../logos/";
	$originalfile = $directory . date("d-m-y") . time() .  round(microtime(true)). ".png";
	move_uploaded_file($_FILES["logo"]["tmp_name"], $originalfile);
	$filenewname = str_replace("../../../logos/",'',$originalfile);
}
else
{
	$filenewname = "";
}

$sql = "INSERT INTO `banners` (`id`, `date`, `type`, `title`, `url`, `imageurl`) VALUES (NULL, NULL, '$bannerType', '$title', '$url', '$filenewname')";
$result = $dbconnect->query($sql);

header("LOCATION: ../../banners.php?b=$bannerType");

//ALTER TABLE phrases AUTO_INCREMENT = 1

?>