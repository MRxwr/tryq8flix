<?php
require('constants.php');

function searchShahid(){
	GLOBAL $website3, $_GET;
	$url = $website3;
	if( isset($_GET["category"]) && !empty($_GET["category"]) ){
		$url .= "/{$_GET["category"]}";
	}else{
		$url .= "/";
	}
	$html = scrapeWecima($url);
	return $html;
}

if ( $result = searchShahid() ){
    $response['ok'] = true;
    $response['status']= $succeed;
    $response['msg']="Data Retrieval Successful.";
    $response['details']['shows'] = $result;
}else{
    $response['ok'] = false;
    $response['status']= $error;
    $response['msg']="Data Retrieval Failed.";
    $response['details']['shows'] = [];
}

echo json_encode($response);
?>