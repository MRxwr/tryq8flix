<?php include 'header.php'; ?>

<div class="login-container">
    <div class="login-card">
        <h2 class="mb-4 text-white fw-bold" style="text-shadow: 1px 1px 8px #000, 0 0 2px #e50914;">Forgot Password</h2>
        <p class="text-white">Enter your email address and we'll send you a link to reset your password.</p>
        <form id="forgetForm">
            <div id="message" class="mb-3"></div>
            <div class="mb-3">
                <input type="email" class="form-control" id="email" placeholder="name@example.com" required>
            </div>
            <button type="submit" class="btn btn-netflix w-100 mt-3">Email Me</button>
        </form>
        <div class="mt-3 text-center">
            <a href="?v=Login" class="text-decoration-none text-white">Back to Login</a>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
$('#forgetForm').submit(function(e) {
    e.preventDefault();
    const email = $('#email').val();
    const btn = $(this).find('button[type="submit"]');
    const originalText = btn.text();
    
    btn.prop('disabled', true).text('Sending...');
    $('#message').html('');

    $.post('api/index.php?endpoint=User&action=forget', {email: email}, function(response) {
        const res = (typeof response === 'string') ? JSON.parse(response) : response;
        
        if(res.ok) {
            $('#message').html('<div class="alert alert-success">' + res.data.msg + '</div>');
            $('#email').val('');
        } else {
            $('#message').html('<div class="alert alert-danger">' + (res.data.msg || 'An error occurred') + '</div>');
        }
    }).fail(function() {
        $('#message').html('<div class="alert alert-danger">Network error. Please try again.</div>');
    }).always(function() {
        btn.prop('disabled', false).text(originalText);
    });
});
</script>
