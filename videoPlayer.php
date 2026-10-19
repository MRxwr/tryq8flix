<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adaptive Video Player</title>
</head>
<body style="background-color: #1A1A1A;margin: auto;">
<?php
require("admin/includes/config.php");
require("admin/includes/functions.php");

$link = isset($_GET["link"]) ? trim($_GET["link"]) : "";
$scheme = strtolower((string) parse_url($link, PHP_URL_SCHEME));
$host = strtolower((string) parse_url($link, PHP_URL_HOST));

// Embed hosts are third-party; several of them disappear over time. A dead host
// leaves the frame black with no explanation, so check the name first (instant,
// no request) and say so instead of framing nothing.
$hostResolves = $host === "" ? false : checkdnsrr($host, "A");

// This page only frames the link: the embed hosts send no CORS headers, so the
// player cannot read them. It used to prefetch the link server-side first, which
// added ~1s to every play and could hang forever (CURLOPT_TIMEOUT => 0).
if ($link !== "" && ($scheme === "http" || $scheme === "https") && $hostResolves) {
    $src = htmlspecialchars($link, ENT_QUOTES, "UTF-8");
    echo "<iframe id='frame' src='{$src}' style='width:100%;height:100vh;border: none;overflow: hidden;' allow='autoplay; fullscreen; encrypted-media; picture-in-picture' allowFullScreen referrerpolicy='no-referrer'></iframe>";
} elseif ($link !== "" && ($scheme === "http" || $scheme === "https")) {
    echo "<div style='color:#eee;font-family:system-ui,sans-serif;padding:18vh 8% 0;text-align:center;line-height:1.6;'>"
        . "<p style='font-weight:700;font-size:1.2em;margin:0 0 8px;'>&#9888;&#65039; This source is offline</p>"
        . "<p style='color:#aaa;margin:0;'>The server <b>" . htmlspecialchars($host, ENT_QUOTES, "UTF-8") . "</b> cannot be reached. "
        . "Go back and pick another server for this title.<br>&#1575;&#1604;&#1587;&#1610;&#1585;&#1601;&#1585; &#1594;&#1610;&#1585; &#1605;&#1578;&#1575;&#1581; &#1581;&#1575;&#1604;&#1610;&#1575;&#1611;&#1548; &#1575;&#1582;&#1578;&#1585; &#1587;&#1610;&#1585;&#1601;&#1585; &#1570;&#1582;&#1585;.</p>"
        . "</div>";
} else {
    echo "لا يوجد روابط متاحه للمشاهده حاليا، الرجاء المحاولة لاحقاً";
}
?>
</body>
</html>
