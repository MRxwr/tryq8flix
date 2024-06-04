<?php
function callCates($type){
	GLOBAL $dbconnect;
	$sql = "SELECT
			MAX(p.id) as realID, MAX(p.title) as realTitle, c.poster, c.imdbrating, c.releasedate, c.id as categoryId, c.title as categoryTitle
			FROM `posts` AS p
			JOIN `category` AS c
			ON p.catid = c.id
			WHERE
			catid IN
				(
					SELECT `id`
					FROM `category`
					WHERE
					`type` LIKE '%".$type."%'
				)
			GROUP BY p.catid
			ORDER BY realID DESC
			LIMIT 5";
	$result = $dbconnect->query($sql);
	$output = "";
	while ( $row = $result->fetch_assoc() ){
		$output .= '
		<div class="col-md-2 col-xs-6 col-sm-6 col-6">
		<a href="category?id='.$row["categoryId"].'"style="text-decoration:none;color:white">
			<div class="box1">
				<img src="'.$row["poster"].'" style="height: 250px;">
				<h3 class="title">'.$row["categoryTitle"] . " " . $row["realTitle"].'</h3>
				<ul class="icon">
					<li>'.$row["releasedate"].'</li>
					<li>'.$row["imdbrating"].'</li>
				</ul>
			</div>
			</a>
		</div>
	';
	}
	return $output;
}

function NumberOfCategories($type){
	GLOBAL $dbconnect;
	$sql = "SELECT `id` FROM `category` WHERE `type` LIKE '%".$type."%'";
	$result = $dbconnect->query($sql);
	$numberofmovies = $result->num_rows;
	return $numberofmovies;
}
?>