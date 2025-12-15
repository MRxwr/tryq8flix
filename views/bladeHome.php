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
    // Fetch Banners for Hero
    $.getJSON('api/index.php?endpoint=Banners', function(response) {
        if(response.ok && response.data.length > 0) {
            const banner = response.data[0];
            $('#hero-section').css('background-image', 'url(' + banner.imageurl + ')');
            // Assuming banner has title, if not we might need to fetch it or use static
            // For now, let's just use a generic title or try to parse from url
            $('#hero-title').text('Featured Content'); 
            $('#hero-desc').text('Watch the latest movies and TV shows on TryQ8Flix.');
        }
    });

    // Fetch Content for Rows
    // We will fetch from different servers to simulate categories
    const servers = [
        {id: 1, name: 'Wecima (Trending)'},
        {id: 4, name: 'Shahid Originals'},
        {id: 7, name: 'MyCima Movies'}
    ];

    servers.forEach(server => {
        $.getJSON('api/index.php?endpoint=Home&action=view&server=' + server.id, function(response) {
            if(response.ok && response.data.shows) {
                let rowHtml = `
                    <div class="section-title">${server.name}</div>
                    <div class="movie-row">
                `;
                
                response.data.shows.forEach(show => {
                    rowHtml += `
                        <div class="movie-card" onclick="window.location.href='?v=More&href=${encodeURIComponent(show.href)}&server=${server.id}'">
                            <img src="${show.image}" alt="${show.title}" onerror="this.src='https://via.placeholder.com/200x300?text=No+Image'">
                        </div>
                    `;
                });
                
                rowHtml += `</div>`;
                $('#content-rows').append(rowHtml);
            }
        });
    });
});
</script>
