
	<h2 style=""><a href="/betatest/flix">Home</a> -> Farourites</h2>

	<div class="row w-100 m-0 mb-5">
	<?php
	$sql = "SELECT *, c.id as realId
			FROM `favourites` as f
			JOIN `category` as c
			ON
			c.id = f.categoryId
			WHERE 
			f.userId LIKE '".$userID."'
			ORDER BY f.date DESC
			";
	$result = $dbconnect->query($sql);
	while ( $row = $result->fetch_assoc() ){
	?>
		<div class="col-xl-2 col-lg-2 col-md-6 col-sm-6 col-6 p-1 colHeight">
					<a href="?id=<?php echo $row["realId"] ?>"><img class="indexImages" src="<?php echo $row["poster"] ?>" ></a>
		</div>
	<?php
	}
	?>
	</div>