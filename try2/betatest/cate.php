<?php 
require ('includes/config.php');
require ('includes/checksouthead.php');

$numberOfPosts = 24 ;

$sql = "SELECT
		*
		FROM
		`category`
		WHERE
		`type` LIKE '".$_GET["type"]."'
		";
$result = $dbconnect->query($sql);
$numberOfPages = $result->num_rows / $numberOfPosts;

if ( !isset($_GET["s"]) ){
	$startPage = 0;
	$nextPage =1;
	$preDisable = 0;
}else{
	$startPage = ($_GET["s"] * $numberOfPosts);
	$nextPage = $_GET["s"] + 1;
	$prePage = $_GET["s"] - 1;
	if ( $prePage < 0 ){
		$preDisable = 0;
	}
	if ( $nextPage > floor($numberOfPages)  ){
		$nextDisable = 0;
	}
}

$link = "type=".$_GET["type"];

if ( isset($_GET["selection"]) AND $_GET["selection"] == "views" ){
	$link .= "&selection=views";
}

if ( isset($_GET["language"]) ){
	$link .= "&language=" . $_GET["language"];
}

if ( isset($_GET["genre"]) ){
	$link .= "&genre=" . $_GET["genre"];
}

if ( isset($_GET["selection"]) AND $_GET["selection"] == "rating" ){
	$link .= "&selection=rating";
}

if ( isset($_GET["selection"]) AND $_GET["selection"] == "new" ){
	$link .= "&selection=new";
}

if ( isset($_GET["arrang"]) ){
	$link .= "&arrang=" . $_GET["arrang"];
}else{
	$_GET["arrang"] = "DESC";
}

?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="icon" href="images/logo.png">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

    <title>TRYQ8FLiX</title>
	
	<style>
	body{
		background-color:#181818;
		color:white;
		font-size:22px;
		max-width: 1280px;
		margin: 0 auto;
	}
	@media only screen and (max-width: 1280px ) {
		body{
			font-size:18px;
		}
	}
	.img-hovering:hover {
		box-shadow: 0 .5rem 1rem rgba(219,198,150,.3)!important;
		border-radius: .25rem!important;
		background-color: #fff!important;
	}
	.img-fluid{
		height: 300px;
		width: 100%;
	}
	@media only screen and (max-width: 1280px ) {
			.img-fluid{
				height: 250px;
				width: 100%;
			}
		}
	.img-fluid:hover {
		border: 1px;
		border-color: grey;
		border-style: solid;
	}
	.modal-content{
		background-color:#212121;
		color:white;
	}
	.page-link{
	color: #bdbdbd;
    background-color: #2e2e2e;
    border: 1px solid #000000;
	}
	.page-link:hover{
		color: orange;
		background-color: #515151;
		border: 1px solid #000000;
	}
	.page-item.disabled .page-link {
		background-color: #2e2e2e;
		border-color: #00060c;
	}
	</style>
	
	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
	<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>

	
  </head>
  <body>
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
	<a data-toggle="modal" data-target="#TryQ8FliXModalFilter" ><img src="https://i.imgur.com/osIDBxz.png" width="25px" class="mr-2"></a>
	<a data-toggle="modal" data-target="#notifications" ><img src="https://i.imgur.com/6B5CrCq.png" width="25px" class="ml-2"></a>
	</div>
	<div class="col-2 col-sm-2 col-md-1 p-1 mt-2" style="text-align: -webkit-center;">
	<a data-toggle="modal" data-target="#TryQ8FliXModalSearch" class="searchButton"><img src="images/search1.png" width="25px"></a>
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
.box1 img,.box1:after,.box1:before{width:100%;transition:all .3s ease 0s}
.box1 .icon,.box2,.box3,.box4,.box5 .icon li a{text-align:center}
.box10:after,.box10:before,.box1:after,.box1:before,.box2 .inner-content:after,.box3:after,.box3:before,.box4:before,.box5:after,.box5:before,.box6:after,.box7:after,.box7:before{content:""}
.box1,.box11,.box12,.box13,.box14,.box16,.box17,.box18,.box2,.box20,.box21,.box3,.box4,.box5,.box5 .icon li a,.box6,.box7,.box8{overflow:hidden}
.box1 .title,.box10 .title,.box4 .title,.box7 .title{letter-spacing:1px}
.box3 .post,.box4 .post,.box5 .post,.box7 .post{font-style:italic}
.mt-30{margin-top:30px}
.mt-40{margin-top:40px}
.mb-30{margin-bottom:30px}
.box1 .icon,.box1 .title{margin:0;position:absolute}
.box1{box-shadow:0 0 3px rgba(0,0,0,.3);position:relative}
.box1:after,.box1:before{height:50%;background:rgba(0,0,0,.5);position:absolute;top:0;left:0;z-index:1;transform-origin:100% 0;transform:rotateZ(90deg)}
.box1:after{top:auto;bottom:0;transform-origin:0 100%}
.box1:hover:after,.box1:hover:before{transform:rotateZ(0)}
.box1 img{height:auto;transform:scale(1) rotate(0)}
.box1:hover img{filter:sepia(80%);transform:scale(1.3) rotate(10deg)}
.box1 .title{font-size:19px;font-weight:600;color:#fff;text-transform:uppercase;text-shadow:0 0 1px #004cbf;bottom:10px;left:10px;opacity:0;z-index:2;transform:scale(0);transition:all .5s ease .2s}
.box1:hover .title{opacity:1;transform:scale(1)}
.box1 .icon{padding:7px 5px;list-style:none;background:#004cbf;border-radius:0 0 0 10px;top:-100%;right:0;z-index:2;transition:all .3s ease .2s}
.box1:hover .icon{top:0}
.box1 .icon li{display:block;margin:10px 0}
.box1 .icon li a{display:block;width:35px;height:35px;line-height:35px;border-radius:10px;font-size:18px;color:#fff;transition:all .3s ease 0s}
.box2 .icon li a,.box3 .icon a:hover,.box4 .icon li a:hover,.box5 .icon li a,.box6 .icon li a{border-radius:50%}
.box1 .icon li a:hover{color:#fff;box-shadow:0 0 10px #000 inset,0 0 0 3px #fff}
@media only screen and (max-width:990px){.box1{margin-bottom:30px}
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
elseif ( $username == "admin" )
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
  
  <div class="pt-2"></div>
  <!--<div class="container-fluid">
  <div class="row">
	<div class="col text-center">
		<a  class="btn btn-warning" data-toggle="modal" data-target="#TryQ8FliXModalFilter" ><b>Filter / Sort<b></a>
	</div>
  </div>
  </div>-->
  
    <div class="container-fluid d-flex m-0 p-0">
      <div class="row justify-content-center w-100 m-0 mt-1">
        
		<?php 
		require('includes/filters.php');
		$result = $dbconnect->query($sql);
		$numRows = $result->num_rows;
		while ( $row = $result->fetch_assoc() ){
		?>
        <!--<div class="col-6 col-sm-4 col-md-3 col-lg-2 text-center m-0 p-2">
          <a data-toggle="modal" data-target="#TryQ8FliXModal" class="flixMovie" id="<?php if ( isset($row["catid"]) ){ echo $row["catid"];}else{echo $row["id"];} ?>"><img src="<?php echo $row["poster"] ?>" class="img-fluid img-hovering rounded"></a>
		  <?php if ( isset($_GET["views"]) ){ echo "<b>Views:</b> " . $row["tview"]; }elseif ( isset($_GET["rating"]) ){ echo "<b style='color:yellow' >IMDb:</b> " . $row["imdbrating"]; }elseif ( isset($_GET["new"]) ){ echo "<b>Year:</b> " . $row["releasedate"]; }else{ /*echo $row["title"];*/} ?>
        </div>-->
		
		<div class="col-md-2 col-xs-6 col-sm-6 col-6 p-3">
		<a data-toggle="modal" data-target="#TryQ8FliXModal" class="flixMovie" id="<?php if ( isset($row["catid"]) ){ echo $row["catid"];}else{echo $row["id"];} ?>">
			<div class="box1">
				<img src="<?php echo $row["poster"] ?>" style="height: 250px;">
				<h3 class="title"><?php echo $row["category"] . " " . $row["title"] ?></h3>
			</div>
			</a>
		</div>
		
		<?php
		}
		?>

      </div>
	</div>

<?php
if ( $numRows < $numberOfPosts ){
	$nextDisable = 1;
}
?>	
	<div class="container-fluid d-flex m-0 p-0">
      <div class="row justify-content-center w-100 m-0 mt-1">	
		<div class="col" >
		<nav aria-label="Page navigation example">
		  <ul class="pagination justify-content-center">
		    <li class="page-item"><a class="page-link" href="?s=
			<?php echo 0 . "&" . $link ?>"><<</a></li>
			<li class="page-item <?php if (isset($preDisable) ){ echo "disabled";}?>"><a class="page-link" href="?s=<?php echo $prePage ?>&<?php echo $link ?>" ><</a>
			</li>
	<?php 
	if ( !isset($_GET["s"]) OR $_GET["s"] == 0){
		?>
			<li class="page-item <?php if(isset($nextDisable)){echo "disabled";} ?>"><a class="page-link" href="?s=1&<?php echo $link ?>">1</a></li>
			<li class="page-item <?php if(isset($nextDisable)){echo "disabled";} ?>"><a class="page-link" href="?s=2&<?php echo $link ?>">2</a></li>
			<li class="page-item <?php if(isset($nextDisable)){echo "disabled";} ?>"><a class="page-link" href="?s=3&<?php echo $link ?>">3</a></li>
	<?php
	}else{
		?>
			<li class="page-item"><a class="page-link" href="?s=<?php echo $_GET["s"]-1;?>&<?php echo $link ?>"><?php echo $_GET["s"]-1 ?></a></li>
			<li class="page-item disabled"><a class="page-link" href="?s=<?php echo $_GET["s"] ?>&<?php echo $link ?>"><?php echo $_GET["s"] ?></a></li>
			<li class="page-item <?php if(isset($nextDisable)){echo "disabled";} ?>"><a class="page-link" href="?s=<?php echo $_GET["s"]+1 ?>&<?php echo $link ?>"><?php echo $_GET["s"]+1 ?></a></li>
		<?php
	}
	?>
	<li class="page-item <?php if(isset($nextDisable)){echo "disabled";} ?>"><a class="page-link" href="?s=<?php echo $nextPage ?>&<?php echo $link ?>">></a>
			</li>
	<li class="page-item"><a class="page-link" href="?s=
			<?php
				if ( strtolower($_GET["type"]) == "movie" ){
					echo (int)$numberOfPages-4 . "&" . $link;
				}elseif( strtolower($_GET["type"]) == "tv-show" ){
					echo (int)$numberOfPages-2 . "&" . $link;
				}elseif( strtolower($_GET["type"]) == "anime" ){
					echo (int)$numberOfPages-1 . "&" . $link;
				}elseif( strtolower($_GET["type"]) == "animov" ){
					echo (int)$numberOfPages-1 . "&" . $link;
				}elseif( strtolower($_GET["type"]) == "wrestling" ){
					echo (int)$numberOfPages-1 . "&" . $link;
				}
			?>">>></a></li>
		  </ul>
		</nav>
		</div>
	  </div>
	</div>

	<!-- details -->
    <div class="modal fade bd-example-modal-lg" id="TryQ8FliXModal" tabindex="-1" aria-labelledby="myLargeModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" >
          <div class="modal-header">
		  <div class="container-fluid">
			<div class="row p-0">
				<div class="col">
					<h5 class="modal-title" id="exampleModalLabel"></h5> <span class="rating"></span> | <span class="duration"></span> | <span class="genre"></span> | <span class="year"></span>
					<button type="button" class="close p-0" data-dismiss="modal" aria-label="Close">
					  <span aria-hidden="true" style="color:white">&times;</span>
					</button>
				</div>
			</div>
			<div class="row">
				<div class="col pt-0 mt-0">
				
				</div>
			</div>
		  </div>
            
          </div>
          <div class="modal-body">
			<div class="row mb-3">
				<div class="col-6 col-sm-6 col-md-4">
					<img src="http://www.google.com" class="img-fluid poster rounded" style="/*height:350px*/">
				</div>
				<div class="col-6 col-sm-6 col-md-8">
				<b style="color:orange">Notes:</b> <span class="notes"></span><br>
				<b style="color:yellow">IMDb:</b> <span class="imdb"></span><br>
				<b>Country:</b> <span class="country"></span><br>
				<b>Language:</b> <span class="language"></span><br>
				<b>Cast:</b> <span class="channel"></span><br>
				</div>
			</div>
			<div class="row">
				  <div class="col" style="text-align: justify;">
					<span class="description"></span>
				  </div>
			</div>
          </div>
          <div class="modal-footer">
		  <?php
	if ( !isset($mobileWidth) ){
		?>
		<a  class="btn btn-warning MovieProfile flixCateHref" href="" id="" ><b>Start watching<b></a>
		<?php
	}else{
		?>
		<a  class="btn btn-warning MovieProfile flixCate" data-toggle="modal" data-target="#TryQ8FliXModalLinks" id="" ><b>Start watching<b></a>
		<?php
	}
	?>
				
				<a  class="btn btn-warning" data-toggle="modal" data-target="#TryQ8FliXModalTrailer" ><b>Trailer<b></a>
				
				<a class="btn btn-warning favoCate" id=""><b>Favourite<b></a>
          </div>
        </div>
      </div>
    </div>
	
	
	<!-- Search -->
	<div class="modal fade bd-example-modal-lg" id="TryQ8FliXModalSearch" tabindex="-1" aria-labelledby="myLargeModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" style="
	  position: fixed;
	  margin: 0;
	  min-width: 100%;
	  min-height: 100%;
	  padding: 0;
	  "
	  >
        <div class="modal-content" 
		style="
		position: fixed;
		top: 0;
		right: 0;
		bottom: 0;
		left: 0;
		overflow: hidden;
		"
		>
          <div class="modal-header" style="border-bottom:0px">
		  <div class="container-fluid">
			<div class="row p-0">
				<div class="col">
					<button type="button" class="close p-0" data-dismiss="modal" aria-label="Close">
					  <span aria-hidden="true" style="color:white">&times;</span>
					</button>
				</div>
			</div>
			<div class="row">
				<div class="col pt-0 mt-0">
				
				</div>
			</div>
		  </div>
            
          </div>
          <div class="modal-body">
			<div class="row">
				<div class="col">
					<div class="embed-responsive embed-responsive-16by9" style="min-height:90vh;">
						<iframe class="embed-responsive-item searchMovies" src="" allowfullscreen style="min-height:90vh;" ></iframe>
					</div>
				</div>
			</div>
          </div>
        </div>
      </div>
    </div>
	
	<style>
	.embed-responsive-16by9::before {
    padding-top: 0px;
}
	</style>
	
	<!-- Links -->
	<div class="modal fade bd-example-modal-lg" id="TryQ8FliXModalLinks" tabindex="-1" aria-labelledby="myLargeModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" >
          <div class="modal-header" style="border-bottom:0px">
		  <div class="container-fluid">
			<div class="row p-0">
				<div class="col">
					<button type="button" class="close p-0" data-dismiss="modal" aria-label="Close">
					  <span aria-hidden="true" style="color:white">&times;</span>
					</button>
				</div>
			</div>
			<div class="row">
				<div class="col pt-0 mt-0">
				
				</div>
			</div>
		  </div>
            
          </div>
          <div class="modal-body">
			<div class="row">
				<div class="col">
					<div class="embed-responsive embed-responsive-16by9 postLink" style="">
						<!--<iframe class="embed-responsive-item postLink" src="" allowfullscreen style="min-height:350px;" ></iframe>-->
					</div>
				</div>
			</div>
          </div>
        </div>
      </div>
    </div>
	
	<!-- trailer -->
	<div class="modal fade " id="TryQ8FliXModalTrailer" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
		<div class="modal-header" style="border-bottom:0px">
		  <div class="container-fluid">
			<div class="row p-0">
				<div class="col">
					<button type="button" class="close p-0" data-dismiss="modal" aria-label="Close">
					  <span aria-hidden="true" style="color:white">&times;</span>
					</button>
				</div>
			</div>
		  </div>
        </div>
          <div class="modal-body m-0 p-0">
		  <div class="embed-responsive embed-responsive-16by9">
			<iframe class="embed-responsive-item MovieTrailer" src="" allowfullscreen style="position:relative ;"></iframe>
		  </div>
          </div>
        </div>
      </div>
    </div>
	
	<!-- trailer -->
	<div class="modal fade " id="notifications" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="min-height: 350px;">
		<div class="modal-header" style="border-bottom:0px">
		  <div class="container-fluid">
			<div class="row p-0">
				<div class="col">
					<span>Notifications</span>
				</div>
				<div class="col">
					<button type="button" class="close p-0" data-dismiss="modal" aria-label="Close">
					  <span aria-hidden="true" style="color:white">&times;</span>
					</button>
				</div>
			</div>
		  </div>
        </div>
          <div class="modal-body m-0 p-0">
		  <div class="embed-responsive embed-responsive-16by9">
			<iframe class="embed-responsive-item " src="live/notifications_update.php" allowfullscreen style="position:relative ;min-height: 350px;"></iframe>
		  </div>
          </div>
		  
		  <div class="modal-footer">

          </div>
		  
        </div>
      </div>
    </div>
	
	<!-- filters -->
	<div class="modal fade bd-example-modal-lg" id="TryQ8FliXModalFilter" tabindex="-1" aria-labelledby="myLargeModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
	  <form action="" method="GET">
	  <input type="hidden" name="s" value="0"> 
	  <input type="hidden" name="type" value="<?php echo $_GET["type"] ?>">
        <div class="modal-content" >
          <div class="modal-header">
            <h5 class="modal-title1" id="exampleModalLabel">Filter Results</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true" style="color:white">&times;</span>
            </button>
          </div>
          <div class="modal-body" style="max-height:500px">
			<div class="row">
				<div class="col-12 col-sm-6 col-md-6 mb-3">
				<span style="color:gold">Select</span>
					<select name="selection" class="custom-select">
					<option value="none">None</option>
					<option value="rating">IMDb</option>
					<option value="new">Year</option>
					<option value="views">Views</option>
					</select>
				</div>
			
				<div class="col-12 col-sm-6 col-md-6 mb-3"> 
				<span style="color:gold">Sort</span>
					<select name="arrang" class="custom-select">
					<option value="DESC">Descending</option>
					<option value="ASC">Ascending</option>
					</select>
				</div>
			</div>
			
			
			<div class="row">
				<div class="col-12 col-sm-6 col-md-6 mb-3">
				<span style="color:gold">Genres</span>
				<select name="genre" class="custom-select">
					<option value="none">None</option>
				<?php
				$allGens = array();
				
				$sql = "SELECT
						genre
						FROM
						`category`
						WHERE
						`type` LIKE '".$_GET["type"]."'
						";
				$result = $dbconnect->query($sql);
				while ( $row = $result->fetch_assoc() ){
					$genres = explode(",",$row["genre"]);
					for ( $i = 0 ; $i < sizeof($genres) ; $i++ ){
						if (
							!in_array(strtolower(str_replace(" ","",$genres[$i])),$allGens) AND
							!empty(str_replace(" ","",$genres[$i])) AND 
							str_replace(" ","",$genres[$i]) != "N/A"
							){
						$allGens[] = strtolower(str_replace(" ","",$genres[$i]));
						$getGenres[] = ltrim($genres[$i]);
						}
					}
				}
				sort($getGenres);
				for ( $i = 0 ; $i < sizeof($getGenres) ; $i++ ){
					?>
					<option value="<?php echo $getGenres[$i] ?>"><?php echo $getGenres[$i] ?></option>
					<?php
				}
				?>
				</select>
				</div>
				<div class="col-12 col-sm-6 col-md-6 mb-3">
				<span style="color:gold">Languages</span>
				<select name="language" class="custom-select">
					<option value="none">None</option>
				<?php
				$allLangs = array();
				
				$sql = "SELECT
						language
						FROM
						`category`
						WHERE
						`type` LIKE '".$_GET["type"]."'
						";
				$result = $dbconnect->query($sql);
				while ( $row = $result->fetch_assoc() ){
					$languages = explode(",",$row["language"]);
					for ( $i = 0 ; $i < sizeof($languages) ; $i++ ){
						if (
							!in_array(strtolower(str_replace(" ","",$languages[$i])),$allLangs) AND
							!empty(str_replace(" ","",$languages[$i])) AND
							str_replace(" ","",$languages[$i]) != "N/A"
							){
								$allLangs[] = strtolower(str_replace(" ","",$languages[$i]));
								$getLang[] = ltrim($languages[$i]);
						}
					}
				}
				sort($getLang);
				for ( $i = 0 ; $i < sizeof($getLang) ; $i++ ){
					?>
					<option value="<?php echo $getLang[$i] ?>"><?php echo $getLang[$i] ?></option>
					<?php
				}
				?>
				</select>
				</div>
			</div>
          </div>
          <div class="modal-footer">
				<input type="submit" value="Submit" class="btn btn-warning">
          </div>
        </div>
		</form>
      </div>
    </div>
	
	<script type="text/javascript">
	$(function(){
		$('.flixMovie').click(function(e){
			e.preventDefault();
			flixMovieID = $(this).attr('id');
			$.ajax({
				type:"POST",
				url: "api/functions.php",
				data: {
					bringData: flixMovieID,
				},
				success:function(result){
					var MovieDetails = result.split('^');
					$('.modal-title').html(MovieDetails[0]);
					$('.poster').attr('src',MovieDetails[9]);
					$('.rating').html(MovieDetails[1]);
					$('.imdb').html(MovieDetails[2]);
					$('.year').html(MovieDetails[5]);
					$('.genre').html(MovieDetails[4]);
					$('.country').html(MovieDetails[7]);
					$('.channel').html(MovieDetails[8]);
					$('.notes').html(MovieDetails[13]);
					$('.description').html(MovieDetails[10]);
					$('.duration').html(MovieDetails[3]);
					$('.language').html(MovieDetails[6]);
					$('.flixCate').attr('id',MovieDetails[12]);
					$('.favoCate').attr('id',MovieDetails[12]);
					if ( MovieDetails[16] == 1 ){
						$('.favoCate').removeClass('btn-warning');
						$('.favoCate').addClass('btn-success');
					}else{
						$('.favoCate').removeClass('btn-success');
						$('.favoCate').addClass('btn-warning');
					}
					$('.flixCateHref').attr('href',"category.php?id="+MovieDetails[12]);
					$('.MovieTrailer').attr('src',MovieDetails[11]);
				}
			});
		});
	});
	
	$(function(){
		$('.flixCate').click(function(e){
			e.preventDefault();
			catID = $(this).attr('id');
			//$('.postLink').attr('src',"showPosts.php?id="+catID);
			
			$.ajax({
				type:"GET",
				url: "showPosts.php",
				data: {
					id: catID,
				},
				success:function(result){
					$('.postLink').html(result);
				}
			});
			
		});
	});
	
	$(function(){
		$('.favoCate').click(function(e){
			e.preventDefault();
			catID = $(this).attr('id');
			
			$.ajax({
				type:"POST",
				url: "live/cate.favo.php",
				data: {
					id: catID,
				},
				success:function(result){
					if ( $('.favoCate').hasClass('btn-warning') ){
						$('.favoCate').removeClass('btn-warning');
						$('.favoCate').addClass('btn-success');
					}else {
						$('.favoCate').removeClass('btn-success');
						$('.favoCate').addClass('btn-warning');
						
					}
				}
			});
			
		});
	});
	
	$(function(){
		$('.searchButton').click(function(e){
			e.preventDefault();
			catID = $(this).attr('id');
			$('.searchMovies').attr('src',"search1.php?id="+catID);
		});
	});
	
	$('.close').click(function(e) {
    e.preventDefault();
    $('.MovieTrailer').attr('src', '');
	$('.postLink').attr('src', '');
  });

	</script>

<div class="pb-5"></div>
	<?php 

if ( !isset ($username) || $username != "admin")
{
	?>
<footer style="background-color: #2E2E2E;
    color: white;
    min-height: 50px;
    box-shadow: 0px 0px 5px #4f4f4f;
    min-width: 100%;
	position: sticky;
    bottom: 0;
    z-index: 1020;">
  <div class="row" style="width: 100%; padding-top: 3px;margin-left: 1px!important;">

	  <div class="col-2 col-sm-2 col-md-2 p-1 text-center" >
	  <a href="index.php"><img src="images/home1.png" style="width: 25px;"></a><div style="font-size: 10px">Home</div>
	  </div>
	  
	  <div class="col-2 col-sm-2 col-md-2 p-1 text-center" >
	  <a href="cate?s=0&type=movie"><img src="images/movies.png" style="width: 25px;"></a><div style="font-size: 10px">Movies</div>
	  </div>
	  
	  <div class="col-2 col-sm-2 col-md-2 p-1 text-center">
	  <a href="cate?s=0&type=tv-show"><img src="images/tvshows.png" style="width: 25px;"></a><div style="font-size: 10px">TVShows</div>
	  </div>
	  
	  <div class="col-2 col-sm-2 col-md-2 p-1 text-center">
	  <a href="cate?s=0&type=anime"><img src="images/animes.png" style="width: 25px;"></a><div style="font-size: 10px">Animes</div>
	  </div>
	  
	  <div class="col-2 col-sm-2 col-md-2 p-1 text-center" style="">
	  <a href="cate?s=0&type=wrestling"><img src="images/wrestling.png" style="width: 25px;"></a><div style="font-size: 10px">Wrestling</div>
	  </div>
	  
	  <div class="col-2 col-sm-2 col-md-2 p-1 text-center">
	  <a href="profile.php"><img src="images/profile1.png" style="width: 30px;"></a><div style="font-size: 10px">Profile</div>
	  </div>
	  
	</div>
</footer>

<?php 
}
elseif ($username == "admin")
{
	?>
<footer style="background-color: #2E2E2E;
    color: white;
    min-height: 50px;
    box-shadow: 0px 0px 5px #4f4f4f;
    min-width: 100%;
	position: sticky;
    bottom: 0;
    z-index: 1020;">  <div class="row">
	  
	  <div class="col-2 col-sm-2 col-md-2 p-1 text-center">
	  <a href="addcategory1.php"><img src="images/imdb21.png" style="width: 45px;"></a><div style="font-size: 10px">Add IMDB</div>
	  </div>
	  
	  <div class="col-2 col-sm-2 col-md-2 p-1 text-center">
	  <a href="addcategory.php"><img src="images/addcat.png" style="width: 25px;"></a><div style="font-size: 10px">Category</div>
	  </div>
	  
	  <div class="col-2 col-sm-2 col-md-2 p-1 text-center">
	  <a href="index.php"><img src="images/home1.png" style="width: 25px;"></a><div style="font-size: 10px">Home</div>
	  </div>
	  
	  <div class="col-2 col-sm-2 col-md-2 p-1 text-center" >
	  <a href="profile.php"><img src="images/profile1.png" style="width: 25px;"></a><div style="font-size: 10px">Profile</div>
	  </div>
	  
	  <div class="col-2 col-sm-2 col-md-2 p-1 text-center">
	  <a href="https://tryq8flix.com/getlinks.php"><img src="https://i.imgur.com/3qawygU.png" style="width: 25px;"></a><div style="font-size: 10px">Grabber</div>
	  </div>
	  
	  <div class="col-2 col-sm-2 col-md-2 p-1 text-center" >
	  <a href="logout.php"><img src="images/logout1.png" style="width: 25px;"></a><div style="font-size: 10px">Exit</div>
	  </div>
	  
	</div>
</footer>

<?php
}
?>

  </body>
</html>