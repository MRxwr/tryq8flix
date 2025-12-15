<?php
session_start();

$urls = $_POST["videolink"]; 
$array = explode(PHP_EOL, $urls);

if ( !isset ( $_POST["catid"] ) )
{
	$catid = "";
}
else
{
	$catid = $_POST["catid"];
	$numberofposts = $_POST["numberofposts"];
	include_once ("config.php");
	$i = 0;
}
if ( !isset ($_SESSION["username"]) )
{
	header ("Location: ../index.php?error=post");
}
else
{
	while ( $i < $numberofposts)
	{
		
		$title = $_POST["title$i"];
		$category = $_POST["category"];
		$postdate = $_POST["postdate"];
		$poster = $_POST["poster"];
		$videolink = $array[$i];
		$download = $array[$i];
		$download = explode("https://uptobox.com/", $download);
		$download = $download[1];
		$posttime = $_POST["posttime"];

		if ( !isset ($originalfile) )
		{
			$subtitle = "https://www.opensubtitles.org/en/search2/sublanguageid-all/moviename-$category+$title";
		}
		else
		{
			$subtitle = $filenewname;
		}

		$sql= "SELECT id FROM posts ORDER BY id DESC LIMIT 1";
		$result = $dbconnect->query($sql);
		$row = $result->fetch_assoc();
		$id = $row["id"]+1;

		$sql = "INSERT INTO posts (id,catid, title, category, postdate, posttime, views, poster, subtitle, videolink, download) VALUES ('$id', '$catid', '$title', '$category', '$postdate', '$posttime', '0', '$poster', '$subtitle', '$videolink', '$download')";
		$result = $dbconnect->query($sql);
		
		
		$uptobox = "";
		$youtube = "";
		$mycima = "";
		
		if ( strstr($videolink,"uptobox") )
		{
			$videolink = explode("https://uptobox.com/", $videolink);
			$videolink = preg_replace('/\s/', '', $videolink[1]);
			$uptobox = $videolink;
		}
		if ( strstr($videolink,"youtube") )
		{
			$youtube = $array[$i];
		}
		if ( strstr($videolink,"mycima") )
		{
			$mycima = $array[$i];
		}
		
		$sql = "INSERT INTO postlinks (id, uptobox, youtube, mycima) VALUES ('$id', '$uptobox', '$youtube', '$mycima')";
		$result = $dbconnect->query($sql);
		
		$i=$i+1;

		$sql = "UPDATE category SET posttime = '$posttime' WHERE id='$catid'";
		$result = $dbconnect->query($sql);

		$sql = "UPDATE category SET postdate = '$postdate' WHERE id='$catid'";
		$result = $dbconnect->query($sql);

	}
	header ("Location: ../category.php?id=$catid&msg=postadded");
	
}

?>