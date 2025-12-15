<?php
include_once ("includes/config.php");
include_once("includes/checksouthead.php");

if ( isset ($_GET["id"]) )
{
	$id = $_GET["id"];
}
if ( isset ($_GET["catid"]) )
{
	$id = $_GET["catid"];
}

if ( isset ($_GET["postnum"]) )
{
	$postnum = $_GET["postnum"];
}
else
{
	$postnum = "";
}


$sql = "SELECT * FROM category";
$result = $dbconnect->query($sql);
$NumberOfPosts = $result->num_rows;
$PostsPerPage = 100;
$NumberOfPages = ceil($NumberOfPosts / $PostsPerPage) ;


if ( !isset ( $_GET["page"] ) )
{
	$page = 1;
	$ppage = 1;
	$currentPage = 0;
}
else
{
	$page = $_GET["page"]+1;
	$ppage = $_GET["page"]-1;
	$currentPage = ($page * $PostsPerPage)-($PostsPerPage);
}

?>
<!DOCTYPE html>
<html>
<title>Latest Categories - Q8Flix</title>
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

    <?php

	$sql = "SELECT * FROM category ORDER BY id DESC LIMIT $currentPage,$PostsPerPage";
	$result = $dbconnect->query($sql);
	
	if ( $result->num_rows > 0 )
	{
		while ( $row = $result->fetch_assoc() )
		{?>
		  <?php
			$letnumtit = strlen($row["title"]); 
			$letnum = $letnumtit + 1;

if ( $username != "admin" )
	{
			?>
	
	<a target="" href="category.php?id=<?php echo $row["id"] ?>"><div class="w3-quarterindex" style="padding: 3px;  position: relative;text-align: center;color: white;">
    <img src="<?php echo $row["poster"] ?>" alt="" id="imageindex">
	  <div style="position: absolute;bottom: 1.5%;right: 1.5%;left: 1%;background: rgba(0, 0, 0, .8);"><b id="fontindex"><?php echo $row["title"]; ?></b>
</div></a>
	
  
              <?php 
}
	elseif ( $username == "admin" )
	{
		?>
		
	<a target="" href="category.php?id=<?php echo $row["id"] ?>"><div class="w3-quarterindex" style="padding: 3px;  position: relative;text-align: center;color: white;">
    <img src="<?php echo $row["poster"] ?>" alt="" id="imageindex">
	  <div style="position: absolute;bottom: 13%;right: 1.5%;left: 1%;background: rgba(0, 0, 0, .8);"><b id="fontindex"><?php echo $row["title"]; ?></b>
</div></a>
    <a  href="includes/deletecatdb.php?id=<?php echo $row["id"] ?>"><img src="images/delete.png" width="25" height="25"></a>
    <a  href="editcategory.php?id=<?php echo $row["id"] ?>"><img src="images/edit1.png" width="25" height="25"></a>
	<?php
	}
			?>
    
              
    </div>
		<?php	
		}
	}
	else
	{
		echo "No shows has been added yet.";
	}
    ?>
    
  </div>
<?php
if ( $postnum == "" )
{
if ( $page == 1 && $page < $NumberOfPages )
{
	echo "<center><a href='latest.php?page=$page'><img src='images/next.png' width='50' height='50'></a></center>";
}
if ( $page < $NumberOfPages && $page > 1)
{
	echo "<center><a href='latest.php?page=$ppage'><img src='images/prev.png' width='50' height='50'></a></a>";
	echo " <a href='latest.php?page=$page'><img src='images/next.png' width='50' height='50'></a></center>";
}
if ( $page == $NumberOfPages && $page > 1 )
{
	echo "<center><a href='latest.php?page=$ppage'><img src='images/prev.png' width='50' height='50'></a></a>";
}
}

?>
    </div>
<?php
include_once ("template/footer.php");
?>
    <!-- End Right Column -->
    </div>
    </div>
    
  <!-- End Grid -->
    
  </div>
  <!-- End Page Container -->
</div>



</body>
</html>
