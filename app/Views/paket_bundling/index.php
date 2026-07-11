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
    .paket-card {
        border: 1px solid rgba(0,0,0,.08);
        border-radius: 1rem;
        transition: transform .2s ease, box-shadow .2s ease;
        overflow: hidden;
    }

    .paket-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.85rem 1.6rem rgba(0, 0, 0, 0.08);
    }

    .paket-card-image {
        width: 100%;
        height: 200px;
        background: #f8f9fa;
        border: 1px solid rgba(0,0,0,.06);
        border-radius: 1rem;
        display: grid;
        place-items: center;
        position: relative;
        overflow: hidden;
    }

    .paket-card-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
    }

    .paket-card-header {
        padding: 1rem 1rem 0.5rem;
        text-align: center;
    }

    .paket-card-body {
        padding: 0 1rem 1rem;
    }

    .paket-card-price {
        font-weight: 700;
    }

    .paket-card-price-original {
        font-size: 0.85rem;
        color: #adb5bd;
        text-decoration: line-through;
        font-weight: 400;
        margin-right: 0.4rem;
    }

    .hemat-badge {
        position: absolute;
        top: 0;
        left: 0;
        margin: 0.5rem;
        z-index: 2;
    }

    .paket-item-list {
        font-size: 0.85rem;
        color: #6c757d;
        text-align: left;
    }
</style>

<div class="row g-3 mb-4">
    <?php if (! empty($showBackButton)): ?>
        <div class="col-auto">
            <a href="<?= site_url('paket-bundling') ?>" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Paket Aktif
            </a>
        </div>
    <?php endif; ?>
    <div class="col-auto">
        <a href="<?= site_url('paket-bundling/pending') ?>" class="btn btn-outline-secondary btn-sm <?= ($statusPage ?? '') === 'pending' ? 'active' : '' ?>">
            <i class="bi bi-hourglass-split me-1"></i> Pending
        </a>
    </div>
    <div class="col-auto">
        <a href="<?= site_url('paket-bundling/cancelled') ?>" class="btn btn-outline-secondary btn-sm <?= ($statusPage ?? '') === 'cancelled' ? 'active' : '' ?>">
            <i class="bi bi-x-circle me-1"></i> Dibatalkan
        </a>
    </div>
    <div class="col-auto ms-auto">
        <a href="<?= site_url('paket-bundling/create') ?>" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i> Tambah Paket Bundling
        </a>
    </div>
</div>

<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 g-4">
    <?php if (! empty($paket)): ?>
        <?php foreach ($paket as $item): ?>
            <div class="col">
                <div class="card paket-card h-100 shadow-sm">
                    <div class="paket-card-header position-relative">
                        <?php if (($item['persen_hemat'] ?? 0) > 0): ?>
                            <span class="badge bg-danger hemat-badge">
                                <i class="bi bi-stars me-1"></i>Hemat <?= esc((int) $item['persen_hemat']) ?>%
                            </span>
                        <?php endif; ?>
                        <div class="dropdown position-absolute top-0 end-0 p-2">
                            <button class="btn btn-sm btn-dark rounded-circle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-three-dots"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="<?= site_url('paket-bundling/' . $item['id']) ?>">Detail</a></li>
                                <li><a class="dropdown-item" href="<?= site_url('paket-bundling/edit/' . $item['id']) ?>">Edit</a></li>
                                <?php if ($statusPage === 'active'): ?>
                                    <li><a class="dropdown-item" href="<?= site_url('paket-bundling/draft/' . $item['id'] . '?return=/paket-bundling') ?>">Set to Draft</a></li>
                                    <li><a class="dropdown-item text-danger" href="<?= site_url('paket-bundling/cancel/' . $item['id'] . '?return=/paket-bundling') ?>">Cancel</a></li>
                                <?php elseif ($statusPage === 'pending'): ?>
                                    <li><a class="dropdown-item text-success" href="<?= site_url('paket-bundling/activate/' . $item['id'] . '?return=/paket-bundling/pending') ?>">Activate</a></li>
                                    <li><a class="dropdown-item text-danger" href="<?= site_url('paket-bundling/cancel/' . $item['id'] . '?return=/paket-bundling/pending') ?>">Cancel</a></li>
                                <?php else: ?>
                                    <li><a class="dropdown-item text-warning" href="<?= site_url('paket-bundling/restore/' . $item['id'] . '?return=/paket-bundling/cancelled') ?>">Restore</a></li>
                                    <li><a class="dropdown-item text-danger" href="<?= site_url('paket-bundling/delete/' . $item['id'] . '?return=/paket-bundling/cancelled') ?>">Hapus Permanen</a></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        <div class="text-center mt-3 fw-bold fs-6"><?= esc($item['nama_paket']) ?></div>
                    </div>

                    <div class="card-body text-center paket-card-body">
                        <div class="paket-card-image mb-3">
                            <?php if (! empty($item['url_gambar'])): ?>
                                <img src="<?= base_url($item['url_gambar']) ?>" alt="<?= esc($item['nama_paket']) ?>">
                            <?php else: ?>
                                <i class="bi bi-box-seam fs-1 text-secondary"></i>
                            <?php endif; ?>
                        </div>

                        <?php if (! empty($item['items'])): ?>
                            <ul class="paket-item-list mb-3 ps-3">
                                <?php foreach ($item['items'] as $isi): ?>
                                    <li><?= esc($isi['qty']) ?>x <?= esc($isi['nama'] ?? 'Menu dihapus') ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>

                        <div class="d-flex justify-content-end align-items-baseline">
                            <?php if (($item['hemat'] ?? 0) > 0): ?>
                                <span class="paket-card-price-original"><?= esc(format_rupiah((float) ($item['harga_normal'] ?? 0))) ?></span>
                                <span class="paket-card-price text-danger"><?= esc(format_rupiah((float) ($item['harga_paket'] ?? 0))) ?></span>
                            <?php else: ?>
                                <span class="paket-card-price"><?= esc(format_rupiah((float) ($item['harga_paket'] ?? 0))) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="card border-0 shadow-sm py-5 text-center">
                <div class="card-body text-muted">
                    <div class="mb-3"><i class="bi bi-box-seam fs-1"></i></div>
                    <h5 class="card-title">Belum ada paket bundling untuk status ini.</h5>
                    <p class="card-text">Buat promo paket bundling baru untuk menggabungkan beberapa menu jadi satu harga spesial.</p>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
