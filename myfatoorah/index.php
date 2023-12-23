<?php
$postdata = file_get_contents('php://input');
$data= json_decode( $postdata, TRUE ); 
//var_dump($data);
if(isset($data['baseurl']) && isset($data['postdata']) & isset($data['token']) && isset($data['point'])){
  $basURL=$data['baseurl'];
  $postData =$data['postdata'];
  $token = $data['token'];
  $point = $data['point'];
  //var_dump($data);
	 ####### Execute Payment ######
	 $curl = curl_init();
	 curl_setopt_array($curl, array(
	 CURLOPT_URL => "$basURL/v2/$point",
	 CURLOPT_CUSTOMREQUEST => "POST",
	 CURLOPT_POSTFIELDS => $postData,
	 CURLOPT_HTTPHEADER => array("Authorization: Bearer $token","Content-Type: application/json"),
	 ));
	 curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	 $response = curl_exec($curl);
	 $err = curl_error($curl);
	 curl_close($curl);
	echo($response); exit;
}
?>