<?php
if (isset($_GET["selection"]) AND strtolower($_GET["selection"]) == "none"){
	unset($_GET["selection"]);
}else{
	if( isset($_GET["selection"]) AND $_GET["selection"] == "new" ){
		$_GET['new'] = "0";
	}
	
	if( isset($_GET["selection"]) AND $_GET["selection"] == "rating"){
		$_GET['rating'] = "0";
	}
	
	if( isset($_GET["selection"]) AND $_GET["selection"] == "views"){
		$_GET['views'] = "0";
	}
}

if (isset($_GET["genre"]) AND  strtolower($_GET["genre"]) == "none"){
	unset($_GET["genre"]);
}

if (isset($_GET["language"]) AND  strtolower($_GET["language"]) == "none"){
	unset($_GET["language"]);
}


$sql = "SELECT 
		p.*
		FROM 
		posts AS p
		WHERE 
		p.type LIKE '%".$_GET['type']."%' 
		AND 
		p.id IN 
		(
			SELECT 
			MAX(pp.id) 
			FROM 
			posts AS pp
			GROUP BY 
			pp.category
		)
		ORDER BY 
		p.id ".$_GET["arrang"]."
		";


if( !isset($_GET['language']) AND
	!isset($_GET['genre']) AND
	!isset($_GET["views"]) AND
	( isset($_GET['new']) OR isset($_GET['rating']) )
	){
	$sql = "SELECT 
			*
			FROM
			`category`
			WHERE 
			`type` LIKE '%".$_GET['type']."%' 
			";
	if ( isset($_GET['rating']) AND !isset($_GET['new']) ){
		$sql .= " ORDER BY `imdbrating` ".$_GET["arrang"]."";
	}elseif ( isset($_GET['new']) AND !isset($_GET['rating']) ){
		$sql .= " ORDER BY `releasedate` ".$_GET["arrang"]."";
	}
}

if( isset($_GET['language']) AND
	!isset($_GET['genre']) AND
	!isset($_GET["views"])
	){
	$sql = "SELECT 
			*
			FROM
			`category`
			WHERE 
			`type` LIKE '%".$_GET['type']."%' 
			AND
			`language` LIKE '%".$_GET['language']."%' 
			";
	if ( isset($_GET['rating']) AND !isset($_GET['new']) ){
		$sql .= " ORDER BY `imdbrating` ".$_GET["arrang"]."";
	}elseif ( isset($_GET['new']) AND !isset($_GET['rating']) ){
		$sql .= " ORDER BY `releasedate` ".$_GET["arrang"]."";
	}else{
		$sql .= " ORDER BY `id` ".$_GET["arrang"]."";
	}
}

if( isset($_GET['genre']) AND
	!isset($_GET['language']) AND
	!isset($_GET["views"])
	){
	$sql = "SELECT 
			*
			FROM
			`category`
			WHERE 
			`type` LIKE '%".$_GET['type']."%' 
			AND
			`genre` LIKE '%".$_GET['genre']."%' 
			";
	if ( isset($_GET['rating']) AND !isset($_GET['new']) ){
		$sql .= " ORDER BY `imdbrating` ".$_GET["arrang"]."";
	}elseif ( isset($_GET['new']) AND !isset($_GET['rating']) ){
		$sql .= " ORDER BY `releasedate` ".$_GET["arrang"]."";
	}else{
		$sql .= " ORDER BY `id` ".$_GET["arrang"]."";
	}
}

if( isset($_GET['genre']) AND
	isset($_GET['language']) AND
	!isset($_GET["views"])
	){
	$sql = "SELECT 
			*
			FROM
			`category`
			WHERE 
			`type` LIKE '%".$_GET['type']."%' 
			AND
			`genre` LIKE '%".$_GET['genre']."%' 
			AND
			`language` LIKE '%".$_GET['language']."%' 
			";
	if ( isset($_GET['rating']) AND !isset($_GET['new']) ){
		$sql .= " ORDER BY `imdbrating` ".$_GET["arrang"]."";
	}elseif ( isset($_GET['new']) AND !isset($_GET['rating']) ){
		$sql .= " ORDER BY `releasedate` ".$_GET["arrang"]."";
	}else{
		$sql .= " ORDER BY `id` ".$_GET["arrang"]."";
	}
}

if( isset($_GET['genre']) AND
	isset($_GET['language']) AND
	isset($_GET["views"])
	){
	$sql = "SELECT * , SUM(views) as tview
			FROM `posts` 
			WHERE 
			`catid` IN 
			(
				SELECT 
				`id` 
				FROM
				`category` 
				WHERE 
				`type` LIKE '%".$_GET['type']."%'
				AND
				`language` LIKE '%".$_GET['language']."%'
				AND
				`genre` LIKE '%".$_GET['genre']."%'
			) 
			GROUP BY 
			`catid`
			ORDER BY 
			CAST( SUM(views) as int ) ".$_GET["arrang"]."
			";
}

if( !isset($_GET['genre']) AND
	isset($_GET['language']) AND
	isset($_GET["views"])
	){
	$sql = "SELECT * , SUM(views) as tview
			FROM `posts` 
			WHERE 
			`catid` IN 
			(
				SELECT 
				`id` 
				FROM
				`category` 
				WHERE 
				`type` LIKE '%".$_GET['type']."%'
				AND
				`language` LIKE '%".$_GET['language']."%'
			) 
			GROUP BY 
			`catid`
			ORDER BY 
			CAST( SUM(views) as int ) ".$_GET["arrang"]."
			";
}

if( isset($_GET['genre']) AND
	!isset($_GET['language']) AND
	isset($_GET["views"])
	){
	$sql = "SELECT * , SUM(views) as tview
			FROM `posts` 
			WHERE 
			`catid` IN 
			(
				SELECT 
				`id` 
				FROM
				`category` 
				WHERE 
				`type` LIKE '%".$_GET['type']."%'
				AND
				`genre` LIKE '%".$_GET['genre']."%'
			) 
			GROUP BY 
			`catid`
			ORDER BY 
			CAST( SUM(views) as int ) ".$_GET["arrang"]."
			";
}

if( !isset($_GET['genre']) AND
	!isset($_GET['language']) AND
	isset($_GET["views"])
	){
	$sql = "SELECT * , SUM(views) as tview
			FROM `posts` 
			WHERE 
			`catid` IN 
			(
				SELECT 
				`id` 
				FROM
				`category` 
				WHERE 
				`type` LIKE '%".$_GET['type']."%'
			) 
			GROUP BY 
			`catid`
			ORDER BY 
			CAST( SUM(views) as int ) ".$_GET["arrang"]."
			";
}
$sql .= " LIMIT ".$startPage." , ".$numberOfPosts ."";

?>