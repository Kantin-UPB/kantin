<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <div>
        <h1 class="h2 mb-1">Daftar Kategori</h1>
        <p class="text-muted mb-0">Kelola kategori menu untuk Kantin_SI_UPB.</p>
    </div>
    <a href="<?= site_url('kategori/create') ?>" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-circle me-1"></i> Tambah Kategori
    </a>
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

<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nama Kategori</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (! empty($kategori)): ?>
                        <?php foreach ($kategori as $item): ?>
                            <tr>
                                <td class="fw-semibold text-muted">#<?= esc($item['id_kategori']) ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-primary-subtle text-primary">
                                            <i class="bi bi-tags"></i>
                                        </span>
                                        <span><?= esc($item['nama_kategori']) ?></span>
                                    </div>
                                </td>
                                <td class="text-end" style="min-width: 200px;">
                                    <a href="<?= site_url('kategori/edit/' . $item['id_kategori']) ?>" class="btn btn-outline-primary me-2" style="padding: 0.5rem 1rem; font-size: 0.95rem;"><i class="bi bi-pencil-square me-1"></i>Edit</a>
                                    <a href="<?= site_url('kategori/delete/' . $item['id_kategori']) ?>" class="btn btn-outline-danger" style="padding: 0.5rem 1rem; font-size: 0.95rem;" onclick="return confirm('Yakin ingin menghapus kategori ini?')"><i class="bi bi-trash me-1"></i>Hapus</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center text-muted py-5">Belum ada kategori. Silakan tambahkan yang pertama.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>