<?php
include_once('admin/includes/config.php');
include_once('admin/includes/functions.php');


$url = isset($_GET["url"]) ? trim($_GET["url"]) : 'https://tuk.tuktukarab.cfd/?s=naruto&page=1';
// Fix spaces in URL (but don't break & or =)
$url = preg_replace('/\s+/', '', $url);

echo "URL: " . htmlspecialchars($url) . "<br>";

$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_USERAGENT => 'PostmanRuntime/7.46.1',
  CURLOPT_HTTPHEADER => array(
    'Accept: */*',
    'Accept-Encoding: gzip, deflate, br',
    'Connection: keep-alive'
  )
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;

?>