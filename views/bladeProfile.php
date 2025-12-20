<?php include 'header.php'; ?>

<div class="container" style="margin-top: 100px;">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card bg-dark text-white p-4">
                <div class="text-center mb-4">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/0/0b/Netflix-avatar.png" class="rounded-circle" width="100" alt="Profile">
                    <h3 class="mt-3" id="profile-username">Loading...</h3>
                </div>
                
                <div class="mb-3">
                    <label class="text-white-50 small text-uppercase fw-bold" style="font-size: 0.75rem;">Email</label>
                    <div class="fs-6" id="profile-email">...</div>
                </div>
                
                <div class="mb-3">
                    <label class="text-white-50 small text-uppercase fw-bold" style="font-size: 0.75rem;">Subscription</label>
                    <div class="fs-6">Premium Plan (4K HDR)</div>
                </div>
                
                <hr class="bg-secondary">
                
                <button class="btn btn-outline-light w-100 mb-3">Manage Profiles</button>
                <button id="editProfileBtn" class="btn btn-outline-light w-100 mb-3">Edit Profile</button>
                <button id="changePasswordBtn" class="btn btn-outline-light w-100 mb-3">Change Password</button>
                <button id="deleteAccountBtn" class="btn btn-outline-danger w-100">Delete Account</button>
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

<?php include 'footer.php'; ?>

<script>
$(document).ready(function() {
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

    // Open modal
    $('#changePasswordBtn').click(function() {
        var myModal = new bootstrap.Modal(document.getElementById('changePasswordModal'));
        myModal.show();
    });

    // Open Edit Profile Modal
    $('#editProfileBtn').click(function() {
        // Pre-fill email
        $('#editEmail').val($('#profile-email').text());
        // Reset file input
        $('#editAvatar').val('');
        $('#fileName').text('No file chosen');
        
        var myModal = new bootstrap.Modal(document.getElementById('editProfileModal'));
        myModal.show();
    });

    // File Input Change Handler
    $('#editAvatar').change(function() {
        var fileName = $(this).val().split('\\').pop();
        $('#fileName').text(fileName ? fileName : 'No file chosen');
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
});
</script>
