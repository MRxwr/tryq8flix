<?php
include_once ("includes/config.php");
include_once("includes/checksouthead.php");

$sql = "SELECT id FROM users WHERE username like '$username'";
$result = $dbconnect->query($sql);
$row = $result->fetch_assoc();
$userid = $row["id"];

include("includes/errormsgs.php");

$sql = "SELECT * FROM posts";
$result = $dbconnect->query($sql);
$NumberOfPosts = $result->num_rows;
$PostsPerPage = 100;
$NumberOfPages = ceil($NumberOfPosts / $PostsPerPage) ;
$numberofpostsperpage = 5;


// generating a random header
			$sql = "SELECT catids FROM favolist WHERE userid like '1'";
			$result = $dbconnect->query($sql);
			$row = $result->fetch_assoc();
			$catids = explode(",",$row["catids"]);
			$i = 0 ;
			while ( $i < sizeof($catids)  )
			{
				if ( $catids[$i] != "" )
				{
					$catidsnew[] = $catids[$i];
				}	
				$i = $i + 1;
			}
			jump1:
			$randomheader = rand(0,sizeof($catidsnew)-1);
			$sql = "SELECT * FROM category WHERE id ='$catidsnew[$randomheader]'";
			$result = $dbconnect->query($sql);
			$row = $result->fetch_assoc();
			if ( $row["header"] == "" )
			{
				goto jump1;
			}

?>
<!DOCTYPE html>
<html>
<title>Q8Flix</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<link rel="icon" href="images/logo.png">
<link rel="stylesheet" href="css/style1.css?dasd">
<link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Roboto'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
html,body,h1,h2,h3,h4,h5,h6 
{
	font-family: "Roboto", sans-serif
}
.boxsizingBorder 
{
    -webkit-box-sizing: border-box;
       -moz-box-sizing: border-box;
            box-sizing: border-box;
}
.tags 
{
  display: inline;
  position: relative;
}

.tags:hover:after 
{
  background: #333;
  background: rgba(0, 0, 0, 0.8);
  border-radius: 5px;
  bottom: 125%;
  color: yellow;
  content: attr(glose);
  font-size: 18px;
  left: 0;
  padding: 5px 5px;
  position: absolute;
  z-index: 98;
  width: 100%;
}
.header01 
{
   height: 480px;
   padding: 200px 0 0 15px;
   background: linear-gradient(transparent 40%, #151515), url("<?php echo $row["header"] ?>") no-repeat center; 
   background-size: 100% 100%;
   overflow:hidden;
}
.details 
{
	padding: 10px;
	width: 350px;
	position: absolute;
	background: rgba(0, 0, 0, .6);
}
.rating 
{
    display: inline-block;
    font-size: 22px;
    color:yellow;
	font-weight: bold;
}  
.year,.seasons
{
    padding: 0 0 0 20px;
    display: inline-block;
    font-size: 20px;
	color:gold;
	font-weight: bold;
}
.description 
{
	padding: 0 0 0 0;
    font-size: 15px;
    line-height: 26px;
    color: rgba(255,255,255,.95);
	font-weight: bold;
}
</style>
<script type="text/javascript">
if (screen.width <= 699) 
{
	document.location = "v2.php";
}
</script>
<script>
function showUser(str) {
    if (window.XMLHttpRequest) 
		{
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } 
		else 
		{
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() 
		{
            if (this.readyState == 4 && this.status == 200) 
			{
                document.getElementById("txtHint").innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET","live/notifications_update_index.php?q="+str,true);
        xmlhttp.send();
    }
</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
<script>
	$(document).ready(function(){
		
		$.ajax({
			url: 'live/notifications_update_index.php',
			success: function(data){
				
				$("#txtHint").html(data);
			}
		})
	});
</script>
<body>
<!-- Page Container -->
	
<div class="w3-content" style="max-width:1300px;">
<?php
include_once ("template/header.php");
?>
<!-- The Grid -->
<div class="">
<div style="padding-top: 40px">
<div class="header01">

   <div class="details" style="text-align: center;">
   <div style="color:gold; font-size: 30px; text-align: center;"><?php echo strtoupper($row["title"]) ?></div>
      <div class="rating">IMDb: <?php echo $row["imdbrating"] ?></div>
      <div class="year"><?php echo $row["releasedate"] ?></div>
      <div class="seasons"><?php if ( strtolower($row["type"]) == "animov" ) { echo "ANIME MOVIE";} else{ echo strtoupper($row["type"]);} ?></div>
      <div class="description"><?php echo str_replace("?singlequtation?","'",substr($row["description"],0,200)) . " ..."; ?></div>
      <table style=" width:100%"><tr><td><a href="category.php?id=<?php echo $row["id"] ?>" class="myButton">▶ Play</a></td><td><a href="<?php echo $row["trailer"] ?>" class="myButton">Trailer</a></td></tr></table>
   </div>
</div>	  
</div>
	  
<!-- Right Column -->
<div class="w3-text-white" style="background:#151515" >
<div class="w3-row-padding w3-padding-16 w3-center">

			
    </div>
	
<?php 
	if ( $errormsg != "" ) 
	{ ?><h1 style="color: black;"><?php echo $errormsg; }?></h1>


<?php
	
	$sql = "SELECT catids FROM favolist WHERE userid LIKE '$userid'";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_assoc();
	$catids = explode(",",$row["catids"]);
	
	$i = 0;
	$checkifwatched = array();
	while ( $i < sizeof($catids))
	{
		$sql = "SELECT id FROM posts WHERE catid LIKE '$catids[$i]' ORDER BY title DESC LIMIT 1";
		$result = $dbconnect->query($sql);
		$row = $result->fetch_assoc();
		$checkifwatched[] = $row["id"];
		$i = $i +1;
	}
	
	$i = 0;
	while ( $i < sizeof($checkifwatched))
	{
		$sql = "SELECT postsid FROM finishedwatching WHERE userid LIKE '$userid'";
		$result = $dbconnect->query($sql);
		$row = $result->fetch_assoc();
		$watchedtitles = explode(",",$row["postsid"]);
		if ( !in_array($checkifwatched[$i],$watchedtitles))
		{
			$notwatched[] = $checkifwatched[$i];
		}
		$i = $i +1;
	}
	
	$i = 0;
	if ( isset ($notwatched) )
	{
		while ( $i < sizeof($notwatched))
		{
			$sql = "SELECT * FROM posts WHERE id LIKE '$notwatched[$i]'";
			$result = $dbconnect->query($sql);
			$row = $result->fetch_assoc();
			$notwatchedids[] = $row["id"];
			$notwatchedcatids[] = $row["catid"];
			$notwatchedtitles[] = $row["title"];
			$notwatchedcategories[] = $row["category"];
			$i = $i +1;
		}

		if ( sizeof($notwatched) > 0 )
		{

			$i = 0 ;
			while ( $i < sizeof($notwatched) )
			{
				$notwatchedlinks = "preparevideo.php?postid=" . $notwatchedids[$i] . "&catid=" . $notwatchedcatids[$i];
				$notwatchedfulltitle = $notwatchedcategories[$i] . " " .$notwatchedtitles[$i];
				$sql = "SELECT * FROM notification WHERE userid LIKE '$userid' ORDER BY id DESC LIMIT 1";
				$result = $dbconnect->query($sql);
				$row = $result->fetch_assoc();
				$notificationlastid = $row["id"] + 1;
				$notificationtype = "notwached";
				$notificationstatus = "unseen";

				$sql = "SELECT * FROM notification WHERE userid LIKE '$userid' AND title LIKE '$notwatchedfulltitle'";
				$result = $dbconnect->query($sql);

				if ( $result->num_rows > 0 )
				{
					goto jump;
				}
				else
				{
					$sql = "INSERT INTO notification (id, userid, type, title, titleid, status) VALUES ('$notificationlastid','$userid', '$notificationtype', '$notwatchedfulltitle', '$notwatchedlinks', '$notificationstatus')";
					$result = $dbconnect->query($sql);
				}
				jump:
				$i = $i + 1;
			}
		}
	}
		
	?>

<!-- Notifications -->
<div class="w3-row-padding w3-padding-16">
<div id="txtHint" ></div>
</div>

<!-- Ramadan -->
<?php 
require ("template/favoCircles.php");
$sql = "SELECT * FROM category WHERE type like '%tv-show%' AND genre LIKE '%ramadan%'ORDER BY id DESC";
$result = $dbconnect->query($sql);
$numberofmovies = $result->num_rows;
?>
<div class="w3-row-padding w3-padding-16 w3-center">
<b><div style="width: 100%;">
<table width="100%"><tr><td style="text-align: left; font-size: 20px;">Ramadan (<?php echo $numberofmovies ?>)</td></tr></table></div></b>

<?php
$categoryout = "ramadan";
include("template/12grid.php");
?>
<!-- Movies End -->
	<a target="" href="tvshow.php?sfg=Ramadan">
							<div class="w3-quarterindex" style="padding: 3px;  position: relative;text-align: center;color: white;">
							<img src="images/ramadan1.png" alt="" id="imageindex">
							</a>
		</div>
	<!--<table width="100%"><tr><td><a class="myButton" style="text-align: center" href="movie.php">More Movies...</a></td></tr></table>-->
	</div>

<!-- Movies -->
<?php 
$sql = "SELECT * FROM category WHERE type like '%movie%' ORDER BY id DESC";
$result = $dbconnect->query($sql);
$numberofmovies = $result->num_rows;
?>
<div class="w3-row-padding w3-padding-16 w3-center">
<b><div style="width: 100%;">
<table width="100%"><tr><td style="text-align: left; font-size: 20px;">Movies (<?php echo $numberofmovies ?>)</td></tr></table></div></b>

<?php
$categoryout = "movie";
include("template/12grid.php");
?>
<!-- Movies End -->
	<a target="" href="newmovies.php">
							<div class="w3-quarterindex" style="padding: 3px;  position: relative;text-align: center;color: white;">
							<img src="images/moremovies.png" alt="" id="imageindex">
							</a>
		</div>
	<!--<table width="100%"><tr><td><a class="myButton" style="text-align: center" href="movie.php">More Movies...</a></td></tr></table>-->
	</div>

<!-- tv-shows -->

<?php 
$sql = "SELECT * FROM category WHERE type like '%tv-show%' ORDER BY id DESC";
$result = $dbconnect->query($sql);
$numberoftvshows = $result->num_rows;
?>
<div class="w3-row-padding w3-padding-16 w3-center">
<b><div style="width: 100%;">
<table width="100%"><tr><td style="text-align: left; font-size: 20px;">TV-Shows (<?php echo $numberoftvshows ?>)</td></tr></table></div></b>

<?php
$categoryout = "tv-show";
include("template/12grid.php");
?>
<!-- tv-shows End -->
	<a target="" href="tvshow.php">
							<div class="w3-quarterindex" style="padding: 3px;  position: relative;text-align: center;color: white;">
							<img src="images/moretvshows.png" alt="" id="imageindex">
							</a>
		</div>
	<!--<table width="100%"><tr><td><a class="myButton" style="text-align: center" href="movie.php">More Movies...</a></td></tr></table>-->
	</div>
	
<!-- Anime -->

<?php 
	$sql = "SELECT * FROM category WHERE type='anime' ORDER BY id DESC";
	$result = $dbconnect->query($sql);
	$numberofanimes = $result->num_rows;
?>
<div class="w3-row-padding w3-padding-16 w3-center">
<b><div style="width: 100%;">
<table width="100%"><tr><td style="text-align: left; font-size: 20px;">Anime (<?php echo $numberofanimes ?>)</td></tr></table></div></b>

<?php
$categoryout = "anime";
include("template/12grid.php");
?>

<!-- Anime End -->
	<a target="" href="anime.php">
							<div class="w3-quarterindex" style="padding: 3px;  position: relative;text-align: center;color: white;">
							<img src="images/moreaime.png" alt="" id="imageindex">
							</a>
		</div>
	<!--<table width="100%"><tr><td><a class="myButton" style="text-align: center" href="movie.php">More Movies...</a></td></tr></table>-->
	</div>
	
<!-- Anime Movies -->

<?php
$sql = "SELECT * FROM category WHERE type='animov' ORDER BY id DESC";
$result = $dbconnect->query($sql);
$numberofanimemovies = $result->num_rows;
?>
<div class="w3-row-padding w3-padding-16 w3-center">
<b><div style="width: 100%;">
<table width="100%"><tr><td style="text-align: left; font-size: 20px;">Anime Movies (<?php echo $numberofanimemovies ?>)</td></tr></table></div></b>

<?php
$categoryout = "animov";
include("template/12grid.php");
?>

<!-- Anime Movies End -->
	<a target="" href="animemovies.php">
							<div class="w3-quarterindex" style="padding: 3px;  position: relative;text-align: center;color: white;">
							<img src="images/moreanimemovies.png" alt="" id="imageindex">
							</a>
		</div>
	<!--<table width="100%"><tr><td><a class="myButton" style="text-align: center" href="movie.php">More Movies...</a></td></tr></table>-->
	</div>
	
<!-- Wrestling -->

<?php

$sql = "SELECT * FROM category WHERE type='wrestling' ORDER BY id DESC";
$result = $dbconnect->query($sql);
$numberofwrestling = $result->num_rows;

?>
<div class="w3-row-padding w3-padding-16 w3-center">
<b><div style="width: 100%;">
<table width="100%"><tr><td style="text-align: left; font-size: 20px;">Wrestling (<?php echo $numberofwrestling ?>)</td></tr></table></div></b>

<?php
$categoryout = "wrestling";
include("template/12grid.php");
?>

<!-- Wrestling End -->
	<a target="" href="wrestling.php">
							<div class="w3-quarterindex" style="padding: 3px;  position: relative;text-align: center;color: white;">
							<img src="images/morewrestling.png" alt="" id="imageindex">
							</a>
		</div>

	</div>

    </div>
<script type="text/javascript">jssor_1_slider_init();</script>		
<?php
include_once ("template/footer.php");
?>
    <!-- End Right Column -->
    </div>
    </div>
    
  <!-- End Grid -->
    
  </div>
  <!-- End Page Container -->
</div>

</body>
</html>