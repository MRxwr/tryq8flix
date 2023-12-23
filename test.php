<?php
$clientId = '386563124e58e6c';
$imageUrl = 'https://createkuwait.com/images/15-03-2316788848811678884882.png';

// API endpoint for image upload
$apiEndpoint = 'https://api.imgur.com/3/image';

// cURL setup
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $apiEndpoint);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Authorization: Client-ID ' . $clientId,
    'Content-Type: application/x-www-form-urlencoded',
));

// Set the image URL as the POST data
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('image' => $imageUrl)));

// Execute cURL session
$response = curl_exec($ch);

// Close cURL session
curl_close($ch);

// Decode the JSON response
$result = json_decode($response, true);

// Check if the upload was successful
if ($result && isset($result['data']['link'])) {
    echo 'Image uploaded successfully! Imgur link: ' . $result['data']['link'];
} else {
    echo 'Image upload failed. Error: ' . print_r($result, true);
}
/*
// pull from instagram 

$curl = curl_init();

curl_setopt_array($curl, [
	CURLOPT_URL => "https://instagram-scraper-20231.p.rapidapi.com/postdetail/C0l5_X8tlk-",
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_ENCODING => "",
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 30,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => "GET",
	CURLOPT_HTTPHEADER => [
		"X-RapidAPI-Host: instagram-scraper-20231.p.rapidapi.com",
		"X-RapidAPI-Key: e1e4eb21b9msh475ffb7cfdf9329p1350c2jsnbd8b9595ad67"
	],
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
	echo "cURL Error #:" . $err;
} else {
	echo $response;
}
*/
?>
