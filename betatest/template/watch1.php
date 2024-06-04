<?php
include_once ("../includes/config.php");
include_once("../includes/checksouthead.php");

//geting main data
if ( isset ($_GET["postid"]) )
{
	$postid = $_GET["postid"];
	$sql = "SELECT * FROM posts WHERE id like '$postid'";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_assoc();
	$posttitle = $row["title"];
	if ( $posttitle == "" )
	{
		header("Location: index.php");
	}
}
if ( isset ($_GET["catid"]) )
{
	$id = $_GET["catid"];
	$sql = "SELECT * FROM category WHERE id like '$id'";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_assoc();
	$posttitle = $row["title"];
	if ( $posttitle == "" )
	{
		header("Location: index.php");
	}
}
if ( isset ($_GET["id"]) )
{
	$id = $_GET["id"];
}
//error msgs
require("../includes/errormsgs.php");

//set post as watch
require("../includes/watchedposts.php");

//getting post data
require ("../includes/postdata.php");

//getting video links 
require("../includes/videolinks.php");

//getting category data
require ("../includes/categorydata.php");

// getting episode number
$nextposttitle = explode("EP",$posttitle);
$nextposttitle1 = explode("E",$posttitle);

//getting user id
$sql = "SELECT id FROM users WHERE username = '$username'";
$result = $dbconnect->query($sql);
$row = $result->fetch_assoc();
$userid = $row["id"];

//calling next and prevoius posts
require("../includes/epnextprev.php");

require ("videoplayer.php");

//require("epnextprev.php");

?>