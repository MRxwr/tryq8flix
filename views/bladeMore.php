<?php include 'header.php'; ?>

<div id="hero-section" class="hero" style="display:none; background-size: cover; background-position: center; align-items: flex-end; padding-bottom: 50px;">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1 class="hero-title" id="hero-title">Loading...</h1>
        <p class="hero-desc" id="hero-desc"></p>
        <button id="favBtnHero" class="btn btn-secondary-netflix" style="display:none;">
            <i class="far fa-heart"></i> Add to Favorites
        </button>
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
        $('#favBtnHero').show();
        // checkFavorite moved to after content load to prevent network errors
    }
    if (title) {
        $('#hero-title').text(title);
    }

    $('#favBtnHero').click(function() {
        toggleFavoriteHero(server, href, image, $('#hero-title').text());
    });

    if(href && server) {
        $.ajax({
            url: `api/index.php?endpoint=More&action=list&server=${server}&href=${encodeURIComponent(href)}`,
            dataType: 'json',
            timeout: 60000,
            success: function(response) {
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
                
                // Check watched status
                checkWatchedStatus(server);

                // Check favorite status
                if (image) {
                    checkFavorite(server, href, image);
                }
            } else {
                $('#details-container').html(`
                    <div class="text-center mt-5">
                        <p class="text-danger mb-3">${response.data && response.data.msg ? response.data.msg : 'Failed to load content.'}</p>
                        <button class="btn btn-netflix" onclick="location.reload()">
                            <i class="fas fa-sync-alt me-2"></i> Try Again
                        </button>
                    </div>
                `);
            }
        }}).fail(function(jqXHR, textStatus, errorThrown) {
            console.error("Load failed:", textStatus, errorThrown);
            $('#details-container').html(`
                <div class="text-center mt-5">
                    <p class="text-danger mb-3">Network error or server timeout.</p>
                    <button class="btn btn-netflix" onclick="location.reload()">
                        <i class="fas fa-sync-alt me-2"></i> Try Again
                    </button>
                </div>
            `);
        });
    } else {
        $('#details-container').html('<p class="text-center text-danger mt-5">Invalid parameters. Missing link or server.</p>');
    }
});

function checkWatchedStatus(server) {
    $.getJSON(`api/index.php?endpoint=History&action=list&server=${server}`, function(response) {
        if(response.ok && response.data.history) {
            const watchedLinks = new Set(response.data.history.map(item => item.link));
            
            $('.card[onclick]').each(function() {
                const onclickAttr = $(this).attr('onclick');
                // Extract href from onclick string: navigateToEncrypted({..., href: 'ENCRYPTED_STRING', ...})
                // We need to parse the params object from the string.
                // Regex to find href: '...'
                const match = onclickAttr.match(/href:\s*'([^']+)'/);
                if(match && match[1]) {
                    const encryptedHref = match[1];
                    const href = decryptLink(encryptedHref);
                    
                    if(watchedLinks.has(href)) {
                        $(this).addClass('border-danger');
                        $(this).find('.card-body').append('<span class="badge bg-danger position-absolute top-0 end-0 m-2">Watched</span>');
                    }
                }
            });
        }
    });
}

function checkFavorite(server, link, poster) {
    $.post('api/index.php?endpoint=Favorites&action=check', {poster: poster}, function(response) {
        if(response.ok && response.data.isFavorite) {
            updateHeroButton(true);
        } else {
            updateHeroButton(false);
        }
    }, 'json');
}

function updateHeroButton(isFav) {
    const btn = $('#favBtnHero');
    if(isFav) {
        btn.html('<i class="fas fa-heart text-danger"></i> Remove from Favorites');
        btn.data('isFav', true);
    } else {
        btn.html('<i class="far fa-heart"></i> Add to Favorites');
        btn.data('isFav', false);
    }
}

function toggleFavoriteHero(server, link, poster, title) {
    const btn = $('#favBtnHero');
    const isFav = btn.data('isFav');
    
    if(isFav) {
        if(confirm('Remove from favorites?')) {
            $.post('api/index.php?endpoint=Favorites&action=remove', {poster: poster}, function(res) {
                if(res.ok) {
                    updateHeroButton(false);
                    showToast('Removed from favorites');
                }
            }, 'json');
        }
    } else {
        if(confirm('Add to favorites?')) {
            $.post('api/index.php?endpoint=Favorites&action=add', {server: server, link: link, poster: poster, title: title}, function(res) {
                if(res.ok) {
                    updateHeroButton(true);
                    showToast('Added to favorites');
                } else {
                    alert(res.error.msg);
                }
            }, 'json');
        }
    }
}

function showToast(message) {
    const toast = $(`<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100">
        <div class="toast show align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>`);
    $('body').append(toast);
    setTimeout(() => { toast.fadeOut(500, () => toast.remove()); }, 3000);
}
</script>
