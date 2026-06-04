<?php include 'header.php'; ?>

<div id="episode-hero" class="hero" style="display:none; background-size: cover; background-position: center; min-height: 500px; display: flex; align-items: flex-end; padding-bottom: 40px;">
    <div class="hero-overlay"></div>
    <div class="hero-content w-100">
        <h1 class="hero-title fw-bold" id="episode-title" style="font-size: 3.5rem; text-shadow: 0 4px 10px rgba(0,0,0,0.8);">Loading...</h1>
        
        <button class="btn btn-secondary-netflix mt-3" onclick="history.back()">
            <i class="fas fa-arrow-left"></i> Back
        </button>
    </div>
</div>

<div class="container" id="main-container" style="margin-top: 100px;">
    <h2 class="section-title">Select Server</h2>
    <div id="servers-list" class="row">
        <div class="text-center"><div class="spinner-border text-danger"></div></div>
    </div>
    
    <div id="player-container" class="mt-5" style="display:none;">
        <div class="d-flex justify-content-center gap-2 mb-3" id="episode-controls">
             <button id="prev-ep-btn" class="btn btn-sm btn-outline-light" style="display:none;">
                <i class="fas fa-step-backward"></i> Previous
             </button>
             <button id="more-ep-btn" class="btn btn-sm btn-outline-light" style="display:none;" onclick="goToMoreEpisodes()">
                <i class="fas fa-list"></i> More Episodes
             </button>
             <button id="next-ep-btn" class="btn btn-sm btn-outline-light" style="display:none;">
                <i class="fas fa-step-forward"></i> Next
             </button>
        </div>
        <div class="ratio ratio-16x9">
            <iframe id="video-player" src="" allowfullscreen referrerpolicy="no-referrer"></iframe>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
$(document).ready(function() {
    const urlParams = new URLSearchParams(window.location.search);
    let href, server, type, link, image, title, more_link, series_title;

    if (urlParams.has('q')) {
        const decryptedQ = decryptLink(urlParams.get('q'));
        const params = new URLSearchParams(decryptedQ);
        href = decryptLink(params.get('href'));
        server = params.get('server');
        type = params.get('type');
        link = decryptLink(params.get('link'));
        image = decryptLink(params.get('image'));
        title = decryptLink(params.get('title'));
        more_link = decryptLink(params.get('more_link'));
        series_title = decryptLink(params.get('series_title'));
    } else {
        href = decryptLink(urlParams.get('href'));
        server = urlParams.get('server');
        type = urlParams.get('type');
        link = decryptLink(urlParams.get('link'));
        image = decryptLink(urlParams.get('image'));
        title = decryptLink(urlParams.get('title'));
        more_link = decryptLink(urlParams.get('more_link'));
        series_title = decryptLink(urlParams.get('series_title'));
    }
    
    // Store metadata for playVideo
    window.currentMetadata = {
        server: server,
        image: image,
        title: title,
        href: href, // This is the episode link usually
        more_link: more_link,
        series_title: series_title
    };

    // Handle More Episodes Logic
    if(more_link && server) {
        $('#more-ep-btn').show();
        
        // Fetch episodes to determine Next/Prev
        $.getJSON(`api/index.php?endpoint=More&action=list&server=${server}&href=${encodeURIComponent(more_link)}`, function(response) {
            if(response.ok && response.data && response.data.episodes) {
                const episodes = response.data.episodes;
                const currentIndex = episodes.findIndex(ep => ep.link === href);
                
                if(currentIndex !== -1) {
                    const currentNum = getEpisodeNumber(title);
                    let nextEp = null;
                    let prevEp = null;

                    // Try to find strictly next/prev numbers
                    if (currentNum !== null) {
                        nextEp = episodes.find(ep => getEpisodeNumber(ep.title) === currentNum + 1);
                        prevEp = episodes.find(ep => getEpisodeNumber(ep.title) === currentNum - 1);
                    } 
                    
                    // Fallback to array index if number matching failed
                    if (!nextEp && !prevEp) {
                         // Check if list is reversed (N..1)
                         // If adjacent element has smaller number, then list is reversed.
                         const adjacentNext = episodes[currentIndex + 1];
                         const adjacentPrev = episodes[currentIndex - 1];
                         
                         let isReversed = false;
                         if (adjacentNext && currentNum !== null && getEpisodeNumber(adjacentNext.title) < currentNum) {
                             isReversed = true;
                         } else if (adjacentPrev && currentNum !== null && getEpisodeNumber(adjacentPrev.title) > currentNum) {
                             isReversed = true;
                         }

                         if (isReversed) {
                             // List is [Ep 10, Ep 9, ...]. Next (Ep 11) is at i-1. Prev (Ep 9) is at i+1.
                             if(currentIndex - 1 >= 0) nextEp = episodes[currentIndex - 1];
                             if(currentIndex + 1 < episodes.length) prevEp = episodes[currentIndex + 1];
                         } else {
                             // List is [Ep 1, Ep 2, ...]. Next (Ep 2) is at i+1. Prev (Ep 0) is at i-1.
                             if(currentIndex + 1 < episodes.length) nextEp = episodes[currentIndex + 1];
                             if(currentIndex - 1 >= 0) prevEp = episodes[currentIndex - 1];
                         }
                    }

                    if(nextEp) {
                        $('#next-ep-btn').show().click(function() {
                            navigateToEpisode(nextEp);
                        });
                    }
                    if(prevEp) {
                        $('#prev-ep-btn').show().click(function() {
                            navigateToEpisode(prevEp);
                        });
                    }
                }
            }
        });
    }
    
    // Display episode hero section
    if (image && title) {
        $('#episode-hero').css('background-image', 'url(' + image + ')');
        $('#episode-title').text(title);
        $('#episode-hero').show();
        $('#main-container').css('margin-top', '20px');
    }
    
    if(type === 'live') {
        // Handle live match logic (different endpoint)
        if (!link) {
            $('#servers-list').html('<p class="text-danger">Error: No match link provided.</p>');
            return;
        }

        $.getJSON(`api/index.php?endpoint=Live&action=match&match=${encodeURIComponent(link)}`, function(response) {
             $('#servers-list').empty();
             
             // Handle string response
             if (typeof response === 'string') {
                 try {
                     response = JSON.parse(response);
                 } catch (e) {
                     console.error("Failed to parse JSON:", e);
                     $('#servers-list').html(`<p class="text-danger">Invalid API Response</p>`);
                     return;
                 }
             }

             console.log("Live Match API Response:", response);

             // Check if response has data
             if(response && response.data) {
                 const servers = response.data.matches || (Array.isArray(response.data) ? response.data : []);
                 const details = response.data.details;

                 if (details) {
                     // Update hero section with match details
                     $('#episode-title').text(`${details.leftTeamName} VS ${details.rightTeamName}`);
                     
                     // Remove any existing meta info to avoid duplication on retries
                     $('.match-meta-info-container').remove();
                     
                     let infoHtml = `
                         <div class="match-meta-info-container mt-3 d-flex flex-wrap gap-3">
                             ${details.league ? `<span class="badge bg-secondary"><i class="fas fa-trophy"></i> ${details.league}</span>` : ''}
                             ${details.channel ? `<span class="badge bg-info text-dark"><i class="fas fa-tv"></i> ${details.channel}</span>` : ''}
                             ${details.commentator ? `<span class="badge bg-warning text-dark"><i class="fas fa-microphone"></i> ${details.commentator}</span>` : ''}
                             ${details.matchTime ? `<span class="badge bg-dark"><i class="fas fa-clock"></i> ${details.matchTime}</span>` : ''}
                             ${details.liveStatus ? `<span class="badge bg-danger"><i class="fas fa-signal"></i> ${details.liveStatus}</span>` : ''}
                         </div>
                     `;
                     $('#episode-title').after(infoHtml);
                     
                     if (details.leftTeamLogo && details.rightTeamLogo) {
                         // Create a banner style background with both logos
                         $('#episode-hero').css('background', `linear-gradient(rgba(0,0,0,0.8), rgba(0,0,0,0.8)), url(${details.leftTeamLogo}) left center no-repeat, url(${details.rightTeamLogo}) right center no-repeat`);
                         $('#episode-hero').css('background-size', 'contain, 30%, 30%');
                     }
                 }

                 if(servers.length > 0) {
                     servers.forEach((srv, index) => {
                         const videoUrl = srv.live || srv.link;
                         const serverLabel = srv.name || `Server ${index + 1}`;

                         if(videoUrl) {
                            let html = `
                                <div class="col-md-3 mb-3">
                                    <button class="btn btn-outline-light w-100 py-3" onclick="playVideo('${videoUrl.replace(/'/g, "\\'")}', this)">
                                        ${serverLabel}
                                    </button>
                                </div>
                            `;
                            $('#servers-list').append(html);
                         }
                     });
                 } else {
                     $('#servers-list').html('<p>No servers found for this match.</p>');
                 }
             } else {
                 // Fallback: Dump the response to see what's wrong
                 $('#servers-list').html(`<p>Unexpected response format. <br><small class="text-muted">${JSON.stringify(response)}</small></p>`);
             }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            console.error("API Request Failed:", textStatus, errorThrown);
            $('#servers-list').html(`<p class="text-danger">Failed to load servers.</p>`);
        });
    } else if(href && server) {
        $.getJSON(`api/index.php?endpoint=Servers&action=list&server=${server}&href=${encodeURIComponent(href)}`, function(response) {
            $('#servers-list').empty();
            
            // Handle string response
            if (typeof response === 'string') {
                 try {
                     response = JSON.parse(response);
                 } catch (e) {
                     console.error("Failed to parse JSON:", e);
                     $('#servers-list').html(`<p class="text-danger">Invalid API Response</p>`);
                     return;
                 }
            }

            if(response && response.data && Array.isArray(response.data) && response.data.length > 0) {
                const firstSrv = response.data[0];
                // Update hero background if backdrop is provided in response (for tvdb servers)
                if (firstSrv.backdrop) {
                    $('#episode-hero').css('background-image', 'url(' + firstSrv.backdrop + ')');
                    window.currentMetadata.image = firstSrv.backdrop;
                }
                
                // Show Overview and Date under title
                let infoHtml = '';
                if (firstSrv.date) {
                    const year = firstSrv.date.split('-')[0];
                    infoHtml += `<div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="badge rounded-pill bg-danger px-2 py-1" style="font-size: 0.75rem;">${year}</span>
                                    <span class="text-white-50" style="font-size: 0.75rem;">Movies & Series</span>
                                 </div>`;
                }
                if (firstSrv.overview) {
                    infoHtml += `<p class="mt-2 text-white-50 info-overview" style="max-width: 650px; font-size: 0.95rem; line-height: 1.5; text-shadow: 0 1px 3px rgba(0,0,0,0.5); display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">${firstSrv.overview}</p>`;
                }
                
                if (infoHtml) {
                    $('#episode-title').after(`<div class="hero-info my-3">${infoHtml}</div>`);
                }

                response.data.forEach((srv, index) => {
                    // Escape single quotes in URL just in case
                    const safeLink = srv.link ? srv.link.replace(/'/g, "\\'") : '';
                    if(safeLink) {
                        let html = `
                            <div class="col-md-3 mb-3">
                                <button class="btn btn-outline-light w-100 py-3" onclick="playVideo('${safeLink}', this)">
                                    ${srv.name || 'Server ' + (index+1)}
                                </button>
                            </div>
                        `;
                        $('#servers-list').append(html);
                    }
                });
            } else {
                $('#servers-list').html('<p>No servers found.</p>');
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            console.error("API Request Failed:", textStatus, errorThrown);
            $('#servers-list').html(`<p class="text-danger">Failed to load servers.</p>`);
        });
    }
});

function playVideo(url, btn) {
    // Remove active class from all buttons
    $('#servers-list .btn').removeClass('active');
    // Add active class to clicked button
    if(btn) $(btn).addClass('active');

    $('#player-container').show();
    $('#video-player').attr('src', 'videoPlayer.php?link=' + encodeURIComponent(url));
    $('html, body').animate({
        scrollTop: $("#player-container").offset().top - 100
    }, 500);

    // Add to History
    const meta = window.currentMetadata;
    if(meta && meta.server && meta.title && meta.image && meta.href) {
        $.post('api/index.php?endpoint=History&action=add', {
            server: meta.server,
            title: meta.title,
            poster: meta.image,
            link: meta.href
        });
    }
}

function getEpisodeNumber(title) {
    if(!title) return null;
    const match = title.match(/\d+/);
    return match ? parseInt(match[0]) : null;
}

function navigateToEpisode(ep) {
    const meta = window.currentMetadata;
    navigateToEncrypted({
        v: 'Servers',
        href: encryptLink(ep.link),
        server: meta.server,
        image: encryptLink(meta.image),
        title: encryptLink(ep.title),
        more_link: encryptLink(meta.more_link),
        series_title: encryptLink(meta.series_title)
    });
}

function goToMoreEpisodes() {
    const meta = window.currentMetadata;
    if(meta.more_link) {
        navigateToEncrypted({
            v: 'More',
            href: encryptLink(meta.more_link),
            server: meta.server,
            image: encryptLink(meta.image),
            title: encryptLink(meta.series_title || meta.title)
        });
    }
}
</script>
