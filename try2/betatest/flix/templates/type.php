<?php
if ( $_GET["typeid"] == 1 ){
	$type = "%movie%'";
	$typeTitle = "Movies";
}elseif( $_GET["typeid"] == 2 ){
	$type = "%tv-show%'
			AND
			c.genre NOT LIKE '%ramadan%'";
	$typeTitle = "TV-Shows";
}elseif( $_GET["typeid"] == 3 ){
	$type = "%anime%'";
	$typeTitle = "Animes";
}elseif( $_GET["typeid"] == 4 ){
	$type = "%animov%'";
	$typeTitle = "Aniem Movies";
}elseif( $_GET["typeid"] == 5 ){
	$type = "%wrestling%'";
	$typeTitle = "Wrestling";
}
/*elseif( $_GET["typeid"] == 2 ){
	$type = "%tv-show%'
			AND
			c.genre LIKE '%ramadan%'";
	$typeTitle = "Ramadan TV-Shows";
}*/
?>
	<h2 style=""><a href="/betatest/flix">Home</a> -> <?php echo $typeTitle ?></h2>

	<div class="row w-100 m-0 mb-5">
	<?php
	if ( !isset($_GET["p"]) ){
		$page = 0;
		$p = 0;
	}else{
		$page = $_GET["p"]*9;
		$p = (int)$_GET["p"];
	}
	$sql = "SELECT DISTINCT
			MAX(p.id) as realID, MAX(p.title) as realTitle, p.catid, c.poster , c.title, c.genre, c.description, c.imdbrating, c.releasedate, c.channel
			FROM posts AS p
			JOIN category AS c
			ON c.id = p.catid
			WHERE
			c.type LIKE '".$type."
			GROUP BY p.catid
			ORDER BY `realID` DESC
			LIMIT ".$page.",9
			";
	$result = $dbconnect->query($sql);
	$numberOfPages = $result->num_rows;
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
	<?php
	if ( $p != 0 ){
		$link = "?typeid=".$_GET["typeid"]."&p=".($p-1);
	}else{
		$link = "#";
	}
	?>
	<div class="row w-100 m-0 mb-5">
		<div class="col-12 m-0 p-0 text-center">
			<nav aria-label="Page navigation example" style="font-size:20px; font-weight:700">
			  <ul class="pagination justify-content-center">
				<li class="page-item">
				  <?php
				if( $p == 0 ){
					?>
					<a class="page-link"  aria-label="Previous">
					<span aria-hidden="true">&laquo;</span>
				  </a>
					<?php
				}else{
					?>
					<a class="page-link" href="<?php echo $link ?>" aria-label="Previous">
					<span aria-hidden="true">&laquo;</span>
				  </a>
					<?php
				}
				?>
				</li>
				<?php
				if( $p == 0 ){
					?>
					<li class="page-item">
					<a class="page-link" href="?typeid=<?php echo $_GET["typeid"] ?>&p=<?php echo $p+1 ?>"><?php echo $p+1 ?></a>
					</li>
					<li class="page-item"><a class="page-link" href="?typeid=<?php echo $_GET["typeid"] ?>&p=<?php echo $p+2 ?>"><?php echo $p+2 ?></a></li>
					<li class="page-item"><a class="page-link" href="?typeid=<?php echo $_GET["typeid"] ?>&p=<?php echo $p+3 ?>"><?php echo $p+3 ?></a></li>
					<?php
				}else{
					?>
					<li class="page-item">
					<a class="page-link" href="?typeid=<?php echo $_GET["typeid"] ?>&p=<?php echo $p-1 ?>"><?php echo $p-1 ?></a>
					</li>
					<li class="page-item"><a class="page-link" ><?php echo $p ?></a></li>
					<li class="page-item"><a class="page-link" <?php if ( $numberOfPages == 9 ) { ?> href="?typeid=<?php echo $_GET["typeid"] ?>&p=<?php echo $p+1 ?>" <?php } ?> ><?php echo $p+1 ?></a></li>
					<?php
				}
				?>
				<li class="page-item">
				  <a class="page-link" <?php if ( $numberOfPages == 9 ) { ?> href="?typeid=<?php echo $_GET["typeid"] ?>&p=<?php echo $p+1 ?>" <?php } ?>  aria-label="Next">
					<span aria-hidden="true">&raquo;</span>
				  </a>
				</li>
			  </ul>
			</nav>
		</div>
	</div>