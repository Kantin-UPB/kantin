<style>
    .sidebar {
        min-height: calc(100vh - 56px);
        border-right: 1px solid #dee2e6;
    }
    .sidebar .nav-link {
        color: #333;
        padding: 0.75rem 1rem;
        border-radius: 0.25rem;
        margin-bottom: 0.25rem;
    }
    .sidebar .nav-link:hover {
        background-color: #e9ecef;
        color: #000;
    }
    .sidebar .nav-link.active {
        background-color: #0d6efd;
        color: #fff;
    }
    main {
        padding-top: 1.5rem;
    }
</style>

<!-- Top Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container-fluid">
        <!-- Brand -->
        <a class="navbar-brand" href="/">
            <i class="bi bi-clipboard-data me-2"></i>
            <?= env('app.name', 'Kantin') ?>
        </a>

        <!-- User Info + Logout Button (hanya tampil kalau sudah login) -->
        <?php if (session()->get('isLoggedIn')): ?>
            <div class="d-flex align-items-center gap-2 ms-auto">
                <span class="text-light small d-none d-sm-inline">
                    <i class="bi bi-person-circle me-1"></i>
                    <?= esc(session()->get('npm') ?? '') ?>
                    <span class="badge bg-secondary ms-1"><?= esc(session()->get('role') ?? '') ?></span>
                </span>
                <a href="<?= site_url('/logout') ?>" class="btn btn-sm btn-outline-light" title="Logout">
                    <i class="bi bi-box-arrow-right me-1"></i>
                    <span class="d-none d-sm-inline">Logout</span>
                </a>
            </div>
        <?php endif; ?>

        <!-- Hamburger Menu Button for Mobile Sidebar Toggle -->
        <button class="btn btn-outline-light d-md-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas">
            <i class="bi bi-list fs-4"></i>
        </button>
    </div>
</nav>

<!-- Offcanvas Sidebar for Mobile -->
<div class="offcanvas offcanvas-start bg-light" tabindex="-1" id="sidebarOffcanvas">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">
            <i class="bi bi-clipboard-data me-2"></i>
            Menu
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#sidebarOffcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <nav class="nav flex-column">
            <a class="nav-link active" href="<?= site_url('/') ?>"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
            <a class="nav-link" href="<?= site_url('/menu') ?>"><i class="bi bi-journal-text me-2"></i>Menu</a>
            <a class="nav-link" href="<?= site_url('/data') ?>"><i class="bi bi-database me-2"></i>Data</a>
            <a class="nav-link" href="<?= site_url('/transaction') ?>"><i class="bi bi-cart3 me-2"></i>Transaction</a>
            <a class="nav-link" href="<?= site_url('/report') ?>"><i class="bi bi-file-earmark-bar-graph me-2"></i>Report</a>
            <hr>
            <a class="nav-link text-danger" href="/logout"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
        </nav>
    </div>
</div>

<!-- Container with Sidebar + Content area -->
<div class="container-fluid">
    <div class="row">
        <!-- Desktop Sidebar -->
        <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse show">
            <div class="position-sticky pt-3">
                <nav class="nav flex-column">
                    <a class="nav-link active" href="<?= site_url('/') ?>">
                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                    </a>
                    <a class="nav-link" href="<?= site_url('/menu') ?>">
                        <i class="bi bi-journal-text me-2"></i>Menu
                    </a>
                    <a class="nav-link" href="<?= site_url('/meja') ?>">
                        <i class="bi bi-ui-checks-grid"></i> Meja
                    </a>                  
                    <a class="nav-link" href="<?= site_url('/data') ?>">
                        <i class="bi bi-database me-2"></i>Data
                    </a>
                    <a class="nav-link" href="<?= site_url('/transaction') ?>">
                        <i class="bi bi-cart3 me-2"></i>Transaction
                    </a>
                    <a class="nav-link" href="<?= site_url('/report') ?>">
                        <i class="bi bi-file-earmark-bar-graph me-2"></i>Report
                    </a>
                    <hr>
                    <a class="nav-link text-danger" href="/logout">
                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                    </a>
                </nav>
            </div>
        </nav>

        <!-- Main Content Area -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <!-- Content renders here -->
