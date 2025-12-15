<?php
$sql = "SELECT MAX(videoId) as realId
		FROM `watchedvideos`
		WHERE
		`userId` LIKE '".$userID."'
		AND
		`videoId` IN ( SELECT `id` FROM `posts` WHERE `id` LIKE `videoId` )
		GROUP BY `categoryId`
		ORDER BY MAX(date) DESC
		LIMIT 100
		";
$result = $dbconnect->query($sql);
while ( $row = $result->fetch_assoc() ){
	$ids[] = $row["realId"];
}
?>
	<h2 style=""><a href="/betatest/flix">Home</a> -> History</h2>

	<div class="row w-100 m-0 mb-5">
	<?php
	for ( $i = 0 ; $i < sizeof($ids) ; $i++ ){
	$sql = "SELECT
			p.*, p.title as realTitle,  c.*
			FROM `posts` as p
			JOIN `category` as c
			ON
			p.catid = c.id
			WHERE
			p.id LIKE '".$ids[$i]."'
			";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_assoc()
	?>
		<div class="col-xl-4 col-lg-4 col-md-6 p-1 colHeight">
			<div class="row w-100 m-0">
				<div class="col">
					<a href="?id=<?php echo $row["catid"] ?>"><h5 class="mDetail" ><?php echo $row["category"] ?></h5></a>
				</div>
			</div>
			<div class="row w-100 m-0">
			
				<div class="col-xl-6 col-lg-4 co-md-4 col-sm-6 col-xs-6 col-6">
					<a href="?id=<?php echo $row["catid"] ?>"><img class="indexImages" src="<?php echo $row["poster"] ?>" ></a>
				</div>
				
				<div class="col-xl-6 col-lg-8 co-md-8 col-sm-6 col-xs-6 col-6">
				<?php 
				/*
				<p><b class="mDetail" style="color:orange">Ep:</b> <?php echo $row["realTitle"] ?></p> 
				*/
				?>
				<p><b class="mDetail" style="color:gold">IMDb:</b> <?php echo $row["imdbrating"] ?></p>
				<p><b class="mDetail">Year:</b> <?php echo $row["releasedate"] ?></p>
				<p><b class="mDetail">Genre:</b> <?php echo $row["genre"] ?></p>
				<p><b class="mDetail">Cast:</b> <?php
				$cast = str_replace("?singlequtation?", "`", $row["channel"]);
				$cast = explode(',',$cast);
				for($y = 0; $y < sizeof($cast) ; $y++){
					echo '<a href="?searchid=1&cast=1&query='.$cast[$y].'">' .$cast[$y]. '</a>, ';
				}
				?></p>
				</div>
				
			</div>
		</div>
	<?php
	}
	?>
	</div>
