<?php
setcookie("tryq8flixAdmin", "", time() - (86400*30 ), "/");
session_start ();
if ( session_destroy() ){
	header("Location: login.php");
}
?>