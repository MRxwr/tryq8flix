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

	$sql = "select title from category where id like $catid";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_assoc();
	$title = $row["title"];
?>
<!DOCTYPE html>
<html>
<title>Add Multiple Posts - Q8Flix</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="images/logo.png">
<link rel="stylesheet" href="css/style1.css?dsadsa">
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
      <div class="w3-row-padding w3-padding-16 w3-center" id="food" >
		  <?php 
	if (!isset ( $_GET["numberofposts"] ))
	{
	?>
		  <h1>Adding Multiple Posts into <b style="color: red"><?php echo $title ?></b></h1>
		  <form method="get" action="">
			  <table style="width: 100%">
				  <tr>
				  <td style="width: 10%">
				  Number of posts: 
				  </td>
				  <td >
				  <input type="text" name="numberofposts" value=""  style="width: 100%">
				  </td>
				  </tr>
				  <tr>
				  <td style="width: 10%">
			 	  Posts title: 
				  </td>
				  <td>
			      <input type="text" name="poststitle" value="" placeholder="EX. S01E01 or EP01"  style="width: 100%">
				  </td>
				  </tr>
				  <tr>
				  <td></td>
			      <input type="hidden" name="catid" value="<?php echo $_GET["catid"] ?>">
		  	      <td colspan="2">
				  <input type="submit" name="submit" value="Submit" style="width: 100%">
				  </td>
			  </tr></table>
		  </form>
<?php
	}
	if (!isset ($_GET["numberofposts"]) && !isset($_GET["poststitle"])){
	}else{
$i = 0;
$title = $_GET["poststitle"];
@$seasonnumber = strstr($title,'E',true);
@$seasonnumber = explode("S",$seasonnumber);
@$seasontitle = "S".$seasonnumber[1]."E";
	$sql = "select title from category where id like $catid";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_assoc();
	$catetitle = $row["title"];

if ( strpos($title, 'EP') !== false ){
	$title = explode("EP",$title);
	$titlevalue = "EP";
	$titlenumber = $title[1];
}elseif (strpos($title, $seasontitle) !== false){
	$title = explode($seasontitle,$title);
	$epnumber = $title[1];
}

if ( $_GET["numberofposts"] > 99 AND $_GET["numberofposts"] < 999 ){
	$strpad = 3;
}elseif ( $_GET["numberofposts"] > 999 ){
	$strpad = 4;
}else{
	$strpad = 2;
}
?>
<h1>Adding Multiple Posts into <b style="color: wheat"><?php echo $catetitle ?></b></h1>

<form method="post" action="includes/multipostdb.php" enctype="multipart/form-data">
<input type="hidden" name="poster" value="<?php echo $poster[0]["poster"] ?>">
<input type="hidden" name="<?php echo "oldvideolink".$i ?>" value="">
<input type="hidden" name="postdate" value="<?php echo date("Y/m/d"); ?>">
<input type="hidden" name="posttime" value="<?php echo date("g:i A"); ?>">
<input type="hidden" name="catid" value="<?php echo $catid ?>">
<input type="hidden" name="subtitle" value="">
<input type="hidden" name="numberofposts" value="<?php echo $_GET["numberofposts"] ?>">
<input type="hidden" name="download" value="">
<table align="center" style="width: 100%">
<tr>
<td style="width: 50%;">UptoBox Links</td>
<td style="width: 50%;">Titles:</td>
</tr>
<tr>
	<td><textarea name="videolink" style="width:100%;height: 300px;"></textarea></td>
	<td><textarea name="titles" style="width:100%;height: 300px;"><?php
	for( $i = 0 ; $i < $_GET["numberofposts"] ; $i++ ){
		if (isset ($titlenumber)){
			echo $titlevalue.str_pad ($titlenumber, 4, "0", STR_PAD_LEFT);
			$titlenumber=$titlenumber+1;
		}elseif (isset($epnumber)){
			echo $seasontitle.str_pad($epnumber, $strpad, "0", STR_PAD_LEFT);
			$epnumber=$epnumber+1;
		}
		$newLine =  ( ($i + 1) != $_GET["numberofposts"] ? "\n" : "" );
		echo $newLine;
	}
	?></textarea></td>
</tr>
	</table>
	<input type="submit" name="submit" value="Submit">
</form>
		  <?php
	}
?>
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
