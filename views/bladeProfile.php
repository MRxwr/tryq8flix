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
// Fetch profile data if we had an endpoint, for now mock or use cookie data
const cookie = document.cookie.split('; ').find(row => row.startsWith('tryq8flix2='));
if(cookie) {
    // In a real app we would call an API to get user details using the token
    $('#profile-username').text('User'); 
    $('#profile-email').text('user@example.com');
} else {
    window.location.href = '?v=Login';
}
</script>
