<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?= esc($title) ?></h1>
    <a href="<?= site_url('paket-bundling') ?>" class="btn btn-outline-secondary btn-sm">Kembali</a>
</div>

<style>
    .paket-image-preview {
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
                <p class="mb-2"><strong>Nama Paket</strong></p>
                <p><?= esc($paket['nama_paket']) ?></p>
            </div>
            <div class="col-md-6">
                <p class="mb-2"><strong>Status</strong></p>
                <p><?= esc($statusLabels[$paket['status_id']] ?? $paket['status_id']) ?></p>
            </div>
            <div class="col-md-12">
                <p class="mb-2"><strong>Deskripsi</strong></p>
                <p><?= esc($paket['deskripsi'] ?: '-') ?></p>
            </div>

            <div class="col-md-6">
                <p class="mb-2"><strong>Isi Paket</strong></p>
                <?php if (! empty($paket['items'])): ?>
                    <ul class="mb-0">
                        <?php foreach ($paket['items'] as $isi): ?>
                            <li>
                                <?= esc($isi['qty']) ?>x <?= esc($isi['nama'] ?? 'Menu dihapus') ?>
                                <span class="text-muted small">(<?= esc(format_rupiah((float) ($isi['harga'] ?? 0))) ?>/pcs)</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-muted">Belum ada menu di paket ini.</p>
                <?php endif; ?>
            </div>

            <div class="col-md-6">
                <p class="mb-2"><strong>Gambar</strong></p>
                <?php if (! empty($paket['url_gambar'])): ?>
                    <img src="<?= base_url($paket['url_gambar']) ?>" alt="<?= esc($paket['nama_paket']) ?>" class="paket-image-preview">
                <?php else: ?>
                    <p class="text-muted">Tidak ada gambar.</p>
                <?php endif; ?>
            </div>

            <div class="col-md-4">
                <p class="mb-2"><strong>Total Harga Normal</strong></p>
                <p class="text-decoration-line-through text-muted"><?= esc(format_rupiah((float) ($paket['harga_normal'] ?? 0))) ?></p>
            </div>
            <div class="col-md-4">
                <p class="mb-2"><strong>Harga Paket</strong></p>
                <p class="fw-bold text-danger fs-5"><?= esc(format_rupiah((float) ($paket['harga_paket'] ?? 0))) ?></p>
            </div>
            <div class="col-md-4">
                <p class="mb-2"><strong>Hemat</strong></p>
                <p>
                    <?= esc(format_rupiah((float) ($paket['hemat'] ?? 0))) ?>
                    <?php if (($paket['persen_hemat'] ?? 0) > 0): ?>
                        <span class="badge bg-danger ms-1">-<?= esc((int) $paket['persen_hemat']) ?>%</span>
                    <?php endif; ?>
                </p>
            </div>
        </div>

        <div class="mt-4">
            <a href="<?= site_url('paket-bundling/edit/' . $paket['id']) ?>" class="btn btn-warning">Edit</a>
            <a href="<?= site_url('paket-bundling') ?>" class="btn btn-outline-secondary ms-2">Kembali</a>
        </div>
    </div>
</div>
