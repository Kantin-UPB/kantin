<?php
/**
 * Login Page — Kantin UPB
 *
 * Aturan (sesuai permintaan tim):
 *   - Field pakai NPM (9 digit angka) + password. TIDAK pakai email.
 *   - NPM wajib tepat 9 digit (client-side: pattern + maxlength + minlength;
 *     server-side: regex /^[0-9]{9}$/ di controller Auth::processLogin).
 *
 * UI diadaptasi dari referensi php-login dengan tema warna Kantin (biru
 * Bootstrap primary). Halaman ini STANDALONE — tidak memakai template
 * Kantin (Layout/Header, Layout/Menu, Layout/Footer) supaya UI login
 * tidak ikut sidebar dashboard.
 *
 * @var string $title
 * @var int    $passwordMinLength
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login Kantin UPB — masuk dengan NPM & password.">
    <title><?= esc($title ?? 'Login — Kantin UPB') ?></title>

    <link rel="stylesheet" href="<?= base_url('assets/css/auth.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<div class="auth-wrap">
    <main class="auth-card">
        <header class="auth-card__header">
            <div class="auth-card__logo">
                <i class="fas fa-utensils"></i>
            </div>
            <h1 class="auth-card__title">Selamat Datang</h1>
            <p class="auth-card__subtitle">Masuk ke akun Kantin UPB Anda untuk melanjutkan</p>
        </header>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success" role="alert">
                <i class="fas fa-circle-check"></i>
                <span><?= esc(session()->getFlashdata('success')) ?></span>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-error" role="alert">
                <i class="fas fa-circle-exclamation"></i>
                <span><?= esc(session()->getFlashdata('error')) ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= site_url('/login') ?>" autocomplete="on" novalidate>
            <?= csrf_field() ?>

            <div class="form-group">
                <label class="form-group__label" for="npm">NPM</label>
                <div class="form-group__input-wrap">
                    <i class="fas fa-id-card"></i>
                    <input
                        type="text"
                        id="npm"
                        name="npm"
                        class="form-control form-control--npm"
                        inputmode="numeric"
                        placeholder="Masukkan NPM (9 digit)"
                        value="<?= old('npm') ?>"
                        required
                        autofocus
                        autocomplete="username"
                        pattern="[0-9]{9}"
                        minlength="9"
                        maxlength="9"
                        title="NPM harus tepat 9 digit angka"
                    >
                </div>
                <p class="form-text">NPM harus tepat 9 digit angka (0-9).</p>
            </div>

            <div class="form-group">
                <label class="form-group__label" for="password">Password</label>
                <div class="form-group__input-wrap">
                    <i class="fas fa-key"></i>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control"
                        placeholder="Masukkan password"
                        required
                        autocomplete="current-password"
                        minlength="<?= esc($passwordMinLength ?? 8) ?>"
                    >
                    <button type="button" class="password-toggle" id="togglePassword" aria-label="Tampilkan password" aria-pressed="false">
                        <svg class="icon-eye" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                        <svg class="icon-eye-off" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                            <line x1="1" y1="1" x2="23" y2="23"></line>
                        </svg>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block">
                <span>Masuk</span>
                <i class="fas fa-arrow-right"></i>
            </button>
        </form>

        <footer class="auth-card__footer">
            Belum punya akun? <a href="<?= site_url('/register') ?>">Daftar di sini</a>
        </footer>

        <div class="demo-hint">
            <strong>Demo Admin:</strong> NPM <code>123456789</code> / password <code>Kantin123456UPB</code>
        </div>
    </main>
</div>

<script>
(function() {
    'use strict';
    var btn = document.getElementById('togglePassword');
    if (!btn) return;
    var input = document.getElementById('password');
    var iconEye = btn.querySelector('.icon-eye');
    var iconEyeOff = btn.querySelector('.icon-eye-off');
    if (!input || !iconEye || !iconEyeOff) return;

    function toggle() {
        if (input.type === 'password') {
            input.type = 'text';
            iconEye.style.display = 'none';
            iconEyeOff.style.display = 'inline-block';
            btn.setAttribute('aria-pressed', 'true');
        } else {
            input.type = 'password';
            iconEye.style.display = 'inline-block';
            iconEyeOff.style.display = 'none';
            btn.setAttribute('aria-pressed', 'false');
        }
    }
    btn.addEventListener('click', toggle);
    btn.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); toggle(); }
    });

    // Strip karakter non-digit di NPM saat user ngetik — UX biar gampang.
    var npm = document.getElementById('npm');
    if (npm) {
        npm.addEventListener('input', function() {
            var digits = npm.value.replace(/\D/g, '').slice(0, 9);
            if (digits !== npm.value) npm.value = digits;
        });
    }
})();
</script>
</body>
</html>
