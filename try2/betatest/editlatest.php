<?php
require ("includes/config.php");
require ("includes/checksouthead.php");

if ( !isset ( $_SESSION["username"] ) || $_SESSION["username"] != "admin" )
{
	header ("Location: index.php?error=editcategory");
}
else
{
	$i = 0;
	if ( isset($_POST["act"]) )
	{
		while ( $i < 50 )
		{
			$title = $_POST["title$i"];
			$id = $_POST["id$i"];
			$sql = "UPDATE `posts` SET `title` = '$title' WHERE `id` LIKE '$id'";
			$result = $dbconnect->query($sql);
			$i++;
		}
	}
	?>
<!DOCTYPE html>
<html>
<title>Edit Post - Q8Flix</title>
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
<div class="w3-content" style="max-width:1300px;">
<?php
require ("template/header.php");
$sql = "SELECT * FROM posts ORDER BY id DESC LIMIT 50";
$result = $dbconnect->query($sql);
?>
  <!-- The Grid -->
  <div class="">
 

    <!-- Right Column -->
    <div class="w3-text-white" style="padding-top: 40px;">
      <div class="w3-row-padding w3-padding-16 w3-center" id="food">
  <h1>Edit Posts</h1>
<form method="post" action="" enctype="multipart/form-data">
<table align="center" style="width: 100%; text-align: left">
<?php 
$i = 0;
while ( $row = $result->fetch_assoc() )
{
	?>
<tr>
<td width="1%">Title:</td>
<td><input type="text" name="title<?php echo $i ?>" value="<?php echo $row["title"] ?>" style="width: 100%">
	<input type="hidden" name="id<?php echo $i ?>" value="<?php echo $row["id"] ?>" style="width: 100%"></td>
	<td><input type="hidden" name="category<?php echo $i ?>" value="<?php echo $row["category"] ?>" style="width: 100%"></td>
</tr>
<?php
$i++;
}
?>
<tr>
<td></td>
<td colspan="2" align="center"><input type="submit" name="submit" value="Submit" style="width: 100%">
							   <input type="hidden" name="act" value="accept" style="width: 100%"></td>
</tr>
</table>
</form>
    
    <!-- End Right Column -->
    </div>
    </div>
    
  <!-- End Grid -->
	  <?php
require ("template/footer.php");
?>
  </div>
  
  <!-- End Page Container -->
</div>



</body>
</html>
    <?php
}
?>