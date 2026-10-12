<?php include __DIR__ . '/header.php'; ?>

<main class="page page--wide">
    <div class="page-head">
        <div>
            <div class="eyebrow">Your activity</div>
            <h1 class="page-title">Watch History</h1>
            <p class="page-sub">Pick up right where you stopped.</p>
        </div>
        <button class="btn btn--danger btn--sm" id="clear-history">
            <i class="fa-solid fa-trash-can"></i> Clear history
        </button>
    </div>

    <div id="historyResults" class="grid"></div>

    <div id="emptyState" class="hide">
        <div class="center-state">
            <i class="fa-solid fa-clock-rotate-left big"></i>
            <h3>Nothing watched yet</h3>
            <p>Movies and shows you play will show up here.</p>
            <a class="btn btn--primary" href="?v=Home"><i class="fa-solid fa-compass"></i> Browse content</a>
        </div>
    </div>
</main>

<?php include __DIR__ . '/footer.php'; ?>

<script>
(function () {
    var grid = document.getElementById("historyResults");
    var empty = document.getElementById("emptyState");

    function fmtDate(value) {
        var d = new Date(value);
        if (isNaN(d.getTime())) return "";
        return d.toLocaleDateString() + " " + d.toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" });
    }

    function load() {
        grid.innerHTML = gridSkeleton(12);
        empty.classList.add("hide");
        $.getJSON(apiUrl("endpoint=History&action=list"), function (response) {
            var items = response && response.ok && response.data.history ? response.data.history : [];
            if (!items.length) {
                grid.innerHTML = "";
                empty.classList.remove("hide");
                return;
            }
            grid.innerHTML = items.map(function (item) {
                return posterCard({
                    href: item.link, server: item.server, image: item.poster,
                    title: item.title, subtitle: fmtDate(item.date),
                    target: "Servers", showFav: false
                });
            }).join("");
        }).fail(function () {
            grid.innerHTML = '<div class="center-state" style="grid-column:1/-1;">' +
                '<i class="fa-solid fa-triangle-exclamation big"></i><h3>Could not load history</h3>' +
                '<p>Please try again.</p></div>';
        });
    }

    document.getElementById("clear-history").addEventListener("click", function () {
        Q8.confirmDialog("This will permanently clear your entire watch history.",
            { confirm: "Clear history", danger: true, title: "Clear history" }).then(function (ok) {
            if (!ok) return;
            $.post(apiUrl("endpoint=History&action=clear"), function (response) {
                if (response && response.ok) { showToast("History cleared", "fa-broom"); load(); }
                else showToast("Could not clear history", "fa-triangle-exclamation");
            }, "json");
        });
    });

    load();
})();
</script>
