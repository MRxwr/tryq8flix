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
$website = "https://shahid4u.diy/";//"https://shed4u1.net/"; 
$website2 = "https://web5.topcinema.world";//"https://tuktukcima.art";
$website3 = "https://wecima.show";
$website4 = "https://26y1dqao5h.sbs/h/"; //egydead
$websiteLive = "https://shootz.yalla-shoot-tv.live/home18/"; //"https://www.yalla-kooora.live/";
$scrappingBeeToken = "0K5RT5UBE82PSAHTQGPJW0XSFE4AR92XXK9YCXF9H0VHSUZT0P2XJQCDOO2N60S332YVUSRD5T2NDOM3";
//"Y0U1YZ5TQUH84HVVU2U31E6II1D776MU2XAMLCGMDEF0YXCD0CWK4PLMG0S6FI0O5ZDILNYE63W3SWW4";
?>