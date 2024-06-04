<?php
jump:
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'http://www.tryq8flix.com/api/watchDownload.php',
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
if ( isset($response["data"]["download"]) ){
	$output = "<div class='row w-100 m-0 p-0'>";
	$output .= "<div class='col-12'>
		<video width='100%' height='350' controls>
		  <source src='{$response["data"]["download"]}' type='video/mp4'>
		  <source src='{$response["data"]["download"]}' type='video/ogg'>
		</video>
		</div>";
	$output .= "</div>";
}else{
	goto jump;
}

echo $output;

?>