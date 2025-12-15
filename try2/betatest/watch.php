<?php
include_once ("includes/config.php");
include_once("includes/checksouthead.php");

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
require("includes/errormsgs.php");

//set post as watch
require("includes/watchedposts.php");

//getting post data
require ("includes/postdata.php");

//getting video links 
require("includes/videolinks.php");

//getting category data
require ("includes/categorydata.php");

// getting episode number
$nextposttitle = explode("EP",$posttitle);
$nextposttitle1 = explode("E",$posttitle);

//getting user id
$sql = "SELECT id FROM users WHERE username = '$username'";
$result = $dbconnect->query($sql);
$row = $result->fetch_assoc();
$userid = $row["id"];

//calling next and prevoius posts
require("includes/epnextprev.php");
?>
<!DOCTYPE html>
<html>
<title><?php echo $categorytitle . " " . $posttitle ?> - Q8Flix</title>
<link href="//vjs.zencdn.net/7.10.2/video-js.min.css" rel="stylesheet">
<link href="https://unpkg.com/@videojs/themes@1/dist/fantasy/index.css" rel="stylesheet">
<script src="//vjs.zencdn.net/7.10.2/video.min.js"></script>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="images/logo.png">
<link rel="stylesheet" href="css/style2.css?x=dasd">
<link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Roboto'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script type="text/javascript" src="http://ajax.microsoft.com/ajax/jquery/jquery-1.3.2.min.js"></script>

<style>
html,body,h1,h2,h3,h4,h5,h6 {font-family: "Roboto", sans-serif}
.boxsizingBorder {
    -webkit-box-sizing: border-box;
       -moz-box-sizing: border-box;
            box-sizing: border-box;
}

</style>

<body>
<!-- Page Container -->
<div class="w3-content" style="max-width: 1300px">
<!-- The Grid -->
	<div class="">
<?php
require ("template/header.php");
?> 
<!-- Left Column -->
		<div class="">
			<div class="w3-text-grey w3-center" style="padding-top: 40px;">
				<div class="w3-display-container">
<?php
require ("template/videoplayer.php");
?>
				</div>
        			<div class="w3-container">
<?php
require("template/videoqualities.php");
?>
						<hr>
						<h4 style="color: white"><a style="color: white; text-decoration:none" href="category.php?id=<?php echo $id ?>"><?php echo $categorytitle .'</a> -> '. $posttitle .'</h4>';
      ?>
<?php
// next and prev posts
require("template/epnextprev.php");
?>
						<hr>
<?php
require("template/moreinfo.php");
require("template/editwatch.php");
require("template/similargenre.php");
?>
        			</div>
   			</div>
<?php
require ("template/footer.php");
?>
<!-- End Left Column -->
    	</div>  
<!-- End Grid -->
	</div>   
<!-- End Page Container -->
</div>
</body>
</html>