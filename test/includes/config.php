<?php
$servername = "localhost";
$username = "u905492195_mrnsr";
$password = "realmadridCR7";
$dbname = "u905492195_tryq8"; 

$dbconnect = new MySQLi($servername,$username,$password,$dbname);

if ( $dbconnect->connect_error )
{
	die("Connection Failed: " .$dbconnect->connect_error );
}
?>
