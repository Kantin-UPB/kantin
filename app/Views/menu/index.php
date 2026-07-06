<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?= esc($title) ?></h1>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= esc(session()->getFlashdata('success')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= esc(session()->getFlashdata('error')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<style>
    .menu-card {
        border: 1px solid rgba(0,0,0,.08);
        border-radius: 1rem;
        transition: transform .2s ease, box-shadow .2s ease;
        overflow: hidden;
    }

    .menu-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.85rem 1.6rem rgba(0, 0, 0, 0.08);
    }

    .menu-card .menu-card-image {
        width: 100%;
        height: 260px;
        background: #f8f9fa;
        border: 1px solid rgba(0,0,0,.06);
        border-radius: 1rem;
        display: grid;
        place-items: center;
        position: relative;
        overflow: hidden;
    }

    .menu-card .menu-card-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
    }

    .menu-card-header {
        padding: 1rem 1rem 0.5rem;
        text-align: center;
    }

    .menu-card-header .menu-card-meta {
        font-size: 0.9rem;
        color: #6c757d;
        line-height: 1.25;
    }

    .menu-card-body {
        padding: 0 1rem 1rem;
    }

    .menu-card-price {
        font-weight: 700;
    }
</style>

<div class="row g-3 mb-4">
    <?php if (! empty($showBackButton)): ?>
        <div class="col-auto">
            <a href="<?= site_url('menu') ?>" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Active Menus
            </a>
        </div>
    <?php endif; ?>
    <div class="col-auto">
        <a href="<?= site_url('menu/pending') ?>" class="btn btn-outline-secondary btn-sm <?= ($statusPage ?? '') === 'pending' ? 'active' : '' ?>">
            <i class="bi bi-hourglass-split me-1"></i> Pending Menus
        </a>
    </div>
    <div class="col-auto">
        <a href="<?= site_url('menu/cancelled') ?>" class="btn btn-outline-secondary btn-sm <?= ($statusPage ?? '') === 'cancelled' ? 'active' : '' ?>">
            <i class="bi bi-x-circle me-1"></i> Cancelled Menus
        </a>
    </div>
    <div class="col-auto ms-auto">
        <a href="<?= site_url('menu/create') ?>" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i> Add Menu
        </a>
    </div>
</div>

<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 g-4">
    <?php if (! empty($menu)): ?>
        <?php foreach ($menu as $item): ?>
            <div class="col">
                <div class="card menu-card h-100 shadow-sm">
                    <div class="menu-card-header position-relative">
                        <div class="d-flex justify-content-center align-items-center">
                            <div class="text-center">
                                <div class="fw-bold fs-5"><?= esc($item['id']) ?></div>
                            </div>
                        </div>
                        <div class="dropdown position-absolute top-0 end-0 p-2">
                            <button class="btn btn-sm btn-dark rounded-circle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-three-dots"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="<?= site_url('menu/' . $item['id']) ?>">Detail</a></li>
                                <li><a class="dropdown-item" href="<?= site_url('menu/edit/' . $item['id']) ?>">Edit</a></li>
                                <?php if ($statusPage === 'active'): ?>
                                    <li><a class="dropdown-item" href="<?= site_url('menu/draft/' . $item['id']) ?>">Set to Draft</a></li>
                                    <li><a class="dropdown-item text-danger" href="<?= site_url('menu/cancel/' . $item['id']) ?>">Cancel</a></li>
                                <?php elseif ($statusPage === 'pending'): ?>
                                    <li><a class="dropdown-item text-success" href="<?= site_url('menu/activate/' . $item['id']) ?>">Activate</a></li>
                                    <li><a class="dropdown-item text-danger" href="<?= site_url('menu/cancel/' . $item['id']) ?>">Cancel</a></li>
                                <?php else: ?>
                                    <li><a class="dropdown-item text-danger" href="<?= site_url('menu/delete/' . $item['id']) ?>">Hapus Permanen</a></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        <div class="text-center mt-3 menu-card-meta">
                            <?= esc($item['nama_kategori'] ?? '-') ?>
                        </div>
                    </div>

                    <div class="card-body text-center menu-card-body">
                        <div class="menu-card-image mb-3">
                            <?php if (! empty($item['url_gambar'])): ?>
                                <img src="<?= base_url($item['url_gambar']) ?>" alt="<?= esc($item['nama']) ?>">
                            <?php else: ?>
                                <i class="bi bi-image fs-1 text-secondary"></i>
                            <?php endif; ?>
                        </div>
                        <h5 class="card-title mb-1"><?= esc($item['nama']) ?></h5>
                        <p class="text-muted small mb-3"><?= esc($item['deskripsi'] ?? '-') ?></p>
                        <div class="d-flex justify-content-end">
                            <span class="menu-card-price"><?= esc(format_rupiah((float) ($item['harga'] ?? 0))) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="card border-0 shadow-sm py-5 text-center">
                <div class="card-body text-muted">
                    <div class="mb-3"><i class="bi bi-card-checklist fs-1"></i></div>
                    <h5 class="card-title">Tidak ada menu untuk status ini.</h5>
                    <p class="card-text">Coba kembali ke menu lain atau tambahkan menu baru.</p>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
