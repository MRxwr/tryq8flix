<?php
// db data connection
$servername = "localhost";
$username = "u905492195_mrnsr";
$password = "N@b$90949089";
$dbname = "u905492195_tryq8";

// Create connection
$dbconnect = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$dbconnect) {
  die("Connection failed: " . mysqli_connect_error());
}
date_default_timezone_set('Asia/Kuwait');
$date = date('Y-m-d H:i:s');
$website = "https://shvip.cam/";//"https://shed4u1.cam/";//"https://shid4u.cam/"; 
$website2 = "https://tuktukcima.art/recent/";
$websiteLive = "https://shootz.yalla-shoot-tv.live/home18/";
$scrappingBeeToken = "Y0U1YZ5TQUH84HVVU2U31E6II1D776MU2XAMLCGMDEF0YXCD0CWK4PLMG0S6FI0O5ZDILNYE63W3SWW4";
?>