<?php include __DIR__ . '/header.php'; ?>

<main class="page page--wide">
    <div class="page-head">
        <div>
            <div class="eyebrow">Today</div>
            <h1 class="page-title">Live Matches</h1>
            <p class="page-sub">Follow the action and pick a stream.</p>
        </div>
        <button class="btn btn--ghost btn--sm" id="refresh-matches"><i class="fa-solid fa-rotate"></i> Refresh</button>
    </div>

    <div id="matches-list" class="grid" style="grid-template-columns:repeat(auto-fill,minmax(340px,1fr));">
        <div class="center-state" style="grid-column:1/-1;"><div class="spinner"></div></div>
    </div>
</main>

<?php include __DIR__ . '/footer.php'; ?>

<script>
(function () {
    var list = document.getElementById("matches-list");

    function matchCard(m) {
        var q = {
            v: "Servers", type: "live",
            link: encryptLink(m.href), href: encryptLink(m.href),
            title: encryptLink((m.leftTeamName || "") + " VS " + (m.rightTeamName || "")),
            image: encryptLink(m.leftTeamLogo || ""),
            leftLogo: encryptLink(m.leftTeamLogo || ""),
            rightLogo: encryptLink(m.rightTeamLogo || ""),
            leftName: encryptLink(m.leftTeamName || ""),
            rightName: encryptLink(m.rightTeamName || "")
        };
        var enc = encryptLink(Object.keys(q).map(function (k) { return k + "=" + encodeURIComponent(q[k]); }).join("&"));

        return '<article class="match" onclick="navigateTo(\'?q=' + encodeURIComponent(enc) + '\')">' +
            '<div class="match__body">' +
                '<div class="match__team">' +
                    '<img src="' + Q8.escAttr(m.leftTeamLogo || "") + '" alt="" loading="lazy">' +
                    '<span>' + escapeHtml(m.leftTeamName || "") + "</span>" +
                "</div>" +
                '<div class="match__mid">' +
                    '<div class="match__time">' + escapeHtml(m.matchTime || "") + "</div>" +
                    '<div class="match__score">' + escapeHtml(m.result || "0 - 0") + "</div>" +
                    '<span class="chip chip--live">' + escapeHtml(m.liveStatus || "Live") + "</span>" +
                "</div>" +
                '<div class="match__team">' +
                    '<img src="' + Q8.escAttr(m.rightTeamLogo || "") + '" alt="" loading="lazy">' +
                    '<span>' + escapeHtml(m.rightTeamName || "") + "</span>" +
                "</div>" +
            "</div>" +
            '<div class="match__foot">' +
                '<span><i class="fa-solid fa-trophy"></i> ' + escapeHtml(m.league || "Match") + "</span>" +
                '<span><i class="fa-solid fa-microphone"></i> ' + escapeHtml(m.commentator || "—") + "</span>" +
                '<span><i class="fa-solid fa-tv"></i> ' + escapeHtml(m.channel || "—") + "</span>" +
            "</div></article>";
    }

    function load(forceFresh) {
        list.innerHTML = '<div class="center-state" style="grid-column:1/-1;"><div class="spinner"></div></div>';
        // Refresh means "ask again": the cached list is only there for coming
        // back to the tab, never for an explicit refresh.
        return (forceFresh ? Q8.freshGetJSON : $.getJSON)(apiUrl("endpoint=Live&action=live"), function (response) {
            var matches = (response && response.ok && response.data) ? response.data.filter(function (m) { return m.rightTeamName; }) : [];
            if (!matches.length) {
                list.innerHTML = '<div class="center-state" style="grid-column:1/-1;">' +
                    '<i class="fa-solid fa-futbol big"></i><h3>No live matches right now</h3>' +
                    "<p>Check back later for upcoming games.</p></div>";
                return;
            }
            list.innerHTML = matches.map(matchCard).join("");
        }).fail(function () {
            list.innerHTML = '<div class="center-state" style="grid-column:1/-1;">' +
                '<i class="fa-solid fa-triangle-exclamation big"></i><h3>Could not load matches</h3>' +
                "<p>Please try again.</p></div>";
        });
    }

    document.getElementById("refresh-matches").addEventListener("click", function () { load(true); });
    // The pull gesture means what the Refresh button means: ask again.
    Q8.onRefresh(function () { return load(true); });
    load();
})();
</script>
