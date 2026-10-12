/* ============================================================
   Q8Flix V2 — the Games Center page

   Lists what can be played, hosts one game frame at a time, and keeps the shared
   board in step with what the frames report. Everything the page knows about a
   game — its name, its artwork, this account's best in it, the site record —
   comes from one call into scoreboard.js, so the markup here stays dumb.
   ============================================================ */
(function (window, document) {
    "use strict";

    var grid = document.getElementById("games-grid");
    var home = document.getElementById("games-home");
    var stage = document.getElementById("game-stage");
    var frame = document.getElementById("game-frame");
    var stageTitle = document.getElementById("game-stage-title");
    var stageNote = document.getElementById("game-stage-note");
    var back = document.getElementById("game-back");
    var boardEl = document.getElementById("games-board");
    var boardState = document.getElementById("games-board-state");
    if (!grid || !home || !stage || !frame || !boardEl || !back || !window.Q8Score) return;

    var ART_FALLBACK = "assets/poster.svg";

    function paint(data) {
        window.Q8Score.renderBoard(boardEl, data.games);
        boardState.textContent = !data.signed_in
            ? "Sign in to keep a best score in every game."
            : (data.played ? data.played + " of " + data.total + " played" : "No runs saved yet.");
    }

    function refresh() {
        return window.Q8Score.board().then(paint).catch(function () {
            // A board that cannot refresh is not worth interrupting a game for.
        });
    }

    function card(game) {
        var el = document.createElement("article");
        el.className = "game-card";
        el.dataset.game = game.id;

        var art = document.createElement("img");
        art.src = game.image || ART_FALLBACK;
        art.alt = "";
        art.loading = "lazy";
        art.decoding = "async";
        art.addEventListener("error", function () { art.src = ART_FALLBACK; }, {once: true});

        var body = document.createElement("div");
        body.className = "game-card__body";
        if (game.tag) {
            var tag = document.createElement("span");
            tag.className = "tag";
            tag.textContent = game.tag;
            body.append(tag);
        }
        var name = document.createElement("h3");
        name.textContent = game.title || game.id;
        var blurb = document.createElement("p");
        blurb.textContent = game.description || "";
        var play = document.createElement("button");
        play.type = "button";
        play.className = "button";
        play.textContent = "Play";
        play.addEventListener("click", function () { start(game); });
        body.append(name, blurb, play);

        el.append(art, body);
        return el;
    }

    function start(game) {
        home.hidden = true;
        stage.hidden = false;
        stageTitle.textContent = game.title || game.id;
        stageNote.hidden = false;
        frame.title = (game.title || game.id) + " game";
        frame.src = "game.php?g=" + encodeURIComponent(game.id);
        frame.focus();
    }

    function close() {
        frame.src = "about:blank";   // stops whatever was running
        stage.hidden = true;
        home.hidden = false;
        refresh();                   // the last run's row is waiting as we return
    }

    function load() {
        window.Q8Score.board().then(function (data) {
            paint(data);
            grid.replaceChildren();
            (data.games || []).forEach(function (game) { grid.append(card(game)); });
        }).catch(function (error) {
            var note = document.createElement("p");
            note.className = "empty-state";
            note.textContent = "The games could not be loaded: " + error.message;
            grid.replaceChildren(note);
        });
    }

    // A frame reports its finished run; the board shows the row it moved and
    // then re-reads the whole thing, so the record column stays honest.
    window.Q8Score.onScore(frame, function (report) {
        window.Q8Score.flashRow(boardEl, report.game);
        refresh();
    });

    // A game asks to be closed from its own Exit / Logout button.
    window.addEventListener("message", function (event) {
        if (event.origin !== window.location.origin) return;
        if (event.source !== frame.contentWindow) return;
        if (!event.data || event.data.type !== "q8:game-exit") return;
        close();
    });

    frame.addEventListener("load", function () {
        if (frame.src && frame.src !== "about:blank") stageNote.hidden = true;
    });
    back.addEventListener("click", close);

    load();
})(window, document);
