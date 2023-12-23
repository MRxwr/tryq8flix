<?php
require('constants.php');
$i = 0;
if ( $_POST["lastest"] == 1 AND !empty($_POST['type']) AND empty($_POST['language']) AND empty($_POST['genre']) AND $_POST["views"] == 0 ){
	$sql = "SELECT 
			p.*
			FROM 
			posts AS p
			WHERE 
			p.type LIKE '%".$_POST['type']."%' 
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
			p.id DESC
			";
	$result = $dbconnect->query($sql);
	$response['ok'] = true;
	$response['status']= $succeed;
	$response['msg']="Data has been generated";
	while ( $row = $result->fetch_assoc() ){
		$response['data'][$i]["id"] = $row["catid"];
		$response['data'][$i]["Title"] = $row["category"];
		$response['data'][$i]["Poster"] = $row["poster"];
		$i++;
	}
	echo json_encode($response);
	die();
}

if( !empty($_POST['type']) AND empty($_POST['language']) AND empty($_POST['genre']) AND $_POST["views"] == 0 ){
	$sql = "SELECT 
			*
			FROM
			`category`
			WHERE 
			`type` LIKE '%".$_POST['type']."%' 
			";
	if($_POST['rating'] == 1 AND $_POST['new'] == 1 ){
		$sql .= " ORDER BY `imdbrating`, `releasedate` DESC";
	}elseif ( $_POST['rating'] == 1 AND $_POST['new'] == 0 ){
		$sql .= " ORDER BY `imdbrating` DESC";
	}elseif ( $_POST['new'] == 1 AND $_POST['rating'] == 0 ){
		$sql .= " ORDER BY `releasedate` DESC";
	}else{
		$sql .= " ORDER BY `id` DESC";
	}
	$result = $dbconnect->query($sql);
	$response['ok'] = true;
	$response['status']= $succeed;
	$response['msg']="Data has been generated";
	while ( $row = $result->fetch_assoc() ){
		$response['data'][$i]["id"] = $row["id"];
		$response['data'][$i]["Title"] = $row["title"];
		$response['data'][$i]["IMDb"] = $row["imdbrating"];
		$response['data'][$i]["Year"] = $row["releasedate"];
		$response['data'][$i]["Genre"] = $row["genre"];
		$response['data'][$i]["Duration"] = $row["duration"];
		$response['data'][$i]["Rating"] = $row["rating"];
		$response['data'][$i]["Country"] = $row["country"];
		$response['data'][$i]["Language"] = $row["language"];
		$response['data'][$i]["Cast"] = $row["channel"];
		$response['data'][$i]["Trailer"] = $row["trailer"];
		$response['data'][$i]["Details"] = $row["description"];
		$response['data'][$i]["Poster"] = $row["poster"];
		$i++;
	}
	echo json_encode($response);
	die();
}

if( !empty($_POST['type']) AND !empty($_POST['language']) AND empty($_POST['genre']) AND $_POST["views"] == 0 ){
	$sql = "SELECT 
			*
			FROM
			`category`
			WHERE 
			`type` LIKE '%".$_POST['type']."%' 
			AND
			`language` LIKE '%".$_POST['language']."%' 
			";
	if($_POST['rating'] == 1 AND $_POST['new'] == 1 ){
		$sql .= " ORDER BY `imdbrating`, `releasedate` DESC";
	}elseif ( $_POST['rating'] == 1 AND $_POST['new'] == 0 ){
		$sql .= " ORDER BY `imdbrating` DESC";
	}elseif ( $_POST['new'] == 1 AND $_POST['rating'] == 0 ){
		$sql .= " ORDER BY `releasedate` DESC";
	}else{
		$sql .= " ORDER BY `id` DESC";
	}
	$result = $dbconnect->query($sql);
	$response['ok'] = true;
	$response['status']= $succeed;
	$response['msg']="Data has been generated";
	while ( $row = $result->fetch_assoc() ){
		$response['data'][$i]["id"] = $row["id"];
		$response['data'][$i]["Title"] = $row["title"];
		$response['data'][$i]["IMDb"] = $row["imdbrating"];
		$response['data'][$i]["Year"] = $row["releasedate"];
		$response['data'][$i]["Genre"] = $row["genre"];
		$response['data'][$i]["Duration"] = $row["duration"];
		$response['data'][$i]["Rating"] = $row["rating"];
		$response['data'][$i]["Country"] = $row["country"];
		$response['data'][$i]["Language"] = $row["language"];
		$response['data'][$i]["Cast"] = $row["channel"];
		$response['data'][$i]["Trailer"] = $row["trailer"];
		$response['data'][$i]["Details"] = $row["description"];
		$response['data'][$i]["Poster"] = $row["poster"];
		$i++;
	}
	echo json_encode($response);
	die();
}

if( !empty($_POST['type']) AND !empty($_POST['genre']) AND empty($_POST['language']) AND $_POST["views"] == 0 ){
	$sql = "SELECT 
			*
			FROM
			`category`
			WHERE 
			`type` LIKE '%".$_POST['type']."%' 
			AND
			`genre` LIKE '%".$_POST['genre']."%' 
			";
	if($_POST['rating'] == 1 AND $_POST['new'] == 1 ){
		$sql .= " ORDER BY `imdbrating`, `releasedate` DESC";
	}elseif ( $_POST['rating'] == 1 AND $_POST['new'] == 0 ){
		$sql .= " ORDER BY `imdbrating` DESC";
	}elseif ( $_POST['new'] == 1 AND $_POST['rating'] == 0 ){
		$sql .= " ORDER BY `releasedate` DESC";
	}else{
		$sql .= " ORDER BY `id` DESC";
	}
	$result = $dbconnect->query($sql);
	$response['ok'] = true;
	$response['status']= $succeed;
	$response['msg']="Data has been generated";
	while ( $row = $result->fetch_assoc() ){
		$response['data'][$i]["id"] = $row["id"];
		$response['data'][$i]["Title"] = $row["title"];
		$response['data'][$i]["IMDb"] = $row["imdbrating"];
		$response['data'][$i]["Year"] = $row["releasedate"];
		$response['data'][$i]["Genre"] = $row["genre"];
		$response['data'][$i]["Duration"] = $row["duration"];
		$response['data'][$i]["Rating"] = $row["rating"];
		$response['data'][$i]["Country"] = $row["country"];
		$response['data'][$i]["Language"] = $row["language"];
		$response['data'][$i]["Cast"] = $row["channel"];
		$response['data'][$i]["Trailer"] = $row["trailer"];
		$response['data'][$i]["Details"] = $row["description"];
		$response['data'][$i]["Poster"] = $row["poster"];
		$i++;
	}
	echo json_encode($response);
	die();
}

if( !empty($_POST['type']) AND !empty($_POST['genre']) AND !empty($_POST['language']) AND $_POST["views"] == 0 ){
	$sql = "SELECT 
			*
			FROM
			`category`
			WHERE 
			`type` LIKE '%".$_POST['type']."%' 
			AND
			`genre` LIKE '%".$_POST['genre']."%' 
			AND
			`language` LIKE '%".$_POST['language']."%' 
			";
	if($_POST['rating'] == 1 AND $_POST['new'] == 1 ){
		$sql .= " ORDER BY `imdbrating`, `releasedate` DESC";
	}elseif ( $_POST['rating'] == 1 AND $_POST['new'] == 0 ){
		$sql .= " ORDER BY `imdbrating` DESC";
	}elseif ( $_POST['new'] == 1 AND $_POST['rating'] == 0 ){
		$sql .= " ORDER BY `releasedate` DESC";
	}else{
		$sql .= " ORDER BY `id` DESC";
	}
	$result = $dbconnect->query($sql);
	$response['ok'] = true;
	$response['status']= $succeed;
	$response['msg']="Data has been generated";
	while ( $row = $result->fetch_assoc() ){
		$response['data'][$i]["id"] = $row["id"];
		$response['data'][$i]["Title"] = $row["title"];
		$response['data'][$i]["IMDb"] = $row["imdbrating"];
		$response['data'][$i]["Year"] = $row["releasedate"];
		$response['data'][$i]["Genre"] = $row["genre"];
		$response['data'][$i]["Duration"] = $row["duration"];
		$response['data'][$i]["Rating"] = $row["rating"];
		$response['data'][$i]["Country"] = $row["country"];
		$response['data'][$i]["Language"] = $row["language"];
		$response['data'][$i]["Cast"] = $row["channel"];
		$response['data'][$i]["Trailer"] = $row["trailer"];
		$response['data'][$i]["Details"] = $row["description"];
		$response['data'][$i]["Poster"] = $row["poster"];
		$i++;
	}
	echo json_encode($response);
	die();
}

if( !empty($_POST['type']) AND !empty($_POST['genre']) AND !empty($_POST['language']) AND $_POST["views"] == 1 ){
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
				`type` LIKE '%".$_POST['type']."%'
				AND
				`language` LIKE '%".$_POST['language']."%'
			) 
			GROUP BY 
			`catid`
			ORDER BY 
			CAST( SUM(views) as int ) DESC
			";
	$result = $dbconnect->query($sql);
	$response['ok'] = true;
	$response['status']= $succeed;
	$response['msg']="Data has been generated";
	while ( $row = $result->fetch_assoc() ){
		$response['data'][$i]["id"] = $row["catid"];
		$response['data'][$i]["Title"] = $row["category"];
		$response['data'][$i]["Poster"] = $row["poster"];
		$response['data'][$i]["Views"] = $row["tview"];
		$i++;
	}
	echo json_encode($response);
	die();
}

$response['msg']="Please enter info correctly.";
echo json_encode($response);
die();

?>