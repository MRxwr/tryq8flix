<?php

include_once ("includes/config.php");
include_once ("includes/checksouthead.php");

$sql= "SELECT id FROM users WHERE username LIKE '$username'";
$result = $dbconnect->query($sql);
$row = $result->fetch_assoc();
$userid = $row["id"];

//getting main data
if ( isset ($_GET["id"]) )
{
	$id = $_GET["id"];
	$sql = "SELECT * FROM category WHERE id like '$id'";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_assoc();
	$categorytitle = $row["title"];
	if ( $categorytitle == "")
	{
		header("Location: index.php");
	}
}
if ( isset ($_GET["catid"]) )
{
	$id = $_GET["catid"];
}

//total number of posts in a season
if ( isset ($_GET["postnum"]) )
{
	$postnum = $_GET["postnum"];
}
else
{
	$postnum = "";
}

// checking errors
require("includes/errormsgs.php");

//getting category title
$sql = "SELECT * FROM category WHERE id like '$id'";
$result = $dbconnect->query($sql);
$row = $result->fetch_assoc();
$categorytitle = $row["title"];

?>
<!DOCTYPE html>
<html>
<title><?php echo $categorytitle ?> - Q8Flix</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="images/logo.png">
<link rel="stylesheet" href="css/style1.css?dasd">
<link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Roboto'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script>
function showUser(str) {
    if (window.XMLHttpRequest) 
		{
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } 
		else 
		{
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() 
		{
            if (this.readyState == 4 && this.status == 200) 
			{
                document.getElementById("txtHint").innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET","live/category_fav_update.php?id=<?php echo $id ?>&q="+str,true);
        xmlhttp.send();
    }
</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
<script>
	$(document).ready(function(){
		
		$.ajax({
			url: 'live/category_fav_update.php?id=<?php echo $id ?>',
			success: function(data){
				
				$("#txtHint").html(data);
			}
		})
	});
</script>

<body>
<!-- Page Container -->
<div class="w3-content" style="max-width:1300px;">

  <!-- The Grid -->
	  <?php
include_once ("template/header.php");
?>
  <div class="w3-row-padding" style="padding-top: 40px;">

    <!-- Left Column -->
    <div class="w3-third" style="padding: 5px">
    
      <div class="w3-text-white">
        <div class="w3-display-container">
<?php
	require("template/categorycontrols.php");
?>
			
<?php
	require("template/categoryinfo.php");
?>
        </div>
	  </div>
    <!-- End Left Column -->
    </div>
	
<?php
	//getting user id
	$sql = "SELECT id FROM users WHERE username = '$username'";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_assoc();
	$userid = $row["id"];
	
	//checing watched posts
	$sql= "SELECT * FROM finishedwatching WHERE userid = '$userid'";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_assoc();
	$watchedposts = explode(",",$row["postsid"]);
?>
    <!-- Right Column -->
    <div class="w3-twothird">
    <div class="w3-text-grey ">
    <div class="w3-row-padding w3-padding-16 w3-center">
	<h1><?php echo $errormsg ?></h1>
<?php
if ( isset($trailer) AND (isset($trailer) AND !empty($trailer)) )
{ 	?> 
	<iframe src="<?php 
	$trailer = str_replace("watch?v=","embed/",$trailer);
	$explode = explode("?",$trailer);
	echo $explode[0] ?>" style="width:100%;height:300px;border:0px" >Trailer</iframe><p>
	<hr>
	<?php 
} 
?>
<?php
	require("template/posts.php");
?>
  </div>
<?php
	require("template/moviecollections.php");
?>
    </div>
    <!-- End Right Column -->
    </div>
    </div>
    <?php
include_once ("template/footer.php");
?>
  <!-- End Grid -->
    
  </div>
  <!-- End Page Container -->
</div>

</body>
</html>