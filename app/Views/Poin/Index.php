<?php
// Lokasi file ini di project: app/Views/Poin/Index.php
// Ini fragmen HTML biasa (BUKAN extend/section) — dipanggil lewat
// ManagePoin::renderPage(), disambung setelah Layout/Header + Layout/Menu,
// dan sebelum Layout/Footer. Persis pola app/Controllers/Menu.php.
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Manage Sistem Poin</h4>
        <a href="<?= site_url('manage-poin/create') ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Tambah Aturan
        </a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Nama Aturan</th>
                        <th>Rasio (Rp per 1 poin)</th>
                        <th>Minimal Transaksi</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($aturan)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">Belum ada aturan poin.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($aturan as $row): ?>
                            <tr>
                                <td><?= esc($row['nama_aturan']) ?></td>
                                <td>Rp<?= number_format((float) $row['rasio_rupiah'], 0, ',', '.') ?></td>
                                <td>Rp<?= number_format((float) $row['minimal_transaksi'], 0, ',', '.') ?></td>
                                <td>
                                    <span class="badge bg-<?= $row['status_aktif'] === 'aktif' ? 'success' : 'secondary' ?>">
                                        <?= esc($row['status_aktif']) ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="<?= site_url('manage-poin/edit/' . $row['id']) ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <a href="<?= site_url('manage-poin/delete/' . $row['id']) ?>"
                                       class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('Yakin hapus aturan ini?')">Hapus</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        <a href="<?= site_url('manage-poin/riwayat') ?>" class="btn btn-outline-secondary">
            Lihat Riwayat Poin Semua User
        </a>
    </div>
</div>
