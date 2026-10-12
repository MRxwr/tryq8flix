/* ============================================================
   TryQ8Flix v2 — shared game scoreboard

   One board for every game. It is loaded in two places and that is the point:
     - inside every game frame (game.php), where it reads the score the module
       itself finishes with and saves it,
     - on the Games Center page, where it renders the board and hears each
       frame's result as it happens.

   The game modules are ports of the live app's games and are hosted untouched,
   so nothing here asks a game to call anything. Each module already ends a run
   by writing the final score into a `…-final-score` element and showing its
   `…#gameover…` overlay; that pair is the interface this reads. Games without
   both (Sudoku, Tic Tac Toe, Code Breaker) simply have nothing to report.

   Depends on: jQuery. Uses core.js when present (the Games page), no-ops without
   it (the frames).
   ============================================================ */
(function (window, $) {
    "use strict";

    var API_BASE = window.API_BASE || "../api/index.php";
    var GAME_RE = /^[a-z0-9]{1,32}$/;
    var BADGE_MS = 5200;

    function apiUrl(query) { return API_BASE + "?" + query; }

    function failed(reason) { var d = $.Deferred(); d.reject(reason); return d.promise(); }

    function esc(value) {
        return String(value == null ? "" : value)
            .replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;");
    }

    // core.js caches GETs for tab switching. The board must not come from that
    // cache: a score saved a moment ago has to appear, and a signed-out
    // "Unauthorized" must not linger over a sign-in. freshGetJSON reads the
    // network and records the answer.
    function reader() {
        return (window.Q8 && window.Q8.freshGetJSON) ? window.Q8.freshGetJSON : $.getJSON;
    }

    /* -------------------------------------------------- reading a score
       The modules print scores as text and in their own units: "12", "1 240",
       "320m". Take the first number and ignore the unit after it. */
    function numberFrom(text) {
        if (text == null) return null;
        var cleaned = String(text).replace(/[^0-9.]/g, "");
        if (cleaned === "" || cleaned === ".") return null;
        var value = parseFloat(cleaned);
        if (!isFinite(value)) return null;
        return Math.round(value);
    }

    // Styled visibility, not laid-out visibility: a frame can be painted in a
    // background tab, where element geometry is not available but the cascade
    // still says exactly what is shown.
    function isVisible(el) {
        for (var node = el; node && node.nodeType === 1; node = node.parentElement) {
            var cs = window.getComputedStyle(node);
            if (cs.display === "none" || cs.visibility === "hidden") return false;
        }
        return true;
    }

    /* -------------------------------------------------- api */
    function listScores() {
        return reader().call($, apiUrl("endpoint=Scores&action=list")).then(function (res) {
            return (res && res.ok) ? res.data : failed(res);
        });
    }

    function report(game, score) {
        if (!GAME_RE.test(String(game == null ? "" : game))) return failed("bad game");
        return $.ajax({
            url: apiUrl("endpoint=Scores&action=save"),
            type: "POST",
            data: { game: game, score: score },
            dataType: "json",
            timeout: 8000
        }).then(function (res) {
            return (res && res.ok) ? res.data : failed(res);
        });
    }

    /* -------------------------------------------------- inside a game frame */
    var badgeEl = null, badgeTimer = null;

    function badge(score, best, isRecord) {
        if (!score) return;   // nothing happened worth showing
        if (!badgeEl) {
            badgeEl = document.createElement("div");
            badgeEl.className = "score-badge";
            badgeEl.setAttribute("role", "status");
            document.body.appendChild(badgeEl);
        }
        badgeEl.className = "score-badge is-in" + (isRecord ? " is-record" : "");
        badgeEl.innerHTML = "<strong>" + (isRecord ? "New best" : "Best") + "</strong>" +
            "<span>" + esc(best) + "</span>";
        clearTimeout(badgeTimer);
        badgeTimer = setTimeout(function () {
            if (badgeEl) badgeEl.className = "score-badge";
        }, BADGE_MS);
    }

    function broadcast(game, score, data) {
        if (window.parent === window) return;
        try {
            window.parent.postMessage({
                type: "v2:score",
                game: game,
                score: score,
                best: data.best,
                isRecord: !!data.isRecord
            }, window.location.origin);
        } catch (e) { /* the board is not worth a broken frame */ }
    }

    /**
     * Follow one game's own end-of-run readout and save it. Queued through a
     * timer rather than rAF: a frame in a background tab still has to report.
     */
    function watch(game) {
        if (!GAME_RE.test(String(game == null ? "" : game))) return;

        var overlayShown = false;   // was the end-of-run surface up last check?
        var pending = false;        // a run ended but its score is not readable yet
        var reported = false;       // this run has been saved
        var queued = false;

        function endSurface() {
            var nodes = document.querySelectorAll('[id*="gameover" i], [id*="game-over" i]');
            for (var i = 0; i < nodes.length; i++) {
                if (isVisible(nodes[i])) return nodes[i];
            }
            return null;
        }

        function finalValue() {
            var el = document.querySelector('[id*="final" i][id*="score" i]');
            var value = el ? numberFrom(el.textContent) : null;
            if (value !== null) return value;

            // No final readout: fall back to the highest score on screen, but
            // never to the game's own stored high score (…#hi-score-val), which
            // would report last session's best as this run's.
            var best = null;
            var nodes = document.querySelectorAll('[id*="score" i]');
            for (var i = 0; i < nodes.length; i++) {
                if (/hi/i.test(nodes[i].id)) continue;
                var candidate = numberFrom(nodes[i].textContent);
                if (candidate !== null && (best === null || candidate > best)) best = candidate;
            }
            return best;
        }

        function save(score) {
            report(game, score).done(function (data) {
                broadcast(game, score, data);
                badge(score, data.best, data.isRecord);
            });
        }

        function run() {
            var shown = !!endSurface();

            if (!shown) {
                // Back to the start screen (or a restart): a replay may report.
                overlayShown = false;
                pending = false;
                reported = false;
                return;
            }
            if (!overlayShown) {
                overlayShown = true;
                pending = true;
                reported = false;
            }
            if (!pending) return;

            var value = finalValue();
            if (value === null) return;   // the module writes it a beat later
            pending = false;
            if (reported) return;
            reported = true;
            save(value);
        }

        function queue() {
            if (queued) return;
            queued = true;
            setTimeout(function () { queued = false; run(); }, 0);
        }

        new MutationObserver(queue).observe(document.body, {
            subtree: true,
            childList: true,
            characterData: true,
            attributes: true,
            attributeFilter: ["style", "class"]
        });
        queue();
    }

    /* -------------------------------------------------- the Games page */
    function renderBoard(container, games, data) {
        if (!container) return;
        var mine = {}, records = (data && data.records) || {};
        ((data && data.scores) || []).forEach(function (row) {
            var value = parseInt(row.score, 10);
            if (!isNaN(value)) mine[row.game] = value;
        });

        container.innerHTML = (games || []).map(function (game) {
            var id = String(game.id || "").toLowerCase();
            var has = Object.prototype.hasOwnProperty.call(mine, id);
            var record = records[id];
            return '<div class="score-row" data-game="' + esc(id) + '">' +
                    '<span class="score-row__name">' + esc(game.title || id) + "</span>" +
                    '<span class="score-row__mine' + (has ? "" : " is-empty") + '">' +
                        (has ? esc(mine[id]) : "—") + "</span>" +
                    '<span class="score-row__record">' +
                        (record === undefined || record === null ? "" : "record " + esc(record)) + "</span>" +
                "</div>";
        }).join("");
    }

    function playedCount(data) {
        return ((data && data.scores) || []).length;
    }

    /** Hear each frame's result while it plays. */
    function onScore(frame, handler) {
        window.addEventListener("message", function (e) {
            if (e.origin !== window.location.origin) return;
            if (!frame || e.source !== frame.contentWindow) return;
            var data = e.data;
            if (!data || data.type !== "v2:score") return;
            handler(data);
        });
    }

    window.Q8Score = {
        API_BASE: API_BASE,
        listScores: listScores,
        report: report,
        watch: watch,
        renderBoard: renderBoard,
        playedCount: playedCount,
        onScore: onScore,
        numberFrom: numberFrom
    };
})(window, jQuery);
