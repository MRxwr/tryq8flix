<?php
//var_dump($_POST);
if(json_decode(file_get_contents('php://input'), true)){
	$_POST = json_decode(file_get_contents('php://input'), true);
}
    include('../languages/lang_config.php');
    include('../admin/config/apply.php');
    include('../includes/functions.php');
    header('Content-Type: application/json');
	@include('api_config.php');
?>