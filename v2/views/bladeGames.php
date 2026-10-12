<?php include __DIR__ . '/header.php'; ?>

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
        <button class="btn btn--ghost btn--sm mb-3" id="games-back"><i class="fa-solid fa-arrow-left"></i> Back to games</button>
        <div id="game-container"></div>
    </div>
</main>

<?php
// Load the existing game modules unchanged from the live app root.
$gamesPath = dirname(dirname(__DIR__)) . '/games/';
if (is_dir($gamesPath)) {
    foreach (glob($gamesPath . "*.php") as $filename) {
        include $filename;
    }
}
?>

<?php include __DIR__ . '/footer.php'; ?>

<script>
(function () {
    var overlayClasses = [".runner-overlay-modal", ".dino-overlay", ".jumper-overlay", ".flap-overlay",
        ".drive-overlay", ".bricks-overlay", ".defender-overlay", ".memory-overlay",
        ".merge-overlay", ".smasher-overlay", ".code-overlay"];

    function loadGames() {
        $.getJSON(apiUrl("endpoint=Games/Home&action=list"), function (response) {
            var grid = document.getElementById("games-grid");
            if (!response || !response.ok || !response.data || !response.data.games) {
                var msg = (response && response.data && response.data.msg) || "Failed to load games.";
                grid.innerHTML = '<div class="center-state" style="grid-column:1/-1;"><i class="fa-solid fa-gamepad big"></i>' +
                    "<h3>No games available</h3><p>" + escapeHtml(msg) + "</p></div>";
                return;
            }
            grid.innerHTML = response.data.games.map(function (game) {
                return '<article class="game-card" data-game="' + Q8.escAttr(game.id) + '">' +
                    '<img class="game-card__img" src="' + Q8.escAttr(game.image) + '" alt="' + Q8.escAttr(game.title) + '" loading="lazy">' +
                    '<div class="game-card__body">' +
                        (game.tag ? '<span class="tag">' + escapeHtml(game.tag) + "</span>" : "") +
                        '<h3 class="game-card__title">' + escapeHtml(game.title) + "</h3>" +
                        '<p class="game-card__desc">' + escapeHtml(game.description || "") + "</p>" +
                        '<button class="btn btn--primary btn--block"><i class="fa-solid fa-play"></i> Play now</button>' +
                    "</div></article>";
            }).join("");

            grid.querySelectorAll(".game-card").forEach(function (card) {
                card.addEventListener("click", function () { playGame(card.getAttribute("data-game")); });
            });
        });
    }

    function showGamesHome() {
        document.getElementById("game-play-view").classList.add("hide");
        overlayClasses.forEach(function (sel) {
            document.querySelectorAll(sel).forEach(function (el) { el.style.display = "none"; });
        });
        document.getElementById("games-home-view").style.display = "";
        document.getElementById("game-container").innerHTML = "";
    }

    function playGame(gameId) {
        document.getElementById("games-home-view").style.display = "none";
        document.getElementById("game-play-view").classList.remove("hide");
        var initName = "init" + gameId.charAt(0).toUpperCase() + gameId.slice(1);
        if (typeof window[initName] === "function") window[initName]();
        else document.getElementById("game-container").innerHTML =
            '<div class="center-state"><i class="fa-solid fa-circle-question big"></i><h3>Game unavailable</h3></div>';
    }

    document.getElementById("games-back").addEventListener("click", showGamesHome);
    window.showGamesHome = showGamesHome;

    loadGames();
})();
</script>
