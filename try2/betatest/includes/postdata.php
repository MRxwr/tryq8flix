<?php
$sql = "SELECT * FROM posts WHERE id like '$postid'";
$result = $dbconnect->query($sql);
$row = $result->fetch_assoc();
$posttitle = $row["title"];
$videosubtitle = $row["subtitle"];
$postPoster = $row["poster"];
$postviews = $row["views"];
$uptobox = str_replace("https://uptobox.com/","",$row["download"]);
$postviews = $postviews +1;

//updating post views
$sql = "UPDATE posts SET views = $postviews WHERE id like '$postid'";
$result = $dbconnect->query($sql);

//getting video links 
$sql = "SELECT * FROM postlinks WHERE id LIKE '$postid'";
$result = $dbconnect->query($sql);
$row = $result->fetch_assoc();
//$uptobox = $row["uptobox"];
$youtube = $row["youtube"];
$mycima = $row["mycima"];

//checking server
if ( isset ($_GET["srv"]) )
{
	$srv = $_GET["srv"];
}
elseif ( !empty($uptobox) )
{
	$srv = "u2b";
}
else
{
	if ( !empty($mycima) )
	{
		$srv = "myc";
	}
	else
	{
		$srv = "utb";
	}
}

?>