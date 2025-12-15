<?php include 'header.php'; ?>

<div class="container" style="margin-top: 100px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="section-title m-0" id="categoryTitle">Category</h2>
    </div>
    
    <div id="categoryResults" class="row">
        <div class="text-center w-100"><div class="spinner-border text-danger"></div></div>
    </div>

    <div class="text-center mt-4 mb-5">
        <button id="loadMoreBtn" class="btn btn-netflix" style="display:none;">Load More</button>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
let currentPage = 1;
let currentServer = 1;
let isLoading = false;

$(document).ready(function() {
    const urlParams = new URLSearchParams(window.location.search);
    currentServer = urlParams.get('server');
    const title = urlParams.get('title');
    
    if(title) {
        $('#categoryTitle').text(decodeURIComponent(title));
    } else {
        $('#categoryTitle').text('Server ' + currentServer);
    }

    if(currentServer) {
        loadContent(currentPage);
    } else {
        $('#categoryResults').html('<p class="text-center">Invalid Server ID</p>');
    }
});

$('#loadMoreBtn').click(function() {
    if(!isLoading) {
        currentPage++;
        loadContent(currentPage);
    }
});

function loadContent(page) {
    isLoading = true;
    $('#loadMoreBtn').prop('disabled', true).text('Loading...');
    
    // If it's the first page, show spinner in the main area
    if(page === 1) {
        $('#categoryResults').html('<div class="text-center w-100"><div class="spinner-border text-danger"></div></div>');
    }

    $.getJSON(`api/index.php?endpoint=Home&action=view&server=${currentServer}&page=${page}`, function(response) {
        if(page === 1) $('#categoryResults').empty();
        
        if(response.ok && response.data.shows && response.data.shows.length > 0) {
            response.data.shows.forEach(show => {
                let html = `
                    <div class="col-6 col-md-3 col-lg-2 mb-4">
                        <div class="movie-card w-100" onclick="navigateTo('?v=More&href=${encodeURIComponent(encryptLink(show.href))}&server=${currentServer}')">
                            <img src="${show.image}" alt="${show.title}" onerror="this.src='https://via.placeholder.com/200x300?text=No+Image'">
                            <div class="mt-2 text-center small text-truncate">${show.title}</div>
                        </div>
                    </div>
                `;
                $('#categoryResults').append(html);
            });
            
            // Show load more button if we got results
            $('#loadMoreBtn').show().prop('disabled', false).text('Load More');
        } else {
            if(page === 1) {
                $('#categoryResults').html('<p class="text-center w-100">No content found.</p>');
            } else {
                $('#loadMoreBtn').hide(); // No more pages
            }
        }
        isLoading = false;
    }).fail(function() {
        if(page === 1) {
            $('#categoryResults').html('<p class="text-center w-100 text-danger">Failed to load content.</p>');
        } else {
            $('#loadMoreBtn').prop('disabled', false).text('Try Again');
        }
        isLoading = false;
    });
}
</script>
