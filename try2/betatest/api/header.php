<?php
$servername = "localhost";
$username = "u905492195_mrnsr";
$password = "N@b$90949089";
$dbname = "u905492195_tryq8"; 
$dbconnect = new MySQLi($servername,$username,$password,$dbname);
if ( $dbconnect->connect_error )
{
	die("Connection Failed: " .$dbconnect->connect_error );
}

require_once("../includes/checksouthead.php");

date_default_timezone_set('Asia/Kuwait');

if ( isset($_POST["headerData"]) ){
	$sql = "SELECT * FROM `category` WHERE `id` LIKE '{$_POST["headerData"]}'";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_assoc();
	
	$trailer = str_replace("watch?v=","embed/",str_replace("autoplay=1","",$row["trailer"]));
	$trailer = explode("?", $trailer);
	
	if ( strtolower($row["type"]) == "animov" ){
		$type = "ANIME MOVIE";
	}else{
		$type = strtoupper($row["type"]);
	}
	
	$data = $row["title"] . "^" . $row["rating"] . "^" . $row["imdbrating"] . "^" . $row["duration"] . "^" . $row["genre"] . "^" . $row["releasedate"] . "^" . $row["language"] . "^" . $row["country"] . "^" . str_replace("?singlequtation?" ,"'",$row["channel"]) . "^" . $row["poster"] . "^" . str_replace("?singlequtation?","'",substr($row["description"],0,200)) . " ..." . "^" . $trailer[0] . "^" . $row["id"] . "^" . $row["notes"] . "^" . $type . "^" . $row["header"];
	
	echo $data;
}
?>