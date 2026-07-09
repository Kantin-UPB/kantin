# Dokumentasi Fitur Login Backoffice Kantin UPB

| | |
|---|---|
| **NPM** | 231510019 |
| **Nama** | Jovan |
| **Fitur** | Login Page (Backoffice) |
| **Tanggal** | 1 Juli 2026 |
| **Project** | Kantin UPB — CodeIgniter 4 |

---

## 1. Deskripsi Fitur

Halaman login backoffice untuk admin & penjual Kantin UPB. Login menggunakan
**username + password** (bukan NPM), karena halaman ini khusus untuk staf
backoffice, bukan untuk mahasiswa (client side).

Halaman ini adalah gerbang pertama sebelum user bisa mengakses dashboard
Kantin (`/`). User yang belum login akan otomatis di-redirect ke `/login`.

> **Catatan:** Client side (login + register mahasiswa dengan NPM 9-digit)
> belum diimplementasi. Akan dikerjakan setelah backend client side siap,
> supaya ada data yang bisa dipakai. Struktur tabel `user` sudah disiapkan
> untuk menampung kedua tipe user (lihat bagian 5).

---

## 2. Alur Fitur

### 2.1 Alur Login

```
User akses / (dashboard)
       │
       ▼
  ┌──────────────────────┐
  │  AuthFilter cek      │─── sudah login? ── Ya ──► tampilkan dashboard
  │  session isLoggedIn  │
  └──────────────────────┘
       │
       Tidak
       │
       ▼
  Redirect ke /login
       │
       ▼
  Tampilkan form login
  (field: username + password)
       │
       ▼
  User submit form
       │
       ▼
  ┌──────────────────────────────────────────┐
  │  Controller Auth::processLogin()         │
  │  1. Validasi: username & password wajib  │
  │  2. Cari user by username                │
  │  3. Hash password input (SHA-256)        │
  │  4. Bandingkan dengan hash di DB         │
  │     pakai hash_equals() (anti timing     │
  │     attack)                              │
  └──────────────────────────────────────────┘
       │
       ▼
  Cocok?
       │
  Ya ──┴── Tidak
   │         │
   │         ▼
   │    Redirect ke /login
   │    + flash error
   │    "Username atau password salah."
   │
   ▼
  Set session:
    isLoggedIn = true
    userId     = ...
    username   = ...
    role       = ...
       │
       ▼
  Redirect ke / (dashboard)
  + flash success "Login berhasil"
```

### 2.2 Alur Logout

```
User klik tombol Logout (di navbar / sidebar)
       │
       ▼
  GET /logout
       │
       ▼
  Controller Auth::logout()
  → session()->destroy()
       │
       ▼
  Redirect ke /login
```

---

## 3. Validasi

### 3.1 Validasi Server-side (Controller `Auth::processLogin`)

| Field | Aturan | Pesan Error |
|---|---|---|
| `username` | Wajib diisi, max 75 karakter | "Username wajib diisi." |
| `password` | Wajib diisi | "Password wajib diisi." |
| Username + Password cocok dengan record di DB | Cari user by `username`, lalu bandingkan hash password | "Username atau password salah." |

### 3.2 Validasi Client-side (Form HTML)

| Field | Aturan |
|---|---|
| `username` | `required`, `maxlength="75"`, `autocomplete="username"`, autofocus |
| `password` | `required`, `autocomplete="current-password"` |

### 3.3 Catatan Keamanan

- **Password di-hash pakai SHA-256** sesuai kontrak existing project Kantin
  (lihat `UserSeeder` — password `Kantin123456UPB` di-hash jadi
  `50db07de21a105f8...`).
- **`hash_equals()`** dipakai untuk perbandingan hash supaya tahan timing
  attack. Hash input tetap dihitung walau user tidak ditemukan, supaya
  response time tidak membocorkan informasi "username ada / tidak ada".
- **CSRF token** otomatis di-include di form via `csrf_field()`.

> **Catatan reviewer:** SHA-256 tanpa salt cukup lemah untuk password
> production. Sebaiknya migrasi ke `password_hash()` dengan bcrypt atau
> argon2. Tapi untuk konsistensi dengan kontrak Kantin existing (yang
> pakai SHA-256), saya ikuti dulu. Bisa di-upgrade terpisah.

---

## 4. Endpoint & Routing

| Method | Path | Controller Method | Filter | Deskripsi |
|---|---|---|---|---|
| GET, POST | `/login` | `Auth::login` | — | Tampilkan form & proses login backoffice |
| GET | `/logout` | `Auth::logout` | — | Destroy session & redirect ke `/login` |
| GET | `/` | `Home::index` | `auth` | Dashboard Kantin (diproteksi) |
| GET | `/sample` | `Home::Sample` | — | Sample page (asli Kantin, tak diubah) |

> Rute `/register` sengaja **tidak ada** — backoffice tidak punya register.
> Akun admin/penjual dibuat manual oleh superadmin atau lewat seeder.

---

## 5. Struktur Tabel `user`

Tabel `user` didesain untuk menampung **2 tipe user**:

| Kolom | Tipe | Null | Default | Keterangan |
|---|---|---|---|---|
| `id` | INT(11) UNSIGNED | NO | AUTO_INCREMENT | Primary key |
| `username` | VARCHAR(75) | NO | `''` | Login backoffice (Admin/Penjual). Untuk mahasiswa biarkan kosong. |
| `npm` | CHAR(9) | YES | NULL | Login client side (Mahasiswa), 9-digit angka. UNIQUE. Untuk admin biarkan NULL. |
| `password` | CHAR(64) | NO | `''` | SHA-256 hash |
| `role` | ENUM('Admin','Penjual','Pembeli') | NO | `Pembeli` | Role user |
| `createdby` | INT(10) UNSIGNED | YES | 0 | User ID yang create |
| `createdat` | DATETIME | YES | NULL | Timestamp create |
| `updatedby` | INT(10) UNSIGNED | YES | 0 | User ID yang update terakhir |
| `updatedat` | DATETIME | YES | NULL | Timestamp update terakhir |

### Index

- `PRIMARY KEY (id)`
- `UNIQUE INDEX uniq_npm (npm)` — NPM boleh NULL (UNIQUE di MySQL
  memperbolehkan multiple NULL), jadi admin backoffice tanpa NPM tidak
  akan konflik.

### Catatan Dual-Auth

- **Backoffice (saat ini):** login pakai `username` + password.
  Method: `UserModel::findByUsername()`.
- **Client side (nanti):** login + register pakai `npm` (9-digit) + password.
  Method: `UserModel::findByNpm()` — sudah disediakan, siap dipakai saat
  backend client side diimplementasi.

---



10/07/2026

# Dokumentasi — Fitur Login Client Side (Mahasiswa)

Tambahan fitur **login mahasiswa (client side)** untuk project Kantin UPB. Tidak mengubah fitur backoffice yang sudah ada — hanya menambah.

## File yang Ditambah / Diubah

| File | Status | Fungsi |
|------|--------|--------|
| `app/Controllers/ClientAuth.php` | **NEW** | Controller login/register/logout mahasiswa (NPM + password, SHA-256 hash, role otomatis Pembeli, login_type mahasiswa) |
| `app/Filters/ClientAuthFilter.php` | **NEW** | Filter proteksi halaman client — cek `isLoggedIn` + `login_type=mahasiswa`, redirect ke `/mahasiswa/login` kalau gagal |
| `app/Views/client/login.php` | **NEW** | View login mahasiswa — styling identik dengan backoffice (gradient ungu, Bootstrap 5, Bootstrap Icons) |
| `app/Views/client/register.php` | **NEW** | View register mahasiswa — field NPM (9 digit) + password + konfirmasi password |
| `app/Config/Routes.php` | **MODIFIED** | `/` & `/pesan` pakai filter `clientauth`. Backoffice dashboard pindah ke `/admin` (filter `auth`). Tambah rute `/mahasiswa/*`. |
| `app/Config/Filters.php` | **MODIFIED** | Daftarkan alias `clientauth` → `ClientAuthFilter::class` |
| `app/Filters/AuthFilter.php` | **PATCHED** | Strict separation: cek `login_type=backoffice`. Mahasiswa di-redirect ke `/` kalau coba akses backoffice. |
| `app/Controllers/Auth.php` | **PATCHED** | Bug fix logout flash + redirect ke `/admin` (bukan `/`) setelah login backoffice |
| `app/Controllers/Pesan.php` | **RENAMED** | Dari `pesan.php` ke `Pesan.php` (PSR-4 case sensitivity) |
| `app/Views/client/layout.php` | **PATCHED** | Tambah blok flash message (alert success/danger) |
| `app/Views/Layout/Menu.php` | **PATCHED** | Link dashboard di sidebar (navbar brand, offcanvas, desktop) dari `/` ke `/admin` |

## Rute Baru

| Method | Path | Filter | Keterangan |
|--------|------|--------|------------|
| GET | `/` | `clientauth` | Halaman client (katalog menu) — wajib login mahasiswa |
| GET | `/pesan` | `clientauth` | Halaman pemesanan — wajib login mahasiswa |
| GET/POST | `/mahasiswa/login` | (public) | Login mahasiswa |
| GET/POST | `/mahasiswa/register` | (public) | Daftar akun mahasiswa |
| GET | `/mahasiswa/logout` | (public) | Logout mahasiswa |
| GET | `/admin` | `auth` | Backoffice dashboard — wajib login backoffice (Admin/Penjual) |
| GET/POST | `/login` | (public) | Login backoffice |
| GET | `/logout` | (public) | Logout backoffice |
| GET | `/menu`, `/kategori`, `/meja`, `/manage-poin/*` | `auth` | Halaman backoffice lainnya |

## Pemisahan Strict (Login Required)

| User State | Bisa Akses | Tidak Bisa Akses |
|------------|------------|------------------|
| Belum login | `/login`, `/mahasiswa/login`, `/mahasiswa/register`, `/mahasiswa/logout`, `/logout` | `/`, `/pesan`, `/admin`, `/menu`, `/kategori`, dll — semua redirect ke login page yang sesuai |
| Login sebagai mahasiswa | `/`, `/pesan`, `/mahasiswa/logout` | `/admin`, `/menu`, `/kategori`, dll — redirect ke `/` dengan flash error "Akses backoffice hanya untuk Admin/Penjual" |
| Login sebagai backoffice (Admin/Penjual) | `/admin`, `/menu`, `/kategori`, `/meja`, `/manage-poin/*`, `/logout` | `/`, `/pesan` — redirect ke `/mahasiswa/login` (karena filter `clientauth` cek `login_type=mahasiswa`) |

## Alur Penggunaan

- User buka `http://localhost:8080/` → **redirect ke `/mahasiswa/login`** (karena belum login mahasiswa)
- User daftar akun di `/mahasiswa/register` → login di `/mahasiswa/login` → masuk ke `/` (katalog menu)
- User mau akses backoffice → ketik `http://localhost:8080/admin` → kalau belum login backoffice, redirect ke `/login`
- User login backoffice (`Admin` / `Kantin123456UPB`) → masuk ke `/admin` (dashboard)
- Mahasiswa coba akses `/admin` → redirect ke `/` dengan error "Akses backoffice hanya untuk Admin/Penjual"
- Backoffice user coba akses `/` → redirect ke `/mahasiswa/login` (harus login mahasiswa dulu)

## Aturan Validasi

- **NPM**: tepat 9 digit angka (regex `^[0-9]{9}$`, auto-strip karakter non-digit saat input)
- **Password**: minimal 8 karakter
- **Konfirmasi password**: wajib cocok dengan password
- **NPM unik**: tidak boleh sama dengan NPM yang sudah terdaftar
- **Password hash**: SHA-256 (sama dengan kontrak backoffice)
- **Role otomatis**: `Pembeli` untuk mahasiswa baru
- **Login type**: `mahasiswa` (dipisah dari `backoffice`)

## Styling

Login & register page mahasiswa **identik** dengan backoffice:
- Background: gradient ungu `linear-gradient(135deg, #667eea 0%, #764ba2 100%)`
- Card putih 420px dengan border-radius 1rem + shadow
- Brand-icon bulat dengan gradient ungu (icon: `bi-mortarboard` untuk login, `bi-person-plus` untuk register)
- Badge "Mahasiswa" (vs "Backoffice" di backoffice)
- Bootstrap 5 + Bootstrap Icons (CDN)
- Form input-group dengan icon prepend
- Toggle password show/hide (icon `bi-eye` ↔ `bi-eye-slash`)

## Akun Demo

| Tipe | URL Login | Kredensial |
|------|-----------|------------|
| Backoffice (Admin) | `/login` | Username: `Admin`, Password: `Kantin123456UPB` |
| Mahasiswa | `/mahasiswa/login` | Buat dulu di `/mahasiswa/register` (NPM 9 digit + password 8+ karakter) |

## Bug Fixes (selama sandbox testing)

1. **`session()->destroy()` hapus flashdata** — logout redirect pakai `with('success', ...)` tapi flash hilang. Fix: ganti ke `session()->remove(...)` + `session()->regenerate(true)`.
2. **`pesan.php` case mismatch** — composer warning "does not comply with psr-4". Fix: rename ke `Pesan.php`.
3. **`client/layout.php` tidak render flash** — alert success/danger tidak muncul setelah redirect. Fix: tambah blok `<?php if (session()->getFlashdata(...))` sebelum `renderSection('content')`.
4. **Migration `AlterUserAddNpmAndLoginType` duplicate column** — konflik dengan `RefactorUserForDualAuth`. Fix: cek `getFieldData()` dulu sebelum alter table.

## Alur Login Mahasiswa

```
[mahasiswa buka /mahasiswa/register]
        ↓
[isi NPM (9 digit) + password + confirm]
        ↓
[submit POST /mahasiswa/register]
        ↓
[validasi server-side: NPM regex + unique, password min 8, confirm match]
        ↓ (gagal) → redirect back + flash error
        ↓ (sukses) → insert user (role=Pembeli, login_type=mahasiswa)
        ↓
[redirect /mahasiswa/login + flash "Registrasi berhasil"]
        ↓
[mahasiswa isi NPM + password]
        ↓
[submit POST /mahasiswa/login]
        ↓
[validasi: NPM regex, password wajib]
[cek user by NPM + login_type=mahasiswa]
[bandingkan hash SHA-256 dengan hash_equals() anti timing-attack]
        ↓ (gagal) → redirect back + flash "NPM atau password salah"
        ↓ (sukses) → set session, log ke logsystem
        ↓
[redirect /pesan (katalog menu + keranjang)]
```