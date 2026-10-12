<?php include __DIR__ . '/header.php'; ?>

<main class="page page--wide">
    <div class="page-head">
        <div>
            <div class="eyebrow">Browse</div>
            <h1 class="page-title" id="categoryTitle">Category</h1>
        </div>
        <a class="btn btn--ghost btn--sm" href="?v=Home"><i class="fa-solid fa-arrow-left"></i> Home</a>
    </div>

    <div id="categoryResults" class="grid"></div>

    <div id="sentinel" class="center-state hide" style="padding:40px;">
        <div class="spinner"></div>
    </div>
</main>

<?php include __DIR__ . '/footer.php'; ?>

<script>
(function () {
    var params = Q8.readParams();
    var server = params.get("server");
    var title = params.get("title");
    var page = 1;
    var loading = false;
    var done = false;

    document.getElementById("categoryTitle").textContent =
        title ? Q8.safeDecode(title) : (server ? "Server " + server : "Category");

    var grid = document.getElementById("categoryResults");
    var sentinel = document.getElementById("sentinel");
    grid.innerHTML = gridSkeleton(12);

    function load() {
        if (loading || done || !server) return;
        loading = true;
        if (page > 1) sentinel.classList.remove("hide");

        $.getJSON(apiUrl("endpoint=Home&action=view&server=" + encodeURIComponent(server) + "&page=" + page), function (response) {
            if (page === 1) grid.innerHTML = "";

            if (response && response.ok && response.data.shows && response.data.shows.length) {
                var html = response.data.shows.map(function (show) {
                    return posterCard({
                        href: show.href, server: server, image: show.image,
                        title: show.title, target: "More"
                    });
                }).join("");
                grid.insertAdjacentHTML("beforeend", html);
                syncFavoriteIcons();
            } else {
                done = true;
                if (page === 1) {
                    grid.innerHTML = '<div class="center-state" style="grid-column:1/-1;">' +
                        '<i class="fa-solid fa-clapperboard big"></i><h3>Nothing here yet</h3>' +
                        '<p>This source has no titles to show right now.</p></div>';
                }
            }
            loading = false;
            sentinel.classList.add("hide");
        }).fail(function () {
            loading = false;
            done = true;
            sentinel.classList.add("hide");
            if (page === 1) {
                grid.innerHTML = '<div class="center-state" style="grid-column:1/-1;">' +
                    '<i class="fa-solid fa-triangle-exclamation big"></i><h3>Could not load content</h3>' +
                    '<p>Please try again in a moment.</p></div>';
            }
        });
    }

    if (server) { load(); loadFavorites(); }
    else {
        grid.innerHTML = '<div class="center-state" style="grid-column:1/-1;">' +
            '<i class="fa-solid fa-circle-exclamation big"></i><h3>Invalid source</h3>' +
            '<p>No server was specified.</p></div>';
    }

    window.addEventListener("scroll", function () {
        if (window.scrollY + window.innerHeight > document.body.offsetHeight - 400) {
            if (!loading && !done) { page++; load(); }
        }
    }, { passive: true });
})();
</script>
