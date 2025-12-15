<?php
header('Content-type: application/json');
$servername = "localhost";
$username = "u905492195_mrnsr";
$password = "N@b$90949089";
$dbname = "u905492195_tryq8"; 

$dbconnect = new MySQLi($servername,$username,$password,$dbname);

if ( $dbconnect->connect_error )
{
	die("Connection Failed: " .$dbconnect->connect_error );
}
$response = array();
$ok = true;
$succeed = 200;
$error = 404;
//$baseUrl = $_SERVER["HTTP_HOST"]."/links/admin/checkout.php?id=";
$response['ok'] = false;
$response['status']= $error;

if ( isset(getallheaders()["q8flixapi"]) ){
	$headerAPI =  getallheaders()["q8flixapi"];
}else{
	$headerAPI = "";
}

if ( $headerAPI != "Q8FLiX" ){
	$response['msg']="Please check your API KEY";
	echo json_encode($response);die();
}

$website = "https://shed4u1.com/"; 
$website2 = "https://web5.topcinema.world";//"https://tuktukcima.art";
$website3 = "https://wecima.show";
$websiteLive = "https://shootz.yalla-shoot-tv.live/home18/";

require('../../admin/includes/functions.php');
require("../../templates/simple_html_dom.php");
?>