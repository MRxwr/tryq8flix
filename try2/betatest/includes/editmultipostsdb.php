<?php
session_start();
include_once ("config.php");
include_once("includes/checksouthead.php");
if ( !isset($username) AND !in_array($username,$usernames) ){
	header ("Location: ../index.php?error=post");
}else{
	$videolink = explode(PHP_EOL, trim($_POST['videolink']));
	for( $i = 0 ; $i < sizeof($_POST["id"]) ; $i++ ){
		$updateData = array(
			'title' => $_POST["title"][$i],
			'videolink' => trim($videolink[$i]),
			'download' => trim($videolink[$i])
		);
		updateDB("posts",$updateData,"`id` = '{$_POST["id"][$i]}'");
	}
	header ("Location: ../category.php?id=".$_POST["catid"]);
}

?>