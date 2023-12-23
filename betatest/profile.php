<?php
include_once ("includes/config.php");
include_once("includes/checksouthead.php");

$sql = "SELECT * FROM users WHERE username like '$username'";
$result = $dbconnect->query($sql);
$row = $result->fetch_assoc();
$userid = $row["id"];

if ( isset ( $username ) )
{
	$sql = "SELECT * FROM users WHERE username like '$username'";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_assoc();
	$userid = $row["id"];
	if ( $userid == "" )
	{
		header("Location: index.php");
	}
}
elseif ( isset($_SESSION["username"]) )
{
	
	$username = $_SESSION["username"];
	$sql = "SELECT * FROM users WHERE username like '$username'";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_assoc();
}
else
{
	header("Location: index.php");
}

if ( !isset ($_GET["msg"]) )
{
	$msg = "";
}
else
{
	$msg = "Category has been deleted.";
}

$sql = "SELECT * FROM favourite WHERE username='$username'";
$result = $dbconnect->query($sql);
$NumberOfPosts = $result->num_rows;
$PostsPerPage = 20;
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
<title>Profile - Q8Flix</title>
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
	
 

.myButton {
	-moz-box-shadow:inset 0px 1px 0px 0px #ffffff;
	-webkit-box-shadow:inset 0px 1px 0px 0px #ffffff;
	box-shadow:inset 0px 1px 0px 0px #ffffff;
	background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #ededed), color-stop(1, #dfdfdf));
	background:-moz-linear-gradient(top, #ededed 5%, #dfdfdf 100%);
	background:-webkit-linear-gradient(top, #ededed 5%, #dfdfdf 100%);
	background:-o-linear-gradient(top, #ededed 5%, #dfdfdf 100%);
	background:-ms-linear-gradient(top, #ededed 5%, #dfdfdf 100%);
	background:linear-gradient(to bottom, #ededed 5%, #dfdfdf 100%);
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#ededed', endColorstr='#dfdfdf',GradientType=0);
	background-color:#ededed;
	-moz-border-radius:6px;
	-webkit-border-radius:6px;
	border-radius:6px;
	border:1px solid #dcdcdc;
	display:inline-block;
	cursor:pointer;
	color:#777777;
	font-family:Arial;
	font-size:15px;
	font-weight:bold;
	padding:6px 24px;
	text-decoration:none;
	text-shadow:0px 1px 0px #ffffff;
	width: 100%;
}
.myButton:hover {
	background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #dfdfdf), color-stop(1, #ededed));
	background:-moz-linear-gradient(top, #dfdfdf 5%, #ededed 100%);
	background:-webkit-linear-gradient(top, #dfdfdf 5%, #ededed 100%);
	background:-o-linear-gradient(top, #dfdfdf 5%, #ededed 100%);
	background:-ms-linear-gradient(top, #dfdfdf 5%, #ededed 100%);
	background:linear-gradient(to bottom, #dfdfdf 5%, #ededed 100%);
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#dfdfdf', endColorstr='#ededed',GradientType=0);
	background-color:#dfdfdf;
}
.myButton:active {
	position:relative;
	top:1px;
}
</style>
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
        xmlhttp.open("GET","live/profile_fav_update.php?q="+str,true);
        xmlhttp.send();
    }
</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
<script>
	$(document).ready(function(){
		
		$.ajax({
			url: 'live/profile_fav_update.php',
			success: function(data){
				
				$("#txtHint").html(data);
			}
		})
	});
</script>

<body>
<!-- Page Container -->
<div class="w3-content" style="max-width:1300px">
 <?php
include_once ("template/header.php");
?> 
  <!-- The Grid -->
  <div class="w3-row-padding">

    <!-- Left Column -->
    <div class="w3-third" style="padding-top: 40px">
    
      <div class="w3-text-white">
        <div class="w3-display-container">
          <img src="<?php if ( $row["avatar"] != "" ){echo $row["avatar"];}else{echo "https://i.imgur.com/FSxhsRh.png";} ?>" style="width:100%"  alt="Avatar">
          <div class="w3-display-bottomleft w3-container w3-text-black">
          </div>
        </div>
        <div class="w3-container">
       <p><h2 style="text-align: center"><?php echo $row["username"] ?></h2><hr>
          <p><div style="text-align: justify;"><?php echo $row["description"] ?></div></p><hr>
          <p><?php echo $row["email"] ?></p><hr>
          <p>
          <?php
          
		  if ( $_SESSION["username"] == $username )
		  {
			  ?>
			  <a class="myButton" style="text-align: center" href="editprofile.php?username=<?php echo $username ?>">Edit Profile</a>
		  	 <?php 
		  }
		  ?>
 
        </div>
      </div>

    <!-- End Left Column -->
    </div>

    <!-- Right Column -->
    <div class="w3-twothird" style="padding-top: 40px">
    <div class="w3-text-white">
      <div class="w3-row-padding w3-padding-16 w3-center" id="food">
    <h1><?php echo $msg ?></h1>
  <h1>Favourites</h1>
<div id="txtHint" ></div>

  </div>
    <?php
if ( $page == 1 && $page < $NumberOfPages )
{
	echo "<center><a href='profile.php?page=$page'>next page</a></center>";
}
if ( $page < $NumberOfPages && $page > 1)
{
	echo "<center><a href='profile.php?page=$ppage'>previous page</a>";
	echo " <a href='profile.php?page=$page'>next page</a></center>";
}
if ( $page == $NumberOfPages && $page > 1 )
{
	echo "<center><a href='profile.php?page=$ppage'>previous page</a></center>";
}
	?>
    <!-- End Right Column -->
    </div>
    </div>

  <!-- End Grid -->
  </div>
      <?php
include_once ("template/footer.php");
?>
  <!-- End Page Container -->
</div>



</body>
</html>
