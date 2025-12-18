<?php include 'header.php'; ?>

<div class="container" style="margin-top: 100px;">
    <h2 class="mb-4">My Favorites</h2>
    
    <div id="favoritesResults" class="row"></div>
    <div id="loadingIndicator" class="text-center mt-4">
        <div class="spinner-border text-danger"></div>
    </div>
    <div id="emptyState" class="text-center mt-5" style="display: none;">
        <i class="far fa-sad-tear fa-4x mb-3 text-muted"></i>
        <h3>No favorites yet</h3>
        <p class="text-muted">Start adding movies and TV shows to your list!</p>
        <a href="?v=Home" class="btn btn-netflix mt-3">Browse Content</a>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
$(document).ready(function() {
    loadFavorites();
});

function loadFavorites() {
    $('#loadingIndicator').show();
    $('#favoritesResults').empty();
    $('#emptyState').hide();

    $.getJSON('api/index.php?endpoint=Favorites&action=list', function(response) {
        $('#loadingIndicator').hide();
        
        if(response.ok && response.data.favorites && response.data.favorites.length > 0) {
            response.data.favorites.forEach(fav => {
                const encHref = encryptLink(fav.link);
                const encImage = encryptLink(fav.poster);
                const encTitle = encryptLink(fav.title);
                const safeTitle = fav.title.replace(/'/g, "&#39;").replace(/"/g, "&quot;");
                
                let html = `
                    <div class="col-6 col-md-3 col-lg-2 mb-4 position-relative">
                        <div class="movie-card w-100" onclick="navigateToEncrypted({v: 'More', href: '${encHref}', server: '${fav.server}', image: '${encImage}', title: '${encTitle}'})">
                            <img src="${fav.poster}" alt="${safeTitle}" onerror="this.src='https://via.placeholder.com/200x300?text=No+Image'">
                            <div class="mt-2 text-center small text-truncate">${safeTitle}</div>
                        </div>
                        <button class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2" style="z-index: 20;" onclick="removeFavorite('${fav.server}', '${fav.link}', this)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                `;
                $('#favoritesResults').append(html);
            });
        } else {
            $('#emptyState').show();
        }
    }).fail(function() {
        $('#loadingIndicator').hide();
        $('#favoritesResults').html('<p class="text-center text-danger">Failed to load favorites.</p>');
    });
}

function removeFavorite(server, link, btnElement) {
    event.stopPropagation(); // Prevent card click
    if(confirm('Are you sure you want to remove this from your favorites?')) {
        $.post('api/index.php?endpoint=Favorites&action=remove', {
            server: server,
            link: link
        }, function(response) {
            if(response.ok) {
                // Remove the element from DOM
                $(btnElement).closest('.col-6').fadeOut(300, function() { 
                    $(this).remove(); 
                    if($('#favoritesResults').children().length === 0) {
                        $('#emptyState').show();
                    }
                });
                showToast('Removed from favorites');
            } else {
                alert(response.error.msg || 'Failed to remove');
            }
        }, 'json');
    }
}

function showToast(message) {
    // Simple toast implementation
    const toast = $(`<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100">
        <div class="toast show align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>`);
    $('body').append(toast);
    setTimeout(() => { toast.fadeOut(500, () => toast.remove()); }, 3000);
}
</script>