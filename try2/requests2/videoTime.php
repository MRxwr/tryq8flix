<?php
require_once("../admin/includes/config.php");
require_once("../admin/includes/functions.php");
if( isset($_POST["videoTime"]) && !empty($_POST["videoTime"]) ){
	$user = checkLogin();
	if( $running = selectDB("watchedvideos","`userId` = '{$user["id"]}' AND `videoId` = '{$_POST["id"]}'") ){
		$data = array("time" => $_POST["videoTime"]);
		updateDB("watchedvideos",$data,"`userId` = '{$user["id"]}' AND `videoId` = '{$_POST["id"]}'");	
	}else{
		$data = array(
			"userId" => $user["id"],
			"videoId" => $_POST["id"],
			"time" => $_POST["videoTime"]
		);
		insertDB("watchedvideos",$data);
	}
}
?>