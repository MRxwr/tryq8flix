<?php
	include_once ("includes/config.php");
	include_once("includes/checksouthead.php");

	if ( !isset ($_SESSION["username"]) AND $_SESSION["username"] != "admin" )
	{
		header ("Location: index.php?error=category");
	}
?>
<!DOCTYPE html>
<html>
<title>Users List - Q8FLiX</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="images/logo.png">
<link rel="stylesheet" href="css/style1.css?dasd">
<link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Roboto'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
html,body,h1,h2,h3,h4,h5,h6 
{
	font-family: "Roboto", sans-serif
}
</style>

<body>

<!-- Page Container -->
<div class="w3-content" style="max-width: 1300px">

  <!-- The Grid -->
  <div class="">
 
<?php
include_once ("template/header.php");
?>
    <!-- Right Column -->
    <div class="w3-text-white" style="padding-top: 40px">
      <div class="w3-row-padding w3-padding-16 w3-center" id="food" >

	<h3>Users list</h3>
<table align="center" style="width: 100%">
<tbody>
<tr style="background-color: goldenrod">
<td><a style="color: black">Username</a></td>
<td><a style="color: black">User Id</a></td>
</tr>
<?php
$sql = "SELECT * FROM users ORDER BY id ASC";
$result = $dbconnect->query($sql);
while ( $row = $result->fetch_assoc() )
{
?>
<tr style="background-color: goldenrod">
<td><a style="color: black"><?php echo $row["username"]; ?></a></td>
<td><a style="color: black"><?php echo $row["id"] ?></a></td>
</tr>
<?php
}
?>
</tbody>
</table>

    <!-- End Right Column -->
    </div>
    </div>
    
  <!-- End Grid -->
  </div>
  
  <!-- End Page Container -->


<?php
	include_once ("template/footer.php");
?>
</div>
</body>
</html>
