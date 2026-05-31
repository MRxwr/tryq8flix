<?php include 'header.php'; ?>

<div class="login-container">
    <div class="login-card">
        <h2 class="mb-4 text-white fw-bold" style="text-shadow: 1px 1px 8px #000, 0 0 2px #e50914;">Sign In</h2>
        <form id="loginForm">
            <div class="mb-3">
                <input type="text" class="form-control" id="username" placeholder="username" tabindex="0" required>
            </div>
            <div class="mb-3 position-relative">
                <input type="password" class="form-control" id="password" placeholder="Password" tabindex="0" required>
                <span class="position-absolute top-50 end-0 translate-middle-y me-3 toggle-password" style="cursor: pointer; color: #8c8c8c;">
                    <i class="fa fa-eye"></i>
                </span>
            </div>
            <button type="submit" class="btn btn-netflix w-100 mt-3" tabindex="0">Sign In</button>
            
            <div class="d-flex justify-content-between mt-3 small">
                <div>
                    <input type="checkbox" id="remember" tabindex="0"> <label for="remember" class="text-white" style="text-shadow: 1px 1px 6px #000;">Remember me</label>
                </div>
                <a href="?v=Forget" class="text-decoration-none text-white" tabindex="0" style="text-shadow: 1px 1px 6px #000;">Need help?</a>
            </div>
        </form>
        
        <div class="mt-5 text-white">
            New to TryQ8Flix? <a href="?v=Register" class="text-white text-decoration-none" tabindex="0">Sign up now</a>.
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
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
            navigateTo('?v=Home');
        } else {
            alert(res.data.msg || "Login failed");
        }
    });
});
</script>
