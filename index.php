<?php 
require_once("admin/includes/config.php");
require_once("admin/includes/functions.php");
require_once("try2/templates/simple_html_dom.php");

$profileData = checkLogin();

// Decrypt 'q' parameter if present
if (isset($_GET['q'])) {
    $encrypted = $_GET['q'];
    // Reverse string
    $reversed = strrev($encrypted);
    // Base64 decode
    $decoded = base64_decode($reversed);
    // URL decode
    $queryString = urldecode($decoded);
    
    // Parse query string into array
    parse_str($queryString, $params);
    
    // Merge into $_GET
    $_GET = array_merge($_GET, $params);
}

$view = isset($_GET["v"]) ? $_GET["v"] : "Home";

// Whitelist of views accessible without login
$publicViews = ["Login", "Forget"];

if (empty($profileData['id']) && !in_array($view, $publicViews)) {
    header("Location: ?v=Login");
    exit();
}

// Optional: Redirect logged-in users away from Login page
if (!empty($profileData['id']) && $view == "Login") {
    header("Location: ?v=Home");
    exit();
}

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