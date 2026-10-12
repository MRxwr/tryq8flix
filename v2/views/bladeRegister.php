<?php include __DIR__ . '/header.php'; ?>

<main class="auth">
    <div class="auth__card">
        <div class="eyebrow mb-2">Join us</div>
        <h1 class="auth__title">Create account</h1>
        <p class="auth__sub">Free access to movies, series and live matches.</p>

        <div id="register-message"></div>

        <form id="registerForm" novalidate>
            <div class="field">
                <label class="field__label" for="username">Username</label>
                <input class="input" type="text" id="username" autocomplete="username" placeholder="choose a username" required>
            </div>

            <div class="field">
                <label class="field__label" for="email">Email</label>
                <input class="input" type="email" id="email" autocomplete="email" placeholder="name@example.com" required>
            </div>

            <div class="field field--icon">
                <label class="field__label" for="password">Password</label>
                <input class="input" type="password" id="password" autocomplete="new-password" placeholder="••••••••" required>
                <button type="button" class="peek" data-peek="password" aria-label="Show password"><i class="fa-regular fa-eye"></i></button>
            </div>

            <div class="field field--icon">
                <label class="field__label" for="confirmPassword">Confirm password</label>
                <input class="input" type="password" id="confirmPassword" autocomplete="new-password" placeholder="••••••••" required>
                <button type="button" class="peek" data-peek="confirmPassword" aria-label="Show password"><i class="fa-regular fa-eye"></i></button>
            </div>

            <button type="submit" class="btn btn--primary btn--block">
                <i class="fa-solid fa-user-plus"></i> Sign Up
            </button>
        </form>

        <p class="auth__foot">Already have an account? <a href="?v=Login">Sign in</a></p>
    </div>
</main>

<?php include __DIR__ . '/footer.php'; ?>

<script>
(function () {
    document.querySelectorAll(".peek").forEach(function (btn) {
        btn.addEventListener("click", function () {
            var input = document.getElementById(btn.getAttribute("data-peek"));
            if (!input) return;
            var show = input.type === "password";
            input.type = show ? "text" : "password";
            btn.innerHTML = show ? '<i class="fa-regular fa-eye-slash"></i>' : '<i class="fa-regular fa-eye"></i>';
        });
    });

    var form = document.getElementById("registerForm");
    var msg = document.getElementById("register-message");

    function fail(text) {
        msg.innerHTML = '<div class="alert alert--err"><i class="fa-solid fa-circle-exclamation"></i> ' + escapeHtml(text) + "</div>";
    }

    form.addEventListener("submit", function (e) {
        e.preventDefault();
        var username = document.getElementById("username").value.trim();
        var email = document.getElementById("email").value.trim();
        var password = document.getElementById("password").value;
        var confirmPassword = document.getElementById("confirmPassword").value;
        var btn = form.querySelector('button[type="submit"]');
        var original = btn.innerHTML;

        msg.innerHTML = "";
        if (password !== confirmPassword) { fail("Passwords do not match."); return; }

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner" style="width:16px;height:16px;border-width:2px;"></span> Creating...';

        $.post(apiUrl("endpoint=User&action=register"), {
            username: username, email: email, password: password, confirmPassword: confirmPassword
        }, function (response) {
            var res = typeof response === "string" ? JSON.parse(response) : response;
            if (res && res.ok) {
                if (res.data && res.data.keepalive) {
                    setAuthCookie(res.data.keepalive, 0);
                    navigateTo("?v=Home");
                } else {
                    navigateTo("?v=Login");
                }
            } else {
                fail((res && res.data && res.data.msg) || "Registration failed");
                btn.disabled = false;
                btn.innerHTML = original;
            }
        }).fail(function () {
            fail("Network error. Please try again.");
            btn.disabled = false;
            btn.innerHTML = original;
        });
    });
})();
</script>
