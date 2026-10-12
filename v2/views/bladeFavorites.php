<?php include __DIR__ . '/header.php'; ?>

<main class="page page--wide">
    <div class="page-head">
        <div>
            <div class="eyebrow">Your library</div>
            <h1 class="page-title">My Favorites</h1>
            <p class="page-sub">Everything you saved in one place.</p>
        </div>
        <a class="btn btn--ghost btn--sm" href="?v=Home"><i class="fa-solid fa-compass"></i> Browse</a>
    </div>

    <div id="favoritesResults" class="grid"></div>

    <div id="emptyState" class="hide">
        <div class="center-state">
            <i class="fa-regular fa-heart big"></i>
            <h3>No favorites yet</h3>
            <p>Tap the heart on any title to build your list.</p>
            <a class="btn btn--primary" href="?v=Home"><i class="fa-solid fa-compass"></i> Browse content</a>
        </div>
        <h2 class="row-title mt-4 mb-2">Suggested for you</h2>
        <div id="suggestionRows" class="grid"></div>
    </div>
</main>

<?php include __DIR__ . '/footer.php'; ?>

<script>
(function () {
    var grid = document.getElementById("favoritesResults");
    var empty = document.getElementById("emptyState");

    function render(favs) {
        grid.innerHTML = favs.map(function (fav) {
            return posterCard({
                href: fav.link, server: fav.server, image: fav.poster,
                title: fav.title, target: "More", isFav: true
            });
        }).join("");
        syncFavoriteIcons();
    }

    function load() {
        grid.innerHTML = gridSkeleton(12);
        return $.getJSON(apiUrl("endpoint=Favorites&action=list"), function (response) {
            var favs = response && response.ok && response.data.favorites ? response.data.favorites : [];
            if (favs.length) {
                empty.classList.add("hide");
                render(favs);
            } else {
                grid.innerHTML = "";
                empty.classList.remove("hide");
                loadSuggestions();
            }
        }).fail(function () {
            grid.innerHTML = '<div class="center-state" style="grid-column:1/-1;">' +
                '<i class="fa-solid fa-triangle-exclamation big"></i><h3>Could not load favorites</h3>' +
                '<p>Please try again.</p></div>';
        });
    }

    function loadSuggestions() {
        $.getJSON(apiUrl("endpoint=Main"), function (response) {
            if (!response || !response.ok || !response.data.banners) return;
            var html = response.data.banners.slice(0, 6).filter(function (b) { return b.url && b.server; })
                .map(function (b) {
                    return posterCard({
                        href: b.url, server: b.server, image: b.imageurl,
                        title: b.title, target: "More", showFav: false
                    });
                }).join("");
            document.getElementById("suggestionRows").innerHTML = html;
        });
    }

    // Refresh in place when a favorite is removed from a card.
    document.addEventListener("click", function (e) {
        if (e.target.closest && e.target.closest(".poster__fav")) {
            setTimeout(load, 700);
        }
    });

    // Pulling the Saved tab down re-reads the list the tab is showing.
    Q8.onRefresh(function () {
        Q8.clearApiCache("endpoint=Favorites");
        return $.when(load(), loadFavorites());
    });

    load();
    loadFavorites();
})();
</script>
