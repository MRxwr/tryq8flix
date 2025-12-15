<?php
setcookie("Q8FLiX", "", time() - (86400*30 ), "/");
session_start ();
if ( isset($_COOKIE["Q8FLiX"]) )
{
	header("Location: index.php");
}
?>
<!DOCTYPE html>
<html>
<title>Sign In - Q8Flix</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="images/logo.png">
<link rel="stylesheet" href="css/style1.css?dasd">
<link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Roboto'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="fonts/material-icon/css/material-design-iconic-font.min.css">
<link rel="stylesheet" href="css/style9.css?dass">
<style>
html,body,h1,h2,h3,h4,h5,h6 {font-family: "Roboto", sans-serif}
	body{background: #131313}
</style>

<body>

<!-- Page Container -->
<div class="w3-content" style="max-width:1300px">
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
elseif ( $_GET["error"] === "dklhjasdkl" )
{
	$errormsg = "This email address is not vaild, Please try another.";
}
elseif ( $_GET["error"] === "eqwweqw" )
{
	$errormsg = "Wrong code, Please enter code correctly.";
}
else
{
	$errormsg = "Please enter a valid username and password.";
}
if ( isset ( $_GET["fgp"]) AND $_GET["fgp"] === "true")
{
	goto jump1;
}
if ( isset ( $_GET["fgp"]) AND $_GET["fgp"] === "check")
{
	goto jump2;
}
if ( isset ( $_GET["fgp"]) AND $_GET["fgp"] === "rpwd")
{
	goto jump3;
}
		  
if ( !isset ($_SESSION["username"]) AND !isset ( $_GET["fgp"] ) )
{
?>
<section class="signup">
<div class="container" style="background: #222222; color: white">
<div class="signup-content" style=" color: white">
<div class="signup-form" style=" color: white">
<?php 

if ( isset ($_GET["msg"]) && $_GET["msg"] == "klasdfjs" )
{
	echo $msg = "<h5>Your registration is completed. Thank you.</h5>";
}
if ( isset ($_GET["msg"]) && $_GET["msg"] == "npwd" )
{
	echo $msg = "<h5>Your password has been updated successfully.</h5>";
}	
?>
<h2 class="form-title" style=" color: white">Sign in</h2>
<form method="POST" class="register-form" id="register-form" action="includes/logindb.php">
<div class="form-group">
<label for="name"><i class="zmdi zmdi-account material-icons-name"></i></label>
<input type="text" name="username" id="name" placeholder="Username"/>
</div>
<div class="form-group">
<label for="pass"><i class="zmdi zmdi-lock"></i></label>
<input type="password" name="password" id="pass" placeholder="Password"/>
</div>
		  <?php
  echo $errormsg;
  ?>
<div class="form-group form-button">
<input type="submit" name="signup" id="signup" class="form-submit" value="Login"/>
</div>
</form>
<br>
<a href="register.php" class="signup-image-link" style=" color: white">Create an account</a>
<a href="?fgp=true" class="signup-image-link" style=" color: lightpink">Forgot Password</a>
</div>
</div>
</div>
</section>
<?php
}
else
{
	header ("Location: index.php?error=login");
}
jump1:		  
if ( isset($_GET["fgp"]) AND $_GET["fgp"]  === "true")
{
?>
<section class="signup">
<div class="container" style="background: #222222; color: white">
<div class="signup-content" style=" color: white">
<div class="signup-form" style=" color: white">
<h3 class="form-title" style=" color: white">Enter your E-mail:</h3>
<form method="POST" class="register-form" id="register-form" action="includes/emailfgp.php">
<div class="form-group">
<label for="email"><i class="zmdi zmdi-account material-icons-name"></i></label>
<input type="text" name="email" id="email" placeholder="E-mail"/>
</div>
<div class="form-group form-button">
<input type="submit" name="signup" id="signup" class="form-submit" value="Send"/>
</div>
</form>
<br>
<a href="register.php" class="signup-image-link" style=" color: white">Create an account</a>
<a href="login.php" class="signup-image-link" style=" color: lightpink">Back to Sign-in</a>
</div>
</div>
</div>
</section>
<?php
}
jump2:		  
if ( isset($_GET["fgp"]) AND $_GET["fgp"] === "check" )
{
?>
<section class="signup">
<div class="container" style="background: #222222; color: white">
<div class="signup-content" style=" color: white">
<div class="signup-form" style=" color: white">
<h6 class="form-title" style=" color: white">Check your email inbox and enter the code below</h6>
<form method="POST" class="register-form" id="register-form" action="includes/emailfgp.php">
<div class="form-group">
<label for="email"><i class="zmdi zmdi-account material-icons-name"></i></label>
<input type="text" name="check" id="check" placeholder="Code"/>
</div>
<div class="form-group form-button">
<input type="submit" name="signup" id="signup" class="form-submit" value="Verify"/>
</div>
</form>
</div>
</div>
</div>
</section>
<?php
}
jump3:		  
if ( isset($_GET["fgp"]) AND $_GET["fgp"] === "rpwd" )
{
?>
<section class="signup">
<div class="container" style="background: #222222; color: white">
<div class="signup-content" style=" color: white">
<div class="signup-form" style=" color: white">
<h6 class="form-title" style=" color: white">Enter new password</h6>
<form method="POST" class="register-form" id="register-form" action="includes/emailfgp.php">
<div class="form-group">
<label for="pass"><i class="zmdi zmdi-lock"></i></label>
<input type="password" name="newpwd" id="pass" placeholder="Password"/>
<input type="hidden" name="ccdi" id="check" value="<?php echo $_GET["ccdi"] ?>"/>
</div>
<div class="form-group form-button">
<input type="submit" name="signup" id="signup" class="form-submit" value="Verify"/>
</div>
</form>
</div>
</div>
</div>
</section>
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
