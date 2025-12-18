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
    // iOS AdBlock Recommendation
    if (/iPhone|iPad/.test(navigator.userAgent) && !window.MSStream && !localStorage.getItem('adblock_recommendation_dismissed')) {
        // Check if AdBlock is active
        const testAd = document.createElement('div');
        testAd.innerHTML = '&nbsp;';
        testAd.className = 'adsbox ad-banner pub_300x250 pub_728x90 text-ad';
        testAd.style.position = 'absolute';
        testAd.style.top = '-1000px';
        document.body.appendChild(testAd);

        setTimeout(function() {
            // If height is 0, it was blocked/hidden by an extension
            const isBlocked = testAd.offsetHeight === 0; 
            document.body.removeChild(testAd);

            if (!isBlocked) {
                const alertHtml = `
                    <div class="alert alert-dark alert-dismissible fade show" role="alert" style="margin: 2rem 4% 0 4%; border: 1px solid #333; background-color: #222; color: #fff;">
                        <div class="d-flex align-items-start">
                            <div class="me-3">
                                <i class="fab fa-apple fa-2x"></i>
                            </div>
                            <div>
                                <h5 class="alert-heading mb-1">iOS User Recommendation</h5>
                                <p class="mb-2 small">For the best experience without ads, we recommend installing <strong>AdBlock Pro</strong>.</p>
                                <a href="https://apps.apple.com/us/app/adblock-pro-for-safari/id1018301773" target="_blank" class="btn btn-sm btn-light mb-2">
                                    <i class="fas fa-download"></i> Install from App Store
                                </a>
                                <div class="mt-2 p-2 rounded" style="background: rgba(255,255,255,0.1); font-size: 0.85rem;">
                                    <strong>Setup Instructions:</strong>
                                    <ol class="mb-0 ps-3">
                                        <li>Go to <strong>Settings</strong> > <strong>Safari</strong> > <strong>Extensions</strong></li>
                                        <li>Enable <strong>AdBlock Pro</strong></li>
                                        <li>Open the App and turn on "<strong>All Categories</strong>"</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close" onclick="localStorage.setItem('adblock_recommendation_dismissed', 'true')"></button>
                    </div>
                `;
                $('#content-rows').before(alertHtml);
            }
        }, 100);
    }

    // Fetch Main Data (Banners & Servers)
    $.getJSON('api/index.php?endpoint=Main', function(response) {
        if(response.ok) {
            const data = response.data;
            
            // 1. Setup Hero Section from Banners
            if(data.banners && data.banners.length > 0) {
                // Pick a random banner or the first one
                const banner = data.banners[Math.floor(Math.random() * data.banners.length)];
                $('#hero-section').css('background-image', 'url(' + banner.imageurl + ')');
                $('#hero-title').text(banner.title || 'Featured Content');
                $('#hero-desc').text('Watch the latest movies and TV shows on TryQ8Flix.');
                
                // Update Play/More Info buttons if needed based on banner data
                // For example, if banner has an endpoint/url, we could attach it to the button
                if(banner.url && banner.server) {
                     const encHref = encryptLink(banner.url);
                     const encImage = encryptLink(banner.imageurl);
                     const encTitle = encryptLink(banner.title);
                     $('.btn-netflix').attr('onclick', `navigateToEncrypted({v: 'More', href: '${encHref}', server: '${banner.server}', image: '${encImage}', title: '${encTitle}'})`);
                     $('.btn-secondary-netflix').attr('onclick', `navigateToEncrypted({v: 'More', href: '${encHref}', server: '${banner.server}', image: '${encImage}', title: '${encTitle}'})`);
                }
            } else {
                 $('#hero-title').text('Welcome to TryQ8Flix');
                 $('#hero-desc').text('Browse our collection of movies and TV shows.');
            }

            // 2. Setup Content Rows from Servers
            if(data.servers && data.servers.length > 0) {
                data.servers.forEach((server, index) => {
                    // Fetch content for each server
                    $.getJSON('api/index.php?endpoint=Home&action=view&page=1&server=' + server.id, function(serverRes) {
                        if(serverRes.ok && serverRes.data.shows && serverRes.data.shows.length > 0) {
                            const rowId = `row-${server.id}`;
                            let rowHtml = `
                                <div class="d-flex justify-content-between align-items-center" style="margin: 2rem 4% 1rem 4%;">
                                    <div class="section-title" style="margin: 0;">${server.name}</div>
                                    <span class="text-white small fw-bold" style="cursor:pointer;" onclick="navigateToEncrypted({v: 'Category', server: '${server.id}', title: '${server.name}'})">View More <i class="fas fa-chevron-right"></i></span>
                                </div>
                                <div class="row-wrapper">
                                    <button class="scroll-btn scroll-left d-none d-md-flex" onclick="scrollRow('${rowId}', -1)"><i class="fas fa-chevron-left"></i></button>
                                    <div class="movie-row" id="${rowId}">
                            `;
                            
                            serverRes.data.shows.forEach(show => {
                                const encHref = encryptLink(show.href);
                                const encImage = encryptLink(show.image);
                                const encTitle = encryptLink(show.title);
                                const safeTitle = show.title.replace(/'/g, "&#39;").replace(/"/g, "&quot;");
                                rowHtml += `
                                    <div class="movie-card" onclick="navigateToEncrypted({v: 'More', href: '${encHref}', server: '${server.id}', image: '${encImage}', title: '${encTitle}'})">
                                        <img src="${show.image}" alt="${safeTitle}" onerror="this.parentElement.classList.add('img-error')">
                                        <div class="title-overlay">${safeTitle}</div>
                                        <button class="btn btn-sm position-absolute top-0 end-0 m-2 fav-btn text-white" 
                                            data-server="${server.id}" data-link="${show.href}" data-poster="${show.image}"
                                            style="z-index: 20; background: rgba(0,0,0,0.5); border: none;" 
                                            onclick="toggleFavorite('${server.id}', '${show.href}', '${show.image}', '${safeTitle.replace(/'/g, "\\'")}', this)">
                                            <i class="far fa-heart"></i>
                                        </button>
                                    </div>
                                `;
                            });
                            
                            rowHtml += `</div>
                                    <button class="scroll-btn scroll-right d-none d-md-flex" onclick="scrollRow('${rowId}', 1)"><i class="fas fa-chevron-right"></i></button>
                                </div>`;
                            
                            // Append directly to container (First to load appears first)
                            $('#content-rows').append(rowHtml);
                        }
                    });
                });
            }
        }
    });
});

function scrollRow(elementId, direction) {
    const container = document.getElementById(elementId);
    const scrollAmount = container.clientWidth * 0.8; // Scroll 80% of the view width
    
    if (direction === 1) {
        container.scrollBy({ left: scrollAmount, behavior: 'smooth' });
    } else {
        container.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
    }
}

let userFavorites = new Set();

$(document).ready(function() {
    fetchUserFavorites();
});

function fetchUserFavorites() {
    $.getJSON('api/index.php?endpoint=Favorites&action=list', function(response) {
        if(response.ok && response.data.favorites) {
            response.data.favorites.forEach(fav => {
                // Store poster URL as unique identifier
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

// Observer to handle dynamically added elements
const observer = new MutationObserver(function(mutations) {
    mutations.forEach(function(mutation) {
        if (mutation.addedNodes.length) {
            updateFavoriteIcons();
        }
    });
});

observer.observe(document.body, { childList: true, subtree: true });

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
