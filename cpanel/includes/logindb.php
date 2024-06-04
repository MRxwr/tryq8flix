<?php
session_start ();
include_once ("../../admin/includes/config.php");
include_once ("../../admin/includes/functions.php");

$username = $_POST["username"];
$password = sha1($_POST["password"]);

if( $user = selectDB("superadmins","`username` = '{$username}' AND `password` = '{$password}'") ){
	setcookie("tryq8flixAdmin", md5($password), time() + (86400*30), "/");
	updateDB("superadmins",array('cookie'=>md5($password)), "`username` = '{$username}' AND `password` = '{$password}'");
	header("Location: ../index.php");
	exit();
}else{
	setcookie("tryq8flixAdmin", '', time() - (86400*30), "/");
	header("Location: ../login.php?error=p");
	exit();
}

?>