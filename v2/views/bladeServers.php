<?php include __DIR__ . '/header.php'; ?>

<main class="page page--wide" style="padding-top:0;">
    <section class="hero" id="hero-section" style="min-height:46vh;display:none;border-radius:0 0 22px 22px;">
        <div class="hero__content">
            <div id="live-header" style="display:none;">
                <div style="display:flex;align-items:center;justify-content:center;gap:36px;flex-wrap:wrap;">
                    <div style="text-align:center;">
                        <img id="hero-left-logo" alt="" style="width:96px;height:96px;object-fit:contain;filter:drop-shadow(0 6px 16px rgba(0,0,0,.6));">
                        <div class="strong mt-2" id="hero-left-name"></div>
                    </div>
                    <div style="font-size:3rem;font-weight:900;letter-spacing:.04em;">VS</div>
                    <div style="text-align:center;">
                        <img id="hero-right-logo" alt="" style="width:96px;height:96px;object-fit:contain;filter:drop-shadow(0 6px 16px rgba(0,0,0,.6));">
                        <div class="strong mt-2" id="hero-right-name"></div>
                    </div>
                </div>
            </div>
            <div class="hero__meta" id="hero-meta"></div>
            <h1 class="hero__title" id="episode-title" style="font-size:clamp(1.5rem,4vw,2.6rem);">Loading…</h1>
            <div class="hero__actions">
                <button class="btn btn--ghost" id="back-btn"><i class="fa-solid fa-arrow-left"></i> Back</button>
            </div>
        </div>
    </section>

    <div class="mt-3" style="padding:0 24px;">
        <h2 class="row-title mb-2">Choose a server</h2>
        <div id="servers-list" class="server-grid">
            <div class="center-state" style="grid-column:1/-1;"><div class="spinner"></div></div>
        </div>

        <div id="player-container" class="mt-3" style="display:none;">
            <div class="row-between mb-2">
                <div class="row-title" style="margin:0;"><i class="fa-solid fa-tv"></i> Now playing</div>
                <div style="display:flex;gap:10px;">
                    <button id="more-ep-btn" class="btn btn--ghost btn--sm" style="display:none;">
                        <i class="fa-solid fa-list"></i> More episodes
                    </button>
                </div>
            </div>
            <div class="player-frame">
                <iframe id="video-player" src="" allowfullscreen referrerpolicy="no-referrer" scrolling="no"></iframe>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/footer.php'; ?>

<script>
(function () {
    var p = Q8.readParams();
    function d(name) { return Q8.param(name); }

    var type        = p.get("type");
    var server      = p.get("server");
    var href        = d("href") || d("link");
    var link        = d("link") || d("href");
    var image       = d("image");
    var title       = d("title");
    var moreLink    = d("more_link");
    var seriesTitle = d("series_title");
    var leftLogo    = d("leftLogo"),  rightLogo = d("rightLogo");
    var leftName    = d("leftName"),  rightName = d("rightName");

    var heroEl   = document.getElementById("hero-section");
    var titleEl  = document.getElementById("episode-title");
    var metaEl   = document.getElementById("hero-meta");
    var listEl   = document.getElementById("servers-list");
    var playerEl = document.getElementById("player-container");
    var frameEl  = document.getElementById("video-player");
    var moreBtn  = document.getElementById("more-ep-btn");

    document.getElementById("back-btn").addEventListener("click", function () { history.back(); });

    if (type === "live") {
        heroEl.style.backgroundImage = "linear-gradient(120deg,#12121a,#1b0d14)";
        document.getElementById("live-header").style.display = "block";
        titleEl.style.display = "none";
        if (leftLogo && rightLogo) {
            document.getElementById("hero-left-logo").src = rightLogo;
            document.getElementById("hero-right-logo").src = leftLogo;
            document.getElementById("hero-left-name").textContent = rightName || "";
            document.getElementById("hero-right-name").textContent = leftName || "";
        } else {
            titleEl.textContent = title || "Live Match";
            titleEl.style.display = "block";
        }
        heroEl.style.display = "flex";
    } else if (title) {
        if (image) heroEl.style.backgroundImage = cssUrl(image);
        titleEl.textContent = title;
        heroEl.style.display = "flex";
    }

    function serverButton(label, url) {
        var btn = document.createElement("button");
        btn.type = "button";
        btn.className = "server-btn";
        btn.innerHTML = '<i class="fa-solid fa-play"></i> ' + escapeHtml(label);
        btn.addEventListener("click", function () { playVideo(url, btn); });
        return btn;
    }

    function loadLive() {
        if (!link) { listEl.innerHTML = '<p class="muted center" style="grid-column:1/-1;">No match link provided.</p>'; return; }
        $.getJSON(apiUrl("endpoint=Live&action=match&match=" + encodeURIComponent(link)), function (response) {
            var servers = [], details = null;
            if (response && response.data) {
                servers = response.data.matches || (Array.isArray(response.data) ? response.data : []);
                details = response.data.details;
            }
            if (!servers.length) {
                listEl.innerHTML = '<p class="muted center" style="grid-column:1/-1;">No servers found for this match.</p>';
                return;
            }
            listEl.innerHTML = "";
            servers.forEach(function (srv, i) {
                var url = srv.live || srv.link;
                if (url) listEl.appendChild(serverButton(srv.name || ("Server " + (i + 1)), url));
            });
            if (details) {
                titleEl.textContent = (details.leftTeamName || "") + " VS " + (details.rightTeamName || "");
                titleEl.style.display = "block";
                document.getElementById("live-header").style.display = "none";
                metaEl.innerHTML =
                    (details.league ? '<span class="chip chip--brand"><i class="fa-solid fa-trophy"></i> ' + escapeHtml(details.league) + "</span>" : "") +
                    (details.channel ? '<span class="chip"><i class="fa-solid fa-tv"></i> ' + escapeHtml(details.channel) + "</span>" : "") +
                    (details.commentator ? '<span class="chip"><i class="fa-solid fa-microphone"></i> ' + escapeHtml(details.commentator) + "</span>" : "") +
                    (details.matchTime ? '<span class="chip"><i class="fa-solid fa-clock"></i> ' + escapeHtml(details.matchTime) + "</span>" : "") +
                    (details.liveStatus ? '<span class="chip chip--live">' + escapeHtml(details.liveStatus) + "</span>" : "");
            }
        }).fail(function () {
            listEl.innerHTML = '<p class="muted center" style="grid-column:1/-1;">Failed to load servers.</p>';
        });
    }

    function loadServers() {
        if (!href || !server) { listEl.innerHTML = '<p class="muted center" style="grid-column:1/-1;">Missing link or server.</p>'; return; }
        $.getJSON(apiUrl("endpoint=Servers&action=list&server=" + encodeURIComponent(server) + "&href=" + encodeURIComponent(href)), function (response) {
            if (!response || !response.data || !Array.isArray(response.data) || !response.data.length) {
                listEl.innerHTML = '<p class="muted center" style="grid-column:1/-1;">No servers found.</p>';
                return;
            }
            var first = response.data[0];
            if (first.backdrop) { heroEl.style.backgroundImage = cssUrl(first.backdrop); image = first.backdrop; }
            var meta = "";
            if (first.date) meta += '<span class="chip chip--brand">' + escapeHtml(String(first.date).split("-")[0]) + "</span>";
            meta += '<span class="chip"><i class="fa-solid fa-film"></i> Movies &amp; Series</span>';
            metaEl.innerHTML = meta;

            listEl.innerHTML = "";
            response.data.forEach(function (srv, i) {
                if (srv.link) listEl.appendChild(serverButton(srv.name || ("Server " + (i + 1)), srv.link));
            });
        }).fail(function () {
            listEl.innerHTML = '<p class="muted center" style="grid-column:1/-1;">Failed to load servers.</p>';
        });
    }

    function playVideo(url, btn) {
        listEl.querySelectorAll(".server-btn").forEach(function (b) { b.classList.remove("is-active"); });
        btn.classList.add("is-active");
        playerEl.style.display = "block";
        frameEl.src = (window.ROOT_BASE || "../") + "videoPlayer.php?link=" + encodeURIComponent(url);
        playerEl.scrollIntoView({ behavior: "smooth", block: "start" });

        if (server && title && image && href) {
            $.post(apiUrl("endpoint=History&action=add"), { server: server, title: title, poster: image, link: href });
        }
    }

    if (moreLink) {
        moreBtn.style.display = "inline-flex";
        moreBtn.addEventListener("click", function () {
            navigateToEncrypted({
                v: "More", href: encryptLink(moreLink), server: server,
                image: encryptLink(image || ""), title: encryptLink(seriesTitle || title || "")
            });
        });
    }

    if (type === "live") loadLive(); else loadServers();
})();
</script>
