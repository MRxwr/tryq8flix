<?php
jump:
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'http://www.tryq8flix.com/api/category.php',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_HTTPHEADER => array(
    'q8flixapi: Q8FLiX'
  ),
  CURLOPT_POSTFIELDS => array('id' => $_POST["id"]),
  
));

$response = curl_exec($curl);
$response = json_decode($response,true);
curl_close($curl);
if ( isset($response["data"]["Videos"]) AND sizeof($response["data"]["Videos"]) > 0 ){
	$output = "<div class='row w-100 m-0 p-0'>";
	for ($i = 0 ; $i < sizeof($response["data"]["Videos"]) ; $i++ ){
		$output .= "<div class='video col-12'id='{$response["data"]["Videos"][$i]["id"]}' ><div class='btn btn-warning w-100 p-3 m-1'>{$response["data"]["Videos"][$i]["Title"]}</div></div>";
	}
	$output .= "</div>";
}else{
	goto jump;
}

echo $output;

?>