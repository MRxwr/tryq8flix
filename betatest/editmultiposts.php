<?php
include_once ("includes/config.php");
include_once("includes/checksouthead.php");
$catid = $_GET["id"];

if ( !isset ($username) || !in_array($username,$usernames) )
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
<link rel="icon" href="images/favicon.png">
<link rel="stylesheet" href="css/style1.css?dasd">
<link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Roboto'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
html,body,h1,h2,h3,h4,h5,h6 {font-family: "Roboto", sans-serif}
</style>

<body class="">

<!-- Page Container -->
<div class="w3-content" style="max-width:1300px;">
<?php
include_once ("template/header.php");
include_once ("includes/config.php");
$date = date("Y/M/D");
?>
  <!-- The Grid -->
  <div class="">
 

    <!-- Right Column -->
    <div class="w3-text-grey" style="padding-top: 60px;">
      <div class="w3-row-padding w3-padding-16 w3-center" id="food" >
  <h1>Edit Post</h1>
  
 <form action="" method="GET">
	<input type="text" name="from" value="" >
	<input type="text" name="to" value="" >
	<input type="hidden" name="id" value="<?php echo $catid ?>" >
	<input type="submit" name="submit" value="submit" >
</form>
<form method="post" action="includes/editmultipostsdb.php" autocomplete="off" enctype="multipart/form-data">
<table align="center" style="width: 50%">
<tr>
<td>Title:</td>
</tr>
<tr>
<?php
$sql = "SELECT `title`, `id`
		FROM `posts`
		WHERE
		`catid` = '".$catid."'
		";
		if ( isset($_GET["from"]) ){
			$sql .= " AND `title` BETWEEN '".$_GET["from"]."' AND '".$_GET["to"]."'";
		}
		$sql .= " ORDER BY `title` ASC";
$result = $dbconnect->query($sql);
echo $totalcategories = $result->num_rows;
while ( $row = $result->fetch_assoc() ){
	?>
	<td><input type="text" width="100%" name="title[]" value="<?php echo $row["title"] ?>" style="width: 100%"></td>
	</tr>
	<tr>
	<td><input type="hidden" name="id[]" value="<?php echo $row["id"] ?>">
	<td><input type="hidden" name="catid" value="<?php echo $catid ?>"></td>
	</tr>
	<?php
}
	?>
	
	<tr><td>
	<textarea name="videolink" style="width: 100%; height:450px"></textarea>
	</td></tr>
    <!-- End Right Column -->
	
    <tr>
	<td colspan="4"><input type="submit" name="submit" value="Submit" style="width: 100%"></td>
	</tr>
	<input type="hidden" name="numberofposts" value="<?php echo $totalcategories ?>">
	</table>
</form>
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