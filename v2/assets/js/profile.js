/* ============================================================
   TryQ8Flix v2 — account module
   Used by both ?v=Profile and ?v=Settings.
   ============================================================ */
(function (window, $) {
    "use strict";

    var DEFAULT_AVATAR = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='120'%3E%3Cdefs%3E%3ClinearGradient id='g' x1='0' y1='0' x2='1' y2='1'%3E%3Cstop offset='0' stop-color='%23e50914'/%3E%3Cstop offset='1' stop-color='%23ff8a3d'/%3E%3C/linearGradient%3E%3C/defs%3E%3Crect width='120' height='120' rx='30' fill='url(%23g)'/%3E%3C/svg%3E";

    // Element lookup helper. Deliberately NOT named $ so it cannot shadow jQuery.
    function byId(id) { return document.getElementById(id); }

    function openModal(id) {
        var el = byId(id);
        if (!el) return;
        el.classList.remove("hide");
        document.body.style.overflow = "hidden";
    }
    function closeModal(id) {
        var el = byId(id);
        if (!el) return;
        el.classList.add("hide");
        document.body.style.overflow = "";
    }
    function bindModalDismiss() {
        document.querySelectorAll("[data-modal-close]").forEach(function (btn) {
            btn.addEventListener("click", function () { closeModal(btn.getAttribute("data-modal-close")); });
        });
        document.querySelectorAll(".overlay").forEach(function (ov) {
            ov.addEventListener("click", function (e) {
                if (e.target === ov) { ov.classList.add("hide"); document.body.style.overflow = ""; }
            });
        });
    }

    function setMessage(id, type, text) {
        var el = byId(id);
        if (el) el.innerHTML = '<div class="alert alert--' + type + '">' +
            '<i class="fa-solid ' + (type === "ok" ? "fa-circle-check" : "fa-circle-exclamation") + '"></i> ' +
            window.escapeHtml(text) + "</div>";
    }

    function applyProfile(user) {
        var avatar = byId("profile-avatar");
        if (avatar) avatar.src = user.avatar ? (window.ROOT_BASE || "../") + user.avatar : DEFAULT_AVATAR;
        var name = byId("profile-username");
        if (name) name.textContent = user.username || "—";
        var hidden = byId("profile-email");
        if (hidden) hidden.textContent = user.email || "";
        document.querySelectorAll("[data-profile-email]").forEach(function (el) {
            el.textContent = user.email || "—";
        });
    }

    function init() {
        if (!byId("profile-avatar") && !byId("editProfileForm")) return;

        // Fetch the profile shown in the header card.
        function loadProfile() {
            return $.getJSON(window.apiUrl("endpoint=User&action=profile"), function (response) {
                if (response && response.ok) applyProfile(response.data);
                else window.navigateTo("?v=Login");
            }).fail(function () {
                var name = byId("profile-username");
                if (name) name.textContent = "Error loading profile";
            });
        }
        loadProfile();

        // Pulling the page down re-reads the account record the card shows. It
        // opts in from the card itself rather than from init(): this module is
        // shared, so a page without the card has nothing of its own to refresh.
        if (byId("profile-avatar") && window.Q8 && window.Q8.onRefresh) {
            window.Q8.onRefresh(function () {
                window.Q8.clearApiCache("endpoint=User");
                return loadProfile();
            });
        }

        // Open modals.
        var editBtn = byId("editProfileBtn");
        if (editBtn) editBtn.addEventListener("click", function () {
            var email = byId("editEmail");
            if (email) email.value = (byId("profile-email") && byId("profile-email").textContent) || "";
            var file = byId("editAvatar");
            if (file) file.value = "";
            var fn = byId("fileName");
            if (fn) fn.textContent = "No file chosen";
            openModal("editProfileModal");
        });

        var passBtn = byId("changePasswordBtn");
        if (passBtn) passBtn.addEventListener("click", function () { openModal("changePasswordModal"); });

        var delBtn = byId("deleteAccountBtn");
        if (delBtn) delBtn.addEventListener("click", function () { openModal("deleteAccountModal"); });

        var avatarInput = byId("editAvatar");
        if (avatarInput) avatarInput.addEventListener("change", function () {
            var fn = byId("fileName");
            if (fn) fn.textContent = (this.value.split("\\").pop() || "No file chosen");
        });

        // Toggle password visibility.
        document.querySelectorAll("[data-peek]").forEach(function (btn) {
            btn.addEventListener("click", function () {
                var input = byId(btn.getAttribute("data-peek"));
                if (!input) return;
                var show = input.type === "password";
                input.type = show ? "text" : "password";
                btn.innerHTML = show ? '<i class="fa-regular fa-eye-slash"></i>' : '<i class="fa-regular fa-eye"></i>';
            });
        });

        // Edit profile.
        var editForm = byId("editProfileForm");
        if (editForm) editForm.addEventListener("submit", function (e) {
            e.preventDefault();
            var btn = editForm.querySelector('button[type="submit"]');
            var original = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner" style="width:16px;height:16px;border-width:2px;"></span> Saving…';

            var fd = new FormData();
            fd.append("email", byId("editEmail").value);
            var file = byId("editAvatar");
            if (file && file.files[0]) fd.append("avatar", file.files[0]);

            $.ajax({
                url: window.apiUrl("endpoint=User&action=profile&update=1"),
                type: "POST", data: fd, processData: false, contentType: false
            }).done(function (response) {
                var res = typeof response === "string" ? JSON.parse(response) : response;
                if (res && res.ok) {
                    setMessage("ep-message", "ok", res.data.msg);
                    var hidden = byId("profile-email");
                    if (hidden) hidden.textContent = byId("editEmail").value;
                    window.showToast("Profile updated", "fa-user-check");
                    setTimeout(function () { location.reload(); }, 1200);
                } else {
                    setMessage("ep-message", "err", (res && res.data && res.data.msg) || "Could not save changes");
                    btn.disabled = false; btn.innerHTML = original;
                }
            }).fail(function () {
                setMessage("ep-message", "err", "Network error. Please try again.");
                btn.disabled = false; btn.innerHTML = original;
            });
        });

        // Change password.
        var passForm = byId("changePasswordForm");
        if (passForm) passForm.addEventListener("submit", function (e) {
            e.preventDefault();
            var password = byId("newPassword").value;
            var confirm = byId("confirmNewPassword").value;
            if (password !== confirm) { setMessage("cp-message", "err", "Passwords do not match."); return; }
            $.post(window.apiUrl("endpoint=User&action=change"), {
                password: password, confirmPassword: confirm
            }, function (response) {
                var res = typeof response === "string" ? JSON.parse(response) : response;
                if (res && res.ok) {
                    setMessage("cp-message", "ok", res.data.msg);
                    passForm.reset();
                    window.showToast("Password changed", "fa-key");
                    setTimeout(function () { closeModal("changePasswordModal"); }, 1400);
                } else {
                    setMessage("cp-message", "err", (res && res.data && res.data.msg) || "Could not change password");
                }
            });
        });

        // Delete account.
        var confirmDelete = byId("confirmDeleteBtn");
        if (confirmDelete) confirmDelete.addEventListener("click", function () {
            var btn = this;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner" style="width:16px;height:16px;border-width:2px;"></span> Deleting…';
            $.post(window.apiUrl("endpoint=User&action=delete"), function (response) {
                var res = typeof response === "string" ? JSON.parse(response) : response;
                if (res && res.ok) {
                    setMessage("da-message", "ok", res.data.msg);
                    setTimeout(function () {
                        window.clearAuthCookie();
                        window.location.href = "?v=Login";
                    }, 1600);
                } else {
                    setMessage("da-message", "err", (res && res.data && res.data.msg) || "Could not delete account");
                    btn.disabled = false; btn.textContent = "Delete my account";
                }
            }).fail(function () {
                setMessage("da-message", "err", "Network error. Please try again.");
                btn.disabled = false; btn.textContent = "Delete my account";
            });
        });

        bindModalDismiss();
    }

    window.Q8Profile = { init: init, openModal: openModal, closeModal: closeModal, DEFAULT_AVATAR: DEFAULT_AVATAR };
})(window, jQuery);
