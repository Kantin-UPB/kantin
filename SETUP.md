## Langkah-langkah Setup

### 0. Git Workflow (Sebelum Memulai)

Sebelum memulai pengerjaan, lakukan langkah-langkah berikut:

```bash
# 1. Pull dari branch master
git pull origin master

# 2. Buat branch baru dengan nama mahasiswa
git checkout -b <nama_mahasiswa>

# 3. Kerjakan fitur, lalu commit
git add .
git commit -m "<deskripsi fitur yang dikerjakan>"

# 4. Pull lagi dari master sebelum push (untuk sinkronisasi terbaru)
git pull origin master

# 5. Jika ada masalah rebase conflict, gunakan --no-rebase
git pull origin master --no-rebase

# 6. Push branch ke remote
git push origin <nama_mahasiswa>
```

**Contoh:**
```bash
git checkout -b john_doe
# Setelah mengerjakan fitur login
git add .
git commit -m "fitur: implementasi login user dengan session"

# Sebelum push, pull lagi dari master
git pull origin master

# Jika ada conflict rebase, gunakan --no-rebase
git pull origin master --no-rebase

# Push branch ke remote
git push origin john_doe
```

**Catatan:**
- Selalu pull dari master sebelum push untuk menghindari conflict
- Jika mengalami masalah rebase, gunakan flag `--no-rebase` untuk bypass rebase otomatis

### 1. Rename Environment File
```bash
rename env jadi .env
```

### 2. Konfigurasi Database di `.env`

Ubah bagian database sesuai dengan kredensial Anda:

```ini
database.default.hostname = 52.237.90.199
database.default.database = kantin_<NPM_Mahasiswa>
database.default.username = <NPM_Mahasiswa>
database.default.password = <Password>
database.default.DBDriver = MySQLi
database.default.DBPrefix =
database.default.port = 3306
```

### 3. Install Dependencies
```bash
composer install
```

### 4. Jalankan Migration
```bash
php spark migrate
```

### 5. Seed Database
```bash
php spark db:seed UserSeeder
```

**Sample Akun Admin Default:**
- Username: `Admin`
- Password: `Kantin123456UPB`

## Menjalankan Aplikasi

```bash
php spark serve
```

Aplikasi dapat diakses di: http://localhost:8080



## Frontend Setup

### Bootstrap Framework
Project ini menggunakan **Bootstrap 5** sebagai framework CSS utama. Tidak perlu instalasi tambahan karena Bootstrap sudah di-load melalui CDN.

### Session Management
Project ini menggunakan **CodeIgniter 4 Built-in Session** untuk manajemen session.

**Contoh Penggunaan Session:**
```php
// Set session
$session = session();
$session->set('username', 'Admin');
$session->set('isLoggedIn', true);

// Get session
$username = $session->get('username');
$isLoggedIn = $session->get('isLoggedIn');

// Remove session
$session->remove('username');

// Destroy all session
$session->destroy();

// Flash message (untuk notifikasi one-time)
$session->setFlashdata('success', 'Data berhasil disimpan');
```

**Contoh di View (menampilkan flash message):**
```php
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
```

## Dokumentasi Perintah - Perintah Umum

```bash
# Migration
php spark migrate
php spark migrate:rollback

# Seeder
php spark db:seed UserSeeder

# Generate Code
php spark make:model ModelName
php spark make:controller ControllerName
php spark make:migration MigrationName
```

<!-- Semangat! -->