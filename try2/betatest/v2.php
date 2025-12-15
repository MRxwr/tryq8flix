<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<link rel="icon" href="images/logo.png">
<link rel="stylesheet" href="css/style1.css?dassd">
<link rel="manifest" href="js13kpwa.webmanifest">
<link rel="manifest" href="manifest.webapp">
<link rel="manifest" href="manifest.json">

<title>Q8FLiX</title>
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
</head>

<body>
<?php
include_once ("includes/config.php");
include_once("includes/checksouthead.php");
$numberofpostsperpage = 10;
$i = 0;
	
$sql = "SELECT id FROM users WHERE username like '$username'";
$result = $dbconnect->query($sql);
$row = $result->fetch_assoc();
$userid = $row["id"];
?>	
	
<div style="margin: auto; max-width: 1300px">	
	
<header class='w3-container' style="max-width: 1300px;background: #2E2E2E;position: fixed; z-index: 1; width: 100%;">
<table align="center" border="0" width="100%">
<tr>
<td align="left">
<span onclick="openNav()"><img src="images/menu.png" width="25px"></span>
</td>
<td align="center">
<center><a href="index.php"><img src="images/logop1.png"style="height: 35px;"><img src="images/logo.png" style="height: 35px;"><img src="images/logop2.png"style="height: 35px;"></a></center>
</td>
<td align="right">
<a href="search.php"><img src="images/search1.png" width="25px"></a>
</td>
</tr>
</table>
</header>

<?php
// generating a random header
$sql = "SELECT
		c.*
		FROM `favourites` AS f
		JOIN `category` AS c
		ON c.id = f.categoryId
		WHERE
		`userId` LIKE '1'
		AND 
		`header` != ''
		ORDER BY RAND()
		LIMIT 1
		";
$result = $dbconnect->query($sql);
$row = $result->fetch_assoc();
?>

<script>
/* Set the width of the side navigation to 250px */
function openNav() {
    document.getElementById("mySidenav").style.width = "100%";
}

/* Set the width of the side navigation to 0 */
function closeNav() {
    document.getElementById("mySidenav").style.width = "0";
}
	
/* Set the width of the side navigation to 250px */
function openNavright() {
    document.getElementById("mySidenavright").style.width = "100%";
}

/* Set the width of the side navigation to 0 */
function closeNavright() {
    document.getElementById("mySidenavright").style.width = "0";
}
</script>

<style>
.header01 
{
   height: 500px;
   padding: 370px 0 0 0;
   background: linear-gradient(transparent 50%, #151515), url("<?php echo $row["poster"] ?>") no-repeat center; 
   background-size: 100% 100%;
   overflow:hidden;
}
.details 
{
	padding: 0 20px 0 0;
	width: 100%;
}
.rating 
{
    display: inline-block;
    font-size: 15px;
    color:yellow;
	font-weight: bold;
}  
.year,.seasons
{
    padding: 0 0 0 20px;
    display: inline-block;
    font-size: 15px;
	color:white;
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
input[name=search] {
  width: 100%;
  box-sizing: border-box;
  border: 2px solid #ccc;
  border-radius: 10px;
  font-size: 16px;
  background-color: white;
  background-image: url('https://www.w3schools.com/howto/searchicon.png');
  background-position: 10px 10px; 
  background-repeat: no-repeat;
  padding: 12px 20px 12px 40px;
  -webkit-transition: width 0.4s ease-in-out;
  transition: width 0.4s ease-in-out;
}

input[name=search]:focus {
  width: 100%;
}
 /* The side navigation menu */
.sidenav {
    height: 100%; /* 100% Full-height */
    width: 0; /* 0 width - change this with JavaScript */
    position: fixed; /* Stay in place */
    z-index: 1; /* Stay on top */
    top: 0;
    left: 0;
    background-color: #111; /* Black*/
    overflow-x: hidden; /* Disable horizontal scroll */
    padding-top: 60px; /* Place content 60px from the top */
    transition: 0.5s; /* 0.5 second transition effect to slide in the sidenav */
}
	
.sidenavright {
    height: 70px; /* 100% Full-height */
    width: 0; /* 0 width - change this with JavaScript */
    position: fixed; /* Stay in place */
    z-index: 1; /* Stay on top */
    top: 0;
    right: 0;;
    background-color: #111; /* Black*/
    overflow-x: hidden; /* Disable horizontal scroll */
    padding-top: 0; /* Place content 60px from the top */
    transition: 0.5s; /* 0.5 second transition effect to slide in the sidenav */
}

/* The navigation menu links */
.sidenav a {
    padding: 8px 8px 8px 32px;
    text-decoration: none;
    font-size: 25px;
    color: #818181;
    display: block;
    transition: 0.3s
}

.sidenavright a {
    padding: 8px 8px 8px 32px;
    text-decoration: none;
    font-size: 25px;
    color: #818181;
    display: block;
    transition: 0.3s
}
	
/* When you mouse over the navigation links, change their color */
.sidenav a:hover, .offcanvas a:focus{
    color: #f1f1f1;
}

/* Position and style the close button (top right corner) */
.sidenav .closebtn {
    position: absolute;
    top: 0;
    right: 0px;
	padding-top: 0px;
    font-size: 36px;
    margin-left: 25px;
}
	
.sidenavright .closebtnright {
    position: relative;
    top: 0;
    right: 0px;
	padding-top: 0px;
    font-size: 36px;
    margin-right: 10px;
}

/* Style page content - use this if you want to push the page content to the right when you open the side navigation */
#main {
    transition: margin-left .5s;
    padding: 20px;
}

/* On smaller screens, where height is less than 450px, change the style of the sidenav (less padding and a smaller font size) */
@media screen and (max-height: 450px) {
    .sidenav {padding-top: 15px;}
    .sidenav a {font-size: 18px;}
}
</style>

 <div id="mySidenavright" class="sidenavright" >
  <form method="get" action="search.php">
  <table style="width: 99%"><tr><td style="width: 99%"><input name="search" type="text" placeholder="Type & Enter.." value=""></td><td><a href="javascript:void(0)" class="closebtnright" onclick="closeNavright()">&times;</a></td></tr></table>
  </form>
</div>

<?php 
if ( !isset ($_SESSION["username"]) ) 
{
?>

 <div id="mySidenav" class="sidenav" >
  <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
  <a href="login.php">Login</a>
  <a href="register.php">Register</a>
</div>

<?php
}
elseif ( $_SESSION["username"] == "admin" )
{
?>
<div id="mySidenav" class="sidenav" >
  <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
  <a href="index.php">Home</a>
  <a href="request.php">Create Request</a>
  <a href="viewrequests.php">Requests</a>
  <a href="viewreports.php">Reports</a>
  <a href="check.php">Check Video</a>
  <a href="renamefiles.php">Rename files</a>
  <a href="latest.php">New Additions</a>
  <a href="anime.php">Animes Series</a>
  <a href="animemovies.php">Anime Movies</a>
  <a href="newmovies.php">Movies</a>
  <a href="tvshow.php">Tv-Shows</a>
  <a href="wrestling.php">Wrestling</a>
  <a href="search.php">Search</a>
  <a href="profile.php">Profile</a>
  <a href="addcategory.php">New Category</a>
  <a href="addcategory1.php">imdb Add</a>
  <a href="logout.php">Logout</a>
</div>

<?php
}
else
{
?>
<div id="mySidenav" class="sidenav" >
  <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
  <a href="index.php">Home</a>
  <a href="request.php">Request</a>
  <a href="anime.php">Animes Series</a>
  <a href="animemovies.php">Anime Movies</a>
  <a href="newmovies.php">Movies</a>
  <a href="tvshow.php">Tv-Shows</a>
  <a href="wrestling.php">Wrestling</a>
  <a href="search.php">Search</a>
  <a href="profile.php">Profile</a>
  <a href="logout.php">Logout</a>
</div>

<?php
}
?>

<!-- Use any element to open the sidenav -->
<!-- header end -->
<div style="padding-top:40px">
    <div class="header01" style="">
   <div class="details" style="text-align:center;">
    
        <table style="width:100%">
        <tr>
            <td rowspan=2>
                <div style="color:gold; font-size: 17px; font-weight:bold"><?php echo strtoupper($row["title"]) ?></div>
                <div class="rating">IMDb: <?php echo $row["imdbrating"] ?></div>
                <div class="year"><?php echo $row["releasedate"] ?></div>
                <div class="seasons"><?php if ( strtolower($row["type"]) == "animov" ) { echo "ANIME MOVIE";} else{ echo strtoupper($row["type"]);} ?></div>
            </td>
            <td style="width:25%">
                <a href="category.php?id=<?php echo $row["id"] ?>" class="myButton">Play</a>
                <a href="<?php echo $row["trailer"] ?>" class="myButton">Trailer</a>
            </td>
        </tr>
        <tr>
            <td>
                
            </td>
        </tr>
    </table>
   </div>
</div>
</div>
<div style="">
    <div style="">
	
	<script src="//jssors8.azureedge.net/script/jssor.slider-27.5.0.min.js" type="text/javascript"></script>
    <script type="text/javascript">
        jssor_1_slider_init = function() {

            var jssor_1_options = {

              $SlideDuration: 300,
              $SlideWidth: 300,
              $SlideSpacing: 10,
              $ArrowNavigatorOptions: {
                $Class: $JssorArrowNavigator$,
                $Steps: 3
              },
              $BulletNavigatorOptions: {
                $Class: $JssorBulletNavigator$
              }
            };

            var jssor_1_slider = new $JssorSlider$("jssor_1", jssor_1_options);

            /*#region responsive code begin*/

            var MAX_WIDTH = 1280;

            function ScaleSlider() {
                var containerElement = jssor_1_slider.$Elmt.parentNode;
                var containerWidth = containerElement.clientWidth;

                if (containerWidth) {

                    var expectedWidth = Math.min(MAX_WIDTH || containerWidth, containerWidth);

                    jssor_1_slider.$ScaleWidth(expectedWidth);
                }
                else {
                    window.setTimeout(ScaleSlider, 30);
                }
            }

            ScaleSlider();

            $Jssor$.$AddEvent(window, "load", ScaleSlider);
            $Jssor$.$AddEvent(window, "resize", ScaleSlider);
            $Jssor$.$AddEvent(window, "orientationchange", ScaleSlider);
            /*#endregion responsive code end*/
        };
    </script>
	<script type="text/javascript">
        jssor_2_slider_init = function() {

            var jssor_2_options = {

                $SlideDuration: 300,
              $SlideWidth: 300,
              $SlideSpacing: 10,
              $ArrowNavigatorOptions: {
                $Class: $JssorArrowNavigator$,
                $Steps: 3
              },
              $BulletNavigatorOptions: {
                $Class: $JssorBulletNavigator$
              }
            };

            var jssor_2_slider = new $JssorSlider$("jssor_2", jssor_2_options);

            /*#region responsive code begin*/

            var MAX_WIDTH = 1280;

            function ScaleSlider() {
                var containerElement = jssor_2_slider.$Elmt.parentNode;
                var containerWidth = containerElement.clientWidth;

                if (containerWidth) {

                    var expectedWidth = Math.min(MAX_WIDTH || containerWidth, containerWidth);

                    jssor_2_slider.$ScaleWidth(expectedWidth);
                }
                else {
                    window.setTimeout(ScaleSlider, 30);
                }
            }

            ScaleSlider();

            $Jssor$.$AddEvent(window, "load", ScaleSlider);
            $Jssor$.$AddEvent(window, "resize", ScaleSlider);
            $Jssor$.$AddEvent(window, "orientationchange", ScaleSlider);
            /*#endregion responsive code end*/
        };
    </script>
	<script type="text/javascript">
        jssor_3_slider_init = function() {

            var jssor_3_options = {

                $SlideDuration: 300,
              $SlideWidth: 300,
              $SlideSpacing: 10,
              $ArrowNavigatorOptions: {
                $Class: $JssorArrowNavigator$,
                $Steps: 3
              },
              $BulletNavigatorOptions: {
                $Class: $JssorBulletNavigator$
              }
            };

            var jssor_3_slider = new $JssorSlider$("jssor_3", jssor_3_options);

            /*#region responsive code begin*/

            var MAX_WIDTH = 1280;

            function ScaleSlider() {
                var containerElement = jssor_3_slider.$Elmt.parentNode;
                var containerWidth = containerElement.clientWidth;

                if (containerWidth) {

                    var expectedWidth = Math.min(MAX_WIDTH || containerWidth, containerWidth);

                    jssor_3_slider.$ScaleWidth(expectedWidth);
                }
                else {
                    window.setTimeout(ScaleSlider, 30);
                }
            }

            ScaleSlider();

            $Jssor$.$AddEvent(window, "load", ScaleSlider);
            $Jssor$.$AddEvent(window, "resize", ScaleSlider);
            $Jssor$.$AddEvent(window, "orientationchange", ScaleSlider);
            /*#endregion responsive code end*/
        };
    </script>
	<script type="text/javascript">
        jssor_4_slider_init = function() {

            var jssor_4_options = {

              $SlideDuration: 300,
              $SlideWidth: 300,
              $SlideSpacing: 10,
              $ArrowNavigatorOptions: {
                $Class: $JssorArrowNavigator$,
                $Steps: 3
              },
              $BulletNavigatorOptions: {
                $Class: $JssorBulletNavigator$
              }
            };

            var jssor_4_slider = new $JssorSlider$("jssor_4", jssor_4_options);

            /*#region responsive code begin*/

            var MAX_WIDTH = 1280;

            function ScaleSlider() {
                var containerElement = jssor_4_slider.$Elmt.parentNode;
                var containerWidth = containerElement.clientWidth;

                if (containerWidth) {

                    var expectedWidth = Math.min(MAX_WIDTH || containerWidth, containerWidth);

                    jssor_4_slider.$ScaleWidth(expectedWidth);
                }
                else {
                    window.setTimeout(ScaleSlider, 30);
                }
            }

            ScaleSlider();

            $Jssor$.$AddEvent(window, "load", ScaleSlider);
            $Jssor$.$AddEvent(window, "resize", ScaleSlider);
            $Jssor$.$AddEvent(window, "orientationchange", ScaleSlider);
            /*#endregion responsive code end*/
        };
    </script>
	<script type="text/javascript">
        jssor_5_slider_init = function() {

            var jssor_5_options = {

              $SlideDuration: 300,
              $SlideWidth: 300,
              $SlideSpacing: 10,
              $ArrowNavigatorOptions: {
                $Class: $JssorArrowNavigator$,
                $Steps: 3
              },
              $BulletNavigatorOptions: {
                $Class: $JssorBulletNavigator$
              }
            };

            var jssor_5_slider = new $JssorSlider$("jssor_5", jssor_5_options);

            /*#region responsive code begin*/

            var MAX_WIDTH = 1280;

            function ScaleSlider() {
                var containerElement = jssor_5_slider.$Elmt.parentNode;
                var containerWidth = containerElement.clientWidth;

                if (containerWidth) {

                    var expectedWidth = Math.min(MAX_WIDTH || containerWidth, containerWidth);

                    jssor_5_slider.$ScaleWidth(expectedWidth);
                }
                else {
                    window.setTimeout(ScaleSlider, 30);
                }
            }

            ScaleSlider();

            $Jssor$.$AddEvent(window, "load", ScaleSlider);
            $Jssor$.$AddEvent(window, "resize", ScaleSlider);
            $Jssor$.$AddEvent(window, "orientationchange", ScaleSlider);
            /*#endregion responsive code end*/
        };
    </script>
<style>
        /*jssor slider loading skin spin css*/
        .jssorl-009-spin img {
            animation-name: jssorl-009-spin;
            animation-duration: 1.6s;
            animation-iteration-count: infinite;
            animation-timing-function: linear;
        }

        @keyframes jssorl-009-spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /*jssor slider bullet skin 057 css*/
        .jssorb057 .i {position:absolute;cursor:pointer;}
        .jssorb057 .i .b {fill:none;stroke:#fff;stroke-width:2000;stroke-miterlimit:10;stroke-opacity:0.4;}
        .jssorb057 .i:hover .b {stroke-opacity:.7;}
        .jssorb057 .iav .b {stroke-opacity: 1;}
        .jssorb057 .i.idn {opacity:.3;}

        /*jssor slider arrow skin 073 css*/
        .jssora073 {display:block;position:absolute;cursor:pointer;}
        .jssora073 .a {fill:#ddd;fill-opacity:.7;stroke:#000;stroke-width:160;stroke-miterlimit:10;stroke-opacity:.7;}
        .jssora073:hover {opacity:.8;}
        .jssora073.jssora073dn {opacity:.4;}
        .jssora073.jssora073ds {opacity:.3;pointer-events:none;}
</style>

<!-- Notifications -->
<div class="w3-row-padding w3-padding-16">
<div id="txtHint" ></div>
</div>
<?php
require ("template/favoCirclesMobile.php");
require ("template/ContWatchingMobile.php");

?>	
<div style="text-align: left; font-size: 15px; "><a target="" href="cate?type=<?php echo "movie" ?>" style="color: white;text-decoration: none;"><b>Movies</b></a></div>
    <div id="jssor_1" style="position:relative;margin:0 auto;top:0px;left:0px;width:1000px;height:400px;overflow:hidden;visibility:hidden;">
        <!-- Loading Screen -->
        <div data-u="loading" class="jssorl-009-spin" style="position:absolute;top:0px;left:0px;width:100%;height:100%;text-align:center;background-color:rgba(0,0,0,0.7);">
            <img style="margin-top:-19px;position:relative;top:50%;width:38px;height:38px;" src="//jssorcdn7.azureedge.net/theme/svg/loading/static-svg/spin.svg" />
        </div>
        <div data-u="slides" style="cursor:default;position:relative;top:0px;left:0px;width:1000px;height:400px;overflow:hidden;">
            
				<?php
// getting data saved into arrays
	
	$sql = "SELECT * 
			FROM `posts`
			WHERE 
			`type` LIKE '%movie%' 
			GROUP BY `catid`
			ORDER BY `id` DESC 
			LIMIT $numberofpostsperpage
			";
	$result = $dbconnect->query($sql);
		while ( $row = $result->fetch_assoc() )
		{
							?>
			
			<div>
                <a target="" href="category.php?id=<?php echo $row["catid"] ?>"><img data-u="image" src="<?php echo $row["poster"] ?>" style="border-radius: 15%;" /></a>
            </div>
			
			<?php 
		}

	?>
			
        </div>
        <!-- Arrow Navigator -->
		<div data-u="arrowleft" class="jssora073" style="background: black; width:50px;height:400px;left:0;opacity: 0.75" data-autocenter="2">
        <div data-u="arrowleft" class="jssora073" style="background: black; width:50px;height:50px;top:175px;" data-autocenter="2" data-scale="0.75" data-scale-left="0.75">
            <svg viewbox="0 0 16000 16000" style="position:absolute;top:0;left:0;width:100%;height:100%;">
                <path class="a" d="M4037.7,8357.3l5891.8,5891.8c100.6,100.6,219.7,150.9,357.3,150.9s256.7-50.3,357.3-150.9 l1318.1-1318.1c100.6-100.6,150.9-219.7,150.9-357.3c0-137.6-50.3-256.7-150.9-357.3L7745.9,8000l4216.4-4216.4 c100.6-100.6,150.9-219.7,150.9-357.3c0-137.6-50.3-256.7-150.9-357.3l-1318.1-1318.1c-100.6-100.6-219.7-150.9-357.3-150.9 s-256.7,50.3-357.3,150.9L4037.7,7642.7c-100.6,100.6-150.9,219.7-150.9,357.3C3886.8,8137.6,3937.1,8256.7,4037.7,8357.3 L4037.7,8357.3z"></path>
            </svg>
        </div>
		</div>
		<div data-u="arrowright" class="jssora073" style="width:50px;height:400px;top:0px;right:0; opacity: 0.75; background: black" data-autocenter="2">
        <div data-u="arrowright" class="jssora073" style="width:50px;height:50px;top:175px;right:0;" data-autocenter="2" data-scale="0.75" data-scale-right="0.75">
            <svg viewbox="0 0 16000 16000" style="position:absolute;top:0;left:0;width:100%;height:100%;">
                <path class="a" d="M11962.3,8357.3l-5891.8,5891.8c-100.6,100.6-219.7,150.9-357.3,150.9s-256.7-50.3-357.3-150.9 L4037.7,12931c-100.6-100.6-150.9-219.7-150.9-357.3c0-137.6,50.3-256.7,150.9-357.3L8254.1,8000L4037.7,3783.6 c-100.6-100.6-150.9-219.7-150.9-357.3c0-137.6,50.3-256.7,150.9-357.3l1318.1-1318.1c100.6-100.6,219.7-150.9,357.3-150.9 s256.7,50.3,357.3,150.9l5891.8,5891.8c100.6,100.6,150.9,219.7,150.9,357.3C12113.2,8137.6,12062.9,8256.7,11962.3,8357.3 L11962.3,8357.3z"></path>
            </svg>
        </div>
		</div>
    </div>
    
	<div style="text-align: left; font-size: 15px; "><p></p><a target="" href="cate?type=<?php echo "tv-show" ?>" style="color: white;text-decoration: none;"><b>TV-Shows</b></a></div>
	<div id="jssor_2" style="position:relative;margin:0 auto;top:0px;left:0px;width:1000px;height:400px;overflow:hidden;visibility:hidden;">
        <!-- Loading Screen -->
        <div data-u="loading" class="jssorl-009-spin" style="position:absolute;top:0px;left:0px;width:100%;height:100%;text-align:center;background-color:rgba(0,0,0,0.7);">
            <img style="margin-top:-19px;position:relative;top:50%;width:38px;height:38px;" src="//jssorcdn7.azureedge.net/theme/svg/loading/static-svg/spin.svg" />
        </div>
        <div data-u="slides" style="cursor:default;position:relative;top:0px;left:0px;width:1000px;height:400px;overflow:hidden;">
            
				<?php
// getting data saved into arrays
	
	$sql = "SELECT 
			MAX(`id`), `catid`, `poster` 
			FROM `posts` 
			WHERE 
			`type` LIKE '%tv-show%' 
			GROUP BY `catid` 
			ORDER BY MAX(`id`) DESC
			LIMIT $numberofpostsperpage 
			";
	$result = $dbconnect->query($sql);
		while ( $row = $result->fetch_assoc() )
		{
							?>
			
			<div>
                <a target="" href="category.php?id=<?php echo $row["catid"] ?>"><img data-u="image" src="<?php echo $row["poster"] ?>" style="border-radius: 15%;" /></a>
            </div>
			
			<?php 
		}

	?>
			
        </div>
        <!-- Arrow Navigator -->
		<div data-u="arrowleft" class="jssora073" style="background: black; width:50px;height:400px;left:0;opacity: 0.75" data-autocenter="2">
        <div data-u="arrowleft" class="jssora073" style="background: black; width:50px;height:50px;top:175px;" data-autocenter="2" data-scale="0.75" data-scale-left="0.75">
            <svg viewbox="0 0 16000 16000" style="position:absolute;top:0;left:0;width:100%;height:100%;">
                <path class="a" d="M4037.7,8357.3l5891.8,5891.8c100.6,100.6,219.7,150.9,357.3,150.9s256.7-50.3,357.3-150.9 l1318.1-1318.1c100.6-100.6,150.9-219.7,150.9-357.3c0-137.6-50.3-256.7-150.9-357.3L7745.9,8000l4216.4-4216.4 c100.6-100.6,150.9-219.7,150.9-357.3c0-137.6-50.3-256.7-150.9-357.3l-1318.1-1318.1c-100.6-100.6-219.7-150.9-357.3-150.9 s-256.7,50.3-357.3,150.9L4037.7,7642.7c-100.6,100.6-150.9,219.7-150.9,357.3C3886.8,8137.6,3937.1,8256.7,4037.7,8357.3 L4037.7,8357.3z"></path>
            </svg>
        </div>
		</div>
		<div data-u="arrowright" class="jssora073" style="width:50px;height:400px;top:0px;right:0; opacity: 0.75; background: black" data-autocenter="2">
        <div data-u="arrowright" class="jssora073" style="width:50px;height:50px;top:175px;right:0;" data-autocenter="2" data-scale="0.75" data-scale-right="0.75">
            <svg viewbox="0 0 16000 16000" style="position:absolute;top:0;left:0;width:100%;height:100%;">
                <path class="a" d="M11962.3,8357.3l-5891.8,5891.8c-100.6,100.6-219.7,150.9-357.3,150.9s-256.7-50.3-357.3-150.9 L4037.7,12931c-100.6-100.6-150.9-219.7-150.9-357.3c0-137.6,50.3-256.7,150.9-357.3L8254.1,8000L4037.7,3783.6 c-100.6-100.6-150.9-219.7-150.9-357.3c0-137.6,50.3-256.7,150.9-357.3l1318.1-1318.1c100.6-100.6,219.7-150.9,357.3-150.9 s256.7,50.3,357.3,150.9l5891.8,5891.8c100.6,100.6,150.9,219.7,150.9,357.3C12113.2,8137.6,12062.9,8256.7,11962.3,8357.3 L11962.3,8357.3z"></path>
            </svg>
        </div>
		</div>
    </div>
	
	<div style="text-align: left; font-size: 15px; "><p></p><a target="" href="cate?type=<?php echo "anime" ?>" style="color: white;text-decoration: none;"><b>Anime</b></a></div>
	<div id="jssor_3" style="position:relative;margin:0 auto;top:0px;left:0px;width:1000px;height:400px;overflow:hidden;visibility:hidden;">
        <!-- Loading Screen -->
        <div data-u="loading" class="jssorl-009-spin" style="position:absolute;top:0px;left:0px;width:100%;height:100%;text-align:center;background-color:rgba(0,0,0,0.7);">
            <img style="margin-top:-19px;position:relative;top:50%;width:38px;height:38px;" src="//jssorcdn7.azureedge.net/theme/svg/loading/static-svg/spin.svg" />
        </div>
        <div data-u="slides" style="cursor:default;position:relative;top:0px;left:0px;width:1000px;height:400px;overflow:hidden;">
         
<?php		 
	$sql = "SELECT 
			MAX(`id`), `catid`, `poster` 
			FROM `posts` 
			WHERE 
			`type` LIKE '%anime%' 
			GROUP BY `catid` 
			ORDER BY MAX(`id`) DESC
			LIMIT $numberofpostsperpage 
			";
	$result = $dbconnect->query($sql);
		while ( $row = $result->fetch_assoc() )
		{
							?>
			
			<div>
                <a target="" href="category.php?id=<?php echo $row["catid"] ?>"><img data-u="image" src="<?php echo $row["poster"] ?>" style="border-radius: 15%;" /></a>
            </div>
			
			<?php 
		}

	?>
			
        </div>
        <!-- Arrow Navigator -->
		<div data-u="arrowleft" class="jssora073" style="background: black; width:50px;height:400px;left:0;opacity: 0.75" data-autocenter="2">
        <div data-u="arrowleft" class="jssora073" style="background: black; width:50px;height:50px;top:175px;" data-autocenter="2" data-scale="0.75" data-scale-left="0.75">
            <svg viewbox="0 0 16000 16000" style="position:absolute;top:0;left:0;width:100%;height:100%;">
                <path class="a" d="M4037.7,8357.3l5891.8,5891.8c100.6,100.6,219.7,150.9,357.3,150.9s256.7-50.3,357.3-150.9 l1318.1-1318.1c100.6-100.6,150.9-219.7,150.9-357.3c0-137.6-50.3-256.7-150.9-357.3L7745.9,8000l4216.4-4216.4 c100.6-100.6,150.9-219.7,150.9-357.3c0-137.6-50.3-256.7-150.9-357.3l-1318.1-1318.1c-100.6-100.6-219.7-150.9-357.3-150.9 s-256.7,50.3-357.3,150.9L4037.7,7642.7c-100.6,100.6-150.9,219.7-150.9,357.3C3886.8,8137.6,3937.1,8256.7,4037.7,8357.3 L4037.7,8357.3z"></path>
            </svg>
        </div>
		</div>
		<div data-u="arrowright" class="jssora073" style="width:50px;height:400px;top:0px;right:0; opacity: 0.75; background: black" data-autocenter="2">
        <div data-u="arrowright" class="jssora073" style="width:50px;height:50px;top:175px;right:0;" data-autocenter="2" data-scale="0.75" data-scale-right="0.75">
            <svg viewbox="0 0 16000 16000" style="position:absolute;top:0;left:0;width:100%;height:100%;">
                <path class="a" d="M11962.3,8357.3l-5891.8,5891.8c-100.6,100.6-219.7,150.9-357.3,150.9s-256.7-50.3-357.3-150.9 L4037.7,12931c-100.6-100.6-150.9-219.7-150.9-357.3c0-137.6,50.3-256.7,150.9-357.3L8254.1,8000L4037.7,3783.6 c-100.6-100.6-150.9-219.7-150.9-357.3c0-137.6,50.3-256.7,150.9-357.3l1318.1-1318.1c100.6-100.6,219.7-150.9,357.3-150.9 s256.7,50.3,357.3,150.9l5891.8,5891.8c100.6,100.6,150.9,219.7,150.9,357.3C12113.2,8137.6,12062.9,8256.7,11962.3,8357.3 L11962.3,8357.3z"></path>
            </svg>
        </div>
		</div>
    </div>
	
	<div style="text-align: left; font-size: 15px; "><p></p><a target="" href="cate?type=<?php echo "animov" ?>" style="color: white;text-decoration: none;"><b>Anime Movies</b></a></div>
	<div id="jssor_4" style="position:relative;margin:0 auto;top:0px;left:0px;width:1000px;height:400px;overflow:hidden;visibility:hidden;">
        <!-- Loading Screen -->
        <div data-u="loading" class="jssorl-009-spin" style="position:absolute;top:0px;left:0px;width:100%;height:100%;text-align:center;background-color:rgba(0,0,0,0.7);">
            <img style="margin-top:-19px;position:relative;top:50%;width:38px;height:38px;" src="//jssorcdn7.azureedge.net/theme/svg/loading/static-svg/spin.svg" />
        </div>
        <div data-u="slides" style="cursor:default;position:relative;top:0px;left:0px;width:1000px;height:400px;overflow:hidden;">
            
<?php		 
	$sql = "SELECT 
			MAX(`id`), `catid`, `poster` 
			FROM `posts` 
			WHERE 
			`type` LIKE '%animov%' 
			GROUP BY `catid` 
			ORDER BY MAX(`id`) DESC
			LIMIT $numberofpostsperpage 
			";
	$result = $dbconnect->query($sql);
		while ( $row = $result->fetch_assoc() )
		{
							?>
			
			<div>
                <a target="" href="category.php?id=<?php echo $row["catid"] ?>"><img data-u="image" src="<?php echo $row["poster"] ?>" style="border-radius: 15%;" /></a>
            </div>
			
			<?php 
		}

	?>
			
        </div>
        <!-- Arrow Navigator -->
		<div data-u="arrowleft" class="jssora073" style="background: black; width:50px;height:400px;left:0;opacity: 0.75" data-autocenter="2">
        <div data-u="arrowleft" class="jssora073" style="background: black; width:50px;height:50px;top:175px;" data-autocenter="2" data-scale="0.75" data-scale-left="0.75">
            <svg viewbox="0 0 16000 16000" style="position:absolute;top:0;left:0;width:100%;height:100%;">
                <path class="a" d="M4037.7,8357.3l5891.8,5891.8c100.6,100.6,219.7,150.9,357.3,150.9s256.7-50.3,357.3-150.9 l1318.1-1318.1c100.6-100.6,150.9-219.7,150.9-357.3c0-137.6-50.3-256.7-150.9-357.3L7745.9,8000l4216.4-4216.4 c100.6-100.6,150.9-219.7,150.9-357.3c0-137.6-50.3-256.7-150.9-357.3l-1318.1-1318.1c-100.6-100.6-219.7-150.9-357.3-150.9 s-256.7,50.3-357.3,150.9L4037.7,7642.7c-100.6,100.6-150.9,219.7-150.9,357.3C3886.8,8137.6,3937.1,8256.7,4037.7,8357.3 L4037.7,8357.3z"></path>
            </svg>
        </div>
		</div>
		<div data-u="arrowright" class="jssora073" style="width:50px;height:400px;top:0px;right:0; opacity: 0.75; background: black" data-autocenter="2">
        <div data-u="arrowright" class="jssora073" style="width:50px;height:50px;top:175px;right:0;" data-autocenter="2" data-scale="0.75" data-scale-right="0.75">
            <svg viewbox="0 0 16000 16000" style="position:absolute;top:0;left:0;width:100%;height:100%;">
                <path class="a" d="M11962.3,8357.3l-5891.8,5891.8c-100.6,100.6-219.7,150.9-357.3,150.9s-256.7-50.3-357.3-150.9 L4037.7,12931c-100.6-100.6-150.9-219.7-150.9-357.3c0-137.6,50.3-256.7,150.9-357.3L8254.1,8000L4037.7,3783.6 c-100.6-100.6-150.9-219.7-150.9-357.3c0-137.6,50.3-256.7,150.9-357.3l1318.1-1318.1c100.6-100.6,219.7-150.9,357.3-150.9 s256.7,50.3,357.3,150.9l5891.8,5891.8c100.6,100.6,150.9,219.7,150.9,357.3C12113.2,8137.6,12062.9,8256.7,11962.3,8357.3 L11962.3,8357.3z"></path>
            </svg>
        </div>
		</div>
    </div>
	
	<div style="text-align: left; font-size: 15px; "><p></p><a target="" href="cate?type=<?php echo "wrestling" ?>" style="color: white;text-decoration: none;"><b>Wrestling</b></a></div>
	<div id="jssor_5" style="position:relative;margin:0 auto;top:0px;left:0px;width:1000px;height:400px;overflow:hidden;visibility:hidden;">
        <!-- Loading Screen -->
        <div data-u="loading" class="jssorl-009-spin" style="position:absolute;top:0px;left:0px;width:100%;height:100%;text-align:center;background-color:rgba(0,0,0,0.7);">
            <img style="margin-top:-19px;position:relative;top:50%;width:38px;height:38px;" src="//jssorcdn7.azureedge.net/theme/svg/loading/static-svg/spin.svg" />
        </div>
        <div data-u="slides" style="cursor:default;position:relative;top:0px;left:0px;width:1000px;height:400px;overflow:hidden;">
            
<?php		 
	$sql = "SELECT 
			MAX(`id`), `catid`, `poster` 
			FROM `posts` 
			WHERE 
			`type` LIKE '%wrestling%' 
			GROUP BY `catid` 
			ORDER BY MAX(`id`) DESC
			LIMIT $numberofpostsperpage 
			";
	$result = $dbconnect->query($sql);
		while ( $row = $result->fetch_assoc() )
		{
							?>
			
			<div>
                <a target="" href="category.php?id=<?php echo $row["catid"] ?>"><img data-u="image" src="<?php echo $row["poster"] ?>" style="border-radius: 15%;" /></a>
            </div>
			
			<?php 
		}

	?>
			
        </div>
        <!-- Arrow Navigator -->
		<div data-u="arrowleft" class="jssora073" style="background: black; width:50px;height:400px;left:0;opacity: 0.75" data-autocenter="2">
        <div data-u="arrowleft" class="jssora073" style="background: black; width:50px;height:50px;top:175px;" data-autocenter="2" data-scale="0.75" data-scale-left="0.75">
            <svg viewbox="0 0 16000 16000" style="position:absolute;top:0;left:0;width:100%;height:100%;">
                <path class="a" d="M4037.7,8357.3l5891.8,5891.8c100.6,100.6,219.7,150.9,357.3,150.9s256.7-50.3,357.3-150.9 l1318.1-1318.1c100.6-100.6,150.9-219.7,150.9-357.3c0-137.6-50.3-256.7-150.9-357.3L7745.9,8000l4216.4-4216.4 c100.6-100.6,150.9-219.7,150.9-357.3c0-137.6-50.3-256.7-150.9-357.3l-1318.1-1318.1c-100.6-100.6-219.7-150.9-357.3-150.9 s-256.7,50.3-357.3,150.9L4037.7,7642.7c-100.6,100.6-150.9,219.7-150.9,357.3C3886.8,8137.6,3937.1,8256.7,4037.7,8357.3 L4037.7,8357.3z"></path>
            </svg>
        </div>
		</div>
		<div data-u="arrowright" class="jssora073" style="width:50px;height:400px;top:0px;right:0; opacity: 0.75; background: black" data-autocenter="2">
        <div data-u="arrowright" class="jssora073" style="width:50px;height:50px;top:175px;right:0;" data-autocenter="2" data-scale="0.75" data-scale-right="0.75">
            <svg viewbox="0 0 16000 16000" style="position:absolute;top:0;left:0;width:100%;height:100%;">
                <path class="a" d="M11962.3,8357.3l-5891.8,5891.8c-100.6,100.6-219.7,150.9-357.3,150.9s-256.7-50.3-357.3-150.9 L4037.7,12931c-100.6-100.6-150.9-219.7-150.9-357.3c0-137.6,50.3-256.7,150.9-357.3L8254.1,8000L4037.7,3783.6 c-100.6-100.6-150.9-219.7-150.9-357.3c0-137.6,50.3-256.7,150.9-357.3l1318.1-1318.1c100.6-100.6,219.7-150.9,357.3-150.9 s256.7,50.3,357.3,150.9l5891.8,5891.8c100.6,100.6,150.9,219.7,150.9,357.3C12113.2,8137.6,12062.9,8256.7,11962.3,8357.3 L11962.3,8357.3z"></path>
            </svg>
        </div>
		</div>
    </div>
	
	<script type="text/javascript">jssor_1_slider_init();</script>
    <script type="text/javascript">jssor_2_slider_init();</script>
	<script type="text/javascript">jssor_3_slider_init();</script>
	<script type="text/javascript">jssor_4_slider_init();</script>
	<script type="text/javascript">jssor_5_slider_init();</script>
	<script type="text/javascript">jssor_7_slider_init();</script>
		
<!-- footer begins -->
</div>	
</div>
<div style="padding-bottom: 15%;"></div>

<?php 

if ( !isset ($_SESSION["username"]) || $_SESSION["username"] != "admin")
{
	?>
<footer class="w3-container w3-cell-row" style="border-top-left-radius: 20px;border-top-right-radius: 20px;position: fixed;bottom: 0;width: 100%;background-color: #2E2E2E;color: white;text-align: center; max-width: 1300px;">
  <div class="w3-cell-row" style="width: 100%; padding-top: 3px">
	  
	  <div align="center" class="w3-cell" style="width: 16.6%;">
	  <a href="index.php"><img src="images/home1.png" style="width: 25px;"></a><div style="font-size: 10px">Home</div>
	  </div>
	  
	  <div align="center" class="w3-cell" style="width: 16.6%;">
	  <a href="cate?type=movie"><img src="images/movies.png" style="width: 25px;"></a><div style="font-size: 10px">Movies</div>
	  </div>
	  
	  <div align="center" class="w3-cell" style="width: 16.6%;">
	  <a href="cate?type=tv-show"><img src="images/tvshows.png" style="width: 25px;"></a><div style="font-size: 10px">TV-Shows</div>
	  </div>
	  
	  <div align="center" class="w3-cell" style="width: 16.6%;">
	  <a href="cate?type=anime"><img src="images/animes.png" style="width: 25px;"></a><div style="font-size: 10px">Animes</div>
	  </div>
	  
	  <div align="center" class="w3-cell" style="">
	  <a href="cate?type=wrestling"><img src="images/wrestling.png" style="width: 25px;"></a><div style="font-size: 10px">Wrestling</div>
	  </div>
	  
	  <div align="center" class="w3-cell" style="width: 16.6%;">
	  <a href="profile.php"><img src="images/profile1.png" style="width: 30px;"></a><div style="font-size: 10px">Profile</div>
	  </div>
	  
	</div>
</footer>

<?php 
}
elseif ($_SESSION["username"] == "admin")
{
	?>
<footer class="w3-container w3-cell-row" style="border-top-left-radius: 20px;border-top-right-radius: 20px;position: fixed;bottom: 0;width: 100%;background-color: #2E2E2E;color: white;text-align: center; max-width: 1300px;">
  <div class="w3-cell-row" style="width: 100%; padding-top: 3px">
	  
	  <div align="center" class="w3-cell" style="width: 16.6%;">
	  <a href="addcategory1.php"><img src="images/imdb21.png" style="width: 45px;"></a><div style="font-size: 10px">Add IMDB</div>
	  </div>
	  
	  <div align="center" class="w3-cell" style="width: 16.6%;">
	  <a href="addcategory.php"><img src="images/addcat.png" style="width: 25px;"></a><div style="font-size: 10px">Category</div>
	  </div>
	  
	  <div align="center" class="w3-cell" style="width: 16.6%;">
	  <a href="index.php"><img src="images/home1.png" style="width: 25px;"></a><div style="font-size: 10px">Home</div>
	  </div>
	  
	  <div align="center" class="w3-cell" style="width: 16.6%;">
	  <a href="profile.php"><img src="images/profile1.png" style="width: 30px;"></a><div style="font-size: 10px">Profile</div>
	  </div>
	  
	  <div align="center" class="w3-cell" style="width: 16.6%;">
	  <a href="https://tryq8flix.com/getlinks.php"><img src="https://i.imgur.com/3qawygU.png" style="width: 25px;"></a><div style="font-size: 10px">Grabber</div>
	  </div>
	  
	  <div align="center" class="w3-cell" style="">
	  <a href="logout.php"><img src="images/logout1.png" style="width: 25px;"></a><div style="font-size: 10px">Exit</div>
	  </div>
	  
	</div>
</footer>

<?php
}
?>
</div>
<script>
  function initFreshChat() {
    window.fcWidget.init({
      token: "91daf21e-6eb5-42ee-92cd-f167a2d6b9de",
      host: "https://wchat.freshchat.com"
    });
  }
  function initialize(i,t){var e;i.getElementById(t)?initFreshChat():((e=i.createElement("script")).id=t,e.async=!0,e.src="https://wchat.freshchat.com/js/widget.js",e.onload=initFreshChat,i.head.appendChild(e))}function initiateCall(){initialize(document,"freshchat-js-sdk")}window.addEventListener?window.addEventListener("load",initiateCall,!1):window.attachEvent("load",initiateCall,!1);
</script>
</body>
</html>