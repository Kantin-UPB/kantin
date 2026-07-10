<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?= esc($title) ?></h1>
    <a href="<?= site_url('paket-bundling') ?>" class="btn btn-outline-secondary btn-sm">Kembali</a>
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
    .menu-picker {
        max-height: 360px;
        overflow-y: auto;
        border: 1px solid rgba(0,0,0,.1);
        border-radius: 0.5rem;
    }
    .menu-picker-row {
        padding: 0.6rem 1rem;
        border-bottom: 1px solid rgba(0,0,0,.05);
    }
    .menu-picker-row:last-child {
        border-bottom: none;
    }
    .menu-qty {
        width: 80px;
    }
</style>

<div class="card shadow-sm">
    <div class="card-body">
        <?= form_open_multipart('paket-bundling/store', ['class' => 'row g-3']) ?>
            <div class="col-md-12">
                <label for="nama_paket" class="form-label">Nama Paket</label>
                <input type="text" name="nama_paket" id="nama_paket" class="form-control" value="<?= esc(old('nama_paket', $paket['nama_paket'] ?? '')) ?>" placeholder="Contoh: Paket Hemat Makan Siang">
            </div>

            <div class="col-md-12">
                <label for="deskripsi" class="form-label">Deskripsi</label>
                <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3"><?= esc(old('deskripsi', $paket['deskripsi'] ?? '')) ?></textarea>
            </div>

            <div class="col-md-6">
                <label for="harga_paket" class="form-label">Harga Paket (Rp)</label>
                <input type="text" name="harga_paket" id="harga_paket" class="form-control" value="<?= esc(old('harga_paket', $hargaDisplay ?? ($paket['harga_paket'] ?? ''))) ?>" inputmode="numeric" placeholder="Contoh: 25000">
                <div class="form-text">Harga jual spesial untuk seluruh isi paket.</div>
            </div>

            <div class="col-md-6">
                <label for="url_gambar" class="form-label">Gambar Paket</label>
                <input type="file" name="url_gambar" id="url_gambar" class="form-control" accept="image/*">
                <div class="form-text">Opsional, pilih gambar dari galeri perangkat Anda.</div>
            </div>

            <div class="col-12">
                <label class="form-label">Pilih Menu untuk Paket</label>
                <div class="menu-picker">
                    <?php if (! empty($availableMenus)): ?>
                        <?php foreach ($availableMenus as $menuItem): ?>
                            <div class="menu-picker-row d-flex align-items-center justify-content-between">
                                <div class="form-check">
                                    <input class="form-check-input menu-check" type="checkbox" name="id_menu[]" value="<?= esc($menuItem['id']) ?>" id="menu-<?= esc($menuItem['id']) ?>" data-harga="<?= esc((int) $menuItem['harga']) ?>" data-nama="<?= esc($menuItem['nama']) ?>">
                                    <label class="form-check-label" for="menu-<?= esc($menuItem['id']) ?>">
                                        <?= esc($menuItem['nama']) ?>
                                        <span class="text-muted small">(<?= esc(format_rupiah((float) $menuItem['harga'])) ?>)</span>
                                    </label>
                                </div>
                                <input type="number" name="qty[]" class="form-control form-control-sm menu-qty" value="1" min="1">
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="p-3 text-muted small">Belum ada menu aktif. Tambahkan menu terlebih dahulu di halaman Menu.</div>
                    <?php endif; ?>
                </div>
                <div class="form-text">Centang menu yang termasuk dalam paket, lalu atur jumlah (qty) masing-masing.</div>
            </div>

            <div class="col-12">
                <div class="alert alert-info d-flex justify-content-between align-items-center mb-0">
                    <span>Total harga normal menu terpilih:</span>
                    <strong id="totalNormalDisplay">Rp 0</strong>
                </div>
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="<?= site_url('paket-bundling') ?>" class="btn btn-outline-secondary ms-2">Batal</a>
            </div>
        <?= form_close() ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const harga = document.getElementById('harga_paket');
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

    const totalDisplay = document.getElementById('totalNormalDisplay');
    const formatRupiah = function (value) {
        return 'Rp ' + Number(value || 0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    };

    const recalcTotal = function () {
        let total = 0;
        document.querySelectorAll('.menu-check:checked').forEach(function (checkbox) {
            const row = checkbox.closest('.menu-picker-row');
            const qtyInput = row ? row.querySelector('.menu-qty') : null;
            const qty = qtyInput ? parseInt(qtyInput.value || '1', 10) : 1;
            total += parseInt(checkbox.dataset.harga || '0', 10) * qty;
        });
        totalDisplay.textContent = formatRupiah(total);
    };

    document.querySelectorAll('.menu-check, .menu-qty').forEach(function (el) {
        el.addEventListener('change', recalcTotal);
        el.addEventListener('input', recalcTotal);
    });

    recalcTotal();
});
</script>
