<?php require('new/head.php'); ?>

<div class="header01">
   <div class="details2" style="text-align: center;">
   <table style="width:100%">
	<tr>
	<?php
	$sql = "SELECT
			f.id, c.poster, f.categoryId
			FROM `favourites` AS f
			JOIN `category` AS c
			ON c.id = f.categoryId
			WHERE
			`userId` LIKE '1'
			AND 
			`header` != ''
            AND
            f.id != '".$adminFav."'
			ORDER BY RAND()
			LIMIT 4
			";
	$result = $dbconnect->query($sql);
	while ( $row = $result->fetch_assoc() ){
		?>
		<td>
		<a class="headerPoster" id="<?php echo $row["categoryId"] ?>" ><img class="adminFavo" src="<?php echo $row["poster"] ?>" style="width:125px;height:150px;border-radius:10px"></a>
		</td>
		<?php
		}
	?>
	</tr>
   </table>
   </div>
   <?php
   $sql =  "SELECT *
			FROM `category`
			WHERE
			`id` LIKE '".$categoryId."'
			";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_assoc();
   ?>
   <div class="details" style="text-align: center;">
   <div class="movieTitle" style="color:gold; font-size: 30px; text-align: center;"><?php echo strtoupper($row["title"]) ?></div>
      <div class="rating">IMDb: <?php echo $row["imdbrating"] ?></div>
      <div class="year"><?php echo $row["releasedate"] ?></div>
      <div class="seasons"><?php if ( strtolower($row["type"]) == "animov" ) { echo "ANIME MOVIE";} else{ echo strtoupper($row["type"]);} ?></div>
      <div class="description"><?php echo str_replace("?singlequtation?","'",substr($row["description"],0,200)) . " ..."; ?></div><br>
      <table style=" width:100%"><tr><td><a href="category.php?id=<?php echo $row["id"] ?>" class="btn btn-warning w-50 visitCategory"><b>Start watching</b></a></td></tr></table>
   </div>
   
</div>	

<div class="pb-5"></div>

<?php
require ("template/favoCircles.php");
?>
<div class="pb-3"></div>
<?php
require ("template/contWatching.php");
?>

<div class="row mt-3">
<h3>Movies (<?php echo NumberOfCategories("movie") ?>)</h3>
	<?php 
	echo callCates("movie");
	?>
	<div class="col-md-2 col-xs-6 col-sm-6 col-6">
	<a href="cate?type=movie" style="text-decoration:none;color:white">
		<div class="box1">
			<img src="images/moremovies.png" style="height: 250px;">
			<h3 class="title">View more movies</h3>
		</div>
		</a>
	</div>
</div>

<div class="row mt-3">
<h3>TV-Shows (<?php echo NumberOfCategories("tv-show") ?>)</h3>
	<?php 
	echo callCates("tv-show");
	?>
	<div class="col-md-2 col-xs-6 col-sm-6 col-6">
	<a href="cate?type=tv-show" style="text-decoration:none;color:white">
		<div class="box1">
			<img src="images/moretvshows.png" style="height: 250px;">
			<h3 class="title">View more TV-shows</h3>
		</div>
		</a>
	</div>
</div>

<div class="row mt-3">
<h3>Animes (<?php echo NumberOfCategories("anime") ?>)</h3>
	<?php 
	echo callCates("anime");
	?>
	<div class="col-md-2 col-xs-6 col-sm-6 col-6">
	<a href="cate?type=anime" style="text-decoration:none;color:white">
		<div class="box1">
			<img src="images/moreaime.png" style="height: 250px;">
			<h3 class="title">View more animes</h3>
		</div>
		</a>
	</div>
</div>

<div class="row mt-3">
<h3>Anime Movies (<?php echo NumberOfCategories("animov") ?>)</h3>
	<?php 
	echo callCates("animov");
	?>
	<div class="col-md-2 col-xs-6 col-sm-6 col-6">
	<a href="cate?type=animov" style="text-decoration:none;color:white">
		<div class="box1">
			<img src="images/moreanimemovies.png" style="height: 250px;">
			<h3 class="title">View more anime movies</h3>
		</div>
		</a>
	</div>
</div>
 
<div class="row mt-3">
<h3>Wrestling (<?php echo NumberOfCategories("wrestling") ?>)</h3>
	<?php 
	echo callCates("wrestling");
	?>
	<div class="col-md-2 col-xs-6 col-sm-6 col-6">
	<a href="cate?type=wrestling" style="text-decoration:none;color:white">
		<div class="box1">
			<img src="images/morewrestling.png" style="height: 250px;">
			<h3 class="title">View more wrestling</h3>
		</div>
		</a>
	</div>
</div>

<?php require('new/fotter.php'); ?>