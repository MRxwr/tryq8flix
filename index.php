<?php 
require_once("admin/includes/config.php");
require_once("admin/includes/functions.php");
require_once("try2/templates/simple_html_dom.php");

// get viewed page from pages folder \\
if( isset($_GET["v"]) && file_exists("views/blade{$_GET["v"]}.php") ){
	require_once("views/blade{$_GET["v"]}.php");
}else{
    if(!isset($_GET["v"])){
        require_once("views/bladeHome.php");
    } else {
	    echo dataOutput(array("msg" => "404 view Not Found"));die();
    }
}
?>