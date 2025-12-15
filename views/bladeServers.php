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
    const href = urlParams.get('href');
    const server = urlParams.get('server');
    const type = urlParams.get('type'); // 'live' or normal
    
    if(type === 'live') {
        // Handle live match logic (different endpoint)
        const link = urlParams.get('link');
        if (!link) {
            $('#servers-list').html('<p class="text-danger">Error: No match link provided.</p>');
            return;
        }

        $.getJSON(`api/index.php?endpoint=Live&action=match&match=${encodeURIComponent(link)}`, function(response) {
             $('#servers-list').empty();
             
             // Ensure response.data is an array and has items
             if(response.ok && Array.isArray(response.data) && response.data.length > 0) {
                 response.data.forEach(srv => {
                     // Use srv.live for the video URL and srv.serv for the label
                     if(srv.live) {
                        let html = `
                            <div class="col-md-3 mb-3">
                                <button class="btn btn-outline-light w-100 py-3" onclick="playVideo('${srv.live}', this)">
                                    Server ${srv.serv}
                                </button>
                            </div>
                        `;
                        $('#servers-list').append(html);
                     }
                 });
             } else {
                 $('#servers-list').html('<p>No servers found for this match.</p>');
             }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            console.error("API Request Failed:", textStatus, errorThrown);
            $('#servers-list').html(`<p class="text-danger">Failed to load servers.</p>`);
        });
    } else if(href && server) {
        $.getJSON(`api/index.php?endpoint=Servers&action=list&server=${server}&href=${encodeURIComponent(href)}`, function(response) {
            $('#servers-list').empty();
            if(response.ok && response.data.length > 0) {
                response.data.forEach((srv, index) => {
                    let html = `
                        <div class="col-md-3 mb-3">
                            <button class="btn btn-outline-light w-100 py-3" onclick="playVideo('${srv.link}', this)">
                                ${srv.name || 'Server ' + (index+1)}
                            </button>
                        </div>
                    `;
                    $('#servers-list').append(html);
                });
            } else {
                $('#servers-list').html('<p>No servers found.</p>');
            }
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
