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

    /* -------------------------------------------------- bootstrapping */
    $(function () {
        // fade the transition overlay out on load
        var el = fade();
        if (el) setTimeout(function () { el.classList.add("is-hidden"); }, 30);

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

        // intercept internal link clicks for the fade transition
        document.addEventListener("click", function (e) {
            var link = e.target.closest && e.target.closest("a");
            if (!link || !link.href || link.target || link.hasAttribute("data-no-fade")) return;
            if (!link.href.startsWith(window.location.origin)) return;
            e.preventDefault();
            navigateTo(link.href);
        });
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
