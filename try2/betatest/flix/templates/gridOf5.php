<?php
for ( $i = 1 ; $i < 6 ; $i++ ){
	if ( $i == 1 ){
		$type = "%movie%'";
		$typeTitle = "Movies";
	}elseif( $i == 2 ){
		$type = "%tv-show%'
				AND
				c.genre NOT LIKE '%ramadan%'";
		$typeTitle = "TV-Shows";
	}elseif( $i == 3 ){
		$type = "%anime%'";
		$typeTitle = "Animes";
	}elseif( $i == 4 ){
		$type = "%animov%'";
		$typeTitle = "Aniem Movies";
	}elseif( $i == 5 ){
		$type = "%wrestling%'";
		$typeTitle = "Wrestling";
	}
/*elseif( $i == 2 ){
	$type = "%tv-show%'
			AND
			c.genre LIKE '%ramadan%'";
	$typeTitle = "Ramadan TV-Shows";
}*/
?>
	<h2 style=""><?php echo $typeTitle ?></h2>

	<div class="row w-100 m-0 mb-5">
	<?php
	$sql = "SELECT DISTINCT
			MAX(p.id) as realID, MAX(p.title) as realTitle, p.catid, c.poster , c.title, c.genre, c.description, c.imdbrating, c.releasedate, c.channel
			FROM posts AS p
			JOIN category AS c
			ON c.id = p.catid
			WHERE
			c.type LIKE '".$type."
			GROUP BY p.catid
			ORDER BY `realID` DESC
			LIMIT 2
			";
	$result = $dbconnect->query($sql);
	while ( $row = $result->fetch_assoc() ){
	?>
		<div class="col-xl-4 col-lg-4 col-md-6 p-1 colHeight">
			<div class="row w-100 m-0">
				<div class="col">
					<a href="?id=<?php echo $row["catid"] ?>"><h5 class="mDetail" ><?php echo $row["title"] ?></h5></a>
				</div>
			</div>
			<div class="row w-100 m-0">
			
				<div class="col-xl-6 col-lg-4 co-md-4 col-sm-6 col-xs-6 col-6">
					<a href="?id=<?php echo $row["catid"] ?>"><img class="indexImages" src="<?php echo $row["poster"] ?>" ></a>
				</div>
				
				<div class="col-xl-6 col-lg-8 co-md-8 col-sm-6 col-xs-6 col-6">
				<?php
				if( strstr(strtolower($typeTitle),"movies") ==  false ){
				?>
				<p><b class="mDetail" style="color:orange">Ep:</b> <?php echo $row["realTitle"] ?></p> 
				<?php
				}
				?>
				<p><b class="mDetail" style="color:gold">IMDb:</b> <?php echo $row["imdbrating"] ?></p>
				<p><b class="mDetail">Year:</b> <?php echo $row["releasedate"] ?></p>
				<p><b class="mDetail">Genre:</b> <?php echo $row["genre"] ?></p>
				<p><b class="mDetail">Cast:</b> 
				<?php
				$cast = str_replace("?singlequtation?", "`", $row["channel"]);
				$cast = explode(',',$cast);
				for($y = 0; $y < sizeof($cast) ; $y++){
					echo '<a href="?searchid=1&cast=1&query='.$cast[$y].'">' .$cast[$y]. '</a>, ';
				}
				?>
				</p>
				</div>
				
			</div>
		</div>
	<?php
	}
	?>
	
	<div class="col-xl-4 col-lg-4 col-md-6 p-1 colHeight text-center">
		<a href="?typeid=<?php echo $i ?>"><img class="moreImage" src="https://i.imgur.com/M5Tx0FE.png" ></a>
	</div>
		
	</div>

<?php
}
?>