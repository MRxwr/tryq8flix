<?php include 'header.php'; ?>

<div class="container" style="margin-top: 100px;">
    <h2 class="section-title mb-4">Settings</h2>
    
    <div class="row justify-content-center">
        <div class="col-md-8">
            
            <!-- Profile Management -->
            <div class="card bg-dark text-white mb-4">
                <div class="card-header border-secondary">
                    <h5 class="mb-0"><i class="fas fa-user-circle me-2"></i>My Profile</h5>
                </div>
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/0/0b/Netflix-avatar.png" class="rounded-circle" width="100" alt="Profile">
                        <h3 class="mt-3" id="profile-username">Loading...</h3>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6 text-center text-md-start">
                             <div class="mb-3">
                                <label class="text-white-50 small text-uppercase fw-bold" style="font-size: 0.75rem;">Email</label>
                                <div class="fs-6" id="profile-email">...</div>
                            </div>
                        </div>
                        <div class="col-md-6 text-center text-md-start">
                            <div class="mb-3">
                                <label class="text-white-50 small text-uppercase fw-bold" style="font-size: 0.75rem;">Subscription</label>
                                <div class="fs-6">Premium Plan (4K HDR)</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-block text-center">
                        <button id="editProfileBtn" class="btn btn-outline-light mb-2"><i class="fas fa-edit me-2"></i>Edit Profile</button>
                        <button id="changePasswordBtn" class="btn btn-outline-light mb-2"><i class="fas fa-key me-2"></i>Change Password</button>
                        <button id="deleteAccountBtn" class="btn btn-outline-danger mb-2"><i class="fas fa-trash-alt me-2"></i>Delete Account</button>
                        <a href="?v=Logout" class="btn btn-danger mb-2"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
                    </div>
                </div>
            </div>

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

        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content bg-dark text-white">
      <div class="modal-header border-secondary">
        <h5 class="modal-title">Edit Profile</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="editProfileForm">
            <div id="ep-message" class="mb-3"></div>
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" class="form-control bg-secondary text-white border-0" id="editEmail" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Profile Picture</label>
                <div class="d-flex align-items-center p-3 border border-secondary rounded bg-secondary bg-opacity-10">
                    <label for="editAvatar" class="btn btn-netflix me-3">
                        <i class="fas fa-cloud-upload-alt me-2"></i>Choose Image
                    </label>
                    <span id="fileName" class="text-white-50 fst-italic text-truncate" style="max-width: 200px;">No file chosen</span>
                </div>
                <input type="file" class="d-none" id="editAvatar" accept="image/*">
                <div class="form-text text-muted mt-2">Allowed formats: JPG, PNG, GIF, WEBP</div>
            </div>
            <button type="submit" class="btn btn-netflix w-100">Save Changes</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content bg-dark text-white">
      <div class="modal-header border-secondary">
        <h5 class="modal-title">Change Password</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="changePasswordForm">
            <div id="cp-message" class="mb-3"></div>
            <div class="mb-3 position-relative">
                <label class="form-label">New Password</label>
                <input type="password" class="form-control bg-secondary text-white border-0" id="newPassword" required>
                <span class="position-absolute top-50 end-0 translate-middle-y me-3 toggle-password" style="cursor: pointer; color: #e5e5e5; margin-top: 10px;">
                    <i class="fa fa-eye"></i>
                </span>
            </div>
            <div class="mb-3 position-relative">
                <label class="form-label">Confirm Password</label>
                <input type="password" class="form-control bg-secondary text-white border-0" id="confirmNewPassword" required>
                <span class="position-absolute top-50 end-0 translate-middle-y me-3 toggle-password" style="cursor: pointer; color: #e5e5e5; margin-top: 10px;">
                    <i class="fa fa-eye"></i>
                </span>
            </div>
            <button type="submit" class="btn btn-netflix w-100">Update Password</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Delete Account Confirmation Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content bg-dark text-white">
      <div class="modal-header border-secondary">
        <h5 class="modal-title text-danger">Delete Account</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete your account? This action cannot be undone.</p>
        <p class="text-muted small">All your data, including watch history and favorites, will be permanently removed.</p>
        <div id="da-message" class="mb-3"></div>
      </div>
      <div class="modal-footer border-secondary">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" id="confirmDeleteBtn" class="btn btn-danger">Delete My Account</button>
      </div>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>

<script>
$(document).ready(function() {
    // --- Profile Logic ---
    
    // Fetch profile data from API
    $.getJSON('api/index.php?endpoint=User&action=profile', function(response) {
        if(response.ok) {
            const user = response.data;
            $('#profile-username').text(user.username);
            $('#profile-email').text(user.email);
            if(user.avatar) {
                $('img.rounded-circle').attr('src', user.avatar);
            }
        } else {
            // If token is invalid or expired, redirect to login
            navigateTo('?v=Login');
        }
    }).fail(function() {
        // Handle network errors
        $('#profile-username').text('Error loading profile');
    });

    // Open Edit Profile Modal
    $('#editProfileBtn').click(function() {
        // Pre-fill email
        $('#editEmail').val($('#profile-email').text());
        var myModal = new bootstrap.Modal(document.getElementById('editProfileModal'));
        myModal.show();
    });

    // Handle Edit Profile Submission
    $('#editProfileForm').submit(function(e) {
        e.preventDefault();
        const btn = $(this).find('button[type="submit"]');
        const originalText = btn.text();
        btn.prop('disabled', true).text('Saving...');
        $('#ep-message').html('');

        const formData = new FormData();
        formData.append('email', $('#editEmail').val());
        const avatarFile = $('#editAvatar')[0].files[0];
        if(avatarFile) {
            formData.append('avatar', avatarFile);
        }

        $.ajax({
            url: 'api/index.php?endpoint=User&action=profile&update=1',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                const res = (typeof response === 'string') ? JSON.parse(response) : response;
                if(res.ok) {
                    $('#ep-message').html('<div class="alert alert-success">' + res.data.msg + '</div>');
                    // Update UI
                    $('#profile-email').text($('#editEmail').val());
                    // Reload page to see new avatar or fetch profile again
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    $('#ep-message').html('<div class="alert alert-danger">' + res.data.msg + '</div>');
                    btn.prop('disabled', false).text(originalText);
                }
            },
            error: function() {
                $('#ep-message').html('<div class="alert alert-danger">Network error. Please try again.</div>');
                btn.prop('disabled', false).text(originalText);
            }
        });
    });

    // Open Change Password Modal
    $('#changePasswordBtn').click(function() {
        var myModal = new bootstrap.Modal(document.getElementById('changePasswordModal'));
        myModal.show();
    });

    // Toggle password visibility
    $(document).on('click', '.toggle-password', function() {
        const input = $(this).siblings('input');
        const icon = $(this).find('i');
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // Handle Change Password Submission
    $('#changePasswordForm').submit(function(e) {
        e.preventDefault();
        const password = $('#newPassword').val();
        const confirmPassword = $('#confirmNewPassword').val();
        
        if(password !== confirmPassword) {
            $('#cp-message').html('<div class="alert alert-danger">Passwords do not match!</div>');
            return;
        }

        $.post('api/index.php?endpoint=User&action=change', {
            password: password,
            confirmPassword: confirmPassword
        }, function(response) {
            const res = (typeof response === 'string') ? JSON.parse(response) : response;
            if(res.ok) {
                $('#cp-message').html('<div class="alert alert-success">' + res.data.msg + '</div>');
                $('#changePasswordForm')[0].reset();
                setTimeout(() => {
                    // Close modal properly
                    const modalEl = document.getElementById('changePasswordModal');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    modal.hide();
                    $('#cp-message').empty();
                }, 2000);
            } else {
                $('#cp-message').html('<div class="alert alert-danger">' + res.data.msg + '</div>');
            }
        });
    });

    // Open Delete Account Modal
    $('#deleteAccountBtn').click(function() {
        var myModal = new bootstrap.Modal(document.getElementById('deleteAccountModal'));
        myModal.show();
    });

    // Handle Account Deletion
    $('#confirmDeleteBtn').click(function() {
        const btn = $(this);
        btn.prop('disabled', true).text('Deleting...');
        
        $.post('api/index.php?endpoint=User&action=delete', function(response) {
            const res = (typeof response === 'string') ? JSON.parse(response) : response;
            if(res.ok) {
                $('#da-message').html('<div class="alert alert-success">' + res.data.msg + '</div>');
                setTimeout(() => {
                    // Clear cookie and redirect to login
                    document.cookie = "tryq8flix2=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
                    window.location.href = '?v=Login';
                }, 2000);
            } else {
                $('#da-message').html('<div class="alert alert-danger">' + res.data.msg + '</div>');
                btn.prop('disabled', false).text('Delete My Account');
            }
        }).fail(function() {
            $('#da-message').html('<div class="alert alert-danger">Network error. Please try again.</div>');
            btn.prop('disabled', false).text('Delete My Account');
        });
    });

    // --- Settings Logic ---
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
