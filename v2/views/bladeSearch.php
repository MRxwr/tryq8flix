<?php include __DIR__ . '/header.php'; ?>

<main class="page page--wide">
    <div class="page-head">
        <div>
            <div class="eyebrow">Discover</div>
            <h1 class="page-title">Search</h1>
            <p class="page-sub">Find movies and TV shows across every source.</p>
        </div>
    </div>

    <div class="surface" style="padding:14px;margin-bottom:26px;">
        <div class="input-group">
            <select class="input" id="serverSelect" style="flex:0 1 190px;">
                <option value="2">EgyDead</option>
            </select>
            <input class="input" type="search" id="searchInput" placeholder="Search for a title…" autocomplete="off">
            <button class="btn btn--primary" id="searchBtn"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
        </div>
    </div>

    <div id="initialMessage" class="center-state">
        <i class="fa-solid fa-magnifying-glass big"></i>
        <h3>Find something to watch</h3>
        <p>Type a title above and pick a source to begin.</p>
    </div>

    <div id="searchResults" class="grid"></div>
    <div id="loadingIndicator" class="center-state hide" style="padding:40px;"><div class="spinner"></div></div>
</main>

<?php include __DIR__ . '/footer.php'; ?>

<script>
(function () {
    var page = 1, query = "", serverId = "", loading = false, done = true;
    var grid = document.getElementById("searchResults");
    var spinner = document.getElementById("loadingIndicator");
    var initial = document.getElementById("initialMessage");

    $.getJSON(apiUrl("endpoint=Main"), function (response) {
        if (response && response.ok && response.data.servers) {
            document.getElementById("serverSelect").innerHTML = response.data.servers.map(function (s) {
                return '<option value="' + escapeHtml(s.id) + '">' + escapeHtml(s.name) + "</option>";
            }).join("");
        }
    });

    function search(more) {
        var q = document.getElementById("searchInput").value.trim();
        var sid = document.getElementById("serverSelect").value;
        if (!q || loading) return;

        loading = true;
        if (!more) {
            page = 1; query = q; serverId = sid; done = false;
            initial.classList.add("hide");
            grid.innerHTML = gridSkeleton(12);
            spinner.classList.add("hide");
        } else {
            page++;
            spinner.classList.remove("hide");
        }

        $.getJSON(apiUrl("endpoint=Home&action=view&page=" + page + "&server=" + encodeURIComponent(serverId) +
            "&search=" + encodeURIComponent(query)), function (response) {
            if (!more) grid.innerHTML = "";

            if (response && response.ok && response.data.shows && response.data.shows.length) {
                var html = response.data.shows.map(function (show) {
                    return posterCard({
                        href: show.href, server: serverId, image: show.image,
                        title: show.title, target: "More"
                    });
                }).join("");
                grid.insertAdjacentHTML("beforeend", html);
                syncFavoriteIcons();
            } else {
                done = true;
                if (!more) {
                    grid.innerHTML = '<div class="center-state" style="grid-column:1/-1;">' +
                        '<i class="fa-solid fa-face-frown big"></i><h3>No results</h3>' +
                        '<p>Try a different spelling or another source.</p></div>';
                }
            }
            loading = false;
            spinner.classList.add("hide");
        }).fail(function () {
            loading = false;
            spinner.classList.add("hide");
            if (!more) {
                grid.innerHTML = '<div class="center-state" style="grid-column:1/-1;">' +
                    '<i class="fa-solid fa-triangle-exclamation big"></i><h3>Search failed</h3>' +
                    '<p>Network or server error. Please retry.</p></div>';
            }
        });
    }

    document.getElementById("searchBtn").addEventListener("click", function () { search(false); });
    document.getElementById("searchInput").addEventListener("keydown", function (e) {
        if (e.key === "Enter") { e.preventDefault(); search(false); }
    });

    window.addEventListener("scroll", function () {
        if (window.scrollY + window.innerHeight > document.body.offsetHeight - 400) {
            if (!loading && !done && query) search(true);
        }
    }, { passive: true });

    loadFavorites();
})();
</script>
