<?php
// Lokasi file ini di project: app/Views/Poin/Form.php
$isEdit = ! empty($aturan);
?>
<?= $this->extend('Layout/Menu') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-4">
    <h4 class="mb-3"><?= $isEdit ? 'Edit' : 'Tambah' ?> Aturan Poin</h4>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form method="post"
                  action="<?= $isEdit ? site_url('manage-poin/update/' . $aturan['id']) : site_url('manage-poin/store') ?>">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label class="form-label">Nama Aturan</label>
                    <input type="text" name="nama_aturan" class="form-control"
                           value="<?= esc(old('nama_aturan', $aturan['nama_aturan'] ?? '')) ?>"
                           placeholder="Contoh: Poin Reguler" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Rasio (Rp per 1 poin)</label>
                    <input type="number" name="rasio_rupiah" class="form-control"
                           value="<?= esc(old('rasio_rupiah', $aturan['rasio_rupiah'] ?? '1000')) ?>"
                           placeholder="Contoh: 1000 artinya tiap Rp1.000 = 1 poin" min="1" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Minimal Transaksi Supaya Dapat Poin (Rp)</label>
                    <input type="number" name="minimal_transaksi" class="form-control"
                           value="<?= esc(old('minimal_transaksi', $aturan['minimal_transaksi'] ?? '0')) ?>"
                           min="0">
                </div>

                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status_aktif" class="form-select">
                        <?php $statusNow = old('status_aktif', $aturan['status_aktif'] ?? 'aktif'); ?>
                        <option value="aktif" <?= $statusNow === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                        <option value="nonaktif" <?= $statusNow === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                    </select>
                    <div class="form-text">Catatan: sebaiknya hanya satu aturan yang aktif dalam satu waktu.</div>
                </div>

                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="<?= site_url('manage-poin') ?>" class="btn btn-outline-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
