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
                    <label class="text-muted">Email</label>
                    <div class="fs-5" id="profile-email">...</div>
                </div>
                
                <div class="mb-3">
                    <label class="text-muted">Subscription</label>
                    <div class="fs-5">Premium Plan (4K HDR)</div>
                </div>
                
                <hr class="bg-secondary">
                
                <button class="btn btn-outline-light w-100 mb-3">Manage Profiles</button>
                <button id="changePasswordBtn" class="btn btn-outline-light w-100">Change Password</button>
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
