<?php include 'header.php'; ?>

<div class="container" style="margin-top: 100px;">
    <h2 class="section-title">Select Server</h2>
    <div id="servers-list" class="row">
        <div class="text-center"><div class="spinner-border text-danger"></div></div>
    </div>
    
    <div id="player-container" class="mt-5" style="display:none;">
        <div class="ratio ratio-16x9">
            <iframe id="video-player" src="" allowfullscreen></iframe>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
$(document).ready(function() {
    const urlParams = new URLSearchParams(window.location.search);
    let href, server, type, link;

    if (urlParams.has('q')) {
        const decryptedQ = decryptLink(urlParams.get('q'));
        const params = new URLSearchParams(decryptedQ);
        href = decryptLink(params.get('href'));
        server = params.get('server');
        type = params.get('type');
        link = decryptLink(params.get('link'));
    } else {
        href = decryptLink(urlParams.get('href'));
        server = urlParams.get('server');
        type = urlParams.get('type');
        link = decryptLink(urlParams.get('link'));
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

             console.log(link);
             console.log("Live Match API Response:", response);

             // Check if response has data array
             if(response && response.data && Array.isArray(response.data)) {
                 if(response.data.length > 0) {
                     response.data.forEach((srv, index) => {
                         // Support both 'live' (matches) and 'link' (movies) keys just in case
                         const videoUrl = srv.live || srv.link;
                         const serverLabel = srv.serv ? `Server ${srv.serv}` : (srv.name || `Server ${index + 1}`);

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
}
</script>
