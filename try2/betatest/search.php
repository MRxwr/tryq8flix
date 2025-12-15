<?php
include_once ("includes/config.php");
include_once("includes/checksouthead.php");
?>
<!DOCTYPE html>
<html>
<title>Search - Q8Flix</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="images/logo.png">
<link rel="stylesheet" href="css/style1.css?dasd">
<link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Roboto'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<style>
html,body,h1,h2,h3,h4,h5,h6 {font-family: "Roboto", sans-serif}
	input[name=search_text] {
  width: 100%;
  box-sizing: border-box;
  border: 2px solid #ccc;
  border-radius: 10px;
  font-size: 16px;
  background-color: white;
  background-image: url('https://www.w3schools.com/howto/searchicon.png');
  background-position: 10px 10px; 
  background-repeat: no-repeat;
  padding: 12px 20px 12px 40px;
  -webkit-transition: width 0.4s ease-in-out;
  transition: width 0.4s ease-in-out;
}

input[name=search_text]:focus {
  width: 100%;
}
 /* The side navigation menu */
</style>

<body>

<!-- Page Container -->
<div class="w3-content" style="max-width:1300px; min-height: 800px;">
<?php
include_once ("template/header.php");
?>
  <!-- The Grid -->
  <div class="">
 

    <!-- Right Column -->
    <div class="w3-text-white" style="min-height: 800px;padding-top: 40px">
      <div class="w3-row-padding w3-padding-16 w3-center" id="">
<?php
if ( isset ($_GET["search"]))
{
	goto jump;
}
?>
 <input name="search_text" id="search_text" type="text" placeholder="Type a title or genre or actors name ..." value="" autoFocus="autoFocus" >  
<p></p>
<div id="result"></div>
<div style="clear:both"></div>
<?php




if ( isset ($_GET["search"]))
{
	jump:
	$searchEntry = trim(preg_replace('/\s+/', ' ', $_GET["search"]));;
	$sql = "
	SELECT * FROM category 
	WHERE title LIKE '%".$searchEntry."%'
	or releasedate LIKE '%".$searchEntry."%'
	or channel LIKE '%".$searchEntry."%'
	";
	$result = $dbconnect->query($sql);

	if ( $result->num_rows > 0 )
	{
		while ( $row = $result->fetch_assoc() )
		{
			?>
            <a target="" href="category.php?id=<?php echo $row["id"] ?>">
			<div class="w3-quarterindex" style="padding: 3px;  position: relative;text-align: center;color: white;">
			<img src="<?php echo $row["poster"] ?>" alt="" id="imageindex">
			<div style="position: absolute;bottom: 1.5%;right: 1.5%;left: 1%;background: rgba(0, 0, 0, .8);">
			<b style="color:yellow"><?php echo $row["type"] ?></b>
			</div>
			</div>
			</a>
		<?php	
		}
	}
	else
	{
		?> <b style="font-size: 20px">We are sad that we could not find what you are looking for. Feel free to request it by <a target="" href="request.php" style="color: red">Clicking Here</a></b><br><br><br><img style="width: 50%" src="https://i.imgur.com/hTG23Qb.png"> <?php
	}
}
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


<script>
$(document).ready(function(){
	load_data();
	function load_data(query)
	{
		$.ajax({
			url:"live/fetch1.php",
			method:"post",
			data:{query:query},
			success:function(data)
			{
				$('#result').html(data);
			}
		});
	}
	
	$('#search_text').keyup(function(){
		var search = $(this).val();
		if(search != '')
		{
			load_data(search);
		}
		else
		{
			load_data();			
		}
	});
});
</script>

</body>
</html>