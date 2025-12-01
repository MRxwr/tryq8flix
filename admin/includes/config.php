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
$website = "https://shhahid4u.diy/";
$website2 = "https://web6.topcinema.cloud/";
$website3 = "https://wecima.ac/we";
$website4 = "https://a.a5s0d.sbs/";
$website5 = "https://shahiid4u.live/";
$website6 = "https://vid.shahidwbas.tv/";
$website7 = "https://topcinema.surf/";
$website8 = "https://tuk.tuktukarab.cfd/";
$websiteLive = "https://yala-shoot-tv.live/";
$scrappingBeeToken = "0K5RT5UBE82PSAHTQGPJW0XSFE4AR92XXK9YCXF9H0VHSUZT0P2XJQCDOO2N60S332YVUSRD5T2NDOM3";
?>