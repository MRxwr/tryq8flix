<?php include 'header.php'; ?>

<div id="hero-section" class="hero" style="display:none; background-size: cover; background-position: center;">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1 class="hero-title" id="hero-title">Loading...</h1>
        <p class="hero-desc" id="hero-desc"></p>
    </div>
</div>

<div class="container" style="margin-top: 20px;">
    <div id="details-container">
        <div class="text-center"><div class="spinner-border text-danger"></div></div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
$(document).ready(function() {
    const urlParams = new URLSearchParams(window.location.search);
    const href = decryptLink(urlParams.get('href'));
    const server = urlParams.get('server');
    const image = urlParams.get('image');
    const title = urlParams.get('title');
    
    // Setup Hero Section
    if (image) {
        $('#hero-section').css('background-image', 'url(' + decodeURIComponent(image) + ')');
        $('#hero-section').show();
    }
    if (title) {
        $('#hero-title').text(decodeURIComponent(title));
    }

    if(href && server) {
        $.getJSON(`api/index.php?endpoint=More&action=list&server=${server}&href=${encodeURIComponent(href)}`, function(response) {
            if(response.ok) {
                const data = response.data;
                
                // If we didn't have a title before, maybe we have it now? 
                if (!title && data.title) {
                     $('#hero-title').text(data.title);
                }
                // If we didn't have an image, maybe use poster from data if available?
                if (!image && data.poster) {
                    $('#hero-section').css('background-image', 'url(' + data.poster + ')');
                    $('#hero-section').show();
                }

                // If no seasons and no episodes, it's likely a movie -> go to servers
                if ((!data.seasons || data.seasons.length === 0) && (!data.episodes || data.episodes.length === 0)) {
                     window.location.replace(`?v=Servers&href=${encodeURIComponent(encryptLink(href))}&server=${server}&image=${encodeURIComponent(image || '')}&title=${encodeURIComponent(title || '')}`);
                     return;
                }

                let html = '';
                
                if(data.seasons && data.seasons.length > 0) {
                    html += `
                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <h2>Seasons</h2>
                            </div>
                        </div>
                        <div class="row mb-5">`;
                    data.seasons.forEach(season => {
                        html += `
                            <div class="col-6 col-md-3 col-lg-2 mb-3">
                                <div class="card bg-dark text-white h-100" onclick="navigateTo('?v=More&href=${encodeURIComponent(encryptLink(season.link))}&server=${server}&image=${encodeURIComponent(image || '')}&title=${encodeURIComponent(title || '')} - ${encodeURIComponent(season.title)}')" style="cursor:pointer;">
                                    <div class="card-body text-center d-flex align-items-center justify-content-center">
                                        <h5 class="card-title">${season.title}</h5>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    html += `</div>`;
                }
                
                if(data.episodes && data.episodes.length > 0) {
                    html += `
                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <h2>Episodes</h2>
                            </div>
                        </div>
                        <div class="row">`;
                    data.episodes.forEach(ep => {
                        html += `
                            <div class="col-6 col-md-3 col-lg-2 mb-3">
                                <div class="card bg-dark text-white h-100" onclick="navigateTo('?v=Servers&href=${encodeURIComponent(encryptLink(ep.link))}&server=${server}&image=${encodeURIComponent(image || '')}&title=${encodeURIComponent(title || '')} - ${encodeURIComponent(ep.title)}')" style="cursor:pointer;">
                                    <div class="card-body text-center d-flex align-items-center justify-content-center">
                                        <h6 class="card-title">${ep.title}</h6>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    html += `</div>`;
                }
                
                $('#details-container').html(html);
            }
        });
    }
});
</script>
