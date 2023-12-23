<?php
include_once ("includes/config.php");
include_once("includes/checksouthead.php");
$id = $_GET["id"];
$catid = $_GET["catid"];

if ( !isset($username) OR !in_array($username,$usernames) )
{
	header ("Location: index.php?error=editcategory");
}
else
{
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
include_once ("template/header.php");
include_once ("includes/config.php");
$date = date("Y/M/D");
$sql = "SELECT * FROM category WHERE id='$id'";
$result = $dbconnect->query($sql);
$row = $result->fetch_assoc();
?>
  <!-- The Grid -->
  <div class="">
 

    <!-- Right Column -->
    <div class="w3-text-white" style="padding-top: 40px;">
      <div class="w3-row-padding w3-padding-16 w3-center" id="food">
  <h1>Edit Post</h1>
<form method="post" action="includes/editpostdb.php" enctype="multipart/form-data">
<?php
//getting video links 
$sql = "SELECT * FROM postlinks WHERE id LIKE '$id'";
$result = $dbconnect->query($sql);
$row = $result->fetch_assoc();
$uptobox = $row["uptobox"];
$youtube = $row["youtube"];
$mycima = $row["mycima"];

$sql = "SELECT * FROM posts WHERE id=$id";
$result = $dbconnect->query($sql);
$row = $result->fetch_assoc();
?>
<table align="center" style="width: 100%; text-align: left">
<tr>
<td width="1%">Title:</td>
<td><input type="text" name="title" value="<?php echo $row["title"] ?>" style="width: 100%"></td>
</tr>
<tr>
<td>Category:</td>
<td><select name='category' style="width: 100%">
<option value="<?php echo $row["category"] ?>"><?php echo $row["category"] ?></option>
<?php 
$result = $dbconnect->query("SELECT title FROM category ORDER BY title ASC");
$categoryNew = "naser";
while ($row = $result->fetch_assoc()) 
{
	$category = $row['title'];
	if ( $category != $categoryNew )
	{
		?>
        <option value="<?php echo $category ?>"><?php echo $category ?></option>
		<?php
        $categoryNew = $category;
	}
}
?>
</select></td>
</tr>
<tr>
<td>Poster:</td>
<td>
<?php
$sql = "SELECT * FROM posts WHERE id=$id";
$result = $dbconnect->query($sql);
$row = $result->fetch_assoc();
?>
<input type="text" name="poster" value="<?php echo $row["poster"] ?>" style="width: 100%"></td>
</tr>
<tr>
<td>UPtoBOX:</td>
<td><input type="text" name="uptobox" value="<?php echo $uptobox ?>" style="width: 100%"></td>
</tr>
<td>YouTube:</td>
<td><input type="text" name="youtube" value="<?php echo $youtube ?>" style="width: 100%"></td>
</tr>
<td>MyCima:</td>
<td><input type="text" name="mycima" value="<?php echo $mycima ?>" style="width: 100%"></td>
</tr>
<tr>
<td>Download:</td>
<td><input type="text" name="download" value="<?php $download = $row["download"]; $download = str_replace(".dll.dll",".dll",$download); echo $download ?>" style="width: 100%"></td>
</tr>
<tr>
<td>Subtitle:</td>
<td><input type="file" name="subtitle" value="<?php echo $row["subtitle"] ?>" style="width: 100%"></td>
</tr>
<tr>
<td><input type="hidden" name="postdate" value="<?php echo date("Y/m/d") ?>"></td>
<td><input type="hidden" name="id" value="<?php echo $id ?>"><input type="hidden" name="catid" value="<?php echo $catid ?>"></td>
</tr>
<tr>
<td></td>
<td colspan="2" align="center"><input type="submit" name="submit" value="Submit" style="width: 100%"></td>
</tr>
</table>
</form>
    
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



</body>
</html>
    <?php
}
?>