<?php include __DIR__ . '/header.php'; ?>

<main class="auth">
    <div class="auth__card">
        <div class="eyebrow mb-2">Welcome back</div>
        <h1 class="auth__title">Sign in</h1>
        <p class="auth__sub">Continue watching where you left off.</p>

        <div id="login-message"></div>

        <form id="loginForm" novalidate>
            <div class="field">
                <label class="field__label" for="username">Username</label>
                <input class="input" type="text" id="username" autocomplete="username" placeholder="your username" required>
            </div>

            <div class="field field--icon">
                <label class="field__label" for="password">Password</label>
                <input class="input" type="password" id="password" autocomplete="current-password" placeholder="••••••••" required>
                <button type="button" class="peek" data-peek="password" aria-label="Show password"><i class="fa-regular fa-eye"></i></button>
            </div>

            <div class="auth__row">
                <label class="check"><input type="checkbox" id="remember"> Remember me</label>
                <a href="?v=Forget" class="small" style="color:#fff;font-weight:700;">Forgot password?</a>
            </div>

            <button type="submit" class="btn btn--primary btn--block">
                <i class="fa-solid fa-right-to-bracket"></i> Sign In
            </button>
        </form>

        <p class="auth__foot">New to TryQ8Flix? <a href="?v=Register">Create an account</a></p>
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

    var form = document.getElementById("loginForm");
    var msg = document.getElementById("login-message");

    form.addEventListener("submit", function (e) {
        e.preventDefault();
        var username = document.getElementById("username").value.trim();
        var password = document.getElementById("password").value;
        var remember = document.getElementById("remember").checked;
        var btn = form.querySelector('button[type="submit"]');
        var original = btn.innerHTML;

        if (!username || !password) {
            msg.innerHTML = '<div class="alert alert--err"><i class="fa-solid fa-circle-exclamation"></i> Enter your username and password.</div>';
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner" style="width:16px;height:16px;border-width:2px;"></span> Signing in...';

        $.post(apiUrl("endpoint=User&action=login"), { username: username, password: password }, function (response) {
            var res = typeof response === "string" ? JSON.parse(response) : response;
            if (res && res.ok) {
                setAuthCookie(res.data.keepalive, remember ? 30 : 0);
                navigateTo("?v=Home");
            } else {
                msg.innerHTML = '<div class="alert alert--err"><i class="fa-solid fa-circle-exclamation"></i> ' +
                    escapeHtml((res && res.data && res.data.msg) || "Login failed") + "</div>";
                btn.disabled = false;
                btn.innerHTML = original;
            }
        }).fail(function () {
            msg.innerHTML = '<div class="alert alert--err"><i class="fa-solid fa-wifi"></i> Network error. Please try again.</div>';
            btn.disabled = false;
            btn.innerHTML = original;
        });
    });
})();
</script>
