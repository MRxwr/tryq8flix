<?php
require('includes/config.php');
require('includes/checksouthead.php');
require('new/functions.php');

$sql = "SELECT *
		FROM `notificationnew`
		WHERE
		`userid` LIKE '".$userID."'
		AND
		`status` LIKE 'unseen'
		ORDER BY `id` DESC
		";
$result = $dbconnect->query($sql);
$notiCount = $result->num_rows;
// generating a random header
$sql = "SELECT
		c.*, c.id as categoryId, f.id as realId
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
$adminFav = $row["realId"];
$categoryId = $row["categoryId"]
?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" href="images/logo.png">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>

    <title>TRYQ8FLiX</title>
	
	<?php require('new/style.php'); ?>
  </head>
  <script type="text/javascript">
	if (screen.width <= 699) {
		document.location = "v2.php";
	}
	</script>
  <body>
    
	
	<div class="container-fluid">
	
	<header class="sticky-top" style="background-color: #2E2E2E;color: white;min-height:50px ;box-shadow: 0px 0px 5px #4f4f4f;min-width:100%">

	<div class="container-fluid">
	<div class="row">
	<div class="col-2 col-sm-2 col-md-2 p-2 pl-3 mt-1">
	<span onclick="openNav()"><img src="images/menu.png" width="25px"></span>
	</div>
	<div class="col-5 col-sm-6 col-md-8 p-1 mt-1">
	<center><a href="index.php"><img src="https://i.imgur.com/78FTnsf.png" style="height: 35px;"></a></center>
	</div>
	<div class="col-3 col-sm-2 col-md-1 p-1 mt-2" style="text-align: -webkit-right;">
	<a data-toggle="modal" data-target="#notifications" ><img src="https://i.imgur.com/6B5CrCq.png" width="25px" class="ml-2" <?php if ($notiCount < 1 ) { echo "style='filter: grayscale(1);'" ;} ?> ></a>
	</div>
	<div class="col-2 col-sm-2 col-md-1 p-1 mt-2" style="text-align: -webkit-center;">
	<a data-toggle="modal" data-target="#TryQ8FliXModalSearch" class="searchButton" style="display:none"><img src="images/search1.png" width="25px"></a>
	<a href="search.php" class="searchButton"><img src="images/search1.png" width="25px"></a>
	</div>
	</div>
	</div>
	</header>
	
<script>
/* Set the width of the side navigation to 250px */
function openNav() {
    document.getElementById("mySidenav").style.width = "70%";
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
    z-index: 100000; /* Stay on top */
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
if ( !isset ($username) ) 
{
?>

 <div id="mySidenav" class="sidenav" >
  	<a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
  	<a href="login.php">Login</a>
  	<a href="register.php">Register</a>
</div>

<?php
}
elseif ( isset($username) AND in_array($username,$usernames) )
{
?>
<div id="mySidenav" class="sidenav" >
	<a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
	<a href="viewrequests.php">Requests</a>
	<a href="viewreports.php">Reports</a>
	<a href="autoaddmovies.php">Add Movies</a>
	<a href="autopostcategory.php">Auto Post Movies</a>
	<a href="latest.php">New Additions</a>
	<a href="renamefiles.php">Rename Files</a>
    <a href="getlinks.php">Link Grapper</a>
    <a href="uptoboxuploader.php">Upload Files</a>
	<a href="check.php">Check Video</a>
	<a href="index.php">Home</a>
	<a href="categorieslist.php">Categories</a>
	<a href="addcategory.php">New Category</a>
	<a href="addcategory1.php">IMDb Category</a>
	<a href="profile.php">Profile</a>
	<a href="request.php">Create Request</a>
	<a href="cate?s=0&type=anime">Animes Series</a>
	<a href="cate?s=0&type=animov">Anime Movies</a>
	<a href="cate?s=0&type=movie">Movies</a>
	<a href="cate?s=0&type=tv-show">Tv-Shows</a>
	<a href="cate?s=0&type=wrestling">Wrestling</a>
	<a href="search.php">Search</a>
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
	<a href="profile.php">Profile</a>
  	<a href="request.php">Request</a>
  	<a href="search.php">Search</a>
	<a href="cate?s=0&type=anime">Animes Series</a>
	<a href="cate?s=0&type=animov">Anime Movies</a>
	<a href="cate?s=0&type=movie">Movies</a>
	<a href="cate?s=0&type=tv-show">Tv-Shows</a>
	<a href="cate?s=0&type=wrestling">Wrestling</a>
	<a href="logout.php">Logout</a>
</div>

<?php
}
?>