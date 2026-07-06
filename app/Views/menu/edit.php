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
        <?= form_open_multipart('menu/update/' . $menu['id'], ['class' => 'row g-3']) ?>
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
                <input type="text" name="harga" id="harga" class="form-control" value="<?= esc(old('harga', $menu['harga_display'] ?? ($menu['harga'] ?? ''))) ?>" inputmode="numeric" placeholder="Contoh: 150000">
            </div>

            <div class="col-md-6">
                <label for="url_gambar" class="form-label">Gambar Menu</label>
                <input type="hidden" name="existing_url_gambar" value="<?= esc($menu['url_gambar'] ?? '') ?>">
                <?php if (! empty($menu['url_gambar'])): ?>
                    <div class="mb-2">
                        <img src="<?= base_url($menu['url_gambar']) ?>" alt="<?= esc($menu['nama'] ?? 'Menu') ?>" class="menu-image-preview">
                    </div>
                    <div class="small text-muted mb-2">
                        File saat ini:
                        <a href="<?= base_url($menu['url_gambar']) ?>" target="_blank" rel="noopener noreferrer">
                            <?= esc(basename($menu['url_gambar'])) ?>
                        </a>
                    </div>
                <?php else: ?>
                    <div class="small text-muted mb-2">Belum ada gambar.</div>
                <?php endif; ?>
                <input type="file" name="url_gambar" id="url_gambar" class="form-control" accept="image/*">
                <div class="form-text">Pilih gambar dari galeri perangkat Anda. Jika dikosongkan, gambar lama akan tetap dipertahankan.</div>
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-primary">Update</button>
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
                window.location.href = '<?= site_url('kategori') ?>';
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
