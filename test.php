<?php
include_once('admin/includes/config.php');
include_once('admin/includes/functions.php');

$url = isset($_GET["url"]) ? trim($_GET["url"]) : 'https://tuk.tuktukarab.cfd/?s=naruto&page=1';
// Fix spaces in URL
$url = str_replace(["\r", "\n", "\t", ' '], '', $url);

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
  CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36',
  CURLOPT_HTTPHEADER => array(
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
    'Accept-Encoding: gzip, deflate, br, zstd',
    'Accept-Language: en-US,en;q=0.9,ar-EG;q=0.8,ar;q=0.7',
    'Connection: keep-alive',
    'Upgrade-Insecure-Requests: 1',
    'DNT: 1',
    'Sec-Fetch-Dest: document',
    'Sec-Fetch-Mode: navigate',
    'Sec-Fetch-Site: none',
    'Sec-Fetch-User: ?1',
    'Sec-CH-UA: "Chromium";v="140", "Not=A?Brand";v="24", "Google Chrome";v="140"',
    'Sec-CH-UA-Mobile: ?0',
    'Sec-CH-UA-Platform: "Windows"',
    // Replace the cookie value below with your actual browser cookie if needed
    'Cookie: _ga=GA1.1.1539968354.1758401519; hidecta=no; cf_clearance=M0JWFRbBGiBGsouR5rl57FIhxKUYC0oraC9ooGEwJeI-1758403018-1.2.1.1-z3_BKDNzRqke8BfZhKfDmUgUZ4taiVTm2GfHCe5Aw9cCW5fRpZbanT2bE9420VZIc_0XC__1pfVxGk43_5Bjowj8EcOlswFfKJw5RPJ25vih7X8EMTTkd2OeAkI1X.ovKQ1y.o5MSlQaHOzJfkO.IYd1.k7MD6bNRfCkvUCUQh1j0D71oWJv5e_Y_KCaHvX1VsUt5zafd_OkQiM1pfT7E4QkEHLql7.rBJRE8pgGzD0; _ga_QLXVQBZ52S=GS2.1.s1758401518$o1$g1$t1758403506$j54$l0$h100827695'
  )
));

var_dump($response = curl_exec($curl));

curl_close($curl);
echo $response;

?>