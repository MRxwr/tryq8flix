<?php
include_once ("includes/config.php");
include_once("includes/checksouthead.php");
if ( !isset ( $_GET["catid"] ) )
{
	$catid = "";
}
else
{
	$catid = $_GET["catid"];
}
if ( !isset($username) OR !in_array($username,$usernames) )
{
	header ("Location: index.php?error=post");
}
elseif ( isset($username) AND in_array($username,$usernames) )
{
	
	$result = $dbconnect->query("SELECT type FROM category WHERE id = '$catid'");
	$row = $result->fetch_assoc();
	if ( strtolower($row["type"])  == "movie" ) 
	{
		$eptitle = "1080p";
	}
	elseif ( strtolower($row["type"]) == "animov" )
	{
		$eptitle = "1080p";
	}
	elseif ( strtolower($row["type"]) == "tv-show" )
	{
		$result = $dbconnect->query("SELECT title FROM posts WHERE catid = '$catid' ORDER BY title DESC");
		$row = $result->fetch_assoc();
		if ( strstr($row["title"],'E',true) != "" )
		{
			$eptitle = explode("E",$row["title"]);
			$eptitle = $eptitle[0] . "E" . str_pad($eptitle[1]+1, 2, "0", STR_PAD_LEFT);
		}
		
		else
		{
			$eptitle = "";
		}
	}
	elseif ( strtolower($row["type"]) == "anime" ) 
	{
		$result = $dbconnect->query("SELECT * FROM posts WHERE catid = '$catid' ORDER BY title DESC LIMIT 1");
		$row = $result->fetch_assoc();
		$findEP = strstr($row["title"],'EP',true);
		$findS = strstr($row["title"],'S',true);
		if ( $findEP === "" )
		{
			$eptitle = explode("EP",$row["title"]);
			$eptitle = "EP" . str_pad($eptitle[1]+1, 4, "0", STR_PAD_LEFT);
		}
		elseif ( $findS === "" )
		{
			$result = $dbconnect->query("SELECT * FROM posts WHERE catid = '$catid' ORDER BY title DESC");
			$row = $result->fetch_assoc();
			$eptitle = explode("E",$row["title"]);
			$eptitle = $eptitle[0] . "E" . str_pad($eptitle[1]+1, 2, "0", STR_PAD_LEFT);
		}
		else
		{
			$eptitle = "S01E01";
		}
		
	}
	else
	{
		$eptitle = "";
	}
	
	
?>
<!DOCTYPE html>
<html>
<title>Add Post - Q8Flix</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="images/logo.png">
<link rel="stylesheet" href="css/style1.css?fsdfs">
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
?>
  <!-- The Grid -->
  <div class="">
 

    <!-- Right Column -->
    <div class="w3-text-white" style="padding-top: 40px;">
      <div class="w3-row-padding w3-padding-16 w3-center" id="food">
  <h1>Add new post:</h1>
<form method="post" action="includes/postdb.php" enctype="multipart/form-data">
<table align="center" style="width: 100%; text-align: left">
<tr>
<td >Title:</td>
<td>
<input type="text" name="title" value="<?php echo $eptitle ?>" style="width: 100%">
</td>
</tr>
<tr>
<td>UPtoBOX:</td>
<td><input type="text" name="uptobox" value="" style="width: 100%" autofocus></td>
</tr>
<tr>
<td>YouTube:</td>
<td><input type="text" name="youtube" value="" style="width: 100%" autofocus></td>
</tr>
<tr>
<td>MyCima:</td>
<td><input type="text" name="mycima" value="" style="width: 100%" autofocus></td>
</tr>
<tr>
<td>Subtitle:</td>
<td><input type="file" name="subtitle" style="width: 100%"></td>
</tr>
<tr>
<td>Category:</td>
<td><select name='category'  style="width: 100%">
<?php 
$result = $dbconnect->query("SELECT title FROM category WHERE id='$catid'");
$row = $result->fetch_assoc();
?>
<option value="<?php echo $row["title"] ?>"><?php echo $row["title"] ?></option>
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
<td></td>
<td colspan="2" align="center"><input type="submit" name="submit" value="Post Video" style="width: 100%"></td>
</tr>
<tr>
<td><input type="hidden" name="postdate" value="<?php echo date("Y/m/d"); ?>"></td>
<td><input type="hidden" name="posttime" value="<?php echo date("g:i A"); ?>"></td>
<td><input type="hidden" name="catid" value="<?php echo $catid ?>"></td>
<?php
	$result = $dbconnect->query("SELECT poster FROM category WHERE id='$catid'");
	$row = $result->fetch_assoc();
	?>
<td><input type="hidden" name="poster" value="<?php echo $row["poster"]; ?>" style="width: 100%"></td>
<td><input type="hidden" name="download" value="" style="width: 100%"></td>
</tr>
</table>
</form>

    <!-- End Right Column -->
    </div>
    </div>
    
  <!-- End Grid -->
	  <?php
}
else
{
	header ("Location: index.php?error=post");
}
include_once ("template/footer.php");
?>
  </div>
  
  <!-- End Page Container -->
</div>


</body>
</html>
