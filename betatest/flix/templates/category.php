<?php
$id = $_GET["id"];
$sql= "SELECT `id`
		FROM `users`
		WHERE
		`username` LIKE '$username'
		";
$result = $dbconnect->query($sql);
$row = $result->fetch_assoc();
$userid = $row["id"];

$sql= "SELECT `categoryId`
		FROM `favourites`
		WHERE
		`userId` LIKE '".$userid."'
		AND
		`categoryId` LIKE '".$id."'
		";
$result = $dbconnect->query($sql);
if ( $result->num_rows > 0 ){
	$favoOn = 1;
}

if ( isset($_GET["q"]) )
{
	if ( isset($favoOn) ){
		$sql = "DELETE FROM `favourites`
				WHERE
				`userId` LIKE '".$userid."'
				AND
				`categoryId` LIKE '".$id."'
				";
		$result = $dbconnect->query($sql);
		$favoOn = 0;
	}else{
		$sql = "INSERT INTO `favourites`
				(`userId`, `categoryId`)
				VALUES
				('".$userid."', '".$id."')
				";
		$result = $dbconnect->query($sql);
		$categoryId = $id;
		$favoOn = 1;
	}
}else{
	$categoryId = $_GET["id"];
}

	
if ( isset($favoOn) AND $favoOn == 1 )
{
	$favoIcon = "<div align='center' style='width: 100%;'>
	<a onclick='showUser(". $id .")'><img src='../images/favoon1.png' style='width: 45px; height: 45px;'></a>
	<div style='font-size: 10px; padding-bottom:5px;'>Unfavorite</div>
	</div>";
}
else
{
	$favoIcon = "<div align='center'  style='width: 100%;'>
	<a onclick='showUser(". $id .")'><img src='../images/favooff1.png' style='width: 45px; height: 45px;'></a>
	<div style='font-size: 10px; padding-bottom:5px;'>Favorite</div>
	</div>";
}

$watchedposts = array();
$sql = "SELECT `videoId`
		FROM `watchedvideos`
		WHERE
		`userId` = '".$userID."'
		AND
		`categoryId` = '".$_GET["id"]."'
		";
$result = $dbconnect->query($sql);
while ( $row = $result->fetch_assoc() ){
	$watchedposts[] = $row["videoId"];
}

$sql = "SELECT *
		FROM `category`
		WHERE
		`id` LIKE '".$_GET["id"]."'
		";
$result = $dbconnect->query($sql);
$row = $result->fetch_assoc();
$trailer = $row["trailer"];
$title = $row["title"];
?>
<div id="txtHint" >
<div class="row w-100 m-0 " id="">
	<div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-12 text-center" style="background-image: url(<?php echo $row["poster"] ?>);
    background-size: cover;height:450px">
	<a data-toggle="modal" data-target="#exampleModal" ><img src="https://i.imgur.com/r0rpquf.png" class="youtube" id="" ></a>
	</div>
	<div class="col-xl-9 col-lg-9 col-md-9 col-sm-12 col-12" style="">
		<div class="row w-100 m-0 mb-2 mt-2">
		<div class="col-12">
		<h5 class="text-center" style="color:gold;font-size:40px;font-weight:700"><?php echo $row["title"] ?></h5>
		</div>
	</div>
	<div class="row w-100 m-0">

		<div class="col-12">
		<p><b class="mDetail" style="color:gold">IMDb:</b> <?php echo $row["imdbrating"] ?></p>
		<p><b class="mDetail" style="color:orange">Note:</b> <?php echo $row["notes"] ?></p>
		<p><b class="mDetail">Year:</b> <?php echo $row["releasedate"] ?></p>
		<p><b class="mDetail">Genre:</b> <?php echo $row["genre"] ?></p>
				<p><b class="mDetail">Cast:</b> <?php
				$cast = str_replace("?singlequtation?", "`", $row["channel"]);
				$cast = explode(',',$cast);
				for($y = 0; $y < sizeof($cast) ; $y++){
					echo '<a style="color:yellow" href="?searchid=1&cast=1&query='.$cast[$y].'">' .$cast[$y]. '</a>, ';
				}
				?></p>
		<p><b class="mDetail">Plot:</b><br> <?php echo str_replace("?singlequtation?", "`", $row["description"]) ?></p>
		<br>
		<div><?php echo $favoIcon ?></div>
		</div>
		
	</div>
</div>
	</div>


<div class="row w-100 m-0 mb-2 mt-5">
		<div class="col-12">
		<h5 class="text-center" style="color:gold;font-size:40px;font-weight:700">Videos</h5>
		</div>
	</div>
	<div class="row w-100 m-0">
	<?php
	$sql = "SELECT title, id
			FROM `posts`
			WHERE
			`catid` LIKE '".$_GET["id"]."'
			ORDER BY `title` DESC
			";
	$result = $dbconnect->query($sql);
	while($row = $result->fetch_assoc()){
		if ( in_array( $row["id"] , $watchedposts ) ){
			$color = "success";
		}else{
			$color = "warning";
		}
	?>
		<div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 col-6 text-center">
			<a href="?videoId=<?php echo $row["id"] ?>"><div class="btn btn-<?php echo $color ?> w-100 p-3 m-1">
			<?php echo $row["title"] ?>
			</div></a>
		</div>
	<?php
	}
	?>
	</div>
</div>

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
        xmlhttp.open("GET","?id=<?php echo $id ?>&q="+str,true);
        xmlhttp.send();
    }
</script>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" style="background-color: #272727;">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><?php echo $title ?></h5>
        <button type="button" class="close btn btn-secondary" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
	  
	  <iframe width="100%" height="315" src="<?php echo str_replace("watch?v=","embed/",$trailer) ?>" title="YouTube video player" frameborder="0" allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
	  
      </div>
    </div>
  </div>
</div>