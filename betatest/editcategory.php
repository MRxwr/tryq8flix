<?php
include_once ("includes/config.php");
include_once("includes/checksouthead.php");

$id = $_GET["id"];

if ( !isset($username) OR !in_array($username,$usernames) ){
	header ("Location: index.php?error=editcategory");
}else{
	$categorieslist = array();
	$i = 0;
	$sql = "SELECT * FROM categorieslist";
	$result = $dbconnect->query($sql);
	while ( $row = $result->fetch_assoc() ){
		$categorieslist[$i] = $row["title"];
		$i = $i + 1 ;
	}
	?>
<!DOCTYPE html>
<html>
<title>Edit Category - Q8FLiX</title>
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
$date = date("Y/M/D");
$sql = "SELECT * FROM category WHERE id='$id'";
$result = $dbconnect->query($sql);
$row = $result->fetch_assoc();
?>
  <!-- The Grid -->
  <div class="" style="padding-top: 40px">
 

    <!-- Right Column -->
    <div class="w3-text-white">
      <div class="w3-row-padding w3-padding-16 w3-center" id="food">
  <h1>Latest shows</h1>
<form method="post" action="includes/editcategorydb.php" id="Myform">
<table align="center" style="width: 100%">
  <tbody>
  	 <tr>
      <td>Type: </td>
      <td><select name="type" style="width: 100%">
		  <option value="<?php echo $row["type"] ?>"><?php echo $row["type"] ?></option>
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
      <td style="width: 1%">Title: </td>
      <td><input type="text" name="title" value="<?php echo $row["title"] ?>" style="width: 100%"></td>
    </tr>
    <tr>
      <td>Rating: </td>
      <td><input type="text" name="rating" value="<?php echo $row["rating"] ?>" style="width: 100%"></td>
    </tr>
	<tr>
      <td>Notes: </td>
      <td><input type="text" name="notes" value="<?php echo $row["notes"] ?>" style="width: 100%" ></td>
    </tr>
	<tr>
      <td>IMDb: </td>
      <td><input type="text" name="imdbrating" value="<?php echo $row["imdbrating"] ?>" style="width: 100%"></td>
    </tr>
    <tr>
      <td>Duration: </td>
      <td><input type="text" name="duration" value="<?php echo $row["duration"] ?> " style="width: 100%"></td>
    </tr>
    <tr>
      <td>Genre: </td>
      <td><input type="text" name="genre" value="<?php echo $row["genre"] ?>" style="width: 100%"></td>
    </tr>
    <tr>
      <td>Date:</td>
      <td><input type="text" name="releasedate" value="<?php echo $row["releasedate"] ?>" style="width: 100%"></td>
    </tr>
    <tr>
      <td>Language: </td>
      <td><input type="text" name="language" value="<?php echo $row["language"] ?>" style="width: 100%"></td>
    </tr>
    <tr>
      <td>Country: </td>
      <td><input type="text" name="country" value="<?php echo $row["country"] ?>" style="width: 100%"></td>
    </tr>
    <tr>
      <td>Channel: </td>
      <td><input type="text" name="channel" value="<?php echo $row["channel"] ?>" style="width: 100%"></td>
    </tr>
    <tr>
      <td>Poster: </td>
      <td><input type="text" name="poster" value="<?php echo $row["poster"] ?>" style="width: 100%"></td>
    </tr>
    <tr>
      <td>Header: </td>
      <td><input type="text" name="header" value="<?php echo $row["header"] ?>" style="width: 100%"></td>
    </tr>
	  <tr>
      <td>Trailer: </td>
      <td><input type="text" name="trailer" value="<?php echo $row["trailer"] ?>" style="width: 100%"></td>
    </tr>
    <tr>
      <td>Description: </td>
      <td><textarea name="description" style="width: 100%; height:200px;" wrap="soft"><?php echo $row["description"] ?></textarea></td>
    </tr>
	  <tr>
      <td>Collections: </td>
      <td><textarea name="collections" style="width: 100%; height:200px;" wrap="soft"></textarea></td>
    </tr>
	  <tr>
	  <td colspan="2">
<?php  
	$collections = explode(",",$row["collections"]);
	$i = 0 ;
	$posters = array();
	while ( $i < sizeof($collections)) 
	{ 
		$result = $dbconnect->query("SELECT poster FROM category WHERE id LIKE '$collections[$i]'");
		$row = $result->fetch_assoc();
		$posters[] = $row["poster"];
		$i = $i + 1;
	}
	$i = 0 ;
	while ( $i < sizeof($collections)) 
	{ 
		if ( $collections[$i] !== "" )
		{
			
?>
			<a href="category.php?id=<?php echo $collections[$i] ?>"><div class="w3-quarterindex" style="padding: 3px;  position: relative;text-align: center;color: white;"><img src="<?php echo $posters[$i] ?>" alt="" id="imageindex"></a>
			<a href="includes/removecol.php?catid=<?php echo $collections[$i] ?>&id=<?php echo $id ?>"><img src="images/unfavo.png" alt="Sandwich" style="width:25px; height:25px"></a></div>
		  
<?php
		}
		$i = $i + 1;
	}
?>
	  </td>
	  </tr>
    <tr>
      <input type="hidden" name="postdate" value="<?php echo $date ?>">
      <input type="hidden" name="id" value="<?php echo $id ?>">
		<td></td>
      <td colspan="" ><input type="submit" name="submit" value="Submit" style="width: 100%"></td>
    </tr>
  </tbody>
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