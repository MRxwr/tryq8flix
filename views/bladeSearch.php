<?php include 'header.php'; ?>

<div class="container" style="margin-top: 100px;">
    <div class="input-group mb-5">
        <select class="form-select bg-dark text-white border-secondary" id="serverSelect" style="max-width: 150px;">
            <option value="1" selected>Wecima</option>
        </select>
        <input type="text" class="form-control bg-dark text-white border-secondary" id="searchInput" placeholder="Search for movies, TV shows...">
        <button class="btn btn-netflix" id="searchBtn">Search</button>
    </div>
    
    <div id="searchResults" class="row"></div>
    <div id="loadMoreBtnContainer" class="text-center mt-4" style="display: none;">
        <button class="btn btn-secondary-netflix" id="loadMoreBtn">Load More</button>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
let currentPage = 1;
let currentQuery = '';
let currentServerId = '';

$(document).ready(function() {
    // Populate Server Dropdown
    $.getJSON('api/index.php?endpoint=Main', function(response) {
        if(response.ok && response.data.servers) {
            let options = '';
            response.data.servers.forEach(server => {
                options += `<option value="${server.id}">${server.name}</option>`;
            });
            $('#serverSelect').html(options);
        }
    });
});

$('#searchBtn').click(function() {
    performSearch(false);
});

$('#searchInput').keypress(function(e) {
    if(e.which == 13) {
        performSearch(false);
    }
});

$('#loadMoreBtn').click(function() {
    performSearch(true);
});

function performSearch(isLoadMore = false) {
    const query = $('#searchInput').val();
    const serverId = $('#serverSelect').val();
    
    if(!query) return;
    
    if (!isLoadMore) {
        currentPage = 1;
        currentQuery = query;
        currentServerId = serverId;
        $('#searchResults').empty();
        $('#searchResults').html('<div class="text-center w-100 loading-spinner"><div class="spinner-border text-danger"></div></div>');
        $('#loadMoreBtnContainer').hide();
    } else {
        currentPage++;
        $('#loadMoreBtn').html('<div class="spinner-border spinner-border-sm text-light"></div> Loading...');
        $('#loadMoreBtn').prop('disabled', true);
    }
    
    $.getJSON(`api/index.php?endpoint=Home&action=view&page=${currentPage}&server=${currentServerId}&search=${encodeURIComponent(currentQuery)}`, function(response) {
        if (!isLoadMore) {
            $('.loading-spinner').remove();
        } else {
            $('#loadMoreBtn').html('Load More');
            $('#loadMoreBtn').prop('disabled', false);
        }

        if(response.ok && response.data.shows && response.data.shows.length > 0) {
            response.data.shows.forEach(show => {
                let html = `
                    <div class="col-6 col-md-3 col-lg-2 mb-4">
                        <div class="movie-card w-100" onclick="navigateTo('?v=More&href=${encodeURIComponent(encryptLink(show.href))}&server=${currentServerId}')">
                            <img src="${show.image}" alt="${show.title}" onerror="this.src='https://via.placeholder.com/200x300?text=No+Image'">
                            <div class="mt-2 text-center small text-truncate">${show.title}</div>
                        </div>
                    </div>
                `;
                $('#searchResults').append(html);
            });
            
            // Show load more button if we got results
            $('#loadMoreBtnContainer').show();
        } else {
            if (!isLoadMore) {
                $('#searchResults').html('<p class="text-center w-100">No results found.</p>');
            } else {
                // No more results for load more
                $('#loadMoreBtnContainer').hide();
            }
        }
    });
}
</script>
