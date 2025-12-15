<?php
include_once ("includes/config.php");
include_once("includes/checksouthead.php");

//check token and update if correct
if ( isset ($_GET["postid"]) AND isset($_GET["catid"]) ){
	$username = $_SESSION["username"];
	$catid = $_GET["catid"];
	$postid = $_GET["postid"];
	$description = "Please check this video, it may be broken";
	$date = date("Y/m/d");
	$time = date("g:i A");
	$issue = "Video is dead";
		
	$sql= "SELECT id FROM users WHERE username = '$username'";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_assoc();
	$userid = $row["id"];

	$sql = "INSERT INTO reports (userid, catid, postid, issue, description, time, status) VALUES ('$userid', '$catid', '$postid', '$issue','$description', '$time', 'Processing...')";
	$result = $dbconnect->query($sql);
}
?>

<!DOCTYPE html>
<html>
<title>Maintenance - Q8Flix</title>
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
<style type="text/css">
  .vjs-default-skin .vjs-control-bar { font-size: 100% }
	</style>
<script src="https://vjs.zencdn.net/ie8/1.1.2/videojs-ie8.min.js"></script>
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
    
      <div class="w3-text-grey w3-center" style="padding-top: 40px;">
        <div class="w3-display-container">
			<table style="width: 100%; border: 0px;">
				<tr>
					<td>
						<img src="https://i.imgur.com/Ah59Xnb.png" width = "150px" height = "150px">
					</td>
				</tr>
				<tr>
					<td>
						<br><h3 style="color: white">This video is not working, we are working on fixing it soon.
						<br>
						(Please check back after a while. Thank you)</h3>
					</td>
				</tr>
				<tr>
					<td style="direction:rtl">
						<h3 style="color: white">هذا الفيديو لا يعمل ، سنقوم بإصلاحه قريبا
						<br>
						(الرجاء زيارة الفيديو مرة اخرى لاحقا، شكراً لتفهمكم)</h3>
					</td>
				</tr>
			</table>

        </div>
   </div>
<?php
include_once ("template/footer.php");
?>
    <!-- End Left Column -->
    </div>
    </div>
    
  <!-- End Grid -->
    

  <!-- End Page Container -->
</body>
</html>
