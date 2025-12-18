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
                                data-server="${currentServerId}" data-link="${show.href}"
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
