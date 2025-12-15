<?php
require('includes/config.php');
require('includes/checkLogin.php');
require('includes/functions.php');

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.imgur.com/3/image/DEgDmP1',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'Authorization: Client-ID 386563124e58e6c'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;

if(isset($_POST["submit"])){
	$image = $_FILES["img"]["tmp_name"];
	$curl = curl_init();
	curl_setopt_array($curl, array(
	  CURLOPT_URL => 'https://api.imgur.com/3/image',
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => '',
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => 'POST',
	  CURLOPT_POSTFIELDS => array('image' => new CURLFILE($image)),
	  CURLOPT_HTTPHEADER => array(
		'Authorization: Client-ID 386563124e58e6c'
	  ),
	));
	$response = curl_exec($curl);
	curl_close($curl);
	echo $response;
}
?>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- Material Icons -->

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <!-- CSS File -->
    <link rel="stylesheet" href="css/styles.css" />
    <title>TRYQ8FiLX - Just Click Play</title>
  </head>
  <body>
	<form method="post" action="" enctype="multipart/form-data">
		<input type="file" name="img" class="form-control">
		<input type="submit" name="submit" class="btn btn-primary">
	</form>
  </body>
</html>