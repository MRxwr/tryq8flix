<?php include __DIR__ . '/header.php'; ?>

<main class="page">
    <div class="page-head">
        <div>
            <div class="eyebrow">Preferences</div>
            <h1 class="page-title">Settings</h1>
            <p class="page-sub">Account, apps and everything else.</p>
        </div>
    </div>

    <div class="grid-2">
        <!-- Profile -->
        <section class="surface">
            <div class="surface__head"><i class="fa-solid fa-circle-user"></i> My profile</div>
            <div class="surface__body">
                <div class="profile-hero">
                    <img class="profile-avatar" id="profile-avatar" alt="Profile picture">
                    <h2 class="profile-name" id="profile-username">Loading…</h2>
                    <div>
                        <div class="pill-label">Email</div>
                        <div data-profile-email>—</div>
                    </div>
                    <div id="profile-email" class="hide"></div>
                    <span class="chip chip--brand"><i class="fa-solid fa-crown"></i> Premium · 4K HDR</span>
                </div>
                <div class="stack--sm" style="display:grid;">
                    <button class="btn btn--ghost btn--block" id="editProfileBtn"><i class="fa-solid fa-pen"></i> Edit profile</button>
                    <button class="btn btn--ghost btn--block" id="changePasswordBtn"><i class="fa-solid fa-key"></i> Change password</button>
                    <button class="btn btn--danger btn--block" id="deleteAccountBtn"><i class="fa-solid fa-trash-can"></i> Delete account</button>
                    <a class="btn btn--primary btn--block" href="?v=Logout" data-no-fade><i class="fa-solid fa-right-from-bracket"></i> Log out</a>
                </div>
            </div>
        </section>

        <div class="stack">
            <!-- Menu -->
            <section class="surface">
                <div class="surface__head"><i class="fa-solid fa-compass"></i> Explore</div>
                <div class="surface__body">
                    <div class="list">
                        <a class="list__item" href="?v=Home"><i class="fa-solid fa-house lead"></i><span class="grow">Home</span><i class="fa-solid fa-chevron-right chev"></i></a>
                        <a class="list__item" href="?v=Download"><i class="fa-solid fa-cloud-arrow-down lead"></i><span class="grow">Video downloader</span><i class="fa-solid fa-chevron-right chev"></i></a>
                        <a class="list__item" href="?v=Games"><i class="fa-solid fa-gamepad lead"></i><span class="grow">Games center</span><i class="fa-solid fa-chevron-right chev"></i></a>
                        <a class="list__item" href="?v=LiveMatchesList"><i class="fa-solid fa-futbol lead"></i><span class="grow">Live matches</span><i class="fa-solid fa-chevron-right chev"></i></a>
                        <a class="list__item" href="?v=Favorites"><i class="fa-solid fa-heart lead"></i><span class="grow">My favorites</span><i class="fa-solid fa-chevron-right chev"></i></a>
                        <a class="list__item" href="?v=History"><i class="fa-solid fa-clock-rotate-left lead"></i><span class="grow">Watch history</span><i class="fa-solid fa-chevron-right chev"></i></a>
                    </div>
                </div>
            </section>

            <!-- App downloads -->
            <section class="surface">
                <div class="surface__head"><i class="fa-solid fa-download"></i> Download the app</div>
                <div class="surface__body">
                    <div id="app-links" class="input-group"><div class="spinner"></div></div>
                </div>
            </section>

            <!-- Social -->
            <section class="surface">
                <div class="surface__head"><i class="fa-solid fa-share-nodes"></i> Follow us</div>
                <div class="surface__body">
                    <div id="social-links" class="input-group"><div class="spinner"></div></div>
                </div>
            </section>

            <!-- Ad-block help (OS specific) -->
            <section class="surface hide" id="ios-adblock-settings">
                <div class="surface__head"><i class="fa-brands fa-safari"></i> Block ads on Safari</div>
                <div class="surface__body">
                    <p class="small">For the best experience on iPhone &amp; iPad, install <strong>AdBlock Pro</strong>.</p>
                    <a class="btn btn--light btn--sm mb-2" target="_blank" rel="noopener"
                       href="https://apps.apple.com/us/app/adblock-pro-for-safari/id1018301773">
                        <i class="fa-brands fa-apple"></i> Get it on the App Store
                    </a>
                    <div class="faint small">Settings › Safari › Extensions › enable AdBlock Pro › turn on All Categories.</div>
                </div>
            </section>

            <section class="surface hide" id="android-adblock-settings">
                <div class="surface__head"><i class="fa-brands fa-firefox-browser"></i> Block ads on Android</div>
                <div class="surface__body">
                    <p class="small">Use <strong>Firefox</strong> with the <strong>uBlock Origin</strong> add-on.</p>
                    <a class="btn btn--light btn--sm mb-2" target="_blank" rel="noopener"
                       href="https://play.google.com/store/apps/details?id=org.mozilla.firefox">
                        <i class="fa-brands fa-google-play"></i> Install Firefox
                    </a>
                    <div class="faint small">Firefox › ⋮ Add-ons › find uBlock Origin › tap (+).</div>
                </div>
            </section>

            <!-- Information -->
            <section class="surface">
                <div class="surface__head"><i class="fa-solid fa-circle-info"></i> Information</div>
                <div class="surface__body">
                    <div class="list">
                        <a class="list__item" href="?v=About"><i class="fa-solid fa-angle-right lead"></i><span class="grow">About us</span><i class="fa-solid fa-chevron-right chev"></i></a>
                        <a class="list__item" href="?v=Terms"><i class="fa-solid fa-angle-right lead"></i><span class="grow">Terms of service</span><i class="fa-solid fa-chevron-right chev"></i></a>
                        <a class="list__item" href="?v=Policy"><i class="fa-solid fa-angle-right lead"></i><span class="grow">Privacy policy</span><i class="fa-solid fa-chevron-right chev"></i></a>
                    </div>
                </div>
            </section>
        </div>
    </div>
</main>

<!-- Account modals (shared with Profile) -->
<div class="overlay hide" id="editProfileModal">
    <div class="modal">
        <div class="modal__head">
            <h5>Edit profile</h5>
            <button class="modal__close" data-modal-close="editProfileModal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal__body">
            <form id="editProfileForm">
                <div id="ep-message"></div>
                <div class="field">
                    <label class="field__label" for="editEmail">Email address</label>
                    <input class="input" type="email" id="editEmail" required>
                </div>
                <div class="field">
                    <label class="field__label">Profile picture</label>
                    <div style="display:flex;align-items:center;gap:12px;">
                        <label for="editAvatar" class="btn btn--ghost btn--sm"><i class="fa-solid fa-cloud-arrow-up"></i> Choose image</label>
                        <span id="fileName" class="faint small">No file chosen</span>
                    </div>
                    <input type="file" id="editAvatar" accept="image/*" class="hide">
                </div>
                <button type="submit" class="btn btn--primary btn--block"><i class="fa-solid fa-floppy-disk"></i> Save changes</button>
            </form>
        </div>
    </div>
</div>

<div class="overlay hide" id="changePasswordModal">
    <div class="modal">
        <div class="modal__head">
            <h5>Change password</h5>
            <button class="modal__close" data-modal-close="changePasswordModal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal__body">
            <form id="changePasswordForm">
                <div id="cp-message"></div>
                <div class="field field--icon">
                    <label class="field__label" for="newPassword">New password</label>
                    <input class="input" type="password" id="newPassword" required>
                    <button type="button" class="peek" data-peek="newPassword" aria-label="Show password"><i class="fa-regular fa-eye"></i></button>
                </div>
                <div class="field field--icon">
                    <label class="field__label" for="confirmNewPassword">Confirm password</label>
                    <input class="input" type="password" id="confirmNewPassword" required>
                    <button type="button" class="peek" data-peek="confirmNewPassword" aria-label="Show password"><i class="fa-regular fa-eye"></i></button>
                </div>
                <button type="submit" class="btn btn--primary btn--block"><i class="fa-solid fa-key"></i> Update password</button>
            </form>
        </div>
    </div>
</div>

<div class="overlay hide" id="deleteAccountModal">
    <div class="modal">
        <div class="modal__head">
            <h5 style="color:var(--danger);">Delete account</h5>
            <button class="modal__close" data-modal-close="deleteAccountModal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal__body">
            <p class="mb-1">Are you sure? This cannot be undone.</p>
            <div id="da-message"></div>
        </div>
        <div class="modal__foot">
            <button class="btn btn--ghost" data-modal-close="deleteAccountModal">Cancel</button>
            <button class="btn btn--danger" id="confirmDeleteBtn">Delete my account</button>
        </div>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
<script src="assets/js/profile.js"></script>
<script>
(function () {
    Q8Profile.init();

    // OS-specific ad-block help
    if (/iPhone|iPad|iPod/.test(navigator.userAgent) && !window.MSStream) {
        document.getElementById("ios-adblock-settings").classList.remove("hide");
    } else if (/Android/.test(navigator.userAgent)) {
        document.getElementById("android-adblock-settings").classList.remove("hide");
    }

    // App download links
    $.getJSON(apiUrl("endpoint=Version&action=version"), function (response) {
        var el = document.getElementById("app-links");
        if (!response || !response.ok) { el.innerHTML = '<span class="faint small">No downloads available.</span>'; return; }
        var d = response.data, html = "";
        if (d.androidLink) html += '<a class="btn btn--ghost btn--sm" target="_blank" rel="noopener" href="' + Q8.escAttr(d.androidLink) + '"><i class="fa-brands fa-android"></i> Android</a>';
        if (d.iosLink)     html += '<a class="btn btn--ghost btn--sm" target="_blank" rel="noopener" href="' + Q8.escAttr(d.iosLink) + '"><i class="fa-brands fa-apple"></i> iOS</a>';
        if (d.windowsLink) html += '<a class="btn btn--ghost btn--sm" target="_blank" rel="noopener" href="' + Q8.escAttr(d.windowsLink) + '"><i class="fa-brands fa-windows"></i> Windows</a>';
        el.innerHTML = html || '<span class="faint small">No downloads available.</span>';
    });

    // Social links
    $.getJSON(apiUrl("endpoint=Settings"), function (response) {
        var el = document.getElementById("social-links");
        if (!response || !response.ok || !response.data || !response.data[0]) {
            el.innerHTML = '<span class="faint small">No social links available.</span>'; return;
        }
        var d = response.data[0], html = "";
        if (d.whatsapp)  html += '<a class="btn btn--ghost btn--sm" target="_blank" rel="noopener" href="https://wa.me/' + encodeURIComponent(d.whatsapp) + '"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a>';
        if (d.instagram) html += '<a class="btn btn--ghost btn--sm" target="_blank" rel="noopener" href="https://instagram.com/' + encodeURIComponent(d.instagram) + '"><i class="fa-brands fa-instagram"></i> Instagram</a>';
        if (d.twitter)   html += '<a class="btn btn--ghost btn--sm" target="_blank" rel="noopener" href="https://twitter.com/' + encodeURIComponent(d.twitter) + '"><i class="fa-brands fa-x-twitter"></i> X</a>';
        if (d.tiktok)    html += '<a class="btn btn--ghost btn--sm" target="_blank" rel="noopener" href="https://tiktok.com/@' + encodeURIComponent(d.tiktok) + '"><i class="fa-brands fa-tiktok"></i> TikTok</a>';
        el.innerHTML = html || '<span class="faint small">No social links available.</span>';
    });
})();
</script>
