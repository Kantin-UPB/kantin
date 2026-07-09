<?php
// Lokasi file ini di project: app/Views/Poin/Riwayat.php
// Fragmen HTML biasa, dipanggil lewat ManagePoin::renderPage() (pola sama seperti Menu.php).
?>

<div class="container-fluid py-4">
    <h4 class="mb-3">Riwayat Poin</h4>

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>User ID</th>
                        <th>Transaksi ID</th>
                        <th>Jenis</th>
                        <th>Jumlah Poin</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($riwayat)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">Belum ada riwayat poin.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($riwayat as $row): ?>
                            <tr>
                                <td><?= esc($row['created_at']) ?></td>
                                <td><?= esc($row['user_id']) ?></td>
                                <td><?= esc($row['transaksi_id'] ?? '-') ?></td>
                                <td>
                                    <span class="badge bg-<?= $row['jenis'] === 'masuk' ? 'success' : 'danger' ?>">
                                        <?= esc($row['jenis']) ?>
                                    </span>
                                </td>
                                <td><?= esc($row['jumlah_poin']) ?></td>
                                <td><?= esc($row['keterangan']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
