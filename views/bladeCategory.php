<?php include 'header.php'; ?>

<div class="container" style="margin-top: 100px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="section-title m-0" id="categoryTitle">Category</h2>
    </div>
    
    <div id="categoryResults" class="row">
        <div class="text-center w-100"><div class="spinner-border text-danger"></div></div>
    </div>

    <div class="text-center mt-4 mb-5" id="loading-indicator" style="display:none;">
        <div class="spinner-border text-danger" role="status"></div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
let currentPage = 1;
let currentServer = 1;
let isLoading = false;
let hasMore = true;

$(document).ready(function() {
    const urlParams = new URLSearchParams(window.location.search);
    let title;
    
    if (urlParams.has('q')) {
        const decryptedQ = decryptLink(urlParams.get('q'));
        const params = new URLSearchParams(decryptedQ);
        currentServer = params.get('server');
        title = params.get('title');
    } else {
        currentServer = urlParams.get('server');
        title = urlParams.get('title');
    }
    
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
    fetchUserFavorites();
});

$(window).scroll(function() {
    if($(window).scrollTop() + $(window).height() > $(document).height() - 200) {
        if(!isLoading && hasMore) {
            currentPage++;
            loadContent(currentPage);
        }
    }
});

function loadContent(page) {
    isLoading = true;
    if(page > 1) $('#loading-indicator').show();
    
    // If it's the first page, show spinner in the main area
    if(page === 1) {
        $('#categoryResults').html('<div class="text-center w-100"><div class="spinner-border text-danger"></div></div>');
    }

    $.getJSON(`api/index.php?endpoint=Home&action=view&server=${currentServer}&page=${page}`, function(response) {
        if(page === 1) $('#categoryResults').empty();
        
        if(response.ok && response.data.shows && response.data.shows.length > 0) {
            response.data.shows.forEach(show => {
                const encHref = encryptLink(show.href);
                const encImage = encryptLink(show.image);
                const encTitle = encryptLink(show.title);
                const safeTitle = show.title.replace(/'/g, "&#39;").replace(/"/g, "&quot;");
                let html = `
                    <div class="col-6 col-md-3 col-lg-2 mb-4">
                        <div class="movie-card w-100" onclick="navigateToEncrypted({v: 'More', href: '${encHref}', server: '${currentServer}', image: '${encImage}', title: '${encTitle}'})">
                            <img src="${show.image}" alt="${safeTitle}" onerror="this.src='https://via.placeholder.com/200x300?text=No+Image'">
                            <div class="mt-2 text-center small text-truncate">${safeTitle}</div>
                            <button class="btn btn-sm position-absolute top-0 end-0 m-2 fav-btn text-white" 
                                data-server="${currentServer}" data-link="${show.href}"
                                style="z-index: 20; background: rgba(0,0,0,0.5); border: none;" 
                                onclick="toggleFavorite('${currentServer}', '${show.href}', '${show.image}', '${safeTitle.replace(/'/g, "\\'")}', this)">
                                <i class="far fa-heart"></i>
                            </button>
                        </div>
                    </div>
                `;
                $('#categoryResults').append(html);
            });
            updateFavoriteIcons();

            
        } else {
            hasMore = false;
            if(page === 1) {
                $('#categoryResults').html('<p class="text-center w-100">No content found.</p>');
            }
        }
        isLoading = false;
        $('#loading-indicator').hide();
    }).fail(function() {
        if(page === 1) {
            $('#categoryResults').html('<p class="text-center w-100 text-danger">Failed to load content.</p>');
        }
        isLoading = false;
        $('#loading-indicator').hide();
    });
}

let userFavorites = new Set();

function fetchUserFavorites() {
    $.getJSON('api/index.php?endpoint=Favorites&action=list', function(response) {
        if(response.ok && response.data.favorites) {
            response.data.favorites.forEach(fav => {
                userFavorites.add(fav.server + '|' + fav.link);
            });
            updateFavoriteIcons();
        }
    });
}

function updateFavoriteIcons() {
    $('.fav-btn').each(function() {
        const btn = $(this);
        const server = btn.data('server');
        const link = btn.data('link');
        if(userFavorites.has(server + '|' + link)) {
            btn.find('i').removeClass('far').addClass('fas').addClass('text-danger');
        } else {
            btn.find('i').removeClass('fas').removeClass('text-danger').addClass('far');
        }
    });
}

function toggleFavorite(server, link, poster, title, btnElement) {
    event.stopPropagation();
    const btn = $(btnElement);
    const isFav = btn.find('i').hasClass('fas');
    
    if(isFav) {
        if(confirm('Remove from favorites?')) {
            $.post('api/index.php?endpoint=Favorites&action=remove', {server: server, link: link}, function(res) {
                if(res.ok) {
                    userFavorites.delete(server + '|' + link);
                    updateFavoriteIcons();
                    showToast('Removed from favorites');
                }
            }, 'json');
        }
    } else {
        if(confirm('Add to favorites?')) {
            $.post('api/index.php?endpoint=Favorites&action=add', {server: server, link: link, poster: poster, title: title}, function(res) {
                if(res.ok) {
                    userFavorites.add(server + '|' + link);
                    updateFavoriteIcons();
                    showToast('Added to favorites');
                } else {
                    alert(res.error.msg);
                }
            }, 'json');
        }
    }
}

function showToast(message) {
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
