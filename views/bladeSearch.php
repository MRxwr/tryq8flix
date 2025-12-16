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
    <div id="loadingIndicator" class="text-center mt-4" style="display: none;">
        <div class="spinner-border text-danger"></div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
let currentPage = 1;
let currentQuery = '';
let currentServerId = '';
let isLoading = false;
let hasMore = true;

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

    // Infinite Scroll
    $(window).scroll(function() {
        if($(window).scrollTop() + $(window).height() > $(document).height() - 100) {
            if(!isLoading && hasMore && currentQuery) {
                performSearch(true);
            }
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

function performSearch(isLoadMore = false) {
    const query = $('#searchInput').val();
    const serverId = $('#serverSelect').val();
    
    if(!query) return;
    if(isLoading) return;
    
    isLoading = true;
    
    if (!isLoadMore) {
        currentPage = 1;
        currentQuery = query;
        currentServerId = serverId;
        hasMore = true;
        $('#searchResults').empty();
        $('#searchResults').html('<div class="text-center w-100 loading-spinner"><div class="spinner-border text-danger"></div></div>');
        $('#loadingIndicator').hide();
    } else {
        currentPage++;
        $('#loadingIndicator').show();
    }
    
    $.getJSON(`api/index.php?endpoint=Home&action=view&page=${currentPage}&server=${currentServerId}&search=${encodeURIComponent(currentQuery)}`, function(response) {
        isLoading = false;
        if (!isLoadMore) {
            $('.loading-spinner').remove();
        } else {
            $('#loadingIndicator').hide();
        }

        if(response.ok && response.data.shows && response.data.shows.length > 0) {
            response.data.shows.forEach(show => {
                let html = `
                    <div class="col-6 col-md-3 col-lg-2 mb-4">
                        <div class="movie-card w-100" onclick="navigateTo('?v=More&href=${encodeURIComponent(encryptLink(show.href))}&server=${currentServerId}&image=${encodeURIComponent(encryptLink(show.image))}&title=${encodeURIComponent(encryptLink(show.title))}')">
                            <img src="${show.image}" alt="${show.title}" onerror="this.src='https://via.placeholder.com/200x300?text=No+Image'">
                            <div class="mt-2 text-center small text-truncate">${show.title}</div>
                        </div>
                    </div>
                `;
                $('#searchResults').append(html);
            });
        } else {
            hasMore = false;
            if (!isLoadMore) {
                $('#searchResults').html('<p class="text-center w-100">No results found.</p>');
            }
        }
    }).fail(function() {
        isLoading = false;
        $('#loadingIndicator').hide();
        if (!isLoadMore) $('.loading-spinner').remove();
    });
}
</script>
