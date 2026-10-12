<?php include __DIR__ . '/header.php'; ?>

<main class="page">
    <div class="page-head">
        <div>
            <div class="eyebrow">Account</div>
            <h1 class="page-title">Profile</h1>
        </div>
    </div>

    <div class="grid-2">
        <section class="surface">
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
            </div>
        </section>

        <section class="surface">
            <div class="surface__head"><i class="fa-solid fa-sliders"></i> Manage account</div>
            <div class="surface__body stack--sm" style="display:grid;">
                <button class="btn btn--ghost btn--block" id="editProfileBtn"><i class="fa-solid fa-pen"></i> Edit profile</button>
                <button class="btn btn--ghost btn--block" id="changePasswordBtn"><i class="fa-solid fa-key"></i> Change password</button>
                <a class="btn btn--ghost btn--block" href="?v=History"><i class="fa-solid fa-clock-rotate-left"></i> Watch history</a>
                <a class="btn btn--ghost btn--block" href="?v=Favorites"><i class="fa-solid fa-heart"></i> My favorites</a>
                <button class="btn btn--danger btn--block" id="deleteAccountBtn"><i class="fa-solid fa-trash-can"></i> Delete account</button>
                <a class="btn btn--primary btn--block" href="?v=Logout" data-no-fade><i class="fa-solid fa-right-from-bracket"></i> Log out</a>
            </div>
        </section>
    </div>
</main>

<!-- Edit profile -->
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
                    <div class="faint xsmall mt-1">JPG, PNG, GIF or WEBP.</div>
                </div>
                <button type="submit" class="btn btn--primary btn--block"><i class="fa-solid fa-floppy-disk"></i> Save changes</button>
            </form>
        </div>
    </div>
</div>

<!-- Change password -->
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

<!-- Delete account -->
<div class="overlay hide" id="deleteAccountModal">
    <div class="modal">
        <div class="modal__head">
            <h5 style="color:var(--danger);">Delete account</h5>
            <button class="modal__close" data-modal-close="deleteAccountModal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal__body">
            <p class="mb-1">Are you sure? This cannot be undone.</p>
            <p class="faint small">Your watch history, favorites and profile will be permanently removed.</p>
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
<script>Q8Profile.init();</script>
