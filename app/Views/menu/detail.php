<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?= esc($title) ?></h1>
    <a href="<?= site_url('menu') ?>" class="btn btn-outline-secondary btn-sm">Kembali</a>
</div>

<style>
    .menu-image-preview {
        width: 100%;
        max-width: 320px;
        max-height: 240px;
        object-fit: cover;
        border-radius: 0.5rem;
    }
</style>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <p class="mb-2"><strong>ID</strong></p>
                <p><?= esc($menu['id']) ?></p>
            </div>
            <div class="col-md-6">
                <p class="mb-2"><strong>Nama Menu</strong></p>
                <p><?= esc($menu['nama']) ?></p>
            </div>
            <div class="col-md-6">
                <p class="mb-2"><strong>Kategori</strong></p>
                <p><?= esc($menu['nama_kategori'] ?? '-') ?></p>
            </div>
            <div class="col-md-6">
                <p class="mb-2"><strong>Status</strong></p>
                <p><?= esc($statusOptions[$menu['status_id']] ?? $menu['status_id']) ?></p>
            </div>
            <div class="col-md-12">
                <p class="mb-2"><strong>Deskripsi</strong></p>
                <p><?= esc($menu['deskripsi']) ?></p>
            </div>
            <div class="col-md-6">
                <p class="mb-2"><strong>Harga</strong></p>
                <?php if ((int) ($menu['diskon'] ?? 0) > 0): ?>
                    <p class="mb-1">
                        <span class="text-decoration-line-through text-muted"><?= esc(format_rupiah((float) ($menu['harga'] ?? 0))) ?></span>
                        <span class="badge bg-danger ms-2">-<?= esc((int) $menu['diskon']) ?>%</span>
                    </p>
                    <p class="fw-bold text-danger fs-5"><?= esc(format_rupiah(hitung_harga_diskon((float) ($menu['harga'] ?? 0), (int) $menu['diskon']))) ?></p>
                <?php else: ?>
                    <p><?= esc(format_rupiah((float) ($menu['harga'] ?? 0))) ?></p>
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <p class="mb-2"><strong>Diskon</strong></p>
                <p><?= (int) ($menu['diskon'] ?? 0) > 0 ? esc((int) $menu['diskon']) . '%' : 'Tidak ada diskon' ?></p>
            </div>
            <div class="col-md-6">
                <p class="mb-2"><strong>Gambar</strong></p>
                <?php if (! empty($menu['url_gambar'])): ?>
                    <img src="<?= base_url($menu['url_gambar']) ?>" alt="<?= esc($menu['nama']) ?>" class="menu-image-preview">
                <?php else: ?>
                    <p class="text-muted">Tidak ada gambar.</p>
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <p class="mb-2"><strong>Created By</strong></p>
                <p><?= esc($menu['created_username'] ?? ($menu['created_by'] ?? '-')) ?></p>
            </div>
            <div class="col-md-6">
                <p class="mb-2"><strong>Created At</strong></p>
                <p><?= esc($menu['created_at'] ?? '-') ?></p>
            </div>
        </div>

        <div class="mt-4">
            <a href="<?= site_url('menu/edit/' . $menu['id']) ?>" class="btn btn-warning">Edit</a>
            <a href="<?= site_url('menu') ?>" class="btn btn-outline-secondary ms-2">Kembali</a>
        </div>
    </div>
</div>
