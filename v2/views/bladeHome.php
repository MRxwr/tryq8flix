<?php include __DIR__ . '/header.php'; ?>

<main class="page page--wide" style="padding-top:0;">
    <section class="hero" id="hero-section" style="min-height:78vh;border-radius:0 0 22px 22px;">
        <div class="hero__content">
            <div class="hero__meta" id="hero-meta">
                <span class="chip chip--brand"><i class="fa-solid fa-bolt"></i> Featured</span>
                <span class="chip"><i class="fa-solid fa-closed-captioning"></i> HD / 4K</span>
            </div>
            <h1 class="hero__title" id="hero-title">Loading…</h1>
            <p class="hero__desc" id="hero-desc">Fetching the latest titles for you.</p>
            <div class="hero__actions">
                <button class="btn btn--primary" id="hero-play"><i class="fa-solid fa-play"></i> Play</button>
                <button class="btn btn--ghost" id="hero-info"><i class="fa-solid fa-circle-info"></i> More info</button>
            </div>
        </div>
    </section>

    <div id="tips"></div>
    <div id="content-rows"></div>
</main>

<?php include __DIR__ . '/footer.php'; ?>

<script>
(function () {
    var hero = { href: "", image: "", title: "", server: "" };

    function rowSection(title, rowId, itemsHtml, moreHref) {
        return '<section class="row-section">' +
            '<div class="row-head">' +
                '<h2 class="row-title">' + escapeHtml(title) + "</h2>" +
                (moreHref ? '<a class="row-more" href="' + moreHref + '">View all <i class="fa-solid fa-chevron-right"></i></a>' : "") +
            "</div>" +
            '<div class="row-wrap">' +
                '<button class="row-nav row-nav--left" aria-label="Scroll left" onclick="scrollRow(\'' + rowId + '\',-1)"><i class="fa-solid fa-chevron-left"></i></button>' +
                '<div class="row-scroll" id="' + rowId + '">' + itemsHtml + "</div>" +
                '<button class="row-nav row-nav--right" aria-label="Scroll right" onclick="scrollRow(\'' + rowId + '\',1)"><i class="fa-solid fa-chevron-right"></i></button>' +
            "</div>" +
        "</section>";
    }

    function setHeroFromShow(show, serverId) {
        var backdrop = show.backdrop || show.image;
        if (backdrop) document.getElementById("hero-section").style.backgroundImage = cssUrl(backdrop);
        document.getElementById("hero-title").textContent = show.title || "Untitled";

        var meta = '<span class="chip chip--brand"><i class="fa-solid fa-bolt"></i> Featured</span>';
        if (show.description) meta += '<span class="chip">' + escapeHtml(show.description) + "</span>";
        meta += '<span class="chip"><i class="fa-solid fa-closed-captioning"></i> HD / 4K</span>';
        document.getElementById("hero-meta").innerHTML = meta;
        document.getElementById("hero-desc").textContent =
            "Watch " + (show.title || "this title") + " on TryQ8Flix in high quality.";

        hero = {
            href: show.href, image: backdrop || show.image,
            title: show.title, server: serverId
        };
    }

    function goHero() {
        if (!hero.href) return;
        navigateToEncrypted({
            v: "More", href: encryptLink(hero.href), server: hero.server,
            image: encryptLink(hero.image || ""), title: encryptLink(hero.title || "")
        });
    }
    document.getElementById("hero-play").addEventListener("click", goHero);
    document.getElementById("hero-info").addEventListener("click", goHero);

    // ---- tips (rendered once, dismissible) --------------------------------
    function renderTips() {
        var dismissed = localStorage.getItem("v2_tips_dismissed");
        if (dismissed) return;
        var isIos = /iPhone|iPad|iPod/.test(navigator.userAgent) && !window.MSStream;
        var isAndroid = /Android/.test(navigator.userAgent);
        if (!isIos && !isAndroid) return;

        var text = isIos
            ? 'For an ad-free experience on iPhone &amp; iPad, install <strong>AdBlock Pro</strong> from the App Store.'
            : 'For an ad-free experience on Android, use <strong>Firefox</strong> with the <strong>uBlock Origin</strong> add-on.';

        document.getElementById("tips").innerHTML =
            '<div class="alert alert--info" style="margin:22px 24px 0;">' +
                '<i class="fa-solid fa-lightbulb"></i>' +
                '<div class="grow" style="flex:1;">' + text + "</div>" +
                '<button class="modal__close" id="tip-dismiss" aria-label="Dismiss"><i class="fa-solid fa-xmark"></i></button>' +
            "</div>";
        document.getElementById("tip-dismiss").addEventListener("click", function () {
            localStorage.setItem("v2_tips_dismissed", "1");
            document.getElementById("tips").innerHTML = "";
        });
    }
    renderTips();

    // ---- continue watching -------------------------------------------------
    $.getJSON(apiUrl("endpoint=History&action=list"), function (response) {
        if (!response || !response.ok || !response.data.history || !response.data.history.length) return;
        var shows = response.data.history.slice(0, 20);
        var items = shows.map(function (show) {
            return posterCard({
                href: show.link, server: show.server, image: show.poster,
                title: show.title, target: "Servers", showFav: false, badge: "Continue"
            });
        }).join("");
        document.getElementById("content-rows").insertAdjacentHTML(
            "afterbegin", rowSection("Continue Watching", "row-history", items, "?v=History"));
    });

    // ---- favorites ---------------------------------------------------------
    $.getJSON(apiUrl("endpoint=Favorites&action=list"), function (response) {
        if (!response || !response.ok || !response.data.favorites || !response.data.favorites.length) return;
        var items = response.data.favorites.slice(0, 20).map(function (show) {
            return posterCard({
                href: show.link, server: show.server, image: show.poster,
                title: show.title, target: "More", isFav: true
            });
        }).join("");
        document.getElementById("content-rows").insertAdjacentHTML(
            "beforeend", rowSection("My Favorites", "row-favorites", items, "?v=Favorites"));
    });

    // ---- per-server rows ---------------------------------------------------
    $.getJSON(apiUrl("endpoint=Main"), function (response) {
        if (!response || !response.ok || !response.data.servers) return;
        var heroSet = false;

        response.data.servers.forEach(function (server) {
            $.getJSON(apiUrl("endpoint=Home&action=view&page=1&server=" + server.id), function (serverRes) {
                if (!serverRes || !serverRes.ok || !serverRes.data.shows || !serverRes.data.shows.length) return;

                if (!heroSet && (server.id == "13" || server.id == "14")) {
                    var pick = serverRes.data.shows[Math.floor(Math.random() * serverRes.data.shows.length)];
                    setHeroFromShow(pick, server.id);
                    heroSet = true;
                }

                var items = serverRes.data.shows.map(function (show) {
                    return posterCard({
                        href: show.href, server: server.id,
                        image: show.image, title: show.title, target: "More"
                    });
                }).join("");

                var moreHref = "?q=" + encodeURIComponent(encryptLink(
                    "v=Category&server=" + server.id + "&title=" + encodeURIComponent(server.name)));

                document.getElementById("content-rows").insertAdjacentHTML(
                    "beforeend", rowSection(server.name, "row-" + server.id, items, moreHref));
            });
        });
    });

    loadFavorites();
})();
</script>
