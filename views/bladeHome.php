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
    // iOS AdBlock Recommendation
    if (/iPhone|iPad/.test(navigator.userAgent) && !window.MSStream && !localStorage.getItem('adblock_recommendation_dismissed')) {
        // Check if AdBlock is active
        const testAd = document.createElement('div');
        testAd.innerHTML = '&nbsp;';
        testAd.className = 'adsbox ad-banner pub_300x250 pub_728x90 text-ad';
        testAd.style.position = 'absolute';
        testAd.style.top = '-1000px';
        document.body.appendChild(testAd);

        setTimeout(function() {
            // If height is 0, it was blocked/hidden by an extension
            const isBlocked = testAd.offsetHeight === 0; 
            document.body.removeChild(testAd);

            if (!isBlocked) {
                const alertHtml = `
                    <div class="alert alert-dark alert-dismissible fade show" role="alert" style="margin: 2rem 4% 0 4%; border: 1px solid #333; background-color: #222; color: #fff;">
                        <div class="d-flex align-items-start">
                            <div class="me-3">
                                <i class="fab fa-apple fa-2x"></i>
                            </div>
                            <div>
                                <h5 class="alert-heading mb-1">iOS User Recommendation</h5>
                                <p class="mb-2 small">For the best experience without ads, we recommend installing <strong>AdBlock Pro</strong>.</p>
                                <a href="https://apps.apple.com/us/app/adblock-pro-for-safari/id1018301773" target="_blank" class="btn btn-sm btn-light mb-2">
                                    <i class="fas fa-download"></i> Install from App Store
                                </a>
                                <div class="mt-2 p-2 rounded" style="background: rgba(255,255,255,0.1); font-size: 0.85rem;">
                                    <strong>Setup Instructions:</strong>
                                    <ol class="mb-0 ps-3">
                                        <li>Go to <strong>Settings</strong> > <strong>Safari</strong> > <strong>Extensions</strong></li>
                                        <li>Enable <strong>AdBlock Pro</strong></li>
                                        <li>Open the App and turn on "<strong>All Categories</strong>"</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close" onclick="localStorage.setItem('adblock_recommendation_dismissed', 'true')"></button>
                    </div>
                `;
                $('#content-rows').before(alertHtml);
            }
        }, 100);
    }

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
                     $('.btn-netflix').attr('onclick', `navigateTo('?v=More&href=${encodeURIComponent(encryptLink(banner.url))}&server=${banner.server}')`);
                     $('.btn-secondary-netflix').attr('onclick', `navigateTo('?v=More&href=${encodeURIComponent(encryptLink(banner.url))}&server=${banner.server}')`);
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
                                <div class="d-flex justify-content-between align-items-center" style="margin: 2rem 4% 1rem 4%;">
                                    <div class="section-title" style="margin: 0;">${server.name}</div>
                                    <span class="text-white small fw-bold" style="cursor:pointer;" onclick="navigateTo('?v=Category&server=${server.id}&title=${encodeURIComponent(server.name)}')">View More <i class="fas fa-chevron-right"></i></span>
                                </div>
                                <div class="row-wrapper">
                                    <button class="scroll-btn scroll-left d-none d-md-flex" onclick="scrollRow('${rowId}', -1)"><i class="fas fa-chevron-left"></i></button>
                                    <div class="movie-row" id="${rowId}">
                            `;
                            
                            serverRes.data.shows.forEach(show => {
                                rowHtml += `
                                    <div class="movie-card" onclick="navigateTo('?v=More&href=${encodeURIComponent(encryptLink(show.href))}&server=${server.id}')">
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
