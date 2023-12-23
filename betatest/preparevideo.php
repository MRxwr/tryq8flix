<?php
include_once ("includes/config.php");
include_once("includes/checksouthead.php");

if (isset( $_GET["q"] ))
{
	$q = $_GET["q"];
}
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
if ( isset($_GET["srv"]))
{
	$server = $_GET["srv"];
	header("Refresh: 10; url=watch.php?postid=$postid&catid=$id&srv=lu2b&q=$q");
}
else
{
	header("Refresh: 10; url=watch.php?postid=$postid&catid=$id&srv=lu2b&q=$q");
}

$sql = "SELECT * FROM posts WHERE id like '$postid'";
$result = $dbconnect->query($sql);
$row = $result->fetch_assoc();
$posttitle = $row["title"];
$postviews = $row["views"];
$postviews = $postviews +1;
$videosubtitle = $row["subtitle"];

$sql = "SELECT * FROM postlinks WHERE id like '$postid'";
$result = $dbconnect->query($sql);
$row = $result->fetch_assoc();
$videolink = $row["uptobox"];
$streamlink = $videolink ;
$videolinktest = explode("uptobox.com/",$videolink);
if (  isset ($videolinktest[1]) )
{
	$streamlink = $videolinktest[1];
}

$sql = "UPDATE posts SET views = $postviews WHERE id like '$postid'";
$result = $dbconnect->query($sql);

$sql = "SELECT * FROM category WHERE id like '$id'";
$result = $dbconnect->query($sql);
$row = $result->fetch_assoc();
$categorytitle = $row["title"];
$categoryposter = $row["poster"];
$categorytype = $row["type"];


?>

<!DOCTYPE html>
<html>
<title><?php echo $categorytitle . " " . $posttitle ?> - Q8Flix</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="images/logo.png">
<link rel="stylesheet" href="css/style1.css?dasd">
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

<body>
<!-- Page Container -->
<div class="w3-content" style="max-width: 1300px">

  <!-- The Grid -->
  <div class="">
 <?php
include_once ("template/header.php");
?> 
    <!-- Left Column -->
    <div class="">
    
      <div class="w3-text-white w3-center" style="padding-top: 40px;">
        <div class="w3-display-container">
			<?php
			/*
<iframe style="display :none" frameborder="0" width="1px" height="1px" src="https://uptostream.com/<?php echo $streamlink ?>"></iframe>
			*/
			?>
        </div>
        <div class="w3-container">
			
			<h3>Make sure that you are logged in with your uptobox account in this device, so no ads will bother you.</h3>
			<h3 style="direction:rtl">الرجاء التأكد من تسجيل دخولك على سيرفر uptobox في هذا الجهاز، حتى لا تزعجك الإعلانات</h3>
			<h2> Please wait 10 sec...</h2>
			<h6 style="color: hotpink">if nothing happens please click on the poster below.</h6>
			<hr>    
			<h3 style="color: red"><?php echo $categorytitle ." ". $posttitle ?></h3>
<?php
if ( isset($_GET["srv"]) )	
{	
?>
			<a href="watch.php?postid=<?php echo $postid ?>&catid=<?php echo $id ?>&srv=lu2b&q=<?php echo $q ?>"><img src="<?php echo $categoryposter ?>" style="width: 300px; height: 250px;"></a>
<?php
}
else
{
?>
<a href="watch.php?postid=<?php echo $postid ?>&catid=<?php echo $id ?>&srv=lu2b&q=<?php echo $q ?>"><img src="<?php echo $categoryposter ?>" style="width: 300px; height: 250px;"></a>
<?php
}
$sql = "SELECT * FROM category WHERE id like '$id'";
$result = $dbconnect->query($sql);
$row = $result->fetch_assoc();
?></p><hr>
          <p><?php $catgenre = explode (", ",$row["genre"]); echo "You might also like: "?></p>
		  <p> <?php $i = 0;$y=0;$repeatedvalue=0;$checktitle[0]="";
			  		$genres = count($catgenre);
while ( $genres > 0 )
	
{
	
$sql = "SELECT * FROM category WHERE genre LIKE '%$catgenre[$i]%' AND type like '$categorytype' ORDER BY RAND() Limit 10 ";
$result = $dbconnect->query($sql);

	if ( $result->num_rows > 0 )
	{
		
		
		while ( $row = $result->fetch_assoc() )
		{	
			if ( $y < 12 )
			{
			$repeatedvalue = array_search($row["title"], $checktitle, false);
			
			if ( $row["title"] == $categorytitle)
			{
			}
			elseif ( $checktitle[$repeatedvalue] != $row["title"] )
			{
			  ?>
				  <div class="w3-quarterindex" style="padding: 5px; align-items: center;"> <a href="category.php?id=<?php echo $row["id"] ?>"><img src="<?php echo $row["poster"] ?>" id="imageindex"></a></div>
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

