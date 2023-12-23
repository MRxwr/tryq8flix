<?php
// db data connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test";

// Create connection
$dbconnect = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$dbconnect) {
  die("Connection failed: " . mysqli_connect_error());
}
date_default_timezone_set('Asia/Kuwait');
$date = date('Y-m-d H:i:s');
$website = "https://shahid4u1.charity/";
?>