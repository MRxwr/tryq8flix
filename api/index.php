<?php
header("Content-Type: application/json; charset=UTF-8");
require_once("../admin/includes/config.php");
require_once("../admin/includes/functions.php");
require_once("../try2/templates/simple_html_dom.php");

if (isset($_SERVER['HTTP_AUTHORIZATION']) && !empty($_SERVER['HTTP_AUTHORIZATION'])) {
    $token = str_replace("Bearer ", "", $_SERVER["HTTP_AUTHORIZATION"]);
} elseif (isset($_COOKIE['tryq8flix2']) && !empty($_COOKIE['tryq8flix2'])) {
    $token = $_COOKIE['tryq8flix2'];
} else {
    $token = "";
}

// get viewed page from pages folder \\
if (!isset($_GET["endpoint"]) || empty($_GET["endpoint"])) {
    echo dataOutput(array("msg" => "400 Bad Request - No endpoint specified"));
    die();
}
$endpoint = $_GET["endpoint"];
if (strpos($endpoint, '/') !== false) {
    $parts = explode('/', $endpoint);
    $filename = array_pop($parts);
    $folder = implode('/', $parts);
    echo $endpointFile = "views/{$folder}/api{$filename}.php";
} else {
    $endpointFile = "views/api{$endpoint}.php";
}
if (isset($_GET["endpoint"]) && file_exists($endpointFile)) {
    require_once($endpointFile);
} else {
    echo dataOutput(array("msg" => "404 endpoint Not Found"));
    die();
}
