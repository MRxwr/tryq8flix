<?php
function uploadImage($imageLocation){
	$curl = curl_init();
	curl_setopt_array($curl, array(
	  CURLOPT_URL => 'https://api.imgbb.com/1/upload?expiration=600&key=d4aba98558417ca912f2669f469950c7',
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => '',
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => 'POST',
	  CURLOPT_POSTFIELDS => array('image'=> new CURLFILE($imageLocation)),
	));
	$response = json_decode(curl_exec($curl),true);
	curl_close($curl);
	if( isset($response["success"]) && $response["success"] == true ){
		file_put_contents("../logos/{$response["data"]["id"]}.{$response["data"]["image"]["extension"]}", file_get_contents($response["data"]["image"]["url"]));
		return "{$response["data"]["id"]}.{$response["data"]["image"]["extension"]}"; 
	}else{
		return "";
	}
}
?>