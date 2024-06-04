<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Request-Headers: x-requested-with");
require_once("../../includes/config.php");
//require_once("functions.php");

if ( isset($_GET["request"]) ){
	if ( $_GET["request"] == "home" ){
		require_once("home.php");
	}elseif( $_GET["request"] == "category" ){
		require_once("category.php");
	}elseif( $_GET["request"] == "video" ){
		require_once("video.php");
	}elseif( $_GET["request"] == "list" ){
		require_once("list.php");
	}elseif( $_GET["request"] == "likes" ){
		require_once("likes.php");
	}elseif( $_GET["request"] == "watching" ){
		require_once("watching.php");
	}elseif( $_GET["request"] == "profile" ){
		require_once("profile.php");
	}
}

?>