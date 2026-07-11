<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profil Mahasiswa - Kantin UPB</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <!-- Navbar Minimalis -->
    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="<?= base_url('index.php/pesan') ?>">🏪 Kantin Client</a>
            <a class="btn btn-outline-light btn-sm" href="<?= base_url('index.php/pesan') ?>">Kembali ke Menu</a>
        </div>
    </nav>

    <!-- Info Detail Profil -->
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white text-center py-3">
                        <h5 class="mb-0">Profil Pengguna</h5>
                    </div>
                    <div class="card-body p-4 text-center">
                        <!-- Avatar Default -->
                        <div class="rounded-circle bg-secondary text-white mx-auto d-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; font-size: 32px;">
                            👤
                        </div>
                        
                        <h4 class="card-title text-capitalize mb-1">Mahasiswa / Pembeli</h4>
                        <p class="text-muted small mb-4">Role: <?= esc($user['role']) ?></p>

                        <hr>

                        <!-- Tabel Detail[cite: 1] -->
                        <div class="text-start">
                            <div class="mb-3">
                                <label class="text-muted small d-block">Nomor Pokok Mahasiswa (NPM)</label>
                                <span class="fw-bold fs-5"><?= esc($user['npm']) ?></span>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small d-block">Status Akun</label>
                                <span class="badge bg-success">Aktif (Dual-Auth Terverifikasi)</span>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small d-block">Tanggal Terdaftar</label>
                                <span><?= esc($user['createdat'] ?? 'Baru saja') ?></span>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small d-block">Saldo Poin</label>
                                <span class="badge bg-warning text-dark fs-6">
                                    <i class="bi bi-coin"></i> <?= number_format((int) ($user['saldo_poin'] ?? 0), 0, ',', '.') ?> poin
                                </span>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        <a href="<?= base_url('index.php/mahasiswa/logout') ?>" class="btn btn-danger w-100">Keluar / Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>