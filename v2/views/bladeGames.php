<?php
/**
 * Games Center — the grid, the frame that plays one game, and the shared board.
 *
 * Each game is embedded from game.php in its own document rather than inlined:
 * the modules ship global styles, full-page overlays and their own globals, so
 * one page for all of them breaks (see game.php for the details). This page only
 * lists what can be played, hosts one frame at a time, and renders the scores
 * every frame reports for the signed-in account.
 *
 * The board itself belongs to assets/js/scoreboard.js, the same file the frames
 * report through, so the two sides can never disagree about the shape of a row.
 */

// header.php expects the signed-in account; index.php does not pass it yet.
try { $user = $user ?? currentUser(); } catch (Throwable $error) { $user = null; }
$view = $view ?? 'Games';
include __DIR__ . '/header.php';
?>

<section class="page" aria-labelledby="games-heading">

    <div id="game-stage" hidden>
        <div class="section-heading">
            <div>
                <p class="eyebrow">PLAYING</p>
                <h2 id="game-stage-title">Game</h2>
            </div>
            <button class="button secondary" id="game-back" type="button">Back to games</button>
        </div>
        <p class="muted" id="game-stage-note">Loading the game…</p>
        <div class="game-frame">
            <iframe id="game-frame" title="Game" allow="fullscreen" tabindex="0"></iframe>
        </div>
    </div>

    <div id="games-home">
        <p class="eyebrow">ARCADE</p>
        <h1 id="games-heading">Games Center</h1>
        <p class="muted">Every game reports the score it finishes a run with, and your best in each one stays on this account. Play as a guest if you like — only saving needs an account.</p>
        <div class="game-grid" id="games-grid">
            <div class="skeleton"></div>
            <div class="skeleton"></div>
            <div class="skeleton"></div>
        </div>
    </div>

    <section class="panel" aria-labelledby="games-board-heading">
        <div class="section-heading">
            <div>
                <p class="eyebrow">SHARED BOARD</p>
                <h2 id="games-board-heading">Your best scores</h2>
            </div>
            <p class="muted" id="games-board-state" role="status" aria-live="polite"></p>
        </div>
        <div class="score-board" id="games-board"></div>
    </section>
</section>

<script src="assets/scoreboard.js" defer></script>
<script src="assets/games.js" defer></script>

<?php include __DIR__ . '/footer.php'; ?>
