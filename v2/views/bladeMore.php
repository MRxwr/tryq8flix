<?php include __DIR__ . '/header.php'; ?>

<main class="page page--wide" style="padding-top:0;">
    <section class="hero" id="hero-section" style="min-height:60vh;display:none;border-radius:0 0 22px 22px;">
        <div class="hero__content">
            <div class="hero__meta" id="hero-meta"></div>
            <h1 class="hero__title" id="hero-title">Loading…</h1>
            <div class="hero__desc" id="hero-desc"></div>
            <div class="hero__actions">
                <button class="btn btn--ghost" id="favBtnHero" style="display:none;">
                    <i class="fa-regular fa-heart"></i> Add to Favorites
                </button>
            </div>
        </div>
    </section>

    <div id="details-container" class="mt-3" style="padding:0 24px;">
        <div class="center-state"><div class="spinner"></div></div>
    </div>
</main>

<?php include __DIR__ . '/footer.php'; ?>

<script>
(function () {
    var jsq = Q8.jsq;
    function escDt(v) { return Q8.escHtml(v == null ? "" : v); }

    var href        = Q8.param("href");
    var server      = Q8.readParams().get("server");
    var image       = Q8.param("image");
    var title       = Q8.param("title");

    var heroEl   = document.getElementById("hero-section");
    var titleEl  = document.getElementById("hero-title");
    var descEl   = document.getElementById("hero-desc");
    var metaEl   = document.getElementById("hero-meta");
    var favBtn   = document.getElementById("favBtnHero");
    var details  = document.getElementById("details-container");

    if (image) {
        heroEl.style.backgroundImage = cssUrl(image);
        favBtn.style.display = "inline-flex";
    }
    if (title) titleEl.textContent = title;
    if (image || title) heroEl.style.display = "flex";

    function updateHeroButton(isFav) {
        favBtn.dataset.isFav = isFav ? "1" : "0";
        favBtn.innerHTML = isFav
            ? '<i class="fa-solid fa-heart" style="color:var(--brand);"></i> Remove from Favorites'
            : '<i class="fa-regular fa-heart"></i> Add to Favorites';
    }

    favBtn.addEventListener("click", function () {
        var isFav = favBtn.dataset.isFav === "1";
        Q8.confirmDialog(isFav ? "Remove from favorites?" : "Add to favorites?",
            { confirm: isFav ? "Remove" : "Add", danger: isFav, title: "Favorites" }).then(function (ok) {
            if (!ok) return;
            $.post(apiUrl("endpoint=Favorites&action=" + (isFav ? "remove" : "add")),
                { server: server, link: href, poster: image, title: titleEl.textContent },
                function (res) {
                    if (res && res.ok) {
                        updateHeroButton(!isFav);
                        showToast(isFav ? "Removed from favorites" : "Added to favorites", isFav ? "fa-heart-crack" : "fa-heart");
                    } else {
                        showToast((res && res.data && res.data.msg) || "Could not update favorites", "fa-triangle-exclamation");
                    }
                }, "json");
        });
    });

    function goToServers() {
        var q = { v: "Servers", href: encryptLink(href), server: server,
                  image: encryptLink(image || ""), title: encryptLink(title || "") };
        window.location.replace("?q=" + encodeURIComponent(encryptLink(
            Object.keys(q).map(function (k) { return k + "=" + encodeURIComponent(q[k]); }).join("&"))));
    }

    if (!href || !server) {
        details.innerHTML = '<div class="center-state"><i class="fa-solid fa-circle-exclamation big"></i>' +
            '<h3>Missing details</h3><p>This link could not be resolved.</p>' +
            '<a class="btn btn--primary" href="?v=Home">Back home</a></div>';
        return;
    }

    $.ajax({
        url: apiUrl("endpoint=More&action=list&server=" + encodeURIComponent(server) + "&href=" + encodeURIComponent(href)),
        dataType: "json",
        timeout: 60000
    }).done(function (response) {
        if (!response || !response.ok) {
            details.innerHTML = '<div class="center-state"><i class="fa-solid fa-triangle-exclamation big"></i>' +
                '<h3>Could not load</h3><p>' +
                escapeHtml((response && response.data && response.data.msg) || "Failed to load content.") + "</p>" +
                '<button class="btn btn--primary" onclick="location.reload()"><i class="fa-solid fa-rotate"></i> Try again</button></div>';
            return;
        }

        var data = response.data || {};
        if (!title && data.title) { titleEl.textContent = data.title; title = data.title; }

        if (data.backdrop && data.backdrop !== image) {
            image = data.backdrop;
            heroEl.style.backgroundImage = cssUrl(image);
        } else if (!image && data.poster) {
            image = data.poster;
            heroEl.style.backgroundImage = cssUrl(image);
        }
        if (image || title) heroEl.style.display = "flex";

        // Meta + overview
        var meta = "";
        if (data.release_date) meta += '<span class="chip chip--brand">' + escapeHtml(String(data.release_date).split("-")[0]) + "</span>";
        meta += '<span class="chip"><i class="fa-solid fa-closed-captioning"></i> HD / 4K</span>';
        metaEl.innerHTML = meta;
        if (data.overview) {
            descEl.innerHTML = '<p style="display:-webkit-box;-webkit-line-clamp:4;-webkit-box-orient:vertical;overflow:hidden;">' +
                escDt(data.overview) + "</p>";
        }

        if ((!data.seasons || !data.seasons.length) && (!data.episodes || !data.episodes.length)) {
            goToServers();
            return;
        }

        var html = "";
        if (data.seasons && data.seasons.length) {
            html += '<h2 class="row-title mt-4 mb-2">Seasons</h2><div class="ep-grid">';
            data.seasons.forEach(function (season) {
                html += '<div class="ep" onclick="navigateToEncrypted({v:\'More\',href:\'' + encryptLink(season.link) +
                    "',server:'" + jsq(server) + "',image:'" + encryptLink(image || "") +
                    "',title:'" + encryptLink(season.title) + "'})\">" + escDt(season.title) + "</div>";
            });
            html += "</div>";
        }

        if (data.episodes && data.episodes.length) {
            var encMoreLink = encryptLink(href);
            var encSeriesTitle = encryptLink(title || data.title || "");
            html += '<h2 class="row-title mt-4 mb-2">Episodes</h2><div class="ep-grid">';
            data.episodes.forEach(function (ep) {
                html += '<div class="ep" data-href="' + escDt(ep.link) + '" onclick="navigateToEncrypted({v:\'Servers\',href:\'' +
                    encryptLink(ep.link) + "',server:'" + jsq(server) + "',image:'" + encryptLink(image || "") +
                    "',title:'" + encryptLink(ep.title) + "',more_link:'" + encMoreLink + "',series_title:'" + encSeriesTitle +
                    "'})\">" + escDt(ep.title) + "</div>";
            });
            html += "</div>";
        }

        details.innerHTML = html;
        highlightWatched(server);

        if (image) {
            $.post(apiUrl("endpoint=Favorites&action=check"), { poster: image }, function (res) {
                updateHeroButton(!!(res && res.ok && res.data && res.data.isFavorite));
            }, "json");
        }
    }).fail(function () {
        details.innerHTML = '<div class="center-state"><i class="fa-solid fa-wifi big"></i>' +
            '<h3>Network error</h3><p>The request timed out. Please try again.</p>' +
            '<button class="btn btn--primary" onclick="location.reload()"><i class="fa-solid fa-rotate"></i> Try again</button></div>';
    });
})();
</script>
