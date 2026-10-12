<?php include __DIR__ . '/header.php'; ?>

<main class="page">
    <div class="page-head">
        <div>
            <div class="eyebrow">Tools</div>
            <h1 class="page-title">Video Downloader</h1>
            <p class="page-sub">Paste an X/Twitter, Instagram, TikTok or YouTube link to preview it. YouTube is metadata-only.</p>
        </div>
    </div>

    <section class="surface mb-3">
        <div class="surface__body">
            <div class="input-group mb-2">
                <input class="input" type="url" id="download-url"
                       placeholder="https://x.com/... or https://instagram.com/... or https://tiktok.com/...">
                <button class="btn btn--primary" id="analyze-btn"><i class="fa-solid fa-magnifying-glass"></i> Analyze</button>
                <button class="btn btn--ghost" id="clear-btn"><i class="fa-solid fa-eraser"></i> Clear</button>
            </div>
            <div id="downloader-message"></div>
        </div>
    </section>

    <section class="surface mb-3 hide" id="preview-card">
        <div class="surface__body">
            <div style="display:grid;grid-template-columns:1fr;gap:20px;">
                <div style="display:grid;grid-template-columns:minmax(0,300px) 1fr;gap:20px;align-items:start;" id="preview-grid">
                    <div>
                        <img id="preview-thumb" alt="Video thumbnail" style="border-radius:14px;width:100%;object-fit:cover;">
                        <video id="preview-video" class="hide" preload="metadata" muted playsinline
                               style="border-radius:14px;width:100%;object-fit:cover;pointer-events:none;"></video>
                    </div>
                    <div>
                        <h3 class="mb-1" id="preview-title" style="font-weight:800;">—</h3>
                        <p class="muted small mb-1" id="preview-uploader"></p>
                        <p class="faint small mb-3" id="preview-meta"></p>
                        <div class="input-group">
                            <button class="btn btn--primary" id="prepare-download-btn"><i class="fa-solid fa-download"></i> Prepare download</button>
                            <a class="btn btn--light hide" id="download-direct-btn" href="#" target="_blank" rel="noopener" download>
                                <i class="fa-solid fa-file-arrow-down"></i> Download now
                            </a>
                            <a class="btn btn--ghost" id="open-source-btn" href="#" target="_blank" rel="noopener">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i> Open source
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="surface">
        <div class="surface__head"><i class="fa-solid fa-circle-info"></i> Notes</div>
        <div class="surface__body prose small">
            <ul>
                <li>Supported: X/Twitter, Instagram, TikTok and YouTube preview links.</li>
                <li>YouTube downloads are disabled on this instance.</li>
                <li>Private or login-only posts may not work.</li>
                <li>Only download content you own or have rights to.</li>
            </ul>
        </div>
    </section>
</main>

<?php include __DIR__ . '/footer.php'; ?>

<script>
(function () {
    var latestSourceUrl = "";
    var latestPreviewVideo = "";

    function message(type, text) {
        document.getElementById("downloader-message").innerHTML =
            '<div class="alert alert--' + type + '">' + text + "</div>";
    }
    function clearPreview() {
        document.getElementById("preview-card").classList.add("hide");
        document.getElementById("preview-thumb").src = "";
        document.getElementById("preview-thumb").classList.remove("hide");
        var v = document.getElementById("preview-video");
        v.classList.add("hide"); v.removeAttribute("src");
        try { v.load(); } catch (e) {}
        document.getElementById("preview-title").textContent = "—";
        document.getElementById("preview-uploader").textContent = "";
        document.getElementById("preview-meta").textContent = "";
        document.getElementById("open-source-btn").setAttribute("href", "#");
        document.getElementById("download-direct-btn").classList.add("hide");
        latestSourceUrl = ""; latestPreviewVideo = "";
    }
    function useImage(src) {
        var v = document.getElementById("preview-video");
        v.classList.add("hide"); v.removeAttribute("src");
        document.getElementById("preview-thumb").classList.remove("hide");
        document.getElementById("preview-thumb").src = src;
    }
    function useVideo(src) {
        document.getElementById("preview-thumb").classList.add("hide");
        document.getElementById("preview-thumb").src = "";
        var v = document.getElementById("preview-video");
        v.classList.remove("hide"); v.src = src;
        try { v.load(); } catch (e) {}
    }
    function hms(total) {
        var s = Number(total || 0);
        if (!s || s < 1) return "unknown length";
        var h = Math.floor(s / 3600), m = Math.floor((s % 3600) / 60), sec = s % 60;
        function p(n) { return String(n).padStart(2, "0"); }
        return h > 0 ? h + ":" + p(m) + ":" + p(sec) : m + ":" + p(sec);
    }

    document.getElementById("analyze-btn").addEventListener("click", function () {
        var url = document.getElementById("download-url").value.trim();
        if (!url) { message("warn", "Paste a video URL first."); return; }
        clearPreview();
        var btn = this, old = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner" style="width:16px;height:16px;border-width:2px;"></span> Analyzing…';
        message("info", "Fetching video details…");

        $.ajax({ url: apiUrl("endpoint=Downloader&action=info"), method: "POST", data: { url: url }, dataType: "json" })
            .done(function (res) {
                if (!res || !res.ok) { message("err", (res && res.data && res.data.msg) || "Failed to analyze this link."); return; }
                var d = res.data || {};
                latestSourceUrl = d.webpage_url || url;
                document.getElementById("preview-title").textContent = d.title || "Untitled";
                document.getElementById("preview-uploader").textContent = d.uploader ? "By: " + d.uploader : "Uploader unknown";
                document.getElementById("preview-meta").textContent =
                    "Platform: " + (d.extractor || "Unknown") + " · Duration: " + hms(d.duration);
                if (d.thumbnail) useImage(d.thumbnail);
                else if (latestPreviewVideo) useVideo(latestPreviewVideo);
                document.getElementById("open-source-btn").setAttribute("href", latestSourceUrl);
                document.getElementById("preview-card").classList.remove("hide");
                message("ok", "Video found. You can download it now.");
            })
            .fail(function (xhr) {
                message("err", (xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.msg) ||
                    "Network or server error while analyzing.");
            })
            .always(function () { btn.disabled = false; btn.innerHTML = old; });
    });

    document.getElementById("prepare-download-btn").addEventListener("click", function () {
        var url = document.getElementById("download-url").value.trim();
        if (!url) { message("warn", "Paste and analyze a URL first."); return; }
        var btn = this, old = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner" style="width:16px;height:16px;border-width:2px;"></span> Preparing…';
        message("info", "Preparing your download link…");

        $.ajax({ url: apiUrl("endpoint=Downloader&action=link"), method: "POST", data: { url: url }, dataType: "json" })
            .done(function (res) {
                if (!res || !res.ok || !res.data || !res.data.stream_url) {
                    message("err", (res && res.data && res.data.msg) || "Unable to get a direct link."); return;
                }
                var direct = res.data.stream_url;
                document.getElementById("download-direct-btn").setAttribute("href", direct);
                document.getElementById("download-direct-btn").classList.remove("hide");
                latestPreviewVideo = direct;
                var win = window.open(direct, "_blank", "noopener");
                message("ok", win ? "Download started." : "Download link is ready — tap Download now.");
            })
            .fail(function (xhr) {
                message("err", (xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.msg) ||
                    "Network or server error while preparing.");
            })
            .always(function () { btn.disabled = false; btn.innerHTML = old; });
    });

    document.getElementById("clear-btn").addEventListener("click", function () {
        document.getElementById("download-url").value = "";
        document.getElementById("downloader-message").innerHTML = "";
        clearPreview();
    });

    var pv = document.getElementById("preview-video");
    pv.addEventListener("play", function () { this.pause(); this.currentTime = 0; });
    pv.addEventListener("loadeddata", function () {
        this.pause();
        try { this.currentTime = 0.1; } catch (e) { this.currentTime = 0; }
    });
})();
</script>
