<?php
include_once ("includes/config.php");
include_once("includes/checksouthead.php");

?>
<!DOCTYPE html>
<html>
<title>Requests - Q8Flix</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="images/logo.png">
<link rel="stylesheet" href="css/style1.css?dasd">
<link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Roboto'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
html,body,h1,h2,h3,h4,h5,h6 {font-family: "Roboto", sans-serif}
.boxsizingBorder {
    -webkit-box-sizing: border-box;
       -moz-box-sizing: border-box;
            box-sizing: border-box;
}
	/* DivTable.com */
.divTable{
	display: table;
	width: 100%;
}
.divTableRow {
	display: table-row;
}
.divTableHeading {
	background-color: #EEE;
	display: table-header-group;
}
.divTableCell, .divTableHead {
	border: 1px solid #999999;
	display: table-cell;
	padding: 3px 10px;
}
.divTableHeading {
	background-color: #EEE;
	display: table-header-group;
	font-weight: bold;
}
.divTableFoot {
	background-color: #EEE;
	display: table-footer-group;
	font-weight: bold;
}
.divTableBody {
	display: table-row-group;
}
</style>

<body>
<!-- Page Container -->
<div class="w3-content" style="max-width:1300px">

  <!-- The Grid -->
  <div class="">
  <?php
include_once ("template/header.php");
?>
    <!-- Right Column -->
    <div class="w3-text-white" style="padding-top: 40px">
      <div class="w3-row-padding w3-padding-16 w3-center">
<h2 style="color: white">Users Requests</h2>
    <?php
if ( !isset ( $_GET["cattitle"] ) )
{
$sql = "SELECT * FROM requests WHERE `status` != 'Done' ORDER BY id DESC LIMIT 20";
$result = $dbconnect->query($sql);
	
	if ( $result->num_rows > 0 )
	{
		while ( $row = $result->fetch_assoc() )
		{

			if ( isset($username) AND in_array($username,$usernames) )
			{
				?>
<table width="100%" border="1" style="color: black; 
	background: <?php if ( $row["status"] == "Done" ) { echo "green"; } else { echo "darkgrey"; } ?>">
  <tbody>
    <tr>
      <td>Username</td>
      <td width="90%"><?php echo $row["username"] ?></td>
    </tr>
    <tr>
      <td>Title</td>
      <td width="90%"><?php echo $row["title"] ?></td>
    </tr>
    <tr>
      <td>Description</td>
      <td width="90%"><?php echo $row["description"] ?></td>
    </tr>
    <tr>
      <td>IMDB Link</td>
      <td width="90%"><?php echo $row["imdblink"] ?></td>
    </tr>
    <tr>
      <td>Date</td>
      <td width="90%"><?php echo $row["date"] ?></td>
    </tr>
	<tr>
      <td>Status</td>
      <td width="90%"><?php echo $row["status"] ?></td>
    </tr>
	<tr>
      <td colspan="2"><a href="viewrequests.php?username=<?php echo $row["username"] ?>&id=<?php echo $row["id"] ?>&title=<?php echo $row["title"] ?>&cattitle=<?php echo "find"; ?>">Done</a></td>
    </tr>
  </tbody>
</table>
		  <p></p>

			<?php
			}
			?>
    
              
		<?php	
		}
	}
	else
	{
		echo "No new requsets has been added.";
	}
}
else
{
	?>
		  <table style="width: 100%">
			  <tr>
				  <td>
					  Enter category title
				  </td>
				  <td style="width: 80%">
					  <form method="get" action="includes/donerequest.php" enctype="multipart/form-data">
					  <input type="text" name="cattitle" style="width: 100%" />
					  <input type="hidden" name="username" value="<?php echo $_GET["username"] ?>" style="width: 100%" />
					  <input type="hidden" name="title" value="<?php echo $_GET["title"] ?>" style="width: 100%" />
					  <input type="hidden" name="id" value="<?php echo $_GET["id"] ?>" style="width: 100%" />
				  </td>
			  </tr>
			  <tr>
				  <td></td>
				  <td>
					  <input type="submit" name="submit" value="Send" style="width: 100%" />
					  </form>
				  </td>
			  </tr>
		  </table>
	<?php
}
    ?>
    
  </div>
    </div>
<?php
include_once ("template/footer.php");
?>
    <!-- End Right Column -->
    </div>


</body>
</html>
