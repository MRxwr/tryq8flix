<?php
require('constants.php');
$i = 0;
if ( !empty($_POST['id']) ){
	
	$sql = "SELECT 
			`uptobox`
			FROM 
			`postlinks`
			WHERE 
			`id` LIKE '".$_POST['id']."'
			";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_assoc();
	if ( $result->num_rows > 0 ){
		$response['ok'] = true;
		$response['status']= $succeed;
		$response['msg']="Link is ready";
		
		$getCode = explode(".com/",$row["uptobox"]);
		if ( !isset($getCode[1]) ){
			$response['msg']="Downlaod link is not set.";
			echo json_encode($response);
			die();
		}
		$url = "https://uptobox.com/api/link?token=c7592f3d7e8a2c6682fb51ebd2e9d96f6uvoo&file_code=".$getCode[1];
		$return = json_decode(file_get_contents($url),true);
		if ( !isset($return["data"]["dlLink"]) OR $return["data"]["dlLink"] == "" ){
			$response['msg']="Server Error. no downlaod link available.";
			echo json_encode($response);
			die();
		}
		
		$response['data']["download"] = $return["data"]["dlLink"];
		
	}else{
		$response['msg']="no downlaod link available.";
		echo json_encode($response);
		die();
	}
	
	echo json_encode($response);
	die();
}else{
	$response['msg']="Please enter info correctly.";
	echo json_encode($response);
	die();
}
?>