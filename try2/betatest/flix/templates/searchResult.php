<?php
require('../../includes/config.php');
require('../../includes/checksouthead.php');

if ( !empty($_GET["query"]) ){
	$_GET["title"] = $_GET["query"];
}else{
	$_GET["title"] = "";
}

?>
<div class="row w-100 m-0 mb-5">
<?php
if ( !empty($_GET["title"]) ){
	if ( isset($_GET["cast"]) && $_GET["cast"] == 1 ){
		$sql = "SELECT * 
				FROM category AS c
				WHERE
				c.channel LIKE '%".$_GET['title']."%'
				ORDER BY `id` DESC
				LIMIT 100
				";
	}else{
		$sql = "SELECT * 
				FROM category AS c
				WHERE
				c.title LIKE '%".$_GET['title']."%'
				ORDER BY `id` DESC
				LIMIT 100
				";
	}

	$result = $dbconnect->query($sql);
	while ( $row = $result->fetch_assoc() ){
	?>
		<div class="col-xl-4 col-lg-4 col-md-6 p-1 colHeight">
			<div class="row w-100 m-0">
				<div class="col">
					<a href="?id=<?php echo $row["id"] ?>"><h5 class="mDetail"><?php echo $row["title"] ?></h5></a>
				</div>
			</div>
			<div class="row w-100 m-0">
			
				<div class="col-xl-6 col-lg-4 co-md-4 col-sm-6 col-xs-6 col-6">
					<a href="?id=<?php echo $row["id"] ?>"><img class="indexImages" src="<?php echo $row["poster"] ?>" ></a>
				</div>
				
				<div class="col-xl-6 col-lg-8 co-md-8 col-sm-6 col-xs-6 col-6">
				<p><b class="mDetail" style="color:gold">IMDb:</b> <?php echo $row["imdbrating"] ?></p>
				<p><b class="mDetail">Year:</b> <?php echo $row["releasedate"] ?></p>
				<p><b class="mDetail">Genre:</b> <?php echo $row["genre"] ?></p>
				<p><b class="mDetail">Cast:</b> 
				<?php
				$cast = str_replace("?singlequtation?", "`", $row["channel"]);
				$cast = explode(',',$cast);
				for($i = 0; $i < sizeof($cast) ; $i++){
					echo '<a href="?searchid=1&cast=1&query='.$cast[$i].'">' .$cast[$i]. '</a>, ';
				}
				?>
				</p>
				</div>
				
			</div>
		</div>
	<?php
	}
}
	?>
</div>