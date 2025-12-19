<?php include 'header.php'; ?>

<div class="container" style="margin-top: 100px;">
    <h2 class="section-title mb-4">Settings</h2>
    
    <div class="row justify-content-center">
        <div class="col-md-8">
            
            <!-- Navigation -->
            <div class="card bg-dark text-white mb-4">
                <div class="card-header border-secondary">
                    <h5 class="mb-0"><i class="fas fa-compass me-2"></i>Menu</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="?v=Home" class="list-group-item list-group-item-action bg-dark text-white border-secondary">
                        <i class="fas fa-home me-2"></i> Home
                    </a>
                    <a href="?v=LiveMatchesList" class="list-group-item list-group-item-action bg-dark text-white border-secondary">
                        <i class="fas fa-futbol me-2"></i> Live Matches
                    </a>
                    <a href="?v=History" class="list-group-item list-group-item-action bg-dark text-white border-secondary">
                        <i class="fas fa-history me-2"></i> Watch History
                    </a>
                </div>
            </div>

            <!-- App Downloads -->
            <div class="card bg-dark text-white mb-4">
                <div class="card-header border-secondary">
                    <h5 class="mb-0"><i class="fas fa-download me-2"></i>Download Apps</h5>
                </div>
                <div class="card-body">
                    <div id="app-links" class="d-grid gap-2 d-md-block text-center">
                        <div class="spinner-border text-danger" role="status"></div>
                    </div>
                </div>
            </div>

            <!-- Block Ads on Safari (iOS Only) -->
            <div class="card bg-dark text-white mb-4" id="ios-adblock-settings" style="display:none;">
                <div class="card-header border-secondary">
                    <h5 class="mb-0"><i class="fab fa-safari me-2"></i>Block Ads on Safari</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-start">
                        <div class="me-3">
                            <i class="fab fa-apple fa-3x text-muted"></i>
                        </div>
                        <div>
                            <p class="mb-2">For the best experience on iPhone & iPad, we recommend installing <strong>AdBlock Pro</strong>.</p>
                            <a href="https://apps.apple.com/us/app/adblock-pro-for-safari/id1018301773" target="_blank" class="btn btn-light btn-sm mb-3">
                                <i class="fas fa-download me-1"></i> Install from App Store
                            </a>
                            <div class="p-3 rounded" style="background: rgba(255,255,255,0.05);">
                                <h6 class="fw-bold mb-2">Setup Instructions:</h6>
                                <ol class="mb-0 ps-3 small text-white">
                                    <li>Go to <strong>Settings</strong> > <strong>Safari</strong> > <strong>Extensions</strong></li>
                                    <li>Enable <strong>AdBlock Pro</strong></li>
                                    <li>Open the App and turn on "<strong>All Categories</strong>"</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CMS Pages -->
            <div class="card bg-dark text-white mb-4">
                <div class="card-header border-secondary">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Information</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="?v=About" class="list-group-item list-group-item-action bg-dark text-white border-secondary">
                        <i class="fas fa-angle-right float-end"></i> About Us
                    </a>
                    <a href="?v=Terms" class="list-group-item list-group-item-action bg-dark text-white border-secondary">
                        <i class="fas fa-angle-right float-end"></i> Terms of Service
                    </a>
                    <a href="?v=Policy" class="list-group-item list-group-item-action bg-dark text-white border-secondary">
                        <i class="fas fa-angle-right float-end"></i> Privacy Policy
                    </a>
                </div>
            </div>

            <!-- Social Media -->
            <div class="card bg-dark text-white mb-4">
                <div class="card-header border-secondary">
                    <h5 class="mb-0"><i class="fas fa-share-alt me-2"></i>Follow Us</h5>
                </div>
                <div class="card-body text-center">
                    <div id="social-links">
                        <div class="spinner-border text-danger" role="status"></div>
                    </div>
                </div>
            </div>

            <!-- Account Actions -->
            <div class="card bg-dark text-white mb-4">
                <div class="card-header border-secondary">
                    <h5 class="mb-0"><i class="fas fa-user-cog me-2"></i>Account</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="?v=Profile" class="list-group-item list-group-item-action bg-dark text-white border-secondary">
                        <i class="fas fa-user me-2"></i> My Profile
                    </a>
                    <a href="?v=Logout" class="list-group-item list-group-item-action bg-danger text-white border-secondary text-center mt-2 rounded">
                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
$(document).ready(function() {
    // Show iOS AdBlock settings only on iOS devices
    if (/iPhone|iPad/.test(navigator.userAgent) && !window.MSStream) {
        $('#ios-adblock-settings').show();
    }

    // Fetch App Version Links
    $.getJSON('api/index.php?endpoint=Version&action=version', function(response) {
        if(response.ok) {
            const data = response.data;
            let html = '';
            if(data.androidLink) {
                html += `<a href="${data.androidLink}" class="btn btn-success me-2 mb-2"><i class="fab fa-android me-2"></i>Android</a>`;
            }
            if(data.iosLink) {
                html += `<a href="${data.iosLink}" class="btn btn-light me-2 mb-2"><i class="fab fa-apple me-2"></i>iOS</a>`;
            }
            if(data.windowsLink) {
                html += `<a href="${data.windowsLink}" class="btn btn-primary me-2 mb-2"><i class="fab fa-windows me-2"></i>Windows</a>`;
            }
            $('#app-links').html(html || '<p class="text-muted">No downloads available.</p>');
        } else {
            $('#app-links').html('<p class="text-danger">Failed to load links.</p>');
        }
    });

    // Fetch Settings (Social Media)
    $.getJSON('api/index.php?endpoint=Settings', function(response) {
        if(response.ok) {
            const data = response.data[0]; // Assuming data is an array of settings
            let html = '';
            
            if(data.whatsapp) html += `<a href="https://wa.me/${data.whatsapp}" target="_blank" class="btn btn-outline-success btn-lg me-3 rounded-circle"><i class="fab fa-whatsapp"></i></a>`;
            if(data.instagram) html += `<a href="https://instagram.com/${data.instagram}" target="_blank" class="btn btn-outline-danger btn-lg me-3 rounded-circle"><i class="fab fa-instagram"></i></a>`;
            if(data.twitter) html += `<a href="https://twitter.com/${data.twitter}" target="_blank" class="btn btn-outline-info btn-lg me-3 rounded-circle"><i class="fab fa-twitter"></i></a>`;
            if(data.tiktok) html += `<a href="https://tiktok.com/@${data.tiktok}" target="_blank" class="btn btn-outline-light btn-lg me-3 rounded-circle"><i class="fab fa-tiktok"></i></a>`;
            
            $('#social-links').html(html || '<p class="text-muted">No social links available.</p>');
        } else {
            $('#social-links').html('<p class="text-danger">Failed to load social links.</p>');
        }
    });
});
</script>
