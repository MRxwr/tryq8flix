<?php
require('constants.php');
$i = 0;
if( !empty($_POST['type']) AND empty($_POST['language']) AND empty($_POST['genre']) ){
	$sql = "SELECT 
			*
			FROM
			`category`
			WHERE 
			`type` LIKE '%".$_POST['type']."%'
			LIMIT 100			
			";
	$result = $dbconnect->query($sql);
	$response['ok'] = true;
	$response['status']= $succeed;
	$response['msg']="Data has been generated";
	while ( $row = $result->fetch_assoc() ){
		$response['data'][$i]["Title"] = $row["title"];
		$titles[] = $row["title"];
		if ( strpos($row['releasedate'], ',') !== false ){
			$datesExp = explode(",",$row["releasedate"]);
			$dates[] = $datesExp[1];
		}
		else{
			$dates[] = $row["releasedate"];
		}
		$i++;
	}
	$y = 0;
	for ( $y = 0 ; $y < sizeof($titles) ; $y++ ){
		$sql = "UPDATE 
				`category`
				SET
				`releasedate` = '".$dates[$y]."'
				WHERE 
				`title` LIKE '".$titles[$y]."'		
				";
		$result = $dbconnect->query($sql);
	}
	echo json_encode($response);
	die();
}
?>