  <script>
if ( screen.width < 699 ){
	document.cookie = "screenWidth="+screen.width;
}else{
	document.cookie = "screenWidth="+screen.width+"; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
}
</script>

<?php
if ( isset($_COOKIE["screenWidth"]) AND $_COOKIE["screenWidth"] < 699 ){
	$mobileWidth = 1;
}
?>
<header class='w3-container' style="max-width: 1300px;background: #2E2E2E;position: fixed; z-index: 1; width: 100%;">

<table align="center" border="0" width="100%">
<tr>
<td align="left">
<span onclick="openNav()"><img src="images/menu.png" width="25px"></span>
</td>
<td align="center">
<center><a href="index.php">TRYQ8FLiX<?php //<img src="https://i.imgur.com/78FTnsf.png"style="height: 35px;"> ?></a></center>
</td>
<td align="right">
<a href="<?php if ( !isset($mobileWidth) ) { echo "search.php";}else{ echo "search1.php";} ?>"><img src="images/search1.png" width="25px"></a>
</td>
</tr>
</table>
</header>
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
  <form method="get" action="search1.php">
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
	<a href="search.php">Search</a>
	<a href="flix">TV</a>
	<a href="deadLinks.php">Dead Links</a>
	<a href="fix.php">Fix Links</a>
	<a href="viewrequests.php">Requests</a>
	<a href="viewreports.php">Reports</a>
	<a href="getlinks.php">Link Grapper</a>
    <a href="uptoboxuploader.php">Upload Files</a>
	<a href="anime.php">Animes Series</a>
	<a href="animemovies.php">Anime Movies</a>
	<a href="newmovies.php">Movies</a>
	<a href="tvshow.php">Tv-Shows</a>
	<a href="wrestling.php">Wrestling</a>
	<a href="autoaddmovies.php">Add Movies TUK</a>
	<a href="autoPostEgydead.php">Add Movies EGY</a>
	<a href="autopostcategory.php">Auto Post Movies</a>
	<a href="latest.php">New Additions</a>
	<a href="renamefiles.php">Rename Files</a>
	<a href="check.php">Check Video</a>
	<a href="index.php">Home</a>
	<a href="categorieslist.php">Categories</a>
	<a href="addcategory.php">New Category</a>
	<a href="addcategory1.php">IMDb Category</a>
	<a href="profile.php">Profile</a>
	<a href="request.php">Create Request</a>
	<a href="logout.php">Logout</a>
</div>

<?php
}else{
?>
<div id="mySidenav" class="sidenav" >
	<a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
	<a href="index.php">Home</a>
	<a href="flix">TV</a>
	<a href="profile.php">Profile</a>
  	<a href="request.php">Request</a>
  	<a href="search1.php">Search</a>
	<?php
	if ( !isset($mobileWidth) ){
		?>
	<a href="anime.php">Animes Series</a>
	<a href="animemovies.php">Anime Movies</a>
	<a href="newmovies.php">Movies</a>
	<a href="tvshow.php">Tv-Shows</a>
	<a href="wrestling.php">Wrestling</a>
		<?php
	}else{
		?>
	<a href="cate?s=0&type=anime">Animes Series</a>
	<a href="cate?s=0&type=animov">Anime Movies</a>
	<a href="cate?s=0&type=movie">Movies</a>
	<a href="cate?s=0&type=tv-show">Tv-Shows</a>
	<a href="cate?s=0&type=wrestling">Wrestling</a>
		<?php
	}
	?>
  	
	<a href="logout.php">Logout</a>
</div>

<?php
}
?>

<!-- Use any element to open the sidenav -->