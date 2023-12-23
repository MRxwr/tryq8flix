<?php
include_once ("includes/config.php");
include_once("includes/checksouthead.php");
?>
<!DOCTYPE html>
<html>
<title>Request - Q8Flix</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="images/logo.png">
<link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Roboto'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="fonts/material-icon/css/material-design-iconic-font.min.css">
<link rel="stylesheet" href="css/style1.css?dasd">
<style>
html,body,h1,h2,h3,h4,h5,h6 {font-family: "Roboto", sans-serif}
</style>

<body>

<!-- Page Container -->
<div class="w3-content" style="max-width:1300px">
<?php
include_once ("template/header.php");
?>
  <!-- The Grid -->
  <div class="" style="padding-top: 40px">
 

    <!-- Right Column -->
    <div class="w3-text-white">
      <div class="w3-row-padding w3-padding-16 w3-center" id="food">
    <?php

if ( !isset ($_GET["msg"]))
{
	$msg = "";
}
else
{
	$msg = "Please enter a valid username and password.";
}

if ( isset ($_SESSION["username"]) )
{
?>
<div class="container" align="center" style="width: 100%; align-content: center; background: #151515	">
<div class="signup-content" align="center" style="width: 100%; align-content: center">
<div class="signup-form" align="center" style="width: 100%; align-content: center">
<?php 
 if ($msg != "")
 {
 ?>
<h3 class="form-title">Your request has been submitted successfully. We will get back to you soon.</h3>
<?php
 }
?>
<h3 class="form-title">Type in your request below:</h3>
<?php
if ( !isset($_GET["q"]) )
{
?>
<form method="POST" class="register-form" id="register-form" action="includes/requestdb.php" >
<table style="width: 100%">
<tr>
<td>Title:</td>
<td style="width: 99%">
<div class="form-group">
<input type="text" name="title" id="name" placeholder="The Avengers.." style="width: 100%"/>
</div>
</td>
</tr>
<tr>
<td>IMDB:</td>
<td>
<div class="form-group">
<input type="text" name="imdblink" id="name" placeholder="imdb.com/title/tt0848228/" style="width: 100%"/>
</div>
</td>
</tr>
<tr>
<td colspan="2">
<div class="form-group">
<textarea type="textbox" name="desctiption" id="name" placeholder="Tell us what you want..." style="width: 100%; height: 250px;"></textarea>
</div>
</td>
</tr>
<tr>
<td colspan="2" align="center">
<div class="form-group form-button">
<input type="submit" name="signup" id="signup" class="form-submit" value="Send" style="width: 100%"/>
<input type="hidden" name="username" value="<?php echo $username ?>"/>
<input type="hidden" name="date" value="<?php echo date("Y/m/d"); ?>">
<input type="hidden" name="time" value="<?php echo date("g:i A"); ?>">
</div>
</td>
</tr>
</form>
<?php
}
else
{
	$postid = $_GET["postid"];
	$sql = "SELECT * FROM posts WHERE id LIKE $postid";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_assoc();
	
?>
<form method="POST" class="register-form" id="register-form" action="includes/requestdb.php" >
<table style="width: 100%">
<tr>
<td>Title:</td>
<td style="width: 99%">
<div class="form-group">
<input type="text" name="title" id="name" placeholder="" value="<?php echo $row["category"] . " (" . $row["title"] . ")"; ?>" style="width: 100%"/>
</div>
</td>
</tr>
<tr>
<td colspan="2">
<div class="form-group">
<textarea type="textbox" name="desctiption" id="name" placeholder="" style="width: 100%; height: 250px;">I Want to watch a higher quality.</textarea>
</div>
</td>
</tr>
<tr>
<td colspan="2" align="center">
<div class="form-group form-button">
<input type="submit" name="signup" id="signup" class="form-submit" value="Send" style="width: 100%"/>
<input type="hidden" name="username" value="<?php echo $username ?>"/>
<input type="hidden" name="date" value="<?php echo date("Y/m/d"); ?>">
<input type="hidden" name="time" value="<?php echo date("g:i A"); ?>">
</div>
</td>
</tr>
</form>
<?php
}
?>
</table>
</div>
</div>
</div>
<?php
}
?>

    <!-- End Right Column -->
    </div>
    </div>
    
  <!-- End Grid -->
	<?php
include_once ("template/footer.php");
?>
  </div>
  
  <!-- End Page Container -->
</div>


    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>
