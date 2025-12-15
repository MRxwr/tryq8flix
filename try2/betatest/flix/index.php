<?php
require('../includes/config.php');
require('includes/checksouthead.php');
?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
  <link href="https://vjs.zencdn.net/7.11.4/video-js.css" rel="stylesheet" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<!-- City -->

    <title>TRYQ8FLiX</title>
  </head>
  <body>
  <div class="container-fluid p-0">
    <?php 
	require('css/css.php');
	if ( strstr($_SERVER['REQUEST_URI'], "id") === false){
		require('templates/carousel.php');
		//<h1 class="tryq8flix p-1"><img src="https://i.imgur.com/78FTnsf.png" style="width: 150px;"></h1> 
		//<h1 class="tryq8flixmobile p-1 text-center"><img src="https://i.imgur.com/78FTnsf.png" style="width: 150px;"></h1> ?>
		<div class="row w-100 p-0 m-0 mt-3 mb-3">
			<div class="col-xl-3 col-lg-3 col-md-3 col-sm- 6 col-6 text-center mb-3">
				<a href="?favoriteid=<?php echo $userID?>"><div class="btn btn-warning p-1 m-0 w-100">
				<b>Favorite</b>
				</div></a>
			</div>
			<div class="col-xl-3 col-lg-3 col-md-3 col-sm- 6 col-6 text-center mb-3">
				<a href="?historyid=<?php echo $userID?>"><div class="btn btn-warning p-1 m-0 w-100">
				<b>History</b>
				</div></a>
			</div>
			<div class="col-xl-3 col-lg-3 col-md-3 col-sm- 6 col-6 text-center mb-3">
				<a href="?notificationid=<?php echo $userID?>"><div class="btn btn-warning p-1 m-0 w-100">
				<b>Notifications</b>
				</div></a>
			</div>
			<div class="col-xl-3 col-lg-3 col-md-3 col-sm- 6 col-6 text-center mb-3">
				<a href="?searchid=1"><div class="btn btn-warning p-1 m-0 w-100">
				<b>Search</b>
				</div></a>
			</div>
		</div>
		
		<?php
		require('templates/gridOf5.php');
	}elseif( strstr($_SERVER['REQUEST_URI'], "?id") ){
		require('templates/category.php');
	}elseif( strstr($_SERVER['REQUEST_URI'], "?videoId") ){
		require('templates/video.php');
	}elseif( strstr($_SERVER['REQUEST_URI'], "?typeid") ){
		require('templates/type.php');
	}elseif( strstr($_SERVER['REQUEST_URI'], "?favoriteid") ){
		require('templates/favorite.php');
	}elseif( strstr($_SERVER['REQUEST_URI'], "?historyid") ){
		require('templates/latest.php');
	}elseif( strstr($_SERVER['REQUEST_URI'], "?notificationid") ){
		require('templates/notifications.php');
	}elseif( strstr($_SERVER['REQUEST_URI'], "?searchid") ){
		require('templates/search.php');
	}
	?>
	
	</div>
	

    <!-- Optional JavaScript; choose one of the two! -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js" integrity="sha384-JEW9xMcG8R+pH31jmWH6WWP0WintQrMb4s7ZOdauHnUtxwoG2vI5DkLtS3qm9Ekf" crossorigin="anonymous"></script>
  <!--<script src="https://vjs.zencdn.net/7.11.4/video.min.js"></script>-->
  
  <script>
  var width = screen.width;
  if ( width < 450 ){
	  $('.carousel').attr('style','display:none');
	  $('.tryq8flix').attr('style','display:none');
	  $('.tryq8flixmobile').attr('style','display:block');
  }
  </script>
  
  </body>
</html>