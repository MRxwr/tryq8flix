<?php
	include_once ("includes/config.php");
	include_once("includes/checksouthead.php");

	if ( !isset ($_SESSION["username"]) )
	{
		header ("Location: index.php?error=category");
	}
	elseif ( $_SESSION["username"] == "admin" )
	{
		if ( isset ( $_POST["acat"] ))
		{
			
			$sql = "SELECT * FROM categorieslist ORDER BY ID DESC";
			$result = $dbconnect->query($sql);
			$row = $result->fetch_assoc();

			if ( !isset ( $row["id"] ) )
			{
				$id = 1;
			}
			else
			{
				$id = $row["id"] + 1 ;
			}

			$title = $_POST["acat"];

			$sql = "INSERT INTO categorieslist ( id, title) VALUES ( '$id' , '$title')";
			$result = $dbconnect->query($sql);
		}
		
		if ( isset ($_GET["rcat"]) )
		{
			$id = $_GET["rcat"];
			
			$sql = "DELETE FROM categorieslist WHERE id LIKE '$id'";
			$result = $dbconnect->query($sql);
		}
?>
<!DOCTYPE html>
<html>
<title>Categories List - Q8FLiX</title>
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
<div class="w3-content" style="max-width: 1300px">

  <!-- The Grid -->
  <div class="">
 
<?php
	include_once ("template/header.php");
	$date=date("Y/m/d");
	$time=date("g:i A");
?>
    <!-- Right Column -->
    <div class="w3-text-white" style="padding-top: 40px">
      <div class="w3-row-padding w3-padding-16 w3-center" id="food" >
		  <h3>Add New Category:</h3>
<form method="post" action="categorieslist.php" id="Myform">
<table align="center" style="width: 100%">
  <tbody>
	  <tr>
		  <td colspan="2"><input type="text" name="acat" value="" style="width: 100%"></td>
		  <td><input type="submit" name="submit" value="Add" style="width: 100%"/></td>
	  </tr>
  </tbody>
</table>
	<h3>Category List</h3>
<table align="center" style="width: 100%">
  <tbody>
<?php
	$sql = "SELECT * FROM categorieslist ORDER BY title ASC";
	$result = $dbconnect->query($sql);
	while ( $row = $result->fetch_assoc() )
	{
?>
	  <tr style="background-color: goldenrod">
		  <td> <a style="color: black"><?php 
	 if ($row["title"] == "AniMov") 
	 { echo "Anime Movie" ; } 
	 elseif ( $row["title"] == "Anime") 
	 { echo "Anime Series" ; } 
	 else
	 { echo $row["title"] ; } ?></a></td>
		  <td><a target="" href="?rcat=<?php echo $row["id"] ?>"><img src="images/delete.png" style="width: 30px; height: 30px;"/></a></td>
	  </tr>
<?php
	}
?>
    </tr>
  </tbody>
</table>
</form>

    <!-- End Right Column -->
    </div>
    </div>
    
  <!-- End Grid -->
  </div>
  
  <!-- End Page Container -->


<?php
	}
	else
	{
		header ("Location: index.php?error=category");
	}
	include_once ("template/footer.php");
?>
</div>
</body>
</html>
