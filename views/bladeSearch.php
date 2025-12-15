<?php include 'header.php'; ?>

<div class="container" style="margin-top: 100px;">
    <div class="input-group mb-5">
        <input type="text" class="form-control bg-dark text-white border-0" id="searchInput" placeholder="Search for movies, TV shows...">
        <button class="btn btn-netflix" id="searchBtn">Search</button>
    </div>
    
    <div id="searchResults" class="row"></div>
</div>

<?php include 'footer.php'; ?>

<script>
$('#searchBtn').click(function() {
    performSearch();
});

$('#searchInput').keypress(function(e) {
    if(e.which == 13) {
        performSearch();
    }
});

function performSearch() {
    const query = $('#searchInput').val();
    if(!query) return;
    
    $('#searchResults').html('<div class="text-center w-100"><div class="spinner-border text-danger"></div></div>');
    
    // Search on Server 1 (Wecima) as default
    $.getJSON('api/index.php?endpoint=Home&action=view&server=1&search=' + encodeURIComponent(query), function(response) {
        $('#searchResults').empty();
        if(response.ok && response.data.shows) {
            response.data.shows.forEach(show => {
                let html = `
                    <div class="col-6 col-md-3 col-lg-2 mb-4">
                        <div class="movie-card w-100" onclick="window.location.href='?v=More&href=${encodeURIComponent(show.href)}&server=1'">
                            <img src="${show.image}" alt="${show.title}" onerror="this.src='https://via.placeholder.com/200x300?text=No+Image'">
                            <div class="mt-2 text-center small text-truncate">${show.title}</div>
                        </div>
                    </div>
                `;
                $('#searchResults').append(html);
            });
        } else {
            $('#searchResults').html('<p class="text-center w-100">No results found.</p>');
        }
    });
}
</script>
