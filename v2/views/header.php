<?php
/** Shared modern layout head + navigation. Expects: $view, $profileData, $apiBase, $rootBase */
$v2View        = isset($view) ? $view : "Home";
$v2Profile     = isset($profileData) && is_array($profileData) ? $profileData : array("username" => "", "logo" => "", "id" => "");
$v2ApiBase     = isset($apiBase) ? $apiBase : "../api/index.php";
$v2RootBase    = isset($rootBase) ? $rootBase : "../";
$v2User        = trim((string)($v2Profile["username"] ?? ""));
$v2Initial     = $v2User !== "" ? strtoupper(substr($v2User, 0, 1)) : "?";
$v2Avatar      = !empty($v2Profile["logo"]) ? $v2RootBase . $v2Profile["logo"] : "";
$v2DefaultAvatar = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='120'%3E%3Cdefs%3E%3ClinearGradient id='g' x1='0' y1='0' x2='1' y2='1'%3E%3Cstop offset='0' stop-color='%23e50914'/%3E%3Cstop offset='1' stop-color='%23ff8a3d'/%3E%3C/linearGradient%3E%3C/defs%3E%3Crect width='120' height='120' rx='30' fill='url(%23g)'/%3E%3C/svg%3E";

$v2Nav = array(
    array("view" => "Home",            "label" => "Home",     "icon" => "fa-house",          "href" => "?v=Home"),
    array("view" => "Games",           "label" => "Games",    "icon" => "fa-gamepad",        "href" => "?v=Games"),
    array("view" => "LiveMatchesList", "label" => "Live",     "icon" => "fa-futbol",         "href" => "?v=LiveMatchesList"),
    array("view" => "Favorites",       "label" => "Saved",    "icon" => "fa-heart",          "href" => "?v=Favorites"),
    array("view" => "Search",          "label" => "Search",   "icon" => "fa-magnifying-glass", "href" => "?v=Search"),
    array("view" => "Settings",        "label" => "Settings", "icon" => "fa-gear",           "href" => "?v=Settings"),
);
$v2BottomNav = array(
    array("view" => "Home",            "label" => "Home",   "icon" => "fa-house",            "href" => "?v=Home"),
    array("view" => "Search",          "label" => "Search", "icon" => "fa-magnifying-glass", "href" => "?v=Search"),
    array("view" => "LiveMatchesList", "label" => "Live",   "icon" => "fa-futbol",           "href" => "?v=LiveMatchesList"),
    array("view" => "Favorites",       "label" => "Saved",  "icon" => "fa-heart",            "href" => "?v=Favorites"),
    array("view" => "Profile",         "label" => "Me",     "icon" => "fa-user",             "href" => "?v=Profile"),
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>TryQ8Flix<?= $v2View !== "Home" ? " · " . htmlspecialchars($v2View) : "" ?></title>
    <meta name="theme-color" content="#07070b">
    <meta name="description" content="Stream movies and TV shows on TryQ8Flix.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <link rel="manifest" href="<?= htmlspecialchars($v2RootBase) ?>v2/manifest.json">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="TryQ8Flix">
    <link rel="apple-touch-icon" href="<?= htmlspecialchars($v2RootBase) ?>logos/icon-192x192.png">

    <link rel="stylesheet" href="assets/css/theme.css">

    <script>
        // Paths consumed by core.js. Kept in the head so view scripts can rely on them.
        window.API_BASE = <?= json_encode($v2ApiBase) ?>;
        window.ROOT_BASE = <?= json_encode($v2RootBase) ?>;
    </script>
</head>
<body>
    <div class="page-fade"></div>

    <nav class="nav">
        <div class="nav__inner">
            <a class="brand" href="?v=Home">
                <span class="brand__mark"><i class="fa-solid fa-play"></i></span>
                <span class="brand__text">TRYQ8FLIX</span>
            </a>

            <div class="nav__links">
                <?php foreach ($v2Nav as $item): ?>
                    <a class="nav__link<?= $v2View === $item["view"] ? " is-active" : "" ?>" href="<?= $item["href"] ?>">
                        <i class="fa-solid <?= $item["icon"] ?>"></i> <?= $item["label"] ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <div class="nav__spacer"></div>

            <div class="nav__actions">
                <a class="icon-btn" href="?v=Search" aria-label="Search"><i class="fa-solid fa-magnifying-glass"></i></a>
                <a class="avatar-btn" href="?v=Profile" title="<?= htmlspecialchars($v2User ?: "Profile") ?>">
                    <?php if ($v2Avatar): ?>
                        <img class="avatar-img" src="<?= htmlspecialchars($v2Avatar) ?>" alt="" onerror="this.style.display='none'">
                    <?php else: ?>
                        <span class="avatar-img" style="display:grid;place-items:center;background:var(--grad);font-weight:800;font-size:.82rem;color:#fff;"><?= htmlspecialchars($v2Initial) ?></span>
                    <?php endif; ?>
                    <span><?= htmlspecialchars($v2User ?: "Guest") ?></span>
                </a>
            </div>
        </div>
    </nav>

    <nav class="bottom-nav">
        <?php foreach ($v2BottomNav as $item): ?>
            <a class="<?= $v2View === $item["view"] ? "is-active" : "" ?>" href="<?= $item["href"] ?>">
                <i class="fa-solid <?= $item["icon"] ?>"></i>
                <span><?= $item["label"] ?></span>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="install" id="install-banner">
        <p><i class="fa-solid fa-layer-group"></i> Install TryQ8Flix for the best experience.
            <span id="install-ios" class="hide"><br><span class="faint">Tap <strong>Share</strong> then <strong>Add to Home Screen</strong>.</span></span>
        </p>
        <div class="row">
            <button class="btn btn--primary btn--sm" id="install-accept"><i class="fa-solid fa-download"></i> Install</button>
            <button class="btn btn--ghost btn--sm" id="install-dismiss">Not now</button>
        </div>
    </div>
    <script>
        (function () {
            var deferred = null;
            var banner = document.getElementById("install-banner");
            if (!banner) return;
            var isIos = /iphone|ipad|ipod/i.test(navigator.userAgent) && !window.MSStream;
            var standalone = ("standalone" in navigator) && navigator.standalone;

            if (isIos && !standalone && !localStorage.getItem("v2_install_dismissed")) {
                document.getElementById("install-ios") && document.getElementById("install-ios").classList.remove("hide");
                document.getElementById("install-accept") && document.getElementById("install-accept").style.setProperty("display", "none");
                banner.style.display = "block";
            }
            window.addEventListener("beforeinstallprompt", function (e) {
                e.preventDefault();
                deferred = e;
                if (!localStorage.getItem("v2_install_dismissed")) banner.style.display = "block";
            });
            document.getElementById("install-accept") && document.getElementById("install-accept").addEventListener("click", function () {
                if (deferred) { deferred.prompt(); deferred = null; }
                banner.style.display = "none";
            });
            document.getElementById("install-dismiss") && document.getElementById("install-dismiss").addEventListener("click", function () {
                banner.style.display = "none";
                localStorage.setItem("v2_install_dismissed", "1");
            });
        })();
    </script>

    <!-- Transform target for tab swipes. Nav bars stay outside the shell so the
         fixed positioning survives the drag; header/footer delimit its content. -->
    <div class="app" id="app-shell" data-view="<?= htmlspecialchars($v2View) ?>">
