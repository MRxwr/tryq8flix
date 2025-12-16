<?php include 'header.php'; ?>

<div id="hero-section" class="hero" style="display:none; background-size: cover; background-position: center; align-items: flex-end; padding-bottom: 50px;">
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
    let href, server, image, title;

    if (urlParams.has('q')) {
        const decryptedQ = decryptLink(urlParams.get('q'));
        const params = new URLSearchParams(decryptedQ);
        href = decryptLink(params.get('href'));
        server = params.get('server');
        image = decryptLink(params.get('image'));
        title = decryptLink(params.get('title'));
    } else {
        href = decryptLink(urlParams.get('href'));
        server = urlParams.get('server');
        image = decryptLink(urlParams.get('image'));
        title = decryptLink(urlParams.get('title'));
    }
    
    // Setup Hero Section
    if (image) {
        $('#hero-section').css('background-image', 'url(' + image + ')');
        $('#hero-section').show();
    }
    if (title) {
        $('#hero-title').text(title);
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
                     const qParams = {
                        v: 'Servers',
                        href: encryptLink(href),
                        server: server,
                        image: encryptLink(image || ''),
                        title: encryptLink(title || '')
                     };
                     const qString = Object.keys(qParams).map(key => key + '=' + encodeURIComponent(qParams[key])).join('&');
                     window.location.replace('?q=' + encodeURIComponent(encryptLink(qString)));
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
                        const encHref = encryptLink(season.link);
                        const encImage = encryptLink(image || '');
                        const encTitle = encryptLink((title || '') + ' - ' + season.title);
                        html += `
                            <div class="col-6 col-md-3 col-lg-2 mb-3">
                                <div class="card bg-dark text-white h-100" onclick="navigateToEncrypted({v: 'More', href: '${encHref}', server: '${server}', image: '${encImage}', title: '${encTitle}'})" style="cursor:pointer;">
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
                        const encHref = encryptLink(ep.link);
                        const encImage = encryptLink(image || '');
                        const encTitle = encryptLink((title || '') + ' - ' + ep.title);
                        html += `
                            <div class="col-6 col-md-3 col-lg-2 mb-3">
                                <div class="card bg-dark text-white h-100" onclick="navigateToEncrypted({v: 'Servers', href: '${encHref}', server: '${server}', image: '${encImage}', title: '${encTitle}'})" style="cursor:pointer;">
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
