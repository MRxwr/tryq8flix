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
	
	if ( isset($_POST["ytslink"]) )
	{
		$i = 1;
		$getcode = file_get_contents($_POST["ytslink"]);
		$gettinginfo = explode("Available in:",$getcode);
		$numberofqualities = sizeof(explode("modal-torrent",$getcode));
		$magnet = array();
		$quality = array();

		
		$gettinginfo = explode('/download/',$gettinginfo[1]);
		while ( $i < $numberofqualities )
		{
			$gettinginfo0 = explode('" rel',$gettinginfo[$i]);
			$magnet[] = $gettinginfo0[0];
			$i = $i + 1;
		}
		$gettinginfo = explode("Available in:",$getcode);
		$gettinginfo = explode('">',$gettinginfo[1]);
		$i = 1;
		while ( $i < $numberofqualities )
		{
			$gettinginfo1 = explode('</a>',$gettinginfo[$i]);
			$quality[] = $gettinginfo1[0];
			$i = $i + 1;
		}
	}
?>
<!DOCTYPE html>
<html>
<title>Add Category - Q8FLiX</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="images/logo.png">
<link rel="stylesheet" href="css/style1.css?dsdsa">
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
<?php
		if ( !isset($_POST["ytslink"]) )
		{
		?>
  <h1>Add new category:</h1>
<form method="post" action="includes/categorydb.php" id="Myform">
<table align="center" style="width: 100%">
  <tbody>
  <tr>
      <td style="width: 5%">Type: </td>
      <td><select name="type" style="width: 100%">
	  <option value="Movie" selected >Movie</option>
<?php
	$i = 0;
	while ( $i < sizeof($categorieslist) )
	{
		if ( strtolower($categorieslist[$i]) != "movie" ){
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
		}
		jump:
		$i = $i + 1;	
	}
	?>
		  </select></td>
    </tr>
    <tr>
      <td>IMDb ID: </td>
      <td><input type="text" name="imdbid" value="" style="width: 100%"></td>
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
	<tr>
      <td><input type="hidden" name="posttime" value="<?php echo $time ?>"></td>
    </tr>
  </tbody>
</table>
</form>
			<?php
		  }
				?>
<?php
		if ( !isset($_POST["ytslink"]) )
		{
		?>		  
  <h1>OR Add YTS.IT Link:</h1>
<form method="post" action="" id="Myform">
<table align="center" style="width: 100%">
  <tbody>
    <tr>
      <td style="width: 5%">YTS.it: </td>
      <td><input type="text" name="ytslink" value="<?php if(isset($_POST["ytslink"])){echo $_POST["ytslink"];} ?>" style="width: 100%"></td>
    </tr>
	<tr>
	  <td></td>
      <td colspan=""><input type="submit" name="submit" value="Submit" style="width: 100%"></td>
    </tr>
	<tr>
      <td><input type="hidden" name="postdate" value="<?php echo $date ?>"></td>
    </tr>
	<tr>
      <td><input type="hidden" name="posttime" value="<?php echo $time ?>"></td>
    </tr>
  </tbody>
</table>
</form>
			<?php
		  }
				?>
	<?php
		if ( isset($_POST["ytslink"]) )
		{
		?>
			<form method="post" action="includes/categorydb2.php" id="Myform">
			<h1>Please choose a quality to upload:</h1>
					<select name="magnet" style="width: 100%" onchange="this.form.submit()">
					<option>Select here...</option>
					<?php 
					$i = 0;
					while ( $i < $numberofqualities-1 )
					{
					?>
					<option value="<?php echo $magnet[$i] ?>"><?php echo $quality[$i] ?></option>
					<?php
					$i = $i + 1;
					}
					?>
					</select>
					<input type="hidden" name="ytslink" value="<?php echo $_POST["ytslink"] ?>">
					<input type="hidden" name="posttime" value="<?php echo $time ?>">
					<input type="hidden" name="postdate" value="<?php echo $date ?>">			
			</form>
			<?php
		  }
				?>
				
<?php
if ( !isset($_POST["ytslink"]) )
{
		?>		  
  <h1>OR Add MyAnimeList Link:</h1>
<form method="post" action="includes/categorydb3.php" id="Myform">
<table align="center" style="width: 100%">
  <tbody>
    <tr>
      <td style="width: 5%">MAL: </td>
      <td><input type="text" name="mal" value="" style="width: 100%" autofocus></td>
    </tr>
	<tr>
	  <td></td>
      <td colspan=""><input type="submit" name="submit" value="Submit" style="width: 100%"></td>
    </tr>
	<tr>
      <td><input type="hidden" name="postdate" value="<?php echo $date ?>"></td>
    </tr>
	<tr>
      <td><input type="hidden" name="posttime" value="<?php echo $time ?>"></td>
    </tr>
  </tbody>
</table>
</form>
<?php
}
?>

<?php
if ( !isset($_POST["ytslink"]) )
{
		?>		  
  <h1>OR Add Elcinema Link:</h1>
<form method="post" action="includes/categorydb4.php" id="Myform">
<table align="center" style="width: 100%">
  <tbody>
    <tr>
      <td style="width: 5%">elcinema: </td>
      <td><input type="text" name="mal" value="" style="width: 100%"></td>
    </tr>
	<tr>
	  <td></td>
      <td colspan=""><input type="submit" name="submit" value="Submit" style="width: 100%"></td>
    </tr>
	<tr>
      <td><input type="hidden" name="postdate" value="<?php echo $date ?>"></td>
    </tr>
	<tr>
      <td><input type="hidden" name="posttime" value="<?php echo $time ?>"></td>
    </tr>
  </tbody>
</table>
</form>
<?php
}
?>
    <!-- End Right Column -->
    </div>
    </div>
    
  <!-- End Grid -->
  </div>
  
  <!-- End Page Container -->


<?php
}else{
	header ("Location: index.php?error=category");
}
include_once ("template/footer.php");
?>
</div>
</body>
</html>
