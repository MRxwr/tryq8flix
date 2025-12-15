<?php
include_once ("includes/config.php");
include_once("includes/checksouthead.php");

//check token and update if correct
if ( isset ($_POST["token"]) ){
	$string = file_get_contents("https://uptobox.com/api/user/me?token=".$_POST["token"]);
	$json_a = json_decode($string, true);
	if ( isset($json_a["data"]["premium"]) AND $json_a["data"]["premium"] == 1 ){
		$sql = "UPDATE `users`
				SET
				`uptoboxToken` = '".$_POST["token"]."'
				WHERE
				`id` = '".$userID."'
				";
		$result = $dbconnect->query($sql);
		header("LOCATION: index.php");
	}else{
		$wrongToken = 1;
	}
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
		<?php 
		if ( isset($wrongToken) ){
			?>
			<h2 style=" color:pink">
			Wrong Token. try again
			<br>
			رمز خاطئ. حاول مرة اخرة
			</h2>
		<?php
		}
		?>
			<table style="width: 100%; border: 0px;">
				<tr>
					<td>
						<img src="https://i.imgur.com/Ah59Xnb.png" width = "150px" height = "150px">
					</td>
				</tr>
				<tr>
					<td>
						<br><h3 style="color: white">Your premium Uptobox account has expired. Please renew it by <a style="color:gold" href="https://uptobox.com/affiliate?aff_id=11518425" target="_blank">clicking here.</a></h3>
						<br>
						(<a style="color:gold" href="https://www.tryq8flix.com/pdfs/Become%20Premium.pdf" target="_blank">How to register</a>)
					</td>
				</tr>
				<tr>
					<td style="direction:rtl">
						<h3 style="color: white">حسابك المميز من Uptobox انتهى. الرجاء تحديثة عن طريقة <a style="color:gold" href="https://uptobox.com/affiliate?aff_id=11518425" target="_blank"> الضغط هنا.</a></h3>
						<br>
						(<a style="color:gold" href="https://www.tryq8flix.com/pdfs/Become%20Premium.pdf" target="_blank">طريقة التسجيل</a>)
					</td>
				</tr>
				<tr>
					<td style="direction:rtl">
						<h4 style="color: white" >
						New token
						(
						<a style="color:gold" href="https://www.tryq8flix.com/pdfs/Getting%20Token.pdf" target="_blank">how to get my token</a>
						)
						<br>
						الرمز الجديد
						(
						<a style="color:gold" href="https://www.tryq8flix.com/pdfs/Getting%20Token.pdf" target="_blank">كيفية الحصول على الرمز الجديد</a>
						)
						</h4>
					</td>
				</tr>
				<tr>
					<td style="direction:rtl">
						<form method="post" action="">
						<input type="text" name="token">
						<input type="submit" value="Add">
						</form>
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
