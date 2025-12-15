<?php 
require_once("../admin/includes/config.php");
require_once("../admin/includes/functions.php");
require_once("../../templates/simple_html_dom.php");

// get viewed page from pages folder \\
if( isset($_GET["v"]) && searchFile("views","blade{$_GET["v"]}.php") ){
	require_once("views/".searchFile("views","blade{$_GET["v"]}.php"));
}else{
	echo dataOutput(array("msg" => "404 view Not Found"));die();
}
?>