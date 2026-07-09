<?php
/**
 * Client Side Register — Kantin UPB
 *
 * Styling: IDENTIK dengan Auth/Login.php (backoffice) — gradient ungu,
 * Bootstrap 5, Bootstrap Icons, card 420px, brand-icon bulat.
 *
 * Field: NPM (9 digit) + password + konfirmasi password.
 * Tidak pakai email (sesuai aturan tim).
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
    <title><?= esc($title ?? 'Daftar Akun Mahasiswa') ?> - Kantin UPB</title>

    <!-- Bootstrap 5 (sama persis dengan backoffice) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            max-width: 420px;
            width: 100%;
            border-radius: 1rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        }
        .login-card .card-body {
            padding: 2.5rem;
        }
        .brand-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            color: white;
            font-size: 2rem;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4490 100%);
        }
        .input-group-text {
            background-color: #f8f9fa;
            border-right: 0;
        }
        .form-control {
            border-left: 0;
        }
        .form-control:focus {
            border-color: #667eea;
        }
        .input-group .form-control:focus {
            border-left: 1px solid #667eea;
        }
        .login-type-badge {
            display: inline-block;
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>
    <div class="card login-card">
        <div class="card-body">
            <div class="brand-icon">
                <i class="bi bi-person-plus"></i>
            </div>

            <div class="text-center">
                <span class="login-type-badge">Mahasiswa</span>
            </div>

            <h3 class="text-center mb-1">Kantin UPB</h3>
            <p class="text-center text-muted mb-4">Daftar akun mahasiswa untuk mulai memesan</p>

            <!-- Flash messages -->
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-1"></i>
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Validation errors -->
            <?php if (session()->get('errors')): ?>
                <div class="alert alert-danger" role="alert">
                    <ul class="mb-0 ps-3">
                        <?php foreach ((array) session()->get('errors') as $field => $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Register form -->
            <form action="/mahasiswa/register" method="post" autocomplete="off" novalidate>
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="npm" class="form-label">NPM</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person-vcard"></i></span>
                        <input
                            type="text"
                            class="form-control form-control-lg <?= session('errors.npm') ? 'is-invalid' : '' ?>"
                            id="npm"
                            name="npm"
                            inputmode="numeric"
                            pattern="[0-9]{9}"
                            minlength="9"
                            maxlength="9"
                            placeholder="Masukkan NPM (9 digit)"
                            value="<?= old('npm') ?>"
                            required
                            autofocus
                            autocomplete="username"
                        >
                        <div class="invalid-feedback">
                            <?= session('errors.npm') ?? 'NPM harus tepat 9 digit angka.' ?>
                        </div>
                    </div>
                    <small class="text-muted">NPM harus tepat 9 digit angka (0-9).</small>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input
                            type="password"
                            class="form-control form-control-lg <?= session('errors.password') ? 'is-invalid' : '' ?>"
                            id="password"
                            name="password"
                            placeholder="Minimal <?= esc($passwordMinLength ?? 8) ?> karakter"
                            required
                            autocomplete="new-password"
                            minlength="<?= esc($passwordMinLength ?? 8) ?>"
                        >
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword" tabindex="-1">
                            <i class="bi bi-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password_confirm" class="form-label">Konfirmasi Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                        <input
                            type="password"
                            class="form-control form-control-lg <?= session('errors.password_confirm') ? 'is-invalid' : '' ?>"
                            id="password_confirm"
                            name="password_confirm"
                            placeholder="Ulangi password"
                            required
                            autocomplete="new-password"
                            minlength="<?= esc($passwordMinLength ?? 8) ?>"
                        >
                        <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm" tabindex="-1">
                            <i class="bi bi-eye" id="toggleIconConfirm"></i>
                        </button>
                    </div>
                </div>

                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-person-plus-fill me-2"></i>Daftar Sekarang
                    </button>
                </div>
            </form>

            <p class="text-center text-muted small mt-4 mb-0">
                <i class="bi bi-shield-lock me-1"></i>
                Sudah punya akun? <a href="<?= site_url('/mahasiswa/login') ?>" class="text-decoration-none fw-semibold">Login di sini</a>
            </p>
        </div>
    </div>

    <script>
        // Toggle visibility password (sama persis dengan backoffice)
        function setupToggle(toggleBtnId, passwordInputId, toggleIconId) {
            const btn = document.getElementById(toggleBtnId);
            const input = document.getElementById(passwordInputId);
            const icon = document.getElementById(toggleIconId);
            if (!btn || !input || !icon) return;
            btn.addEventListener('click', function () {
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                }
            });
        }
        setupToggle('togglePassword', 'password', 'toggleIcon');
        setupToggle('togglePasswordConfirm', 'password_confirm', 'toggleIconConfirm');

        // Strip karakter non-digit di NPM saat user ngetik
        const npmInput = document.getElementById('npm');
        if (npmInput) {
            npmInput.addEventListener('input', function () {
                const digits = npmInput.value.replace(/\D/g, '').slice(0, 9);
                if (digits !== npmInput.value) npmInput.value = digits;
            });
        }
    </script>
</body>

</html>
