<?php include 'header.php'; ?>

<div class="login-container">
    <div class="login-card">
        <h2 class="mb-4">Sign In</h2>
        <form id="loginForm">
            <div class="mb-3">
                <input type="text" class="form-control" id="username" placeholder="Email or phone number" required>
            </div>
            <div class="mb-3">
                <input type="password" class="form-control" id="password" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-netflix w-100 mt-3">Sign In</button>
            
            <div class="d-flex justify-content-between mt-3 text-muted small">
                <div>
                    <input type="checkbox" id="remember"> <label for="remember">Remember me</label>
                </div>
                <a href="?v=Forget" class="text-decoration-none text-muted">Need help?</a>
            </div>
        </form>
        
        <div class="mt-5 text-muted">
            New to TryQ8Flix? <a href="#" class="text-white text-decoration-none">Sign up now</a>.
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
$('#loginForm').submit(function(e) {
    e.preventDefault();
    const username = $('#username').val();
    const password = $('#password').val();
    const remember = $('#remember').is(':checked');
    
    $.post('api/index.php?endpoint=User&action=login', {username: username, password: password}, function(response) {
        // jQuery might auto-parse JSON if the server sends correct headers
        const res = (typeof response === 'string') ? JSON.parse(response) : response;
        
        if(res.ok) {
            // Set cookie expiration
            let expires = "";
            if (remember) {
                const date = new Date();
                date.setTime(date.getTime() + (30 * 24 * 60 * 60 * 1000)); // 30 days
                expires = "; expires=" + date.toUTCString();
            }
            
            document.cookie = "tryq8flix2=" + res.data.keepalive + expires + "; path=/";
            window.location.href = '?v=Home';
        } else {
            alert(res.data.msg || "Login failed");
        }
    });
});
</script>
