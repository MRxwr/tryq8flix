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
$website = "https://shid4u.cam/";
$website2 = "https://egydead.space/";
$websiteLive = "https://www.yalla-live.live/";
?>