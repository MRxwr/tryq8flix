<?php
include_once('admin/includes/config.php');
include_once('admin/includes/functions.php');
$url = urlencode($_GET['url']);
echo urldecode($url);
var_dump(curlCall($url));

?>