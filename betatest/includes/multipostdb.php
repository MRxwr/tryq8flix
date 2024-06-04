<?php
session_start();
include_once("config.php");
include_once("checksouthead.php");

$urls = $_POST["videolink"]; 
$links = explode(PHP_EOL, $_POST["videolink"]);
$titles = explode(PHP_EOL, $_POST["titles"]);
$category = selectDB("category","`id` = '{$_POST["catid"]}'");
if ( !isset($username) AND !in_array($username,$usernames) ){
	header ("Location: ../index.php?error=post");
}else{
	for( $i = 0; $i < sizeof($links); $i++ ){
		$posts = array(
			"catid" => $_POST["catid"],
			"category" => $category[0]["title"],
			"title" => $titles[$i],
			"videolink" => $links[$i],
			"download" => $links[$i],
			"poster" => $category[0]["poster"],
			"type" => $category[0]["type"],
			"subtitle" => "https://www.opensubtitles.org/en/search2/sublanguageid-all/moviename-{$category[0]["title"]}+{$titles[$i]}"
		);
		insertDB("posts",$posts);
		$getpostId = selectDB("posts","`download` LIKE '{$links[$i]}'");
		$postLinks = array(
			"id" => $getpostId[0]["id"],
			"uptobox" => $category[0]["title"]
		);
		insertDB("postlinks",$postLinks);
	}
	header ("Location: ../category.php?id={$_POST["catid"]}&msg=postadded");
}

?>