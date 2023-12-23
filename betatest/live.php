<?php
include_once ("includes/config.php");
include_once("includes/checksouthead.php");
?>
<!DOCTYPE html>
<html>
<title>Latest Categories - Q8Flix</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="images/logo.png">
<link rel="stylesheet" href="css/style1.css?dasd">
<link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Roboto'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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
	<div class="w3-content" style="max-width:1300px">
	  <?php
	include_once ("template/header.php");
	?>
		<!-- Right Column -->
		<div class="w3-text-white" style="padding-top: 40px">
			<div class="w3-row-padding w3-padding-16 w3-center">
			
			<video controls>
        <source src="https://cdn1.livehd720.com/hlsbeinp1/bein/video.m3u8?nimblesessionid=163231131" type="application/x-mpegURL">
    </video>

			
			</div>
	<?php
	include_once ("template/footer.php");
	?>
		<!-- End Right Column -->
		</div>
	</div>
  <!-- End Page Container -->
</body>
</html>
