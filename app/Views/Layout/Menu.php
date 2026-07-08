<style>
    .sidebar {
        min-height: calc(100vh - 56px);
        border-right: 1px solid #dee2e6;
        background-color: #f8f9fa;
        padding-top: 1rem;
        z-index: 2;
    }
    .sidebar .nav-link {
        color: #333;
        padding: 0.75rem 1rem;
        border-radius: 0.25rem;
        margin-bottom: 0.25rem;
        min-height: 48px;
        display: flex;
        align-items: center;
        text-decoration: none;
        cursor: pointer;
    }
    .sidebar .nav-link:hover,
    .sidebar .nav-link:focus {
        background-color: #e9ecef;
        color: #000;
        outline: none;
    }
    .sidebar .nav-link:hover {
        background-color: #e9ecef;
        color: #000;
    }
    .sidebar .nav-link.active {
        background-color: #0d6efd;
        color: #fff;
    }
    .sidebar .nav-link {
        position: relative;
        z-index: 1;
        display: block;
        width: 100%;
        text-decoration: none;
    }
    main {
        padding-top: 1.5rem;
    }
</style>

<?php
$uri = service('request')->getUri();
$currentPath = '/' . trim($uri->getPath(), '/');
$currentPath = preg_replace('#^/index\.php#', '', $currentPath);
$currentPath = $currentPath === '' ? '/' : $currentPath;

$matches = static function (string $path) use ($currentPath): bool {
    if ($path === '/') {
        return $currentPath === '/';
    }

    return $currentPath === $path || str_starts_with($currentPath, $path . '/');
};
?>

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
            <a class="nav-link <?= $matches('/') ? 'active' : '' ?>" href="<?= site_url('/') ?>" data-nav="dashboard"><i class="bi bi-speedometer2 me-2"></i><span>Dashboard</span></a>
            <a class="nav-link <?= $matches('/menu') ? 'active' : '' ?>" href="<?= site_url('/menu') ?>" data-nav="menu"><i class="bi bi-journal-text me-2"></i><span>Menu</span></a>
            <a class="nav-link <?= $matches('/kategori') ? 'active' : '' ?>" href="<?= site_url('/kategori') ?>" data-nav="kategori"><i class="bi bi-tags me-2"></i><span>Kategori</span></a>
            <a class="nav-link <?= $matches('/data') ? 'active' : '' ?>" href="<?= site_url('/data') ?>" data-nav="data"><i class="bi bi-database me-2"></i><span>Data</span></a>
            <a class="nav-link <?= $matches('/transaction') ? 'active' : '' ?>" href="<?= site_url('/transaction') ?>" data-nav="transaction"><i class="bi bi-cart3 me-2"></i><span>Transaction</span></a>
            <a class="nav-link <?= $matches('/report') ? 'active' : '' ?>" href="<?= site_url('/report') ?>" data-nav="report"><i class="bi bi-file-earmark-bar-graph me-2"></i><span>Report</span></a>
            <hr>
            <a class="nav-link text-danger" href="<?= site_url('/logout') ?>" data-nav="logout"><i class="bi bi-box-arrow-right me-2"></i><span>Logout</span></a>
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
                    <a class="nav-link <?= $matches('/') ? 'active' : '' ?>" href="<?= site_url('/') ?>" data-nav="dashboard">
                        <i class="bi bi-speedometer2 me-2"></i><span>Dashboard</span>
                    </a>
                    <a class="nav-link <?= $matches('/menu') ? 'active' : '' ?>" href="<?= site_url('/menu') ?>" data-nav="menu">
                        <i class="bi bi-journal-text me-2"></i><span>Menu</span>
                    </a>
                     <a class="nav-link" href="<?= site_url('/meja') ?>">
                        <i class="bi bi-ui-checks-grid"></i> Meja
                    </a>  
                    <a class="nav-link <?= $matches('/kategori') ? 'active' : '' ?>" href="<?= site_url('/kategori') ?>" data-nav="kategori">
                        <i class="bi bi-tags me-2"></i><span>Kategori</span>
                    </a>
                    <a class="nav-link <?= $matches('/data') ? 'active' : '' ?>" href="<?= site_url('/data') ?>" data-nav="data">
                        <i class="bi bi-database me-2"></i><span>Data</span>
                    </a>
                    <a class="nav-link <?= $matches('/transaction') ? 'active' : '' ?>" href="<?= site_url('/transaction') ?>" data-nav="transaction">
                        <i class="bi bi-cart3 me-2"></i><span>Transaction</span>
                    </a>
                    <a class="nav-link <?= $matches('/report') ? 'active' : '' ?>" href="<?= site_url('/report') ?>" data-nav="report">
                        <i class="bi bi-file-earmark-bar-graph me-2"></i><span>Report</span>
                    </a>
                    <hr>
                    <a class="nav-link text-danger" href="<?= site_url('/logout') ?>" data-nav="logout">
                        <i class="bi bi-box-arrow-right me-2"></i><span>Logout</span>
                    </a>
                </nav>
            </div>
        </nav>

        <!-- Main Content Area -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <!-- Content renders here -->
