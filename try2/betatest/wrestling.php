<?php
include_once ("includes/config.php");
include_once ("includes/checksouthead.php");

//number of posts per page
$PostsPerPage = 24;

//total number of wrestling shows
$sql = "SELECT * FROM category WHERE type LIKE '%wrestling%'";
$result = $dbconnect->query($sql);
$NumberOfPosts = $result->num_rows;
$NumberOfPages = ceil($NumberOfPosts / $PostsPerPage);

//pagination
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
<title>Q8Flix - Wrestling</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<link rel="icon" href="images/logo.png">
<link rel="stylesheet" href="css/style1.css?dasd">
<link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Roboto'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
html,body,h1,h2,h3,h4,h5,h6 
{
    font-family: "Roboto", sans-serif;
}
.boxsizingBorder 
{
    -webkit-box-sizing: border-box;
       -moz-box-sizing: border-box;
            box-sizing: border-box;
}
.tags 
{
  display: inline;
  position: relative;
}
.tags:hover:after 
{
  background: #333;
  background: rgba(0, 0, 0, 0.8);
  border-radius: 5px;
  bottom: 125%;
  color: yellow;
  content: attr(glose);
  font-size: 18px;
  left: 0;
  padding: 5px 5px;
  position: absolute;
  z-index: 98;
  width: 100%;
}	
</style>

<body>
<!-- Page Container -->
	
<div class="w3-content" style="max-width:1300px;">
<?php
include_once ("template/header.php");
?>
<!-- The Grid -->
<div class="">
	  

	  
<!-- Right Column -->
<div class="w3-text-white" style="padding-top: 40px" >
<div class="w3-row-padding w3-padding-16 w3-center">
<?php
	$sql = "
	SELECT p.title, p.catid, p.poster, p.category 
	FROM posts AS p
	WHERE p.type LIKE '%wrestling%' 
	AND p.id IN 
	(
		SELECT MAX(pp.id) 
		FROM posts AS pp
		GROUP BY pp.category
	)
	ORDER BY p.id DESC
	LIMIT $currentPage,$PostsPerPage
	";
	$result = $dbconnect->query($sql);
	$lastid = 0;
	while ( $row = $result->fetch_assoc() )
	{
		$glose = $row["category"];
    ?>
    <a target="" class="tags" glose="<?php echo $glose; ?>" href="category.php?id=<?php echo $row["catid"] ?>">
    <div class="w3-quarterindex" style="padding: 3px;  position: relative;text-align: center;color: white;">
    <img src="<?php echo $row["poster"] ?>" alt="" id="imageindex">
    <div style="position: absolute;bottom: 1.5%;right: 1.5%;left: 1%;background: rgba(0, 0, 0, .8);">
    <b id="fontindex"><?php echo $row["title"] ?></b>
    </div>
    </a>
    </div>
	<?php		 
	}
?>
	</div>
    </div>
	<?php
		 
		 if ( $NumberOfPages != 0 )
		 {
		 if ( $page == 1 && $page < $NumberOfPages )
		 {
			 ?><center><a href='
			 <?php 
			 
			 echo 'wrestling.php?page='.$page; 

		  ?>'><img src='images/next1.png' width='50' height='50'></a></center>
		 <?php
		 }
		 if ( $page < $NumberOfPages && $page > 1)
		 {
			 echo "<center><table><tr><td><a onclick='goBack()'><img src='images/prev1.png' width='50' height='50'></a></td>";
			 ?><td><a href='
			 <?php 
			 
				echo 'wrestling.php?page='.$page; 

			 ?>'><img src='images/next1.png' width='50' height='50'></a></td></tr></table></center>
		 <?php
		 ;
		 }
		 if ( $page == $NumberOfPages && $page > 1 )
		 {
			 echo "<center><a onclick='goBack()'><img src='images/prev1.png' width='50' height='50'></a></a>";
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

<script>
function goBack() {
  window.history.back();
}
</script>

</body>
</html>
