<?php include 'header.php'; ?>

<div class="hero" style="background-image: url('https://images.unsplash.com/photo-1611162618071-b39a2ec055fb?q=80&w=1500&auto=format&fit=crop'); min-height: 62vh; align-items: flex-end;">
    <div class="hero-overlay"></div>
    <div class="hero-content" style="max-width: 760px;">
        <h1 class="hero-title">Video Downloader</h1>
        <p class="hero-desc text-white-50">Paste an X/Twitter, Instagram, or TikTok video link and get a downloadable MP4 stream.</p>
    </div>
</div>

<div class="container" style="margin-top: 30px; margin-bottom: 60px;">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card bg-dark text-white border-secondary shadow-lg">
                <div class="card-body p-4">
                    <h4 class="mb-3"><i class="fas fa-link me-2 text-danger"></i>Paste Video URL</h4>

                    <div class="input-group mb-3">
                        <span class="input-group-text bg-secondary border-0 text-white"><i class="fas fa-video"></i></span>
                        <input id="download-url" type="url" class="form-control bg-secondary text-white border-0" placeholder="https://x.com/... or https://instagram.com/... or https://tiktok.com/...">
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <button id="analyze-btn" class="btn btn-netflix"><i class="fas fa-search me-1"></i>Analyze</button>
                        <button id="clear-btn" class="btn btn-secondary-netflix"><i class="fas fa-eraser me-1"></i>Clear</button>
                    </div>

                    <div id="downloader-message" class="mt-3"></div>
                </div>
            </div>

            <div id="preview-card" class="card bg-dark text-white border-secondary shadow-lg mt-4" style="display:none;">
                <div class="row g-0">
                    <div class="col-md-4">
                        <img id="preview-thumb" src="" alt="Video thumbnail" class="img-fluid rounded-start" style="height:100%; object-fit:cover; min-height:220px;">
                    </div>
                    <div class="col-md-8">
                        <div class="card-body p-4">
                            <h5 id="preview-title" class="card-title mb-2">-</h5>
                            <p id="preview-uploader" class="text-white-50 mb-1"></p>
                            <p id="preview-meta" class="text-white-50 mb-3"></p>

                            <div class="d-flex flex-wrap gap-2">
                                <button id="download-btn" class="btn btn-success">
                                    <i class="fas fa-download me-1"></i>Download MP4
                                </button>
                                <a id="open-source-btn" class="btn btn-outline-light" href="#" target="_blank" rel="noopener">
                                    <i class="fas fa-external-link-alt me-1"></i>Open Source Post
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card bg-dark text-white border-secondary mt-4">
                <div class="card-body p-4">
                    <h5 class="mb-3"><i class="fas fa-circle-info me-2"></i>Notes</h5>
                    <ul class="text-white-50 mb-0">
                        <li>Supported links: X/Twitter, Instagram, TikTok.</li>
                        <li>Some posts may require login or may be private.</li>
                        <li>Use this only for content you own or have rights to download.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
$(document).ready(function() {
    let latestSourceUrl = '';

    function showMessage(type, msg) {
        $('#downloader-message').html(`<div class="alert alert-${type} mb-0">${msg}</div>`);
    }

    function clearPreview() {
        $('#preview-card').hide();
        $('#preview-thumb').attr('src', '');
        $('#preview-title').text('-');
        $('#preview-uploader').text('');
        $('#preview-meta').text('');
        $('#open-source-btn').attr('href', '#');
        latestSourceUrl = '';
    }

    function secondsToHms(total) {
        const sec = Number(total || 0);
        if (!sec || sec < 1) return 'Unknown duration';
        const h = Math.floor(sec / 3600);
        const m = Math.floor((sec % 3600) / 60);
        const s = sec % 60;
        if (h > 0) return `${h}:${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
        return `${m}:${String(s).padStart(2, '0')}`;
    }

    $('#analyze-btn').on('click', function() {
        const url = ($('#download-url').val() || '').trim();
        if (!url) {
            showMessage('warning', 'Please paste a video URL first.');
            return;
        }

        clearPreview();
        const btn = $(this);
        const oldText = btn.html();
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Analyzing...');
        showMessage('info', 'Fetching video details...');

        $.ajax({
            url: 'api/index.php?endpoint=videoDownloader&action=info',
            method: 'POST',
            data: { url: url },
            dataType: 'json'
        }).done(function(res) {
            if (!res || !res.ok) {
                const err = res && res.data && res.data.msg ? res.data.msg : 'Failed to analyze this link.';
                showMessage('danger', err);
                return;
            }

            const data = res.data || {};
            latestSourceUrl = data.webpage_url || url;

            $('#preview-title').text(data.title || 'Untitled');
            $('#preview-uploader').text(data.uploader ? `By: ${data.uploader}` : 'Uploader unknown');
            $('#preview-meta').text(`Platform: ${data.extractor || 'Unknown'} | Duration: ${secondsToHms(data.duration)}`);
            $('#preview-thumb').attr('src', data.thumbnail || 'https://dummyimage.com/640x360/1f1f1f/ffffff&text=No+Thumbnail');
            $('#open-source-btn').attr('href', latestSourceUrl);
            $('#preview-card').show();

            showMessage('success', 'Video found. You can download it now.');
        }).fail(function(xhr) {
            const msg = xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.msg
                ? xhr.responseJSON.data.msg
                : 'Network/server error while analyzing the link.';
            showMessage('danger', msg);
        }).always(function() {
            btn.prop('disabled', false).html(oldText);
        });
    });

    $('#download-btn').on('click', function() {
        const url = ($('#download-url').val() || '').trim();
        if (!url) {
            showMessage('warning', 'Paste and analyze a URL first.');
            return;
        }

        const btn = $(this);
        const oldText = btn.html();
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Preparing...');
        showMessage('info', 'Preparing your downloadable MP4 link...');

        $.ajax({
            url: 'api/index.php?endpoint=videoDownloader&action=link',
            method: 'POST',
            data: { url: url },
            dataType: 'json'
        }).done(function(res) {
            if (!res || !res.ok || !res.data || !res.data.stream_url) {
                const err = res && res.data && res.data.msg ? res.data.msg : 'Unable to get direct download link.';
                showMessage('danger', err);
                return;
            }

            const directUrl = res.data.stream_url;
            const a = document.createElement('a');
            a.href = directUrl;
            a.target = '_blank';
            a.rel = 'noopener';
            a.download = '';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);

            showMessage('success', 'Download link is ready. If it did not auto-start, open in new tab and save the video.');
        }).fail(function(xhr) {
            const msg = xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.msg
                ? xhr.responseJSON.data.msg
                : 'Network/server error while preparing download.';
            showMessage('danger', msg);
        }).always(function() {
            btn.prop('disabled', false).html(oldText);
        });
    });

    $('#clear-btn').on('click', function() {
        $('#download-url').val('');
        $('#downloader-message').empty();
        clearPreview();
    });
});
</script>
