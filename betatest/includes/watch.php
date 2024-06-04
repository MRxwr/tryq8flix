<?php
include_once ("includes/config.php");
include_once("includes/checksouthead.php");

if ( isset ($_GET["postid"]) )
{
	$postid = $_GET["postid"];
}
if ( isset ($_GET["catid"]) )
{
	$id = $_GET["catid"];
}
if ( isset ($_GET["id"]) )
{
	$id = $_GET["id"];
}

if ( isset ($_GET["postnum"]) )
{
	$postnum = $_GET["postnum"];
}
else
{
	$postnum = "";
}

if ( !isset ($_GET["msg"]) )
{
	$msg = "";
}
elseif ( $_GET["msg"] == "deletepost" )
{
	$msg = "Post Has Been deleted.";
}
elseif ( $_GET["msg"] == "editcategory" )
{
	$msg = "You are not allowed to edit a category.";
}
elseif ( $_GET["msg"] == "categorydone" )
{
	$msg = "Category has been updates successfully.";
}
elseif ( $_GET["msg"] == "postadded" )
{
	$msg = "Post has been added successfully.";
}

$sql = "SELECT * FROM posts WHERE id like '$postid'";
$result = $dbconnect->query($sql);
$row = $result->fetch_assoc();
$posttitle = $row["title"];
$videolink = $row["videolink"];

$sql = "SELECT * FROM category WHERE id like '$id'";
$result = $dbconnect->query($sql);
$row = $result->fetch_assoc();
$categorytitle = $row["title"];
$categoryposter = $row["poster"];

?>
<!DOCTYPE html>
<html>
<title><?php echo $categorytitle . " " . $posttitle ?> - Q8Flix</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="images/favicon.png">
<link rel="stylesheet" href="css/style.css">
<link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Roboto'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="//releases.flowplayer.org/7.0.4/commercial/skin/skin.css">

<style>
html,body,h1,h2,h3,h4,h5,h6 {font-family: "Roboto", sans-serif}
.boxsizingBorder {
    -webkit-box-sizing: border-box;
       -moz-box-sizing: border-box;
            box-sizing: border-box;
}
</style>
	
<link href="http://vjs.zencdn.net/4.12/video-js.css" rel="stylesheet">
<script src="http://vjs.zencdn.net/4.12/video.js"></script>
<style type="text/css">
  .vjs-default-skin .vjs-control-bar,
  .vjs-default-skin .vjs-big-play-button { background: rgba(0,0,0,0.7) }
  .vjs-default-skin .vjs-slider { background: rgba(0,0,0,0.2333333333333333) }
</style>
	
<!-- CSS  -->

<!-- HTML -->

<body class="w3-light-grey">
<!-- Page Container -->
<div class="w3-content" style="max-width: 1300px">

  <!-- The Grid -->
  <div class="">
 <?php
include_once ("template/header.php");
?> 
    <!-- Left Column -->
    <div class="">
    
      <div class="w3-white w3-text-grey w3-center">
        <div class="w3-display-container">
<video id="MY_VIDEO_1" class="video-js vjs-default-skin" controls width="100%" height="350px" poster="<?php echo $categoryposter ?>" data-setup="{}">
 <source src="<?php echo $videolink ?>" type='video/mp4'>
 <source src="<?php echo $videolink ?>" type='video/webm'>
 <p class="vjs-no-js">To view this video please enable JavaScript, and consider upgrading to a web browser that <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a></p>
</video>

          <div class="w3-display-bottomleft w3-container w3-text-black">
          </div>
        </div>
        <div class="w3-container">
			      <?php 
      	echo "<h4>".$categorytitle ." ". $posttitle ."</h4>";
      ?><hr>
       <p><h6><a href="category.php?id=<?php echo $id ?>">Click here for more: <?php echo $row["title"] ?>.</a></h6><?php 
	
if ( !isset ($_SESSION["username"]) )
{
}
else
{
	$username = $_SESSION["username"];
}
if ( $username == "admin" )
{?>
<a  href="includes/deletepostdb.php?id=<?php echo $postid ?>&catid=<?php echo $id ?>"><img src="images/delete.png" width="25" height="25"></a>
<a  href="editpost.php?id=<?php echo $postid ?>&catid=<?php echo $id ?>"><img src="images/edit.png" width="25" height="25"></a>
<?php
}
?></p><hr>
          <p><?php $catgenre = explode (", ",$row["genre"]); echo "You might also like: "?></p>
		  <p> <?php $i = 0;$y=0;$repeatedvalue=0;$checktitle[0]="";
			  		$genres = count($catgenre);
while ( $genres > 0 )
	
{
	
$sql = "SELECT * FROM category WHERE genre LIKE '%$catgenre[$i]%' Limit 10";
$result = $dbconnect->query($sql);

	if ( $result->num_rows > 0 )
	{
		
		
		while ( $row = $result->fetch_assoc() )
		{	
			if ( $y <= 12 )
			{
			$repeatedvalue = array_search($row["title"], $checktitle, false);
			
			if ( $row["title"] == $categorytitle)
			{
			}
			elseif ( $checktitle[$repeatedvalue] != $row["title"] )
			{
			  ?>
				  <div class="w3-quarterindex" style="padding: 5px; align-items: center;"> <a href="category.php?id=<?php echo $row["id"] ?>"><img src="<?php echo $row["poster"] ?>" style="width:100%" height="250px"></a></div>
		  <?php
				  $checktitle[$y] = $row["title"];
				  $checktitle[$repeatedvalue] = $checktitle[$y];
				  
			}
			}
			
			$y++;
		}
	}
	$i++;
	$genres--;
}
			  ?></p>
          
          <br>
        </div>
      </div>
<?php
include_once ("template/footer.php");
?>
    <!-- End Left Column -->
    </div>
    </div>
    </div>
    
  <!-- End Grid -->
    
  </div>

  <!-- End Page Container -->
</div>

<!-- JS code -->
<!-- If you'd like to support IE8 (for Video.js versions prior to v7) -->
<script src="https://vjs.zencdn.net/ie8/ie8-version/videojs-ie8.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/videojs-contrib-hls/5.14.1/videojs-contrib-hls.js"></script>
<script src="https://vjs.zencdn.net/7.2.3/video.js"></script>

</body>
</html>

