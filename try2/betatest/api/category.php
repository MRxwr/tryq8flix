<?php
require('constants.php');
$i = 0;
if ( !empty($_POST['id']) ){
	$sql = "SELECT 
			*
			FROM 
			`category`
			WHERE 
			`id` LIKE '".$_POST['id']."'
			";
	$result = $dbconnect->query($sql);
	$response['ok'] = true;
	$response['status']= $succeed;
	$response['msg']="Data has been generated";
	$row = $result->fetch_assoc();
	$response['data']["id"] = $row["id"];
	$response['data']["Title"] = $row["title"];
	$response['data']["IMDb"] = $row["imdbrating"];
	$response['data']["Year"] = $row["releasedate"];
	$response['data']["Genre"] = $row["genre"];
	$response['data']["Duration"] = $row["duration"];
	$response['data']["Rating"] = $row["rating"];
	$response['data']["Country"] = $row["country"];
	$response['data']["Language"] = $row["language"];
	$response['data']["Cast"] = $row["channel"];
	$response['data']["Trailer"] = $row["trailer"];
	$response['data']["Details"] = $row["description"];
	$response['data']["Poster"] = $row["poster"];
	
	$sql = "SELECT 
			*
			FROM 
			`posts`
			WHERE 
			`catid` LIKE '".$_POST['id']."'
			ORDER BY `title` ASC
			";
	$result = $dbconnect->query($sql);
	while ( $row = $result->fetch_assoc()){
		$response['data']["Videos"][$i]["id"] = $row["id"];
		$response['data']["Videos"][$i]["Title"] = $row["title"];
		$response['data']["Videos"][$i]["Video"] = $row["videolink"];
		$response['data']["Videos"][$i]["Download"] = $row["download"];
		$response['data']["Videos"][$i]["Subtitle"] = $row["subtitle"];
		$i++;
	}
	
	echo json_encode($response);
	die();
}else{
	$response['msg']="Please enter info correctly.";
	echo json_encode($response);
	die();
}
?>