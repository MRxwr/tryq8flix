<?php 
header("Content-Type: application/json; charset=UTF-8");
require_once("../admin/includes/config.php");
require_once("../admin/includes/functions.php");
require_once("../templates/simple_html_dom.php");

if( isset($_SERVER['HTTP_AUTHORIZATION']) && !empty($_SERVER['HTTP_AUTHORIZATION']) ){
    $token = str_replace("Bearer ","",$_SERVER["HTTP_AUTHORIZATION"]);
}else{
    $token = "";
}

// get viewed page from pages folder \\
if( isset($_GET["endpoint"]) && searchFile("views","api{$_GET["endpoint"]}.php") ){
	require_once("views/".searchFile("views","api{$_GET["endpoint"]}.php"));
}else{
	echo dataOutput(array("msg" => "404 endpoint Not Found"));die();
}
?>