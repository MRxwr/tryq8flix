<?php
/**
 * TryQ8Flix v2 — single-game host document.
 *
 * The modules in /games/ were written for the live app, which loads Bootstrap 5
 * globally and provides a #game-container to render into. They also each inject
 * their own global <style> and <script>, show full-page overlays, and several
 * declare the same top-level `let` names — so inlining all fourteen into one app
 * page (which the port originally did) left every game's modals and start screens
 * rendered as unstyled page content, and made duplicate declarations throw.
 *
 * Hosting one module per document fixes that: it gets exactly the environment it
 * expects and nothing it does can reach the app around it.
 */

$id = isset($_GET["g"]) ? strtolower(preg_replace("/[^A-Za-z0-9]/", "", $_GET["g"])) : "";
$modulePath = dirname(__DIR__) . "/games/" . $id . ".php";

// Only ever load a module that actually exists in /games/.
if ($id === "" || !is_file($modulePath)) {
    http_response_code(404);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Game unavailable</title>
        <style>
            html, body { height: 100%; margin: 0; background: #07070b; color: #a1a1b0;
                font-family: "Inter", "Helvetica Neue", Helvetica, Arial, system-ui, sans-serif; }
            body { display: flex; align-items: center; justify-content: center; }
            p { font-size: 0.9rem; }
        </style>
    </head>
    <body><p>That game isn&rsquo;t available right now.</p></body>
    </html>
    <?php
    exit;
}

// Convention every module follows: init + ucfirst(id).
$initFunction = "init" . strtoupper(substr($id, 0, 1)) . substr($id, 1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>TryQ8Flix · Game</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        /* The module brings its own skin. This is only a dark stage that centres
           it and keeps its absolutely-positioned overlays covering the frame. */
        html, body { height: 100%; margin: 0; overflow: hidden; background: #07070b; }
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            color: #f4f4f7;
            font-family: "Inter", "Helvetica Neue", Helvetica, Arial, system-ui, sans-serif;
            -webkit-font-smoothing: antialiased;
        }
        #game-container { width: 100%; }
    </style>
</head>
<body>
    <div id="game-container"></div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <?php include $modulePath; ?>

    <script>
        // Every module calls showGamesHome() from its Exit / Logout / Suspend
        // button. Here that means "close this game" — the Games view does the rest.
        function showGamesHome() {
            try { window.parent.postMessage({ type: "v2:game-exit" }, window.location.origin); }
            catch (e) { /* ignore */ }
        }

        function v2StartGame() {
            var start = window["<?= $initFunction ?>"];
            if (typeof start === "function") start();
            else showGamesHome();   // module present but couldn't start
        }

        if (document.readyState === "loading") {
            document.addEventListener("DOMContentLoaded", v2StartGame);
        } else {
            v2StartGame();
        }
    </script>
</body>
</html>
