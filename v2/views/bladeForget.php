<?php include __DIR__ . '/header.php'; ?>

<main class="auth">
    <div class="auth__card">
        <div class="eyebrow mb-2">Account recovery</div>
        <h1 class="auth__title">Forgot password</h1>
        <p class="auth__sub">Enter your email and we'll send you a new password.</p>

        <div id="forget-message"></div>

        <form id="forgetForm" novalidate>
            <div class="field">
                <label class="field__label" for="email">Email</label>
                <input class="input" type="email" id="email" autocomplete="email" placeholder="name@example.com" required>
            </div>

            <button type="submit" class="btn btn--primary btn--block">
                <i class="fa-solid fa-paper-plane"></i> Email me a password
            </button>
        </form>

        <p class="auth__foot"><a href="?v=Login"><i class="fa-solid fa-arrow-left"></i> Back to sign in</a></p>
    </div>
</main>

<?php include __DIR__ . '/footer.php'; ?>

<script>
(function () {
    var form = document.getElementById("forgetForm");
    var msg = document.getElementById("forget-message");

    form.addEventListener("submit", function (e) {
        e.preventDefault();
        var email = document.getElementById("email").value.trim();
        var btn = form.querySelector('button[type="submit"]');
        var original = btn.innerHTML;

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner" style="width:16px;height:16px;border-width:2px;"></span> Sending...';
        msg.innerHTML = "";

        $.post(apiUrl("endpoint=User&action=forget"), { email: email }, function (response) {
            var res = typeof response === "string" ? JSON.parse(response) : response;
            if (res && res.ok) {
                msg.innerHTML = '<div class="alert alert--ok"><i class="fa-solid fa-circle-check"></i> ' +
                    escapeHtml(res.data.msg) + "</div>";
                document.getElementById("email").value = "";
            } else {
                msg.innerHTML = '<div class="alert alert--err"><i class="fa-solid fa-circle-exclamation"></i> ' +
                    escapeHtml((res && res.data && res.data.msg) || "Something went wrong") + "</div>";
            }
            btn.disabled = false;
            btn.innerHTML = original;
        }).fail(function () {
            msg.innerHTML = '<div class="alert alert--err"><i class="fa-solid fa-wifi"></i> Network error. Please try again.</div>';
            btn.disabled = false;
            btn.innerHTML = original;
        });
    });
})();
</script>
