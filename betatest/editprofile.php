<?php
include_once ("includes/config.php");
include_once("includes/checksouthead.php");

$comingusername = $_GET["username"];

if ( !isset ( $_SESSION["username"] ) )
{
	header ("Location: index.php?error=editprofile");
}
elseif ( $_SESSION["username"] != $comingusername )
{
	header ('Location: profile.php');
}
else
{
	?>
<!DOCTYPE html>
<html>
<title>Edit Profile - Q8Flix</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="images/logo.png">
<link rel="stylesheet" href="css/style1.css?dasd">
<link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Roboto'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
html,body,h1,h2,h3,h4,h5,h6 {font-family: "Roboto", sans-serif}
</style>

<body>

<!-- Page Container -->
<div class="w3-content" style="max-width:1300px">
 <?php
include_once ("template/header.php");

$result = $dbconnect->query("SELECT * FROM users WHERE username='$comingusername'");
$row = $result->fetch_assoc();
?>
  <!-- The Grid -->
  <div class="">


    <!-- Right Column -->
    <div class="w3-text-white" style="padding-top: 40px">
      <div class="w3-row-padding w3-padding-16 w3-center" id="food">
  <h1>Edit Profile</h1>
    <form method="post" action="includes/profiledb.php" enctype="multipart/form-data">
    <table align="center" style="width: 100%; text-align: center">
    <tr>
    <td>Email:</td>
    <td style="width: 100%"><input type="text" name="email" value="<?php echo $row["email"] ?>" style="width: 100%" readonly></td>
    </tr>
    <tr>
    <td>password:</td>
    <td style="width: 100%"><input type="password" name="password" value="<?php echo $row["password"] ?>" style="width: 100%"></td>
    </tr>
    <tr>
    <td>Avatar:</td>
    <td style="width: 100%"><input type="file" name="fileToUpload" id="fileToUpload" style="width: 100%"></td>
    </tr>
    <tr>
    <td>Description:</td>
    <td style="width: 100%"><textarea name="description" style="width: 100%; height: 250px"><?php echo $row["description"] ?></textarea></td>
    </tr>
    <tr>
    <td></td>
	<td align="center"><input type="submit" name="submit" value="Confirm Changes" style="width: 100%"></td>
    </tr>
    <tr>
    <td colspan="2" align="center"><input type="hidden" name="username" value="<?php echo $_SESSION["username"]?>"></td>
    </tr>
    </table>
    </form>
    
    <!-- End Right Column -->
    </div>
    </div>
    
  <!-- End Grid -->

  </div>
  	  <?php
include_once ("template/footer.php");
?>
  <!-- End Page Container -->
</div>



</body>
</html>
    <?php
}
?>