<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Client Side'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#"><i class="bi bi-shop me-2"></i>Kantin Client</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
  <ul class="navbar-nav ms-auto align-items-center">
    <!-- Menu Home -->
    <li class="nav-item">
        <a class="nav-link active" href="#">Home</a>
    </li>
    
    <!-- Badge Status -->
    <li class="nav-item">
        <span class="badge bg-warning text-dark my-2 my-lg-1 py-2 mx-lg-3">Mode Preview Chasly</span>
    </li>

    <!-- Switch Dark Mode & Label Irvansyah -->
    <li class="nav-item d-flex align-items-center">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch" id="themeSwitch">
            <label class="form-check-label text-light fw-bold" for="themeSwitch">Irvansyah</label>
        </div>
    </li>
</ul>
        </div>
    </nav>

    <main class="container my-5">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-1"></i>
                <?= esc(session()->getFlashdata('success')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-1"></i>
                <?= esc(session()->getFlashdata('error')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?= $this->renderSection('content'); ?>
    </main>

    <footer class="footer mt-auto py-3 bg-white border-top text-center text-muted">
        <div class="container">
            <span>&copy; 2026 Progres Kantin - Client Side.</span>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    const themeSwitch = document.getElementById('themeSwitch');
    const htmlElement = document.documentElement;

    // Cek apakah sebelumnya sudah pernah di-set ke dark
    if (localStorage.getItem('theme') === 'dark') {
        htmlElement.setAttribute('data-bs-theme', 'dark');
        themeSwitch.checked = true;
    }

    themeSwitch.addEventListener('change', () => {
        const isDark = themeSwitch.checked;
        htmlElement.setAttribute('data-bs-theme', isDark ? 'dark' : 'light');
        
        // Simpan pilihan user ke browser
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
    });
</script>
</body>
</html>