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
                <button class="btn btn-outline-light w-100">Account Settings</button>
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
            window.location.href = '?v=Login';
        }
    }).fail(function() {
        // Handle network errors
        $('#profile-username').text('Error loading profile');
    });
});
</script>
