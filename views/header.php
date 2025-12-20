<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TryQ8Flix</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #141414; color: #fff; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; overflow-x: hidden; }
        .navbar { background-color: transparent; transition: background-color 0.5s; padding: 15px 4%; }
        .navbar.scrolled { background-color: #141414; }
        .navbar-brand { color: #e50914; font-weight: bold; font-size: 1.8rem; }
        .nav-link { color: #e5e5e5; font-size: 0.9rem; margin-left: 15px; }
        .nav-link:hover { color: #b3b3b3; }
        
        .hero { height: 85vh; position: relative; background-size: cover; background-position: center; display: flex; align-items: center; }
        .hero-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(to top, #141414 0%, transparent 60%, rgba(0,0,0,0.7) 100%); }
        .hero-content { position: relative; z-index: 2; max-width: 600px; padding-left: 4%; margin-top: 100px; }
        .hero-title { font-size: 3.5rem; font-weight: bold; margin-bottom: 1rem; text-shadow: 2px 2px 4px rgba(0,0,0,0.5); }
        .hero-desc { font-size: 1.2rem; margin-bottom: 2rem; text-shadow: 1px 1px 2px rgba(0,0,0,0.5); }
        
        .section-title { font-size: 1.4rem; font-weight: bold; margin: 2rem 0 1rem 4%; color: #e5e5e5; }
        .movie-row { display: flex; overflow-x: auto; padding: 0 4% 2rem 4%; scrollbar-width: none; }
        .movie-row::-webkit-scrollbar { display: none; }
        .movie-card { flex: 0 0 auto; width: 200px; height: 300px; margin-right: 10px; transition: transform 0.3s; cursor: pointer; position: relative; }
        .movie-card:hover { transform: scale(1.1); z-index: 10; }
        .movie-card img { width: 100%; height: 100%; object-fit: cover; border-radius: 4px; }
        .movie-card .title-overlay { position: absolute; bottom: 0; left: 0; width: 100%; display: flex; align-items: center; justify-content: center; background: linear-gradient(to top, rgba(0,0,0,0.95) 0%, rgba(0,0,0,0.7) 50%, transparent 100%); border-radius: 0 0 4px 4px; padding: 10px; text-align: center; font-weight: 600; font-size: 13px; line-height: 1.3; color: white; text-shadow: 0 2px 4px rgba(0,0,0,0.8); }
        .movie-card.img-error .title-overlay { display: flex; height: 100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 4px; }
        .movie-card.img-error img { display: none; }
        
        .btn-netflix { background-color: #e50914; color: white; border: none; padding: 0.5rem 1.5rem; font-weight: bold; border-radius: 4px; }
        .btn-netflix:hover { background-color: #f40612; color: white; }
        .btn-secondary-netflix { background-color: rgba(109, 109, 110, 0.7); color: white; border: none; padding: 0.5rem 1.5rem; font-weight: bold; border-radius: 4px; margin-left: 10px; }
        .btn-secondary-netflix:hover { background-color: rgba(109, 109, 110, 0.4); color: white; }

        .login-container { height: 100vh; background-image: url('https://assets.nflxext.com/ffe/siteui/vlv3/f841d4c7-10e1-40af-bcae-07a3f8dc141a/f6d7434e-d6de-4185-a6d4-c77a2d08737b/US-en-20220502-popsignuptwoweeks-perspective_alpha_website_medium.jpg'); background-size: cover; display: flex; align-items: center; justify-content: center; }
        .login-card { background-color: rgba(0,0,0,0.75); padding: 60px; border-radius: 4px; width: 450px; }
        .form-control { background-color: #333; border: none; color: white; height: 50px; }
        .form-control::placeholder { color: #8c8c8c; }
        .form-control:focus { background-color: #454545; color: white; box-shadow: none; }
        
        .modal-content { background-color: #181818; color: white; }
        .modal-header { border-bottom: none; }
        .modal-footer { border-top: none; }
        
        .live-match-card { position: relative; background-color: #2f2f2f; border-radius: 8px; margin-bottom: 15px; padding: 15px; display: flex; align-items: center; justify-content: space-between; }
        .team-logo { width: 50px; height: 50px; object-fit: contain; }
        .match-time { font-weight: bold; color: #e50914; }

        /* Scroll Buttons for Desktop */
        .row-wrapper { position: relative; }
        .scroll-btn {
            position: absolute;
            top: 0;
            bottom: 0;
            width: 4%;
            background: rgba(20, 20, 20, 0.5);
            border: none;
            color: white;
            z-index: 20;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            opacity: 0;
            transition: opacity 0.3s;
            height: 100%; /* Ensure it covers the full height of the row */
        }
        .row-wrapper:hover .scroll-btn { opacity: 1; }
        .scroll-left { left: 0; border-top-right-radius: 4px; border-bottom-right-radius: 4px; }
        .scroll-right { right: 0; border-top-left-radius: 4px; border-bottom-left-radius: 4px; }
        .scroll-btn:hover { background: rgba(20, 20, 20, 0.8); color: #e50914; }
        .scroll-btn i { font-size: 2rem; }

        /* Page Transition Overlay */
        .page-transition-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #141414;
            z-index: 9999;
            opacity: 1;
            transition: opacity 0.4s ease-in-out;
            pointer-events: none;
        }
        .page-transition-overlay.hidden {
            opacity: 0;
        }
    </style>
    <script>
        // Simple obfuscation/encryption for links
        function encryptLink(str) {
            if (!str) return '';
            try {
                // Base64 encode, then reverse string
                return btoa(encodeURIComponent(str)).split('').reverse().join('');
            } catch (e) {
                console.error('Encryption failed', e);
                return str;
            }
        }

        function decryptLink(str) {
            if (!str) return '';
            try {
                // Reverse string, then Base64 decode
                return decodeURIComponent(atob(str.split('').reverse().join('')));
            } catch (e) {
                // Fallback if string wasn't encrypted or format changed
                return str;
            }
        }

        // Page Transition Logic
        // Handle back/forward cache (bfcache) restoration
        window.addEventListener('pageshow', (event) => {
            const overlay = document.querySelector('.page-transition-overlay');
            if (overlay) {
                // Force hide overlay when page is shown (including back button)
                overlay.classList.add('hidden');
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            const overlay = document.querySelector('.page-transition-overlay');
            if (overlay) {
                // Fade out overlay on load
                setTimeout(() => {
                    overlay.classList.add('hidden');
                }, 50); // Small delay to ensure render
            }

            // Intercept clicks for smooth transition
            document.addEventListener('click', (e) => {
                const link = e.target.closest('a');
                // Check if it's a link, not opening in new tab, and is internal
                if (link && link.href && !link.target && link.href.startsWith(window.location.origin)) {
                    e.preventDefault();
                    if (overlay) {
                        overlay.classList.remove('hidden');
                        setTimeout(() => {
                            window.location.href = link.href;
                        }, 400); // Wait for transition to finish
                    } else {
                        window.location.href = link.href;
                    }
                }
                
                // Also handle elements with onclick="window.location.href=..."
                // This is trickier as we can't easily intercept inline JS.
                // We'll rely on the fact that most of our navigation is via <a> or specific onclicks we can modify.
            });
            
            // Monkey patch window.location.href setters if possible, or just helper function
            window.navigateTo = function(url) {
                if (overlay) {
                    overlay.classList.remove('hidden');
                    setTimeout(() => {
                        window.location.href = url;
                    }, 400);
                } else {
                    window.location.href = url;
                }
            };

            window.navigateToEncrypted = function(params) {
                // Construct query string from params object
                const queryString = Object.keys(params).map(key => key + '=' + encodeURIComponent(params[key])).join('&');
                // Encrypt the query string
                const encrypted = encryptLink(queryString);
                // Navigate
                navigateTo('?q=' + encodeURIComponent(encrypted));
            };
        });
    </script>
</head>
<body>
<div class="page-transition-overlay"></div>
<nav class="navbar fixed-top">
  <div class="container-fluid d-flex justify-content-between align-items-center">
    <a class="navbar-brand" href="?v=Home">TRYQ8FLIX</a>
    <div class="d-flex align-items-center">
        <a class="nav-link text-white me-3" href="?v=LiveMatchesList"><i class="fas fa-futbol fa-lg"></i></a>
        <a class="nav-link text-white me-3" href="?v=Search"><i class="fas fa-search fa-lg"></i></a>
        <a class="nav-link text-white" href="?v=Settings"><i class="fas fa-cog fa-lg"></i></a>
    </div>
  </div>
</nav>
