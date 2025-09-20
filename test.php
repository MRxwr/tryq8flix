<?php
include_once('admin/includes/config.php');
include_once('admin/includes/functions.php');

var_dump(curlCall(str_replace("+"," ",$_GET['url'])));

?>