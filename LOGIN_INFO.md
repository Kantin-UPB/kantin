# Login Page — Kantin UPB

Dokumentasi singkat fitur **Login Page** yang ditambahkan ke project Kantin UPB.

## Yang Ditambahkan (Scope Login Page Saja)

| File | Status | Keterangan |
|------|--------|------------|
| `app/Database/Migrations/2026-07-01-000000_AlterUserNpm.php` | **NEW** | Alter tabel `user`: ganti kolom `username VARCHAR(75)` → `npm CHAR(9)` + `UNIQUE KEY uniq_npm`. |
| `app/Database/Seeds/UserSeeder.php` | **MODIFIED** | Seeder admin diubah: pakai field `npm` (NPM=`123456789`) daripada `username`. Password `Kantin123456UPB` tak berubah (SHA-256). |
| `app/Models/UserModel.php` | **NEW** | Model untuk tabel `user`. Punya method `findByNpm(string $npm)`. |
| `app/Controllers/Auth.php` | **NEW** | Controller login/register/logout. Validasi NPM 9-digit (server-side regex `/^[0-9]{9}$/`). Password di-hash pakai SHA-256 sesuai kontrak Kantin. Role default register = `Pembeli`. |
| `app/Filters/AuthFilter.php` | **NEW** | Filter session guard — cek `isLoggedIn`. Belum login → redirect ke `/login`. |
| `app/Config/Filters.php` | **MODIFIED** | Daftarkan alias filter `auth` => `AuthFilter::class`. |
| `app/Config/Routes.php` | **MODIFIED** | Tambah rute `/login`, `/register`, `/logout`. Rute `/` dipasang filter `auth`. |
| `app/Views/Auth/Login.php` | **NEW** | View login — field NPM (9-digit) + password + tombol show/hide password + demo hint. |
| `app/Views/Auth/Register.php` | **NEW** | View register — field NPM (9-digit) + password + konfirmasi password. |
| `app/Views/Layout/Menu.php` | **MODIFIED** | Tambah button Logout di navbar (kanan atas) + link Logout di sidebar desktop & offcanvas mobile. Conditional: hanya tampil kalau user sudah login. Juga tampilkan info NPM + role badge. **Perubahan ini atas request eksplisit user setelah submit awal** — elemen template yang sudah ada (link Dashboard/Data/Transaction/Report) tetap utuh. |
| `public/assets/css/auth.css` | **NEW** | Stylesheet halaman auth (clean minimal Linear/Stripe-inspired, warna biru Bootstrap selaras Kantin). |
| `LOGIN_INFO.md` | **NEW** | Dokumen ini. |

## File Kantin yang TIDAK Diubah (sesuai rules)

- `app/Views/Layout/Header.php`
- `app/Views/Layout/Footer.php`
- `app/Views/Home.php`
- `app/Views/SamplePage.php`
- `app/Controllers/Home.php`
- `app/Helpers/Func_helper.php`
- `app/Database/Migrations/2026-06-26-191114_CreateUser.php` (migration CreateUser original tetap utuh — `npm` di-handle migration baru `AlterUserNpm`)
- `app/Database/Migrations/2026-06-27-091114_CreateLogsystem.php`

Verifikasi: jalankan `git diff` di folder project — file template Kantin tidak ada perubahan.

## Aturan yang Diterapkan

1. ✅ Login & register pakai **NPM + password** (bukan email).
2. ✅ NPM **tepat 9 digit angka** — gagal validasi kalau kurang/lebih atau non-digit.
   - Client-side: `pattern="[0-9]{9}"`, `minlength="9"`, `maxlength="9"`, `inputmode="numeric"`, plus JS auto-strip karakter non-digit.
   - Server-side: regex `preg_match('/^[0-9]{9}$/', $npm)`.
3. ✅ Database tetap pakai tabel `user` dengan field `npm` (CHAR 9, UNIQUE).
4. ✅ Template Kantin (Header/Menu/Footer/Home) **tidak diubah**.
5. ✅ Password disimpan sebagai `hash('sha256', $plaintext)` — sama dengan kontrak existing project Kantin (lihat UserSeeder).
6. ✅ User baru register → role otomatis `Pembeli`.
7. ✅ Session guard: rute `/` (dashboard) diproteksi filter `auth`. Belum login → redirect ke `/login`.

## Setup & Run

### 1. Konfigurasi `.env`

Edit file `.env` di root project, sesuaikan kredensial database Anda:

```ini
database.default.hostname = 127.0.0.1
database.default.database = kantin_login
database.default.username = <user_mysql_anda>
database.default.password = <password_mysql_anda>
database.default.DBDriver = MySQLi
database.default.port = 3306
```

> Untuk testing di sandbox lokal: hostname=`127.0.0.1`, user=`kantin`, password=`kantinpass`, db=`kantin_login`.

### 2. Install Dependencies

```bash
composer install
```

### 3. Jalankan Migration

```bash
php spark migrate
```

Urutan migration yang akan jalan:
1. `2026-06-26-191114_CreateUser` — buat tabel `user` dengan kolom `username VARCHAR(75)`.
2. `2026-06-27-091114_CreateLogsystem` — buat tabel logsystem.
3. `2026-07-01-000000_AlterUserNpm` — **baru**: rename `username` → `npm CHAR(9)` + UNIQUE index.

### 4. Seed Database

```bash
php spark db:seed UserSeeder
```

### 5. Jalankan Server

```bash
php spark serve
```

Buka browser ke `http://localhost:8080`.

## Akun Demo

Setelah `db:seed UserSeeder`, akun admin default:

| Field | Value |
|-------|-------|
| NPM | `123456789` |
| Password | `Kantin123456UPB` |
| Role | `Admin` |

> **Catatan:** Admin asli di kampus tidak punya NPM mahasiswa, jadi NPM `123456789` hanya placeholder 9-digit untuk login akun admin default. Tim bisa ganti NPM admin ini lewat query SQL manual kalau perlu.

## Rute

| Method | Path | Controller | Filter | Keterangan |
|--------|------|------------|--------|------------|
| GET/POST | `/login` | `Auth::login` | — | Halaman & proses login |
| GET/POST | `/register` | `Auth::register` | — | Halaman & proses registrasi |
| GET | `/logout` | `Auth::logout` | — | Destroy session & redirect ke `/login` |
| GET | `/` | `Home::index` | **auth** | Dashboard Kantin (asli, tak diubah) |
| GET | `/sample` | `Home::Sample` | — | Sample page (asli, tak diubah) |

## Alur Validasi

### Login (`POST /login`)

1. NPM wajib tepat 9 digit angka → kalau gagal, redirect ke `/login` + flash error `NPM harus tepat 9 digit angka.`
2. Password wajib diisi → kalau kosong, redirect + flash error `Password wajib diisi.`
3. Cari user by NPM. Bandingkan `hash('sha256', $password_input)` dengan `$user['password']` pakai `hash_equals()` (cegah timing attack).
4. Kalau user tidak ada ATAU password tidak cocok → redirect + flash error `NPM atau password salah.`
5. Kalau sukses → set session `isLoggedIn=true`, `userId`, `npm`, `role`. Redirect ke `/`.

### Register (`POST /register`)

1. NPM wajib 9 digit angka → kalau gagal, redirect + flash error `NPM harus tepat 9 digit angka (0-9).`
2. Password minimal 8 karakter → kalau kurang, redirect + flash error `Password minimal 8 karakter.`
3. Password dan konfirmasi harus cocok → kalau mismatch, redirect + flash error `Konfirmasi password tidak cocok.`
4. NPM harus unik → kalau sudah dipakai, redirect + flash error `NPM sudah terdaftar. Silakan login atau gunakan NPM lain.`
5. Kalau semua valid → insert user baru dengan role `Pembeli`, password SHA-256 hash. Redirect ke `/login` + flash success `Registrasi berhasil. Silakan login dengan NPM & password Anda.`

## QC Test Results

Automated test dengan 30 skenario (lihat `scripts/qc_login.sh`):

```
[1]  GET / (not logged in) — redirect ke /login                       PASS
[2]  GET /login — form NPM + password, no email field                  PASS (6 sub-checks)
[3]  GET /register — form NPM + password + confirm, no email           PASS (3 sub-checks)
[4]  POST /login wrong password — ditolak                              PASS
[5]  POST /login NPM 8-digit — ditolak dengan pesan validasi           PASS
[6]  POST /login admin benar — sukses redirect ke /                    PASS
[7]  GET / setelah login — bisa akses dashboard                        PASS
[8]  GET /logout — destroy session & redirect                          PASS
[9]  GET / setelah logout — balik redirect ke /login                   PASS
[10] POST /register NPM valid — sukses & redirect ke /login            PASS
[11] Login dengan user baru diregister — sukses                        PASS
[12] User baru tersimpan di DB dengan role=Pembeli                     PASS
[13] Register dengan NPM dobel — ditolak                               PASS
[14] Register dengan NPM 10-digit — ditolak dengan pesan validasi      PASS
[15] Register dengan password mismatch — ditolak                       PASS
[16] Verifikasi tabel user: ada npm, tidak ada username/email          PASS
[17] Verifikasi template Kantin (Header/Menu/Footer/Home) tidak diubah PASS

HASIL: 30 passed, 0 failed
```

## Catatan untuk Tim

- **Untuk anggota tim lain yang sudah run migration lama (buat tabel `user` dengan `username`):** migration `AlterUserNpm` akan otomatis rename kolom `username` → `npm` saat jalan. Data lama di kolom `username` akan dipindah ke kolom `npm` (tapi mungkin value-nya bukan 9-digit untuk admin lama, jadi perlu update manual kalau ada admin lama yang username-nya bukan 9-digit angka).
- **Logout button:** sudah ditambahkan ke `Layout/Menu.php` (navbar atas + sidebar desktop + sidebar mobile offcanvas). Cuma muncul kalau user sudah login. Tampilkan NPM + role badge juga biar user tahu siapa yang lagi login.
- **CSRF:** form sudah include `csrf_field()`. CI4's CSRF filter belum di-enable globally (sesuai konfigurasi asli Kantin). Bisa di-enable nanti di `Config\Filters` kalau diperlukan.

## Update — Penambahan Button Logout (Atas Request User)

Setelah submit awal, user minta tambah button Logout di dashboard. Modifikasi `app/Views/Layout/Menu.php`:

1. **Navbar atas (kanan):** button outline-light dengan icon `bi-box-arrow-right`. Sebelum button ada info NPM + badge role (text-light small, hidden di layar <576px).
2. **Sidebar desktop (paling bawah):** link `nav-link text-danger` dengan icon `bi-box-arrow-right`. Dipisah dari menu utama pakai `<hr>`.
3. **Offcanvas mobile sidebar (paling bawah):** sama seperti sidebar desktop — link logout dengan text-danger.

Semua elemen dibungkus `<?php if (session()->get('isLoggedIn')): ?>` jadi cuma muncul setelah login. Elemen template Kantin yang sudah ada (link Dashboard, Data, Transaction, Report) **tidak diubah/dihapus** — hanya disisip elemen baru di bawahnya.
