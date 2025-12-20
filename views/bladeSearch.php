<?php include 'header.php'; ?>

<div class="container" style="margin-top: 100px;">
    <div class="row justify-content-center mb-5">
        <div class="col-md-10 col-lg-8">
            <div class="input-group input-group-lg shadow">
                <span class="input-group-text bg-dark border-secondary text-white-50"><i class="fas fa-server"></i></span>
                <select class="form-select bg-dark text-white border-secondary focus-ring-none" id="serverSelect" style="max-width: 140px; border-left: none;">
                    <option value="1" selected>Wecima</option>
                </select>
                <input type="text" class="form-control bg-dark text-white border-secondary" id="searchInput" placeholder="What do you want to watch?">
                <button class="btn btn-netflix px-4" id="searchBtn"><i class="fas fa-search"></i></button>
            </div>
        </div>
    </div>
    
    <div id="initialMessage" class="text-center py-5">
        <div class="mb-4">
            <i class="fas fa-search fa-6x text-secondary opacity-25"></i>
        </div>
        <h3 class="text-white-50 fw-light">Find Movies & TV Shows</h3>
        <p class="text-white-50 small">Enter a title above to start searching across our servers.</p>
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
        $('#initialMessage').hide();
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
                const encHref = encryptLink(show.href);
                const encImage = encryptLink(show.image);
                const encTitle = encryptLink(show.title);
                const safeTitle = show.title.replace(/'/g, "&#39;").replace(/"/g, "&quot;");
                let html = `
                    <div class="col-6 col-md-3 col-lg-2 mb-4">
                        <div class="movie-card w-100" onclick="navigateToEncrypted({v: 'More', href: '${encHref}', server: '${currentServerId}', image: '${encImage}', title: '${encTitle}'})">
                            <img src="${show.image}" alt="${safeTitle}" onerror="this.src='https://via.placeholder.com/200x300?text=No+Image'">
                            <div class="mt-2 text-center small text-truncate">${safeTitle}</div>
                            <button class="btn btn-sm position-absolute top-0 end-0 m-2 fav-btn text-white" 
                                data-server="${currentServerId}" data-link="${show.href}" data-poster="${show.image}"
                                style="z-index: 20; background: rgba(0,0,0,0.5); border: none;" 
                                onclick="toggleFavorite('${currentServerId}', '${show.href}', '${show.image}', '${safeTitle.replace(/'/g, "\\'")}', this)">
                                <i class="far fa-heart"></i>
                            </button>
                        </div>
                    </div>
                `;
                $('#searchResults').append(html);
            });
            updateFavoriteIcons();
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

let userFavorites = new Set();

$(document).ready(function() {
    fetchUserFavorites();
});

function fetchUserFavorites() {
    $.getJSON('api/index.php?endpoint=Favorites&action=list', function(response) {
        if(response.ok && response.data.favorites) {
            response.data.favorites.forEach(fav => {
                userFavorites.add(fav.poster);
            });
            updateFavoriteIcons();
        }
    });
}

function updateFavoriteIcons() {
    $('.fav-btn').each(function() {
        const btn = $(this);
        const poster = btn.data('poster');
        if(poster && userFavorites.has(poster)) {
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
            $.post('api/index.php?endpoint=Favorites&action=remove', {poster: poster}, function(res) {
                if(res.ok) {
                    userFavorites.delete(poster);
                    updateFavoriteIcons();
                    showToast('Removed from favorites');
                }
            }, 'json');
        }
    } else {
        if(confirm('Add to favorites?')) {
            $.post('api/index.php?endpoint=Favorites&action=add', {server: server, link: link, poster: poster, title: title}, function(res) {
                if(res.ok) {
                    userFavorites.add(poster);
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
