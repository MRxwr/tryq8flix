<?php
session_start ();

if ( isset ($_COOKIE["Q8FLiX"]) )
{
	header("LOCATION: index.php?error=register");
}

?>
<!DOCTYPE html>
<html>
<title>Register - Q8Flix</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="images/logo.png">
<link rel="stylesheet" href="css/style1.css?dasd">
<link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Roboto'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="fonts/material-icon/css/material-design-iconic-font.min.css">
<link rel="stylesheet" href="css/style9.css">

<style>
html,body,h1,h2,h3,h4,h5,h6 {font-family: "Roboto", sans-serif}
	body{background: #151515}
</style>

<body>

<!-- Page Container -->
<div class="w3-content" style="max-width:1300px;">
<?php
include_once ("template/header.php");
?>
  <!-- The Grid -->
  <div class="">
 

    <!-- Right Column -->
    <div class="w3-text-white">
      <div class="w3-row-padding w3-padding-16 w3-center" id="food">
    <?php

if ( !isset ($_GET["error"]))
{
	$errormsg = "";
}
elseif ( $_GET["error"] == 1 )
{
	$errormsg = "Password does not match, please type password again.";
}
elseif ( $_GET["error"] == 2 )
{
	$errormsg = "E-mail does not match, please type e-mail again.";
}
elseif ( $_GET["error"] == 3 )
{
	$errormsg = "Username is already in use, please choose another one.";
}
elseif ( $_GET["error"] == 4 )
{
	$errormsg = "A user with this e-mail is already registerd, please choose another email address.";
}
elseif ( $_GET["error"] == 5 )
{
	$errormsg = "Please enter a valid Email address.";
}
elseif ( $_GET["error"] == 6 )
{
	$errormsg = "No spaces are allowed in a username, Please choose another.";
}
elseif ( $_GET["error"] == 7 )
{
	$errormsg = "Username should be more than 3 letters.";
}
elseif ( $_GET["error"] == 8 )
{
	$errormsg = "Password should be more than 7 letters.";
}
elseif ( $_GET["error"] == 9 )
{
	$errormsg = "Please chose another username.";
}
elseif ( $_GET["error"] == 10 )
{
	$errormsg = "Password with letters and numbers.";
}


?>
<section class="signup">
<div class="container" style="background: #222222; color: white">
<div class="signup-content" style=" color: white">
<div class="signup-form" style=" color: white">
<h2 class="form-title" style=" color: white">Sign up</h2>
<form method="POST" class="register-form" id="register-form" action="includes/registerdb.php">
<div class="form-group">
<label for="name"><i class="zmdi zmdi-account material-icons-name"></i></label>
<input type="text" name="username" id="name" placeholder="Username"/>
</div>
<div class="form-group">
<label for="email"><i class="zmdi zmdi-email"></i></label>
<input type="email" name="email" id="email" placeholder="Your Email"/>
</div>
<div class="form-group">
<label for="email"><i class="zmdi zmdi-email"></i></label>
<input type="email" name="email1" id="email" placeholder="Repeat your Email"/>
</div>
<div class="form-group">
<label for="pass"><i class="zmdi zmdi-lock"></i></label>
<input type="password" name="password" id="pass" placeholder="Password"/>
</div>
<div class="form-group">
<label for="re-pass"><i class="zmdi zmdi-lock-outline"></i></label>
<input type="password" name="password1" id="re_pass" placeholder="Repeat your password"/>
</div>
		  <?php
  echo $errormsg;
  ?>
<div class="form-group form-button">
<input type="submit" name="signup" id="signup" class="form-submit" value="Register"/>
</div>
<a href="login.php" class="signup-image-link" style=" color: white">I am already member</a>

</form>
</div>
</div>
</div>
</section>


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
