<?php
jump:
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://www.tryq8flix.com/api/listOf.php',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => array('type' => $_POST["type"],'genre' => '','language' => '','rating' => '0','new' => '0','views' => '0','lastest' => '1'),
  CURLOPT_HTTPHEADER => array(
    'q8flixapi: Q8FLiX'
  ),
));

$response = curl_exec($curl);
$response = json_decode($response,true);
curl_close($curl);
if ( isset($response["data"]) AND sizeof($response["data"]) > 0 ){
	$output = "<div class='row w-100 m-0 p-0'>";
	for ($i = 0 ; $i < 51 ; $i++ ){
		$output .= "<div class='poster col-4 m-0 p-1' id='{$response["data"][$i]["id"]}'><div  class='w-100' ><img src='{$response["data"][$i]["Poster"]}' class='rounded w-100' style='height:180px'></div>"/*<label>{$response["data"][$i]["Title"]}</label>*/."</div>";
	}
	$output .= "</div>";
}else{
	goto jump;
}

echo $output;

?>