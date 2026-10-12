/* ============================================================
   TryQ8Flix v2 — shared core
   Nav, crypto, favorites, toasts, dialogs, card rendering.
   Depends on: jQuery (loaded in footer.php before this file).
   ============================================================ */
(function (window, $) {
    "use strict";

    // Base paths are injected by header.php so v2 works from any subfolder.
    var API_BASE = window.API_BASE || "../api/index.php";
    var ROOT_BASE = window.ROOT_BASE || "../";

    /* -------------------------------------------------- api helpers */
    function apiUrl(query) {
        return API_BASE + "?" + query;
    }

    /* -------------------------------------------------- escaping */
    function escAttr(value) {
        return String(value == null ? "" : value)
            .replace(/&/g, "&amp;")
            .replace(/"/g, "&quot;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;");
    }
    function escHtml(value) {
        return escAttr(value);
    }
    // Safe to embed inside a single-quoted JS string that lives in a
    // double-quoted HTML attribute. Uses JS hex escapes (never HTML entities)
    // so the value survives attribute tokenising and decodes back unchanged.
    function jsq(value) {
        return String(value == null ? "" : value)
            .replace(/\\/g, "\\\\")
            .replace(/'/g, "\\'")
            .replace(/"/g, "\\x22")
            .replace(/</g, "\\x3c")
            .replace(/>/g, "\\x3e")
            .replace(/&/g, "\\x26")
            .replace(/\r?\n/g, "\\n");
    }

    /* -------------------------------------------------- link crypto (matches live app) */
    function encryptLink(str) {
        if (!str) return "";
        try {
            return btoa(encodeURIComponent(str)).split("").reverse().join("");
        } catch (e) {
            return str;
        }
    }
    function decryptLink(str) {
        if (!str) return "";
        try {
            return decodeURIComponent(atob(str.split("").reverse().join("")));
        } catch (e) {
            return str;
        }
    }

    /* -------------------------------------------------- query params (plain or ?q= encrypted) */
    function readParams() {
        var params = new URLSearchParams(window.location.search);
        if (params.has("q")) {
            try {
                return new URLSearchParams(decryptLink(params.get("q")));
            } catch (e) {
                /* fall through to plain params */
            }
        }
        return params;
    }
    function param(name) {
        var value = readParams().get(name);
        if (value === null || value === undefined) return null;
        return name === "q" || name === "server" ? value : decryptLink(value);
    }
    // URLSearchParams already decodes, but some payloads are percent-encoded a
    // second time. Decode defensively: a bare "%" must not throw.
    function safeDecode(value) {
        try {
            return decodeURIComponent(value);
        } catch (e) {
            return value;
        }
    }

    /* -------------------------------------------------- navigation + transition */
    var fadeEl = null;
    function fade() {
        if (!fadeEl) fadeEl = document.querySelector(".page-fade");
        return fadeEl;
    }
    function navigateTo(url) {
        saveScroll();
        var el = fade();
        if (el) {
            el.classList.remove("is-hidden");
            setTimeout(function () { window.location.href = url; }, 320);
        } else {
            window.location.href = url;
        }
    }
    function navigateToEncrypted(params) {
        var qs = Object.keys(params)
            .map(function (k) { return k + "=" + encodeURIComponent(params[k]); })
            .join("&");
        navigateTo("?q=" + encodeURIComponent(encryptLink(qs)));
    }

    /* -------------------------------------------------- cookie + auth header */
    function getCookie(name) {
        var parts = ("; " + document.cookie).split("; " + name + "=");
        if (parts.length === 2) return parts.pop().split(";").shift();
        return "";
    }
    function setAuthCookie(token, days) {
        var expires = "";
        if (days) {
            var d = new Date();
            d.setTime(d.getTime() + days * 24 * 60 * 60 * 1000);
            expires = "; expires=" + d.toUTCString();
        }
        document.cookie = "tryq8flix2=" + token + expires + "; path=/";
    }
    function clearAuthCookie() {
        document.cookie = "tryq8flix2=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    }

    /* -------------------------------------------------- toast */
    function toastWrap() {
        var wrap = document.querySelector(".toast-wrap");
        if (!wrap) {
            wrap = document.createElement("div");
            wrap.className = "toast-wrap";
            document.body.appendChild(wrap);
        }
        return wrap;
    }
    function showToast(message, icon) {
        var el = document.createElement("div");
        el.className = "toast";
        el.innerHTML = '<i class="fa-solid ' + (icon || "fa-circle-check") + '"></i><span>' +
            escHtml(message) + "</span>";
        toastWrap().appendChild(el);
        setTimeout(function () {
            el.classList.add("is-out");
            setTimeout(function () { el.remove(); }, 300);
        }, 3000);
    }

    /* -------------------------------------------------- confirm dialog */
    function confirmDialog(message, opts) {
        opts = opts || {};
        return new Promise(function (resolve) {
            var overlay = document.createElement("div");
            overlay.className = "overlay";
            overlay.innerHTML =
                '<div class="modal" role="dialog" aria-modal="true">' +
                    '<div class="modal__head"><h5>' + escHtml(opts.title || "Are you sure?") + "</h5></div>" +
                    '<div class="modal__body"><p class="mb-0 muted">' + escHtml(message) + "</p></div>" +
                    '<div class="modal__foot">' +
                        '<button type="button" class="btn btn--ghost" data-act="no">' + escHtml(opts.cancel || "Cancel") + "</button>" +
                        '<button type="button" class="btn ' + (opts.danger ? "btn--danger" : "btn--primary") + '" data-act="yes">' +
                            escHtml(opts.confirm || "Confirm") + "</button>" +
                    "</div>" +
                "</div>";
            document.body.appendChild(overlay);

            function close(result) {
                overlay.classList.add("is-out");
                setTimeout(function () { overlay.remove(); }, 200);
                resolve(result);
            }
            overlay.addEventListener("click", function (e) {
                var act = e.target.getAttribute && e.target.getAttribute("data-act");
                if (act === "yes") close(true);
                else if (act === "no" || e.target === overlay) close(false);
            });
            document.addEventListener("keydown", function onKey(e) {
                if (e.key === "Escape") { document.removeEventListener("keydown", onKey); close(false); }
            });
        });
    }

    /* -------------------------------------------------- poster cards */
    function posterCard(o) {
        o = o || {};
        var target = o.target || "More";
        var encHref = encryptLink(o.href || "");
        var encImage = encryptLink(o.image || "");
        var encTitle = encryptLink(o.title || "");
        var safeTitle = escHtml(o.title || "");
        var sub = o.subtitle ? '<div class="poster__sub">' + escHtml(o.subtitle) + "</div>" : "";
        var badge = o.badge ? '<span class="poster__badge">' + escHtml(o.badge) + "</span>" : "";

        var params = "v:'" + target + "', href:'" + encHref + "', server:'" + jsq(o.server || "") +
            "', image:'" + encImage + "', title:'" + encTitle + "'";
        if (o.extra) params += "," + o.extra;

        var fav = "";
        if (o.showFav !== false) {
            fav = '<button type="button" class="poster__fav' + (o.isFav ? " is-on" : "") + '" ' +
                'aria-label="Toggle favorite" data-poster="' + escAttr(o.image || "") + '" ' +
                'onclick="toggleFavorite(event, \'' + jsq(o.server || "") + "','" + jsq(o.href || "") + "','" +
                jsq(o.image || "") + "','" + jsq(o.title || "") + "', this)\">" +
                '<i class="' + (o.isFav ? "fa-solid" : "fa-regular") + ' fa-heart"></i></button>';
        }

        return '<article class="poster" onclick="' + escAttr("navigateToEncrypted({" + params + "})") + '">' +
                '<div class="poster__media">' +
                    '<div class="poster__fallback"><i class="fa-solid fa-film"></i></div>' +
                    '<img src="' + escAttr(o.image || "") + '" alt="' + safeTitle + '" loading="lazy" ' +
                        'onerror="this.classList.add(\'is-hidden\')">' +
                    badge + fav +
                    '<div class="poster__grad"><div class="poster__title">' + safeTitle + "</div>" + sub + "</div>" +
                "</div>" +
            "</article>";
    }

    function posterSkeleton(count) {
        var html = "";
        for (var i = 0; i < (count || 8); i++) {
            html += '<div class="poster poster-sk"><div class="sk sk--poster"></div></div>';
        }
        return html;
    }

    function gridSkeleton(count) {
        var html = "";
        for (var i = 0; i < (count || 12); i++) {
            html += '<div class="poster"><div class="sk sk--poster"></div></div>';
        }
        return html;
    }

    /* -------------------------------------------------- favorites */
    var userFavorites = new Set();

    function loadFavorites() {
        return $.getJSON(apiUrl("endpoint=Favorites&action=list")).then(function (res) {
            if (res && res.ok && res.data && res.data.favorites) {
                res.data.favorites.forEach(function (fav) { userFavorites.add(fav.poster); });
                syncFavoriteIcons();
            }
            return userFavorites;
        });
    }
    function isFavorite(poster) { return userFavorites.has(poster); }

    function syncFavoriteIcons() {
        document.querySelectorAll(".poster__fav").forEach(function (btn) {
            var on = userFavorites.has(btn.getAttribute("data-poster"));
            btn.classList.toggle("is-on", on);
            var icon = btn.querySelector("i");
            if (icon) {
                icon.classList.toggle("fa-solid", on);
                icon.classList.toggle("fa-regular", !on);
            }
        });
    }

    function toggleFavorite(event, server, link, poster, title, btnElement) {
        if (event && event.stopPropagation) event.stopPropagation();
        var isFav = userFavorites.has(poster);
        var message = isFav ? "Remove this title from your favorites?" : "Add this title to your favorites?";
        confirmDialog(message, { confirm: isFav ? "Remove" : "Add", danger: isFav, title: "Favorites" })
            .then(function (ok) {
                if (!ok) return;
                var action = isFav ? "remove" : "add";
                $.post(apiUrl("endpoint=Favorites&action=" + action), {
                    server: server, link: link, poster: poster, title: title
                }, function (res) {
                    if (res && res.ok) {
                        if (isFav) userFavorites.delete(poster); else userFavorites.add(poster);
                        syncFavoriteIcons();
                        showToast(isFav ? "Removed from favorites" : "Added to favorites",
                            isFav ? "fa-heart-crack" : "fa-heart");
                    } else {
                        showToast((res && res.data && res.data.msg) || "Could not update favorites", "fa-triangle-exclamation");
                    }
                }, "json");
            });
    }

    /* -------------------------------------------------- row scrolling */
    function scrollRow(elementId, direction) {
        var container = document.getElementById(elementId);
        if (!container) return;
        var amount = container.clientWidth * 0.8 * (direction === 1 ? 1 : -1);
        container.scrollBy({ left: amount, behavior: "smooth" });
    }

    /* -------------------------------------------------- misc */
    // Build a safe CSS url() value: unquoted URLs break on spaces.
    function cssUrl(url) {
        return 'url("' + String(url == null ? "" : url).replace(/["\\\r\n]/g, "\\$&") + '")';
    }

    function highlightWatched(server) {
        if (!server) return;
        $.getJSON(apiUrl("endpoint=History&action=list&server=" + encodeURIComponent(server)), function (res) {
            if (!res || !res.ok || !res.data || !res.data.history) return;
            var watched = new Set(res.data.history.map(function (i) { return i.link; }));
            document.querySelectorAll(".ep").forEach(function (el) {
                var link = decryptLink(el.getAttribute("data-href") || "");
                if (watched.has(link)) el.classList.add("is-watched");
            });
        });
    }

    /* -------------------------------------------------- session storage */
    // Guarded: private-mode Safari throws on access instead of returning null.
    function storeGet(key) { try { return window.sessionStorage.getItem(key); } catch (e) { return null; } }
    function storeSet(key, value) { try { window.sessionStorage.setItem(key, value); } catch (e) { /* ignore */ } }
    function storeDel(key) { try { window.sessionStorage.removeItem(key); } catch (e) { /* ignore */ } }

    /* -------------------------------------------------- scroll memory
       The offset is remembered per view signature, so coming back to a list —
       by tab tap, swipe, browser back or reload — lands where the user left it. */
    var SCROLL_PREFIX = "v2:scroll:";
    // Params that identify *which* list a view is showing. Everything else
    // (the encrypted card payloads) differs per poster and must not split keys.
    var IDENTITY = ["server", "title", "category", "cat", "search", "s", "filter"];
    var DIR_KEY = "v2:tabdir";

    function viewSignature() {
        var params = readParams();
        var view = params.get("v") || "Home";
        var parts = [];
        for (var i = 0; i < IDENTITY.length; i++) {
            var value = params.get(IDENTITY[i]);
            if (value) parts.push(IDENTITY[i] + "=" + value);
        }
        return parts.length ? view + "?" + parts.join("&") : view;
    }
    function scrollKey() { return SCROLL_PREFIX + viewSignature(); }

    var restoringScroll = false;
    var scrollMemoryReady = false;
    var scrollSaveQueued = false;

    function saveScroll() {
        if (restoringScroll || !scrollMemoryReady) return;
        storeSet(scrollKey(), String(Math.round(window.scrollY || window.pageYOffset || 0)));
    }

    // Re-apply the remembered offset, retrying while async content is still
    // growing the page. Returns true when the caller should hold the reveal
    // overlay until the position settles (avoids a flash at the top).
    function beginScrollRestore(reveal) {
        var raw = storeGet(scrollKey());
        var target = raw === null ? 0 : (parseInt(raw, 10) || 0);
        if (target < 8) return false;

        var docEl = document.documentElement;
        var prevBehavior = docEl.style.scrollBehavior;
        // `html { scroll-behavior: smooth }` would turn this into an animation.
        docEl.style.scrollBehavior = "auto";

        restoringScroll = true;
        var started = Date.now();
        var frames = 0;
        var stableFrames = 0;
        var lastMax = -1;

        // Views fill in from AJAX after load, so the page is far shorter than it
        // ends up. Standing down while requests are still in flight (instead of
        // trusting a brief pause in growth) is what keeps the remembered offset
        // from being clamped to the initial hero-plus-footer height.
        function stillLoading() {
            if (document.readyState !== "complete") return true;
            var jq = window.jQuery;
            return !!(jq && jq.active > 0);
        }

        // Independent of rAF: a hidden tab (or a thrown error mid-loop) must
        // never leave the reveal overlay stuck over the page.
        var watchdog = setTimeout(finish, 2500);

        function finish() {
            if (!restoringScroll) return;
            restoringScroll = false;
            clearTimeout(watchdog);
            docEl.style.scrollBehavior = prevBehavior;
            scrollMemoryReady = true;
            storeSet(scrollKey(), String(Math.round(window.scrollY || 0)));
            if (reveal) reveal();
        }

        function attempt() {
            if (!restoringScroll) return;
            frames++;
            window.scrollTo(0, target);
            if (Math.abs((window.scrollY || 0) - target) <= 2) return finish();

            var maxY = Math.max(0, docEl.scrollHeight - window.innerHeight);
            var busy = stillLoading();
            if (maxY === lastMax && !busy) stableFrames++; else stableFrames = 0;
            lastMax = maxY;

            // Nothing left loading and the height has stopped moving, so the
            // remembered offset is simply gone: accept the clamped position.
            if (stableFrames > 25 || frames > 200 || Date.now() - started > 1200) return finish();
            requestAnimationFrame(attempt);
        }

        requestAnimationFrame(attempt);
        return true;
    }

    /* -------------------------------------------------- tab swiping
       Horizontal drag between the main tabs, with rubber-banding at the ends
       and a directional slide-in on arrival. Vertical scrolls, form fields and
       horizontally scrollable rows keep their native gestures.

       Touch listeners are bound unconditionally: they stay inert on
       pointer-only devices, which also keeps hybrid touchscreen laptops (and
       automated review harnesses) working without a feature test. */
    var TAB_ORDER = ["Home", "Search", "LiveMatchesList", "Favorites", "Profile"];

    function shell() { return document.querySelector(".app"); }

    function tabIndex(view) {
        for (var i = 0; i < TAB_ORDER.length; i++) if (TAB_ORDER[i] === view) return i;
        return -1;
    }

    // Elements that own their own gesture (or must not move under the finger).
    var SWIPE_BLOCKERS = "input, textarea, select, [contenteditable], [data-no-swipe], " +
        ".overlay, .install, .toast, iframe, video";

    function initSwipe() {
        var el = shell();
        if (!el) return;

        var index = tabIndex(el.getAttribute("data-view") || "");
        if (index < 0) return; // not a main tab: no tab-to-tab gesture here

        var startX = 0, startY = 0, startAt = 0;
        var tracking = false, decided = false, direction = 0;

        function drag(dx) {
            var atEnd = (direction > 0 && index >= TAB_ORDER.length - 1) ||
                        (direction < 0 && index <= 0);
            var shift = dx * (atEnd ? 0.15 : 0.42);
            el.style.transform = "translate3d(" + shift.toFixed(1) + "px,0,0)";
            el.style.opacity = String(Math.max(0.72, 1 - Math.abs(dx) / (window.innerWidth * 1.8)));
        }

        function clearDrag() {
            el.style.transition = "";
            el.style.transform = "";
            el.style.opacity = "";
            el.classList.remove("is-dragging");
        }

        function snapBack() {
            el.style.transition = "transform .3s var(--ease), opacity .3s var(--ease)";
            el.style.transform = "translate3d(0,0,0)";
            el.style.opacity = "1";
            setTimeout(clearDrag, 320);
        }

        function goTab(delta) {
            var target = index + delta;
            if (target < 0 || target >= TAB_ORDER.length) return false;
            storeSet(DIR_KEY, delta > 0 ? "next" : "prev");
            saveScroll();
            el.style.transition = "transform .22s var(--ease), opacity .22s var(--ease)";
            el.style.transform = "translate3d(" + (delta > 0 ? -28 : 28) + "%,0,0)";
            el.style.opacity = "0";
            setTimeout(function () { window.location.href = "?v=" + TAB_ORDER[target]; }, 175);
            return true;
        }

        el.addEventListener("touchstart", function (e) {
            if (e.touches.length !== 1) { tracking = false; return; }
            var t = e.target;
            if (t && t.closest && t.closest(SWIPE_BLOCKERS)) { tracking = false; return; }
            startX = e.touches[0].clientX;
            startY = e.touches[0].clientY;
            startAt = Date.now();
            direction = 0; decided = false; tracking = true;
            el.classList.remove("is-in-next", "is-in-prev");
        }, { passive: true });

        el.addEventListener("touchmove", function (e) {
            if (!tracking || e.touches.length !== 1) return;
            var dx = e.touches[0].clientX - startX;
            var dy = e.touches[0].clientY - startY;

            if (!decided) {
                if (Math.abs(dx) < 12) return;
                // A mostly-vertical drag belongs to the page scroll.
                if (Math.abs(dx) < Math.abs(dy) * 1.15) { tracking = false; return; }

                var want = dx < 0 ? 1 : -1;
                var row = e.target.closest && e.target.closest(".row-scroll");
                if (row) {
                    var max = row.scrollWidth - row.clientWidth;
                    var canConsume = want > 0 ? row.scrollLeft < max - 2 : row.scrollLeft > 2;
                    // Let the row pan until it hits its edge, then hand over.
                    if (canConsume) { tracking = false; return; }
                }
                direction = want;
                decided = true;
                el.classList.add("is-dragging");
            }

            if (e.cancelable) e.preventDefault();
            drag(dx);
        }, { passive: false });

        function end(e) {
            if (!tracking) return;
            tracking = false;
            if (!decided) { clearDrag(); return; }

            var touch = (e.changedTouches && e.changedTouches[0]) || null;
            var dx = touch ? touch.clientX - startX : 0;
            var moved = Math.abs(dx);
            var elapsed = Math.max(Date.now() - startAt, 1);
            var far = moved > Math.min(110, window.innerWidth * 0.2);
            var flick = moved / elapsed > 0.45 && moved > 34;

            if ((far || flick) && goTab(dx < 0 ? 1 : -1)) return;
            snapBack();
        }
        el.addEventListener("touchend", end, { passive: true });
        el.addEventListener("touchcancel", end, { passive: true });

        // Desktop parity: arrow keys move between the same tabs.
        document.addEventListener("keydown", function (e) {
            if (e.metaKey || e.ctrlKey || e.altKey || e.shiftKey) return;
            if (e.key !== "ArrowLeft" && e.key !== "ArrowRight") return;
            var t = e.target;
            if (t && t.closest && t.closest("input, textarea, select, [contenteditable]")) return;
            if (document.querySelector(".overlay")) return;
            if (goTab(e.key === "ArrowRight" ? 1 : -1)) e.preventDefault();
        });
    }

    /* -------------------------------------------------- bootstrapping */
    // `history.scrollRestoration` would fight our own restore.
    try { if (history.scrollRestoration) history.scrollRestoration = "manual"; } catch (e) { /* ignore */ }

    // Direction the previous page left in, so this page slides in the same way.
    var enterDir = storeGet(DIR_KEY);
    if (enterDir) {
        storeDel(DIR_KEY);
        var bootShell = shell();
        if (bootShell) bootShell.classList.add(enterDir === "prev" ? "is-in-prev" : "is-in-next");
    }

    $(function () {
        // Reveal the page. Holding the cover until a remembered offset settles
        // keeps the restore from flashing at the top first.
        var revealed = false;
        function reveal() {
            if (revealed) return;
            revealed = true;
            var overlay = fade();
            if (overlay) overlay.classList.add("is-hidden");
        }

        var holding = beginScrollRestore(reveal);
        if (!holding) scrollMemoryReady = true;
        if (enterDir || !holding) setTimeout(reveal, enterDir ? 0 : 30);

        // shared auth header on every ajax call
        $(document).ajaxSend(function (e, xhr) {
            var token = getCookie("tryq8flix2");
            if (token) xhr.setRequestHeader("Authorization", "Bearer " + token);
        });

        // nav background on scroll
        function onScroll() {
            var nav = document.querySelector(".nav");
            if (nav) nav.classList.toggle("scrolled", window.scrollY > 20);
        }
        window.addEventListener("scroll", onScroll, { passive: true });
        onScroll();

        // remember the offset as the user moves (one write per frame at most)
        window.addEventListener("scroll", function () {
            if (!scrollMemoryReady || scrollSaveQueued) return;
            scrollSaveQueued = true;
            requestAnimationFrame(function () { scrollSaveQueued = false; saveScroll(); });
        }, { passive: true });
        window.addEventListener("pagehide", function () { saveScroll(); });

        // intercept internal link clicks for the fade transition
        document.addEventListener("click", function (e) {
            var link = e.target.closest && e.target.closest("a");
            if (!link || !link.href || link.target || link.hasAttribute("data-no-fade")) return;
            if (!link.href.startsWith(window.location.origin)) return;
            e.preventDefault();
            navigateTo(link.href);
        });

        initSwipe();
    });

    // restore overlay on back/forward cache
    window.addEventListener("pageshow", function () {
        var el = fade();
        if (el) el.classList.add("is-hidden");
    });

    /* -------------------------------------------------- exports */
    window.Q8 = {
        apiUrl: apiUrl,
        API_BASE: API_BASE,
        ROOT_BASE: ROOT_BASE,
        encryptLink: encryptLink,
        decryptLink: decryptLink,
        readParams: readParams,
        param: param,
        safeDecode: safeDecode,
        navigateTo: navigateTo,
        navigateToEncrypted: navigateToEncrypted,
        getCookie: getCookie,
        setAuthCookie: setAuthCookie,
        clearAuthCookie: clearAuthCookie,
        showToast: showToast,
        confirmDialog: confirmDialog,
        posterCard: posterCard,
        posterSkeleton: posterSkeleton,
        gridSkeleton: gridSkeleton,
        loadFavorites: loadFavorites,
        isFavorite: isFavorite,
        toggleFavorite: toggleFavorite,
        syncFavoriteIcons: syncFavoriteIcons,
        scrollRow: scrollRow,
        highlightWatched: highlightWatched,
        saveScroll: saveScroll,
        restoreScroll: beginScrollRestore,
        viewSignature: viewSignature,
        tabOrder: function () { return TAB_ORDER.slice(); },
        cssUrl: cssUrl,
        escapeHtml: escHtml,
        escHtml: escHtml,
        escAttr: escAttr,
        jsq: jsq
    };

    // Flat globals consumed by inline view scripts (keeps ports close to the
    // original). Exposed in one pass so a new helper can never be namespaced
    // but forgotten as a global.
    Object.keys(window.Q8).forEach(function (key) {
        if (typeof window[key] === "undefined") window[key] = window.Q8[key];
    });
})(window, jQuery);
