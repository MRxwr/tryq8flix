<?php include __DIR__ . '/header.php'; ?>

<main class="page">
    <div class="page-head">
        <div>
            <div class="eyebrow">Legal</div>
            <h1 class="page-title">Privacy Policy</h1>
        </div>
    </div>
    <section class="surface">
        <div class="surface__body prose" id="content-area">
            <div class="center-state"><div class="spinner"></div></div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/footer.php'; ?>

<script>
(function () {
    $.getJSON(apiUrl("endpoint=Settings"), function (response) {
        var el = document.getElementById("content-area");
        if (response && response.ok && response.data && response.data.length) {
            el.innerHTML = response.data[0].policy || "<p>No information available.</p>";
        } else {
            el.innerHTML = '<p class="muted">Content is unavailable right now.</p>';
        }
    }).fail(function () {
        document.getElementById("content-area").innerHTML = '<p class="muted">Could not load content.</p>';
    });
})();
</script>
