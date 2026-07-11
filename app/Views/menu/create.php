<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?= esc($title) ?></h1>
    <a href="<?= site_url('menu') ?>" class="btn btn-outline-secondary btn-sm">Kembali</a>
</div>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-body">
        <?= form_open_multipart('menu/store', ['class' => 'row g-3']) ?>
            <div class="col-md-6">
                <label for="id_kategori" class="form-label">Kategori</label>
                <select name="id_kategori" id="id_kategori" class="form-select">
                    <option value="">-- Pilih Kategori --</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= esc($category['id_kategori']) ?>" <?= old('id_kategori', $menu['id_kategori'] ?? '') == $category['id_kategori'] ? 'selected' : '' ?>><?= esc($category['nama_kategori']) ?></option>
                    <?php endforeach; ?>
                    <option value="__add_category__">+ Tambah Kategori</option>
                </select>
            </div>

            <div class="col-md-12">
                <label for="nama" class="form-label">Nama Menu</label>
                <input type="text" name="nama" id="nama" class="form-control" value="<?= esc(old('nama', $menu['nama'] ?? '')) ?>">
            </div>

            <div class="col-md-12">
                <label for="deskripsi" class="form-label">Deskripsi</label>
                <textarea name="deskripsi" id="deskripsi" class="form-control" rows="4"><?= esc(old('deskripsi', $menu['deskripsi'] ?? '')) ?></textarea>
            </div>

            <div class="col-md-6">
                <label for="harga" class="form-label">Harga</label>
                <input type="text" name="harga" id="harga" class="form-control" value="<?= esc(old('harga', $hargaDisplay ?? ($menu['harga'] ?? ''))) ?>" inputmode="numeric" placeholder="Contoh: 150000">
            </div>

            <div class="col-md-6">
                <label for="diskon" class="form-label">Diskon (%)</label>
                <div class="input-group">
                    <input type="number" name="diskon" id="diskon" class="form-control" min="0" max="100" step="1" value="<?= esc(old('diskon', $menu['diskon'] ?? 0)) ?>" placeholder="0">
                    <span class="input-group-text">%</span>
                </div>
                <div class="form-text">Kosongkan atau isi 0 jika menu tidak sedang diskon.</div>
            </div>

            <div class="col-md-6">
                <label for="url_gambar" class="form-label">Gambar Menu</label>
                <input type="file" name="url_gambar" id="url_gambar" class="form-control" accept="image/*">
                <div class="form-text">Pilih gambar dari galeri perangkat Anda.</div>
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="<?= site_url('menu') ?>" class="btn btn-outline-secondary ms-2">Batal</a>
            </div>
        <?= form_close() ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const categorySelect = document.getElementById('id_kategori');
    if (categorySelect) {
        categorySelect.addEventListener('change', function () {
            if (this.value === '__add_category__') {
                window.location.href = '<?= site_url('kategori/create') ?>' + '?return=' + encodeURIComponent('<?= '/menu/create' ?>');
            }
        });
    }

    const harga = document.getElementById('harga');
    if (harga) {
        const formatHarga = function () {
            const raw = String(harga.value).replace(/[^\d]/g, '');
            if (!raw) {
                harga.value = '';
                return;
            }

            harga.value = raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        };

        harga.addEventListener('input', formatHarga);
        harga.addEventListener('blur', formatHarga);
        formatHarga();
    }
});
</script>

