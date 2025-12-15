<?php
include_once ("includes/config.php");
include_once("includes/checksouthead.php");

if ( !isset ($_SESSION["username"]) ){
	header ("Location: index.php?error=category");
}elseif ( isset($username) AND in_array($username,$usernames) ){
	$categorieslist = array();
	$i = 0;
	$sql = "SELECT * FROM categorieslist ORDER BY title ASC";
	$result = $dbconnect->query($sql);
	while ( $row = $result->fetch_assoc() )
	{
		$categorieslist[$i] = $row["title"];
		$i = $i + 1 ;
	}
?>
<!DOCTYPE html>
<html>
<title>Add Category - Q8Flix</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="images/logo.png">
<link rel="stylesheet" href="css/style1.css?dsasd">
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
$date=date("Y/m/d");
?>
  <!-- The Grid -->
  <div class="">
 

    <!-- Right Column -->
    <div class="w3-text-white" style="padding-top: 40px">
      <div class="w3-row-padding w3-padding-16 w3-center" id="food">
  <h1>Add new category:</h1>
<form method="post" action="includes/categorydb1.php" id="Myform">
<table align="center" style="width: 100%">
  <tbody>
  <tr>
      <td style="width: 1%">Type: </td>
      <td><select name="type" style="width: 100%">
<?php
	$i = 0;
	while ( $i < sizeof($categorieslist) )
	{
		if ( $row["type"] == $categorieslist[$i] )
		{
			goto jump;
		}
		else
		{
		?>
		<option value="<?php echo $categorieslist[$i] ?>"><?php 
	 if ($categorieslist[$i]== "AniMov") 
	 { echo "Anime Movie" ; } 
	 elseif ( $categorieslist[$i] == "Anime") 
	 { echo "Anime Series" ; } 
	 else
	 { echo $categorieslist[$i] ; } ?></option>
<?php
		}
		jump:
		$i = $i + 1;	
	}
	?>
		  </select></td>
    </tr>
    <tr>
      <td>Title: </td>
      <td><input type="text" name="title" value="" style="width: 100%"></td>
    </tr>
	<tr>
      <td>Notes: </td>
      <td><input type="text" name="notes" value="" style="width: 100%"></td>
    </tr>
    <tr>
      <td>Rating: </td>
      <td><input type="text" name="rating" value="" style="width: 100%"></td>
    </tr>
	<tr>
      <td>IMDB: </td>
      <td><input type="text" name="imdbrating" value="" style="width: 100%"></td>
    </tr>
    <tr>
      <td>Duration: </td>
      <td><input type="text" name="duration" value="" style="width: 100%"></td>
    </tr>
    <tr>
      <td>Genre: </td>
      <td><input type="text" name="genre" value="" style="width: 100%"></td>
    </tr>
    <tr>
      <td>Date:</td>
      <td><input type="text" name="releasedate" value="" style="width: 100%"></td>
    </tr>
    <tr>
      <td>Language: </td>
      <td><input type="text" name="language" value="" style="width: 100%"></td>
    </tr>
    <tr>
      <td>Country: </td>
      <td><input type="text" name="country" value="" style="width: 100%"></td>
    </tr>
    <tr>
      <td>Channel: </td>
      <td><input type="text" name="channel" value="" style="width: 100%"></td>
    </tr>
    <tr>
      <td>Poster: </td>
      <td><input type="text" name="poster" value="" style="width: 100%"></td>
    </tr>
    <tr>
      <td>Header: </td>
      <td><input type="text" name="header" value="" style="width: 100%"></td>
    </tr>
    <tr>
      <td>Description: </td>
      <td><textarea name="description" style="width: 100%; height:200px;" wrap="soft"></textarea></td>
    </tr>
    <tr>
      <td>Trailer: </td>
      <td><input type="text" name="trailer" value="" style="width: 100%"></td>
    </tr>
    <tr>
	  <td></td>
      <td colspan=""><input type="submit" name="submit" value="Submit" style="width: 100%"></td>
    </tr>
    <tr>
      <td><input type="hidden" name="postdate" value="<?php echo $date ?>"></td>
    </tr>
  </tbody>
</table>
</form>

    <!-- End Right Column -->
    </div>
    </div>
    
  <!-- End Grid -->
	  <?php
}
else{
	header ("Location: index.php?error=category");
}
include_once ("template/footer.php");
?>

  </div>
  
  <!-- End Page Container -->
</div>


</body>
</html>
