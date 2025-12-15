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
    
    $.post('api/index.php?endpoint=User&action=login', {username: username, password: password}, function(response) {
        const res = JSON.parse(response);
        if(res.ok) {
            // Save token to cookie or local storage
            document.cookie = "tryq8flix2=" + res.data.keepalive + "; path=/";
            window.location.href = '?v=Home';
        } else {
            alert(res.data.msg);
        }
    });
});
</script>
