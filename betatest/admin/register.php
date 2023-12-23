<?php
session_start ();

if ( isset ($_COOKIE["CreatekwBLZRDA"]) )
{
	header("LOCATION: index.php?error=register");
}
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
<link rel="stylesheet" href="css/style9.css?v=dass">
<style>
input {
  width: 100%;
  padding: 12px 20px;
  margin: 8px 0;
  display: inline-block;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-sizing: border-box;
  color: black;
  
}
</style>
<div class="w3-row-padding w3-padding-16 w3-center" id="food">
<?php


?>

<section class="signup">
	<div class="container w3-center" style="background: darkgray; color: white">
		<div class="signup-content" style=" color: white">
			<div class="signup-form" style=" color: white">
				<h2 class="form-title" style=" color: white">Sign up</h2>
				<?php
					echo $errormsg;
				?>
				<form method="POST" class="register-form" id="register-form" action="includes/registerdb.php">
					<div class="form-group">
					<label for="name"><i class="zmdi zmdi-account material-icons-name"></i></label>
					<input type="text" name="name" id="name" placeholder="Full Name"/>
					</div>
					
					<div class="form-group">
					<label for="email"><i class="zmdi zmdi-email material-icons-name"></i></label>
					<input type="email" name="email" id="email" placeholder="Your Email"/>
					</div>
					
					<div class="form-group">
					<label for="re-pass"><i class="zmdi zmdi-lock-outline material-icons-name"></i></label>
					<input type="password" name="password" id="pass" placeholder="password"/>
					</div>
					
					<div class="form-group">
					<label for="re-pass"><i class="zmdi zmdi-lock-outline material-icons-name"></i></label>
					<input type="password" name="password1" id="re_pass" placeholder="Repeat your password"/>
					</div>
					
					<div class="form-group">
					<label for="name"><i class="zmdi zmdi-phone-in-talk material-icons-name"></i></label>
					<input type="phone" name="phone" id="phone" placeholder="Phone number"/>
					</div>

					<div class="form-group form-button">
					<input type="submit" name="signup" id="signup" class="form-submit" value="Register"/>
					</div>
					<a href="login.php" class="myButton" style="color: gray;font-size:12px">I am already member</a>
				</form>
			</div>
		</div>
	</div>
</section>

</div>
<script src="vendor/jquery/jquery.min.js"></script>
<script src="js/main.js"></script>
<?php
require ("template/footer.php");
?>

