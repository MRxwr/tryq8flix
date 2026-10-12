<?php
/**
 * TryQ8Flix v2 — front controller.
 *
 * Reuses the live backend for everything that matters:
 *   - admin/includes/config.php   (DB connection + tokens)
 *   - admin/includes/functions.php (checkLogin, DB helpers, dataOutput, …)
 *
 * Only the presentation layer is new. All data still flows through the
 * existing JSON API at ../api/index.php.
 */

require_once("../admin/includes/config.php");
require_once("../admin/includes/functions.php");
require_once("../try2/templates/simple_html_dom.php");

$profileData = checkLogin();

// Decrypt an encrypted navigation payload (?q=…) into normal query params.
if (isset($_GET['q'])) {
    $reversed = strrev($_GET['q']);
    $decoded = base64_decode($reversed);
    $queryString = urldecode($decoded);

    parse_str($queryString, $params);
    $_GET = array_merge($_GET, $params);
}

$view = isset($_GET["v"]) ? $_GET["v"] : "Home";

// Views reachable without a session.
$publicViews = ["Login", "Forget", "Register"];

if (empty($profileData['id']) && !in_array($view, $publicViews)) {
    header("Location: ?v=Login");
    exit();
}

// Signed-in users never need the login screen.
if (!empty($profileData['id']) && $view == "Login") {
    header("Location: ?v=Home");
    exit();
}

// Relative to the /v2/ directory the pages are served from.
$apiBase = "../api/index.php";
$rootBase = "../";

$viewFile = "views/blade{$view}.php";

if (isset($_GET["v"]) && !empty($_GET["v"])) {
    if (file_exists($viewFile)) {
        require_once($viewFile);
    } else {
        http_response_code(404);
        include __DIR__ . "/views/header.php";
        echo '<div class="page"><div class="center-state">'
            . '<i class="fa-solid fa-compass big"></i>'
            . '<h3>Page not found</h3>'
            . '<p>We could not find <strong>' . htmlspecialchars($view) . '</strong>. It may have moved.</p>'
            . '<a class="btn btn--primary" href="?v=Home"><i class="fa-solid fa-house"></i> Back home</a>'
            . '</div></div>';
        include __DIR__ . "/views/footer.php";
    }
} else {
    require_once("views/bladeHome.php");
}
