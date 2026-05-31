<?php include 'header.php'; ?>

<div id="hero-section" class="hero" style="background-image: url('https://image.tmdb.org/t/p/original/9yBVqNruk6Ykr9zD297HQNAmUe7.jpg'); min-height: 80vh; display: flex; align-items: flex-end; padding-bottom: 60px;">
    <div class="hero-overlay"></div>
    <div class="hero-content w-100">
        <h1 class="hero-title fw-bold mb-3" id="hero-title" style="font-size: 4rem; text-shadow: 0 4px 15px rgba(0,0,0,0.8); line-height: 1.1;">Loading...</h1>
        <p class="hero-desc mb-4" id="hero-desc" style="max-width: 600px; font-size: 1.1rem; text-shadow: 0 2px 8px rgba(0,0,0,0.8); opacity: 0.9;">Please wait while we fetch the latest content for you.</p>
        <div class="d-flex gap-3">
            <button class="btn btn-netflix px-4 py-2 fw-bold"><i class="fas fa-play me-2"></i> Play</button>
            <button class="btn btn-secondary-netflix px-4 py-2 fw-bold"><i class="fas fa-info-circle me-2"></i> More Info</button>
        </div>
    </div>
</div>

<div id="content-rows">
    <div id="continue-watching-section"></div>
    <div id="favorites-section"></div>
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

        // Android Adblock Recommendation
        if (/Android/.test(navigator.userAgent) && !localStorage.getItem('android_adblock_dismissed')) {
            const alertHtml = `
            <div class="alert alert-dark alert-dismissible fade show" role="alert" style="margin: 2rem 4% 0 4%; border: 1px solid #333; background-color: #222; color: #fff;">
                <div class="d-flex align-items-start">
                    <div class="me-3">
                        <i class="fab fa-android fa-2x text-success"></i>
                    </div>
                    <div>
                        <h5 class="alert-heading mb-1">Android User Recommendation</h5>
                        <p class="mb-2 small">For the best ad-free experience, we recommend using <strong>Firefox</strong> with the <strong>uBlock Origin</strong> extension.</p>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="https://play.google.com/store/apps/details?id=org.mozilla.firefox" target="_blank" class="btn btn-sm btn-light">
                                <i class="fab fa-firefox-browser me-1"></i> Install Firefox
                            </a>
                        </div>
                        <div class="mt-2 p-2 rounded" style="background: rgba(255,255,255,0.1); font-size: 0.85rem;">
                            <strong>Setup Instructions:</strong>
                            <ol class="mb-0 ps-3">
                                <li>Open Firefox on your Android</li>
                                <li>Tap the <strong>three dots (⋮)</strong> > <strong>Add-ons</strong></li>
                                <li>Find and click <strong>(+)</strong> next to <strong>uBlock Origin</strong></li>
                            </ol>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close" onclick="localStorage.setItem('android_adblock_dismissed', 'true')"></button>
            </div>
        `;
            $('#content-rows').before(alertHtml);
        }

        // Fetch Continue Watching (History)
        $.getJSON('api/index.php?endpoint=History&action=list', function(response) {
            if (response.ok && response.data.history && response.data.history.length > 0) {
                const shows = response.data.history.slice(0, 20);
                const rowId = 'row-history';
                let rowHtml = `
                <div class="d-flex justify-content-between align-items-center" style="margin: 2rem 4% 1rem 4%;">
                    <div class="section-title" style="margin: 0;">Continue Watching</div>
                    <a href="?v=History" class="text-white small fw-bold text-decoration-none">View More <i class="fas fa-chevron-right"></i></a>
                </div>
                <div class="row-wrapper">
                    <button class="scroll-btn scroll-left d-none d-md-flex" onclick="scrollRow('${rowId}', -1)"><i class="fas fa-chevron-left"></i></button>
                    <div class="movie-row" id="${rowId}">
            `;

                shows.forEach(show => {
                    const encHref = encryptLink(show.link);
                    const encImage = encryptLink(show.poster);
                    const encTitle = encryptLink(show.title);
                    const safeTitle = show.title.replace(/'/g, "&#39;").replace(/"/g, "&quot;");

                    rowHtml += `
                    <div class="movie-card position-relative overflow-hidden rounded-3 shadow-sm" 
                         tabindex="0"
                         style="transition: all 0.3s ease; cursor: pointer;"
                         onclick="navigateToEncrypted({v: 'More', href: '${encHref}', server: '${show.server}', image: '${encImage}', title: '${encTitle}'})"
                         onmouseover="this.focus();"
                         onmouseout="this.blur();">
                        <img src="${show.poster}" alt="${safeTitle}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.parentElement.classList.add('img-error')">
                        <div class="title-overlay position-absolute bottom-0 start-0 w-100 p-2" style="background: linear-gradient(to top, rgba(0,0,0,0.95) 0%, rgba(0,0,0,0.7) 50%, transparent 100%); line-height: 1.3;">${safeTitle}</div>
                        <button class="btn btn-sm position-absolute top-0 end-0 m-2 fav-btn text-white" 
                            data-server="${show.server}" data-link="${show.link}" data-poster="${show.poster}"
                            style="z-index: 20; background: rgba(0,0,0,0.7); border: none; backdrop-filter: blur(10px); border-radius: 50%; width: 36px; height: 36px; padding: 0; display: flex; align-items: center; justify-content: center;" 
                            onclick="toggleFavorite('${show.server}', '${show.link}', '${show.poster}', '${safeTitle.replace(/'/g, "\\'")}', this)">
                            <i class="far fa-heart"></i>
                        </button>
                    </div>
                `;
                });

                rowHtml += `</div>
                    <button class="scroll-btn scroll-right d-none d-md-flex" onclick="scrollRow('${rowId}', 1)"><i class="fas fa-chevron-right"></i></button>
                </div>`;

                $('#continue-watching-section').html(rowHtml);
            }
        });

        // Fetch Favorites
        $.getJSON('api/index.php?endpoint=Favorites&action=list', function(response) {
            if (response.ok && response.data.favorites && response.data.favorites.length > 0) {
                const shows = response.data.favorites.slice(0, 20);
                const rowId = 'row-favorites';
                let rowHtml = `
                <div class="d-flex justify-content-between align-items-center" style="margin: 2rem 4% 1rem 4%;">
                    <div class="section-title" style="margin: 0;">My Favorites</div>
                    <a href="?v=Favorites" class="text-white small fw-bold text-decoration-none">View More <i class="fas fa-chevron-right"></i></a>
                </div>
                <div class="row-wrapper">
                    <button class="scroll-btn scroll-left d-none d-md-flex" onclick="scrollRow('${rowId}', -1)"><i class="fas fa-chevron-left"></i></button>
                    <div class="movie-row" id="${rowId}">
            `;

                shows.forEach(show => {
                    const encHref = encryptLink(show.link);
                    const encImage = encryptLink(show.poster);
                    const encTitle = encryptLink(show.title);
                    const safeTitle = show.title.replace(/'/g, "&#39;").replace(/"/g, "&quot;");

                    rowHtml += `
                    <div class="movie-card position-relative overflow-hidden rounded-3 shadow-sm" 
                         tabindex="0"
                         style="transition: all 0.3s ease; cursor: pointer;"
                         onclick="navigateToEncrypted({v: 'More', href: '${encHref}', server: '${show.server}', image: '${encImage}', title: '${encTitle}'})"
                         onmouseover="this.focus();"
                         onmouseout="this.blur();">
                        <img src="${show.poster}" alt="${safeTitle}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.parentElement.classList.add('img-error')">
                        <div class="title-overlay position-absolute bottom-0 start-0 w-100 p-2" style="background: linear-gradient(to top, rgba(0,0,0,0.95) 0%, rgba(0,0,0,0.7) 50%, transparent 100%); line-height: 1.3;">${safeTitle}</div>
                        <button class="btn btn-sm position-absolute top-0 end-0 m-2 fav-btn text-white" 
                            data-server="${show.server}" data-link="${show.link}" data-poster="${show.poster}"
                            style="z-index: 20; background: rgba(0,0,0,0.7); border: none; backdrop-filter: blur(10px); border-radius: 50%; width: 36px; height: 36px; padding: 0; display: flex; align-items: center; justify-content: center;" 
                            onclick="toggleFavorite('${show.server}', '${show.link}', '${show.poster}', '${safeTitle.replace(/'/g, "\\'")}', this)">
                            <i class="far fa-heart"></i>
                        </button>
                    </div>
                `;
                });

                rowHtml += `</div>
                    <button class="scroll-btn scroll-right d-none d-md-flex" onclick="scrollRow('${rowId}', 1)"><i class="fas fa-chevron-right"></i></button>
                </div>`;

                $('#favorites-section').html(rowHtml);
            }
        });

        // Whitelist of buttons for D-pad
        $('.btn, .nav-link').attr('tabindex', '0');

        // Fetch Main Data (Servers)
        $.getJSON('api/index.php?endpoint=Main', function(response) {
            if (response.ok) {
                const data = response.data;
                
                // Function to set random hero from TVDB servers
                const setRandomHero = (shows, serverId) => {
                    if (!shows || shows.length === 0) return;
                    const show = shows[Math.floor(Math.random() * shows.length)];
                    const backdrop = show.backdrop || show.image;
                    
                    $('#hero-section').css('background-image', 'url(' + backdrop + ')');
                    $('#hero-title').text(show.title);
                    
                    let infoHtml = '';
                    if (show.description) {
                        infoHtml += `<div class="d-flex align-items-center gap-3 mb-3">
                                        <span class="badge rounded-pill px-3 py-2" style="background: rgba(229, 9, 20, 0.85); font-weight: 600; letter-spacing: 0.5px;">${show.description}</span>
                                        <span class="text-white-50 small"><i class="fas fa-closed-captioning me-1"></i> HD / Ultra 4K</span>
                                     </div>`;
                    }
                    infoHtml += `<p class="mt-2 text-light" style="max-width: 600px; font-size: 1.1rem; text-shadow: 0 2px 8px rgba(0,0,0,0.8); opacity: 0.9;">Watch ${show.title} on TryQ8Flix. High quality streaming available now.</p>`;
                    $('#hero-desc').html(infoHtml);

                    const encHref = encryptLink(show.href);
                    const encImage = encryptLink(backdrop);
                    const encTitle = encryptLink(show.title);
                    
                    $('.btn-netflix').attr('onclick', `navigateToEncrypted({v: 'More', href: '${encHref}', server: '${serverId}', image: '${encImage}', title: '${encTitle}'})`);
                    $('.btn-secondary-netflix').attr('onclick', `navigateToEncrypted({v: 'More', href: '${encHref}', server: '${serverId}', image: '${encImage}', title: '${encTitle}'})`);
                };

                // 2. Setup Content Rows from Servers
                if (data.servers && data.servers.length > 0) {
                    let heroSet = false;
                    data.servers.forEach((server, index) => {
                        // Fetch content for each server
                        $.getJSON('api/index.php?endpoint=Home&action=view&page=1&server=' + server.id, function(serverRes) {
                            if (serverRes.ok && serverRes.data.shows && serverRes.data.shows.length > 0) {
                                // Set Random Hero from TVDB servers (13 or 14)
                                if (!heroSet && (server.id == "13" || server.id == "14")) {
                                    setRandomHero(serverRes.data.shows, server.id);
                                    heroSet = true;
                                }

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
                                    const imageForNextPage = show.backdrop || show.image;
                                    const encImage = encryptLink(imageForNextPage);
                                    const encTitle = encryptLink(show.title);
                                    const safeTitle = show.title.replace(/'/g, "&#39;").replace(/"/g, "&quot;");
                                    rowHtml += `
                                    <div class="movie-card position-relative overflow-hidden rounded-3 shadow-sm" 
                                         tabindex="0"
                                         style="transition: all 0.3s ease; cursor: pointer;"
                                         onclick="navigateToEncrypted({v: 'More', href: '${encHref}', server: '${server.id}', image: '${encImage}', title: '${encTitle}'})"
                                         onmouseover="this.focus();"
                                         onmouseout="this.blur();">
                                        <img src="${show.image}" alt="${safeTitle}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.parentElement.classList.add('img-error')">
                                        <div class="title-overlay position-absolute bottom-0 start-0 w-100 p-2" style="background: linear-gradient(to top, rgba(0,0,0,0.95) 0%, rgba(0,0,0,0.7) 50%, transparent 100%); line-height: 1.3;">${safeTitle}</div>
                                        <button class="btn btn-sm position-absolute top-0 end-0 m-2 fav-btn text-white" 
                                            data-server="${server.id}" data-link="${show.href}" data-poster="${show.image}"
                                            style="z-index: 20; background: rgba(0,0,0,0.7); border: none; backdrop-filter: blur(10px); border-radius: 50%; width: 36px; height: 36px; padding: 0; display: flex; align-items: center; justify-content: center;" 
                                            onclick="toggleFavorite('${server.id}', '${show.href}', '${show.image}', '${safeTitle.replace(/'/g, "\\'")}', this)">
                                            <i class="far fa-heart"></i>
                                        </button>
                                    </div>
                                `;
                                });

                                rowHtml += `</div>
                                    <button class="scroll-btn scroll-right d-none d-md-flex" onclick="scrollRow('${rowId}', 1)"><i class="fas fa-chevron-right"></i></button>
                                </div>`;

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
            container.scrollBy({
                left: scrollAmount,
                behavior: 'smooth'
            });
        } else {
            container.scrollBy({
                left: -scrollAmount,
                behavior: 'smooth'
            });
        }
    }

    let userFavorites = new Set();

    $(document).ready(function() {
        fetchUserFavorites();
    });

    function fetchUserFavorites() {
        $.getJSON('api/index.php?endpoint=Favorites&action=list', function(response) {
            if (response.ok && response.data.favorites) {
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

            if (poster && userFavorites.has(poster)) {
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

    observer.observe(document.body, {
        childList: true,
        subtree: true
    });

    function toggleFavorite(server, link, poster, title, btnElement) {
        event.stopPropagation();
        const btn = $(btnElement);
        const isFav = btn.find('i').hasClass('fas');

        if (isFav) {
            if (confirm('Remove from favorites?')) {
                $.post('api/index.php?endpoint=Favorites&action=remove', {
                    poster: poster
                }, function(res) {
                    if (res.ok) {
                        userFavorites.delete(poster);
                        updateFavoriteIcons();
                        showToast('Removed from favorites');
                    }
                }, 'json');
            }
        } else {
            if (confirm('Add to favorites?')) {
                $.post('api/index.php?endpoint=Favorites&action=add', {
                    server: server,
                    link: link,
                    poster: poster,
                    title: title
                }, function(res) {
                    if (res.ok) {
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
        setTimeout(() => {
            toast.fadeOut(500, () => toast.remove());
        }, 3000);
    }
</script>