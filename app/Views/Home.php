<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard</h1>
    <?php if (session()->get('username')): ?>
        <div class="btn-toolbar mb-2 mb-md-0">
            <span class="badge bg-primary fs-6">
                <i class="bi bi-person-circle me-1"></i>
                <?= esc(session()->get('username')) ?>
                <span class="badge bg-light text-dark ms-1"><?= esc(session()->get('role')) ?></span>
            </span>
        </div>
    <?php endif; ?>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-md-6 mx-auto text-center py-5">
        <i class="bi bi-shop" style="font-size: 5rem; color: #0d6efd;"></i>
        <h2 class="mt-3">Selamat Datang di Kantin UPB</h2>
        <p class="text-muted">Anda berhasil login sebagai <strong><?= esc(session()->get('role')) ?></strong>.</p>
        <p class="text-muted">Silakan pilih menu di sidebar untuk mulai menggunakan aplikasi.</p>
    </div>
</div>
