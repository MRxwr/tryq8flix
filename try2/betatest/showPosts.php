<?php
require("includes/config.php");
require("includes/checksouthead.php");
?>
<style>
body{
		background-color:#181818;
		color:white;
		font-size:22px;
	}
	@media only screen and (max-width: 1280px ) {
		body{
			font-size:18px;
		}
	}
	a{
		color:white;
	}
	a:hover{
		color:gold;
	}
</style>
<?php

$sql= "SELECT id FROM users WHERE username LIKE '$username'";
$result = $dbconnect->query($sql);
$row = $result->fetch_assoc();
$userid = $row["id"];

//getting main data
if ( isset ($_GET["id"]) )
{
	$id = $_GET["id"];
	$sql = "SELECT * FROM category WHERE id like '$id'";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_assoc();
	$categorytitle = $row["title"];
	if ( $categorytitle == "")
	{
		header("Location: index.php");
	}
}
if ( isset ($_GET["catid"]) )
{
	$id = $_GET["catid"];
}

//total number of posts in a season
if ( isset ($_GET["postnum"]) )
{
	$postnum = $_GET["postnum"];
}
else
{
	$postnum = "";
}

// checking errors
require("includes/errormsgs.php");

//getting category title
$sql = "SELECT * FROM category WHERE id like '$id'";
$result = $dbconnect->query($sql);
$row = $result->fetch_assoc();
$categorytitle = $row["title"];

//getting user id
$sql = "SELECT id FROM users WHERE username = '$username'";
$result = $dbconnect->query($sql);
$row = $result->fetch_assoc();
$userid = $row["id"];

//checing watched posts
$sql= "SELECT * FROM finishedwatching WHERE userid = '$userid'";
$result = $dbconnect->query($sql);
$row = $result->fetch_assoc();
$watchedposts = explode(",",$row["postsid"]);
require("template/posts1.php");
?>