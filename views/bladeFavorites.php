<?php include 'header.php'; ?>

<div class="container" style="margin-top: 100px;">
    <h2 class="mb-4">My Favorites</h2>
    
    <div id="favoritesResults" class="row"></div>
    <div id="loadingIndicator" class="text-center mt-4">
        <div class="spinner-border text-danger"></div>
    </div>
    <div id="emptyState" class="text-center mt-5" style="display: none;">
        <i class="far fa-sad-tear fa-4x mb-3 text-white-50"></i>
        <h3>No favorites yet</h3>
        <p class="text-white-50">Start adding movies and TV shows to your list!</p>
        <a href="?v=Home" class="btn btn-netflix mt-3">Browse Content</a>
        
        <div id="suggestions" class="mt-5 text-start">
            <h4 class="mb-3">Suggested for you</h4>
            <div class="row" id="suggestionRows"></div>
        </div>
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
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-4 px-2 position-relative">
                        <div class="movie-card w-100 position-relative overflow-hidden rounded-3 shadow-sm" 
                             style="transition: all 0.3s ease; cursor: pointer;"
                             onclick="navigateToEncrypted({v: 'More', href: '${encHref}', server: '${fav.server}', image: '${encImage}', title: '${encTitle}'})"
                             onmouseover="this.style.transform='scale(1.05) translateY(-5px)'; this.style.boxShadow='0 8px 25px rgba(229,9,20,0.4)';"
                             onmouseout="this.style.transform='scale(1) translateY(0)'; this.style.boxShadow='';"> 
                            <div style="position: relative; padding-bottom: 150%; background: #1a1a1a;">
                                <img src="${fav.poster}" alt="${safeTitle}" 
                                     style="position: absolute; width: 100%; height: 100%; object-fit: cover; object-position: center;"
                                     onerror="this.src='https://via.placeholder.com/200x300?text=No+Image'">
                                <div class="position-absolute bottom-0 start-0 w-100 p-2" 
                                     style="background: linear-gradient(to top, rgba(0,0,0,0.95) 0%, rgba(0,0,0,0.7) 50%, transparent 100%);">
                                    <div class="text-white text-center small fw-semibold" style="text-shadow: 0 2px 4px rgba(0,0,0,0.8); line-height: 1.3;">${safeTitle}</div>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2" 
                                style="z-index: 20; background: rgba(229,9,20,0.9); border: none; backdrop-filter: blur(10px); border-radius: 50%; width: 36px; height: 36px; padding: 0; display: flex; align-items: center; justify-content: center;" 
                                onclick="removeFavorite('${fav.poster}', this)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                `;
                $('#favoritesResults').append(html);
            });
        } else {
            $('#emptyState').show();
            loadSuggestions();
        }
    }).fail(function() {
        $('#loadingIndicator').hide();
        $('#favoritesResults').html('<p class="text-center text-danger">Failed to load favorites.</p>');
    });
}

function loadSuggestions() {
    // Fetch banners as suggestions
    $.getJSON('api/index.php?endpoint=Main', function(response) {
        if(response.ok && response.data.banners) {
            const banners = response.data.banners.slice(0, 6); // Take first 6
            let html = '';
            banners.forEach(banner => {
                if(banner.url && banner.server) {
                    const encHref = encryptLink(banner.url);
                    const encImage = encryptLink(banner.imageurl);
                    const encTitle = encryptLink(banner.title);
                    const safeTitle = banner.title.replace(/'/g, "&#39;").replace(/"/g, "&quot;");
                    
                    html += `
                        <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-4 px-2">
                            <div class="movie-card w-100 position-relative overflow-hidden rounded-3 shadow-sm" 
                                 style="transition: all 0.3s ease; cursor: pointer;"
                                 onclick="navigateToEncrypted({v: 'More', href: '${encHref}', server: '${banner.server}', image: '${encImage}', title: '${encTitle}'})"
                                 onmouseover="this.style.transform='scale(1.05) translateY(-5px)'; this.style.boxShadow='0 8px 25px rgba(229,9,20,0.4)';"
                                 onmouseout="this.style.transform='scale(1) translateY(0)'; this.style.boxShadow='';"> 
                                <div style="position: relative; padding-bottom: 150%; background: #1a1a1a;">
                                    <img src="${banner.imageurl}" alt="${safeTitle}" 
                                         style="position: absolute; width: 100%; height: 100%; object-fit: cover; object-position: center;"
                                         onerror="this.src='https://via.placeholder.com/200x300?text=No+Image'">
                                    <div class="position-absolute bottom-0 start-0 w-100 p-2" 
                                         style="background: linear-gradient(to top, rgba(0,0,0,0.95) 0%, rgba(0,0,0,0.7) 50%, transparent 100%);">
                                        <div class="text-white text-center small fw-semibold" style="text-shadow: 0 2px 4px rgba(0,0,0,0.8); line-height: 1.3;">${safeTitle}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                }
            });
            $('#suggestionRows').html(html);
        }
    });
}

function removeFavorite(poster, btnElement) {
    event.stopPropagation(); // Prevent card click
    if(confirm('Are you sure you want to remove this from your favorites?')) {
        $.post('api/index.php?endpoint=Favorites&action=remove', {
            poster: poster
        }, function(response) {
            if(response.ok) {
                // Remove the element from DOM
                $(btnElement).closest('.col-6').fadeOut(300, function() { 
                    $(this).remove(); 
                    if($('#favoritesResults').children().length === 0) {
                        $('#emptyState').show();
                        loadSuggestions();
                    }
                });
                showToast('Removed from favorites');
            } else {
                alert(response.error.msg || 'Failed to remove');
            }
        }, 'json').fail(function() {
            alert('Network error: Failed to remove from favorites');
        });
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