<?php include 'header.php'; ?>

<div id="hero-section" class="hero" style="background-image: url('https://image.tmdb.org/t/p/original/9yBVqNruk6Ykr9zD297HQNAmUe7.jpg');">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1 class="hero-title" id="hero-title">Loading...</h1>
        <p class="hero-desc" id="hero-desc">Please wait while we fetch the latest content for you.</p>
        <button class="btn btn-netflix"><i class="fas fa-play"></i> Play</button>
        <button class="btn btn-secondary-netflix"><i class="fas fa-info-circle"></i> More Info</button>
    </div>
</div>

<div id="content-rows">
    <!-- Rows will be injected here -->
</div>

<?php include 'footer.php'; ?>

<script>
$(document).ready(function() {
    // Fetch Main Data (Banners & Servers)
    $.getJSON('api/index.php?endpoint=Main', function(response) {
        if(response.ok) {
            const data = response.data;
            
            // 1. Setup Hero Section from Banners
            if(data.banners && data.banners.length > 0) {
                // Pick a random banner or the first one
                const banner = data.banners[Math.floor(Math.random() * data.banners.length)];
                $('#hero-section').css('background-image', 'url(' + banner.imageurl + ')');
                $('#hero-title').text(banner.title || 'Featured Content');
                $('#hero-desc').text('Watch the latest movies and TV shows on TryQ8Flix.');
                
                // Update Play/More Info buttons if needed based on banner data
                // For example, if banner has an endpoint/url, we could attach it to the button
                if(banner.url && banner.server) {
                     $('.btn-netflix').attr('onclick', `window.location.href='?v=More&href=${encodeURIComponent(encryptLink(banner.url))}&server=${banner.server}'`);
                }
            } else {
                 $('#hero-title').text('Welcome to TryQ8Flix');
                 $('#hero-desc').text('Browse our collection of movies and TV shows.');
            }

            // 2. Setup Content Rows from Servers
            if(data.servers && data.servers.length > 0) {
                data.servers.forEach(server => {
                    // Fetch content for each server
                    $.getJSON('api/index.php?endpoint=Home&action=view&server=' + server.id, function(serverRes) {
                        if(serverRes.ok && serverRes.data.shows && serverRes.data.shows.length > 0) {
                            const rowId = `row-${server.id}`;
                            let rowHtml = `
                                <div class="section-title">${server.name}</div>
                                <div class="row-wrapper">
                                    <button class="scroll-btn scroll-left d-none d-md-flex" onclick="scrollRow('${rowId}', -1)"><i class="fas fa-chevron-left"></i></button>
                                    <div class="movie-row" id="${rowId}">
                            `;
                            
                            serverRes.data.shows.forEach(show => {
                                rowHtml += `
                                    <div class="movie-card" onclick="window.location.href='?v=More&href=${encodeURIComponent(encryptLink(show.href))}&server=${server.id}'">
                                        <img src="${show.image}" alt="${show.title}" onerror="this.src='https://via.placeholder.com/200x300?text=No+Image'">
                                    </div>
                                `;
                            });
                            
                            rowHtml += `</div>
                                    <button class="scroll-btn scroll-right d-none d-md-flex" onclick="scrollRow('${rowId}', 1)"><i class="fas fa-chevron-right"></i></button>
                                </div>`;
                            $('#content-rows').append(rowHtml);
                        }
                    });
                });
            }
        }
    });
});

function scrollRow(elementId, direction) {
    const container = document.getElementById(elementId);
    const scrollAmount = container.clientWidth * 0.8; // Scroll 80% of the view width
    
    if (direction === 1) {
        container.scrollBy({ left: scrollAmount, behavior: 'smooth' });
    } else {
        container.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
    }
}
</script>
