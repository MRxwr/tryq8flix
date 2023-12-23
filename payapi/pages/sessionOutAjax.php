<?php 
include('../languages/lang_config.php');
include('../admin/config/apply.php');
include('../includes/functions.php');
// session id
$session_id = session_id();
  $searches=delete_tempBookingDateTimeBySession($session_id); 
  echo 1;
		
?>