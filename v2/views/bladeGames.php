<?php
/**
 * Games Center — the grid of games plus the play surface.
 *
 * Each game is embedded from game.php in its own document rather than inlined:
 * the modules ship global CSS/JS, full-page overlays and Bootstrap-dependent
 * dialogs, so one page for all of them breaks (see game.php for the details).
 * Only the list of module names is read here, to know what can be played.
 */
$gamesRoot = dirname(dirname(__DIR__)) . "/games/";
$availableGames = array();
if (is_dir($gamesRoot)) {
    foreach (glob($gamesRoot . "*.php") as $gameFile) {
        $availableGames[] = basename($gameFile, ".php");
    }
}

include __DIR__ . '/header.php';
?>

<main class="page page--wide">
    <div id="games-home-view">
        <div class="page-head">
            <div>
                <div class="eyebrow">Play</div>
                <h1 class="page-title">Games Center</h1>
                <p class="page-sub">Take a break with a quick game.</p>
            </div>
        </div>
        <div id="games-grid" class="game-grid">
            <div class="center-state" style="grid-column:1/-1;"><div class="spinner"></div></div>
        </div>
    </div>

    <div id="game-play-view" class="hide">
        <div class="row-between mb-3">
            <button class="btn btn--ghost btn--sm" id="games-back">
                <i class="fa-solid fa-arrow-left"></i> Back to games
            </button>
            <span class="faint xsmall" id="game-play-title"></span>
        </div>
        <div class="game-frame">
            <div class="game-frame__loading" id="game-loading"><div class="spinner"></div></div>
            <iframe id="game-frame" title="Game" tabindex="0" allow="fullscreen"></iframe>
        </div>
    </div>
</main>

<?php include __DIR__ . '/footer.php'; ?>

<script>
(function () {
    var AVAILABLE = <?= json_encode($availableGames) ?>;

    var homeView = document.getElementById("games-home-view");
    var playView = document.getElementById("game-play-view");
    var grid     = document.getElementById("games-grid");
    var frame    = document.getElementById("game-frame");
    var loading  = document.getElementById("game-loading");
    var playTitle = document.getElementById("game-play-title");

    function loadGames() {
        $.getJSON(apiUrl("endpoint=Games/Home&action=list"), function (response) {
            if (!response || !response.ok || !response.data || !response.data.games) {
                var msg = (response && response.data && response.data.msg) || "Failed to load games.";
                grid.innerHTML = '<div class="center-state" style="grid-column:1/-1;">' +
                    '<i class="fa-solid fa-gamepad big"></i><h3>No games available</h3>' +
                    "<p>" + escapeHtml(msg) + "</p></div>";
                return;
            }
            grid.innerHTML = response.data.games.map(function (game) {
                var title = game.title || game.id;
                return '<article class="game-card" data-game="' + Q8.escAttr(String(game.id).toLowerCase()) +
                        '" data-title="' + Q8.escAttr(title) + '">' +
                    '<img class="game-card__img" src="' + Q8.escAttr(game.image || "") + '" alt="' +
                        Q8.escAttr(title) + '" loading="lazy">' +
                    '<div class="game-card__body">' +
                        (game.tag ? '<span class="tag">' + escapeHtml(game.tag) + "</span>" : "") +
                        '<h3 class="game-card__title">' + escapeHtml(title) + "</h3>" +
                        '<p class="game-card__desc">' + escapeHtml(game.description || "") + "</p>" +
                        '<button class="btn btn--primary btn--block"><i class="fa-solid fa-play"></i> Play now</button>' +
                    "</div></article>";
            }).join("");

            grid.querySelectorAll(".game-card").forEach(function (card) {
                card.addEventListener("click", function () {
                    playGame(card.getAttribute("data-game"), card.getAttribute("data-title"));
                });
            });
        });
    }

    function showGamesHome() {
        playView.classList.add("hide");
        homeView.style.display = "";
        frame.src = "about:blank";   // stops whatever was running
        loading.classList.remove("hide");
        playTitle.textContent = "";
    }

    function playGame(gameId, title) {
        if (AVAILABLE.indexOf(gameId) === -1) {
            showToast("That game isn't available right now.", "fa-triangle-exclamation");
            return;
        }
        homeView.style.display = "none";
        playView.classList.remove("hide");
        loading.classList.remove("hide");
        playTitle.textContent = title || gameId;
        frame.src = "game.php?g=" + encodeURIComponent(gameId);
    }

    frame.addEventListener("load", function () {
        loading.classList.add("hide");
        try { frame.focus(); } catch (e) { /* ignore */ }
    });

    // A game asks to be closed when its own Exit / Logout button is used.
    window.addEventListener("message", function (e) {
        if (e.origin !== window.location.origin) return;
        if (e.source !== frame.contentWindow) return;
        if (!e.data || e.data.type !== "v2:game-exit") return;
        showGamesHome();
    });

    document.getElementById("games-back").addEventListener("click", showGamesHome);
    window.showGamesHome = showGamesHome;

    loadGames();
})();
</script>
