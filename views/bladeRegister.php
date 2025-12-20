<?php include 'header.php'; ?>

<div class="login-container">
    <div class="login-card">
        <h2 class="mb-4 text-white fw-bold" style="text-shadow: 1px 1px 8px #000, 0 0 2px #e50914;">Sign Up</h2>
        <form id="registerForm">
            <div class="mb-3">
                <input type="text" class="form-control" id="username" placeholder="Username" required>
            </div>
            <div class="mb-3">
                <input type="email" class="form-control" id="email" placeholder="Email Address" required>
            </div>
            <div class="mb-3">
                <input type="password" class="form-control" id="password" placeholder="Password" required>
            </div>
            <div class="mb-3">
                <input type="password" class="form-control" id="confirmPassword" placeholder="Confirm Password" required>
            </div>
            <button type="submit" class="btn btn-netflix w-100 mt-3">Sign Up</button>
        </form>
        
        <div class="mt-5 text-white">
            Already have an account? <a href="?v=Login" class="text-white text-decoration-none">Sign in now</a>.
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
$('#registerForm').submit(function(e) {
    e.preventDefault();
    const username = $('#username').val();
    const email = $('#email').val();
    const password = $('#password').val();
    const confirmPassword = $('#confirmPassword').val();
    
    if(password !== confirmPassword) {
        alert("Passwords do not match!");
        return;
    }

    $.post('api/index.php?endpoint=User&action=register', {
        username: username, 
        email: email, 
        password: password, 
        confirmPassword: confirmPassword
    }, function(response) {
        const res = (typeof response === 'string') ? JSON.parse(response) : response;
        
        if(res.ok) {
            // Auto login or redirect to login
            // Based on apiUser.php, register returns keepalive token, so we can log them in directly
            document.cookie = "tryq8flix2=" + res.data.keepalive + "; path=/";
            navigateTo('?v=Home');
        } else {
            alert(res.data.msg || "Registration failed");
        }
    });
});
</script>
