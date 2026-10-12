/* ============================================================
   Q8Flix V2 — the shared game scoreboard (both sides of the frame)

   One board for every game, loaded in exactly two places:

     - inside a game frame (game.php), where it reads the score the module
       itself finished a run with and saves it to the player's board,
     - on the Games Center page, where it renders that board for every game and
       hears each frame's result as it lands.

   The modules in /games/ are legacy documents hosted untouched, so nothing here
   asks a game to call anything. Each module ends a run by writing the final
   score into a `…-final-score` element and showing its `…#gameover…` overlay;
   that pair is the interface this reads. Games without both (Sudoku, Tic Tac
   Toe, Code Breaker) simply have nothing to report.

   No framework and no app bundle: a frame loads neither, and this file must
   work in both places. What it needs from its host is window.Q8_SCORE (game.php
   sets it: the api base and the account's CSRF token).
   ============================================================ */
(function (window, document) {
    "use strict";

    var CONFIG = window.Q8_SCORE || {};
    var API_BASE = CONFIG.api || "api/index.php";
    var GAME_RE = /^[a-z0-9]{1,32}$/;
    var BADGE_MS = 5200;

    function apiUrl(query) { return API_BASE + "?" + query; }

    function esc(value) {
        return String(value == null ? "" : value)
            .replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;");
    }

    /* -------------------------------------------------- api
       Reads answer {ok, data}; problems answer {ok:false, error, data} with a
       real HTTP status. Both are folded into one rejection so callers only have
       to handle "it worked" or "it didn't, and here is why". */
    function request(method, query, body) {
        var options = {method: method, credentials: "same-origin", cache: "no-store", headers: {}};
        if (body) {
            options.headers["Content-Type"] = "application/x-www-form-urlencoded;charset=UTF-8";
            if (CONFIG.csrf) options.headers["X-CSRF-Token"] = CONFIG.csrf;
            options.body = Object.keys(body).map(function (key) {
                return encodeURIComponent(key) + "=" + encodeURIComponent(body[key]);
            }).join("&");
        }
        return window.fetch(apiUrl(query), options).then(function (response) {
            return response.json().then(function (payload) {
                if (!response.ok || !payload || !payload.ok) {
                    var said = payload && ((payload.error && payload.error.msg) || (payload.data && payload.data.msg));
                    throw new Error(said || "The scoreboard is unavailable right now.");
                }
                return payload.data;
            }, function () {
                throw new Error("The scoreboard is unavailable right now.");
            });
        });
    }

    /** Every game, this account's best in it, and the site record beside it. */
    function board() { return request("GET", "endpoint=Games&action=list", null); }

    /** Save one finished run. The api keeps the higher score of the two. */
    function report(game, score) {
        if (!GAME_RE.test(String(game == null ? "" : game))) return Promise.reject(new Error("Unknown game."));
        if (!CONFIG.csrf) return Promise.reject(new Error("Sign in to keep your scores."));
        return request("POST", "endpoint=Games&action=save", {game: game, score: score});
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

    /* -------------------------------------------------- inside a game frame */
    var badgeEl = null, badgeTimer = null;

    // A frame loads neither the app's stylesheet nor its icon font, so the badge
    // carries the little it needs with it and stays out of the module's way.
    function injectBadgeStyle() {
        if (document.getElementById("q8-score-badge-style")) return;
        var style = document.createElement("style");
        style.id = "q8-score-badge-style";
        style.textContent =
            ".score-badge{position:fixed;top:calc(env(safe-area-inset-top,0px) + 14px);left:50%;" +
            "transform:translate(-50%,-18px);z-index:2500;display:flex;align-items:center;gap:10px;" +
            "padding:10px 16px;border-radius:999px;background:rgba(18,18,26,.94);" +
            "border:1px solid rgba(255,255,255,.16);color:#f4f4f7;font-size:.82rem;font-weight:800;" +
            "font-variant-numeric:tabular-nums;opacity:0;pointer-events:none;" +
            "transition:opacity .28s ease,transform .28s ease;" +
            "box-shadow:0 18px 40px -18px rgba(0,0,0,.9)}" +
            ".score-badge.is-in{opacity:1;transform:translate(-50%,0)}" +
            ".score-badge strong{font-weight:700;color:#a1a1b0}" +
            ".score-badge.is-record{border-color:rgba(229,9,20,.55)}" +
            ".score-badge.is-record strong{color:#ff4d6d}";
        (document.head || document.body).appendChild(style);
    }

    function badge(score, best, isRecord) {
        if (!score) return;   // nothing happened worth showing
        if (!badgeEl) {
            injectBadgeStyle();
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

    // The board is on the page around the frame; the frame only says what
    // happened and the page decides what to show.
    function broadcast(game, score, data) {
        if (window.parent === window) return;
        try {
            window.parent.postMessage({
                type: "q8:score",
                game: game,
                score: score,
                best: data.best,
                isRecord: !!data.is_record
            }, window.location.origin);
        } catch (e) { /* the board is not worth a broken frame */ }
    }

    /**
     * Follow one game's own end-of-run readout and save it. Watched through a
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
            report(game, score).then(function (data) {
                broadcast(game, score, data);
                badge(score, data.best, data.is_record);
            }).catch(function () {
                // Signed out, or the api is unreachable: the run plays on, it is
                // just not written down.
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

    /* -------------------------------------------------- the Games Center page */
    function rows(container) { return container.querySelectorAll(".score-row"); }

    /**
     * Paint the board: one row per game, this account's best in it, and the site
     * record beside it. Returns how many games have been played.
     */
    function renderBoard(container, games) {
        if (!container) return 0;
        var played = 0;
        container.innerHTML = (games || []).map(function (game) {
            var mine = (game.mine === null || game.mine === undefined) ? null : numberFrom(game.mine);
            var record = (game.record === null || game.record === undefined) ? null : numberFrom(game.record);
            if (mine !== null) played++;
            return '<div class="score-row' + (mine === null ? " is-empty" : "") + '" data-game="' + esc(game.id) + '">' +
                    '<span class="score-row__name">' + esc(game.title || game.id) + "</span>" +
                    '<span class="score-row__mine">' + (mine === null ? "—" : esc(mine)) + "</span>" +
                    '<span class="score-row__record">' + (record === null ? "" : "record " + esc(record)) + "</span>" +
                "</div>";
        }).join("");
        return played;
    }

    /** Mark the row a finished run just changed, so the board shows its work. */
    function flashRow(container, game) {
        if (!container) return;
        var row = container.querySelector('.score-row[data-game="' + String(game).replace(/"/g, "") + '"]');
        if (!row) return;
        var mine = row.querySelector(".score-row__mine");
        if (mine) mine.textContent = mine.textContent;   // keep the cell, restart the pulse
        row.classList.remove("is-new");
        void row.offsetWidth;                            // reflow: the class must re-enter
        row.classList.add("is-new");
        setTimeout(function () { row.classList.remove("is-new"); }, 1600);
    }

    /** Hear each frame's result while it plays. */
    function onScore(frame, handler) {
        window.addEventListener("message", function (event) {
            if (event.origin !== window.location.origin) return;
            if (!frame || event.source !== frame.contentWindow) return;
            var data = event.data;
            if (!data || data.type !== "q8:score") return;
            handler(data);
        });
    }

    window.Q8Score = {
        api: API_BASE,
        board: board,
        report: report,
        watch: watch,
        renderBoard: renderBoard,
        flashRow: flashRow,
        onScore: onScore,
        numberFrom: numberFrom
    };
})(window, document);
