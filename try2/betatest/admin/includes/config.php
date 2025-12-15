<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test";

$dbconnect = new MySQLi($servername,$username,$password,$dbname);

if ( $dbconnect->connect_error )
{
	die("Connection Failed: " .$dbconnect->connect_error );
}

$sql = "SET CHARACTER SET utf8";
$dbconnect->query($sql);
?>
