# Dokumentasi Fitur: Login Backoffice

> **NPM:** 231510019
> **Nama:** Jovan
> **Fitur:** Login Backoffice (Auth)
> **Project:** Kantin UPB - CodeIgniter 4.7.3
> **Tanggal:** 2 Juli 2026

---

## 1. Deskripsi Fitur

Fitur **Login Backoffice** adalah halaman autentikasi yang digunakan oleh administrator (admin Kantin UPB) untuk masuk ke dashboard backoffice. Halaman ini menerima input **username** (bebas, tanpa format khusus) dan **password**, lalu memverifikasi kredensial terhadap tabel `user` di database.

**Scope fitur (yang dikerjakan saat ini):**
- Halaman login dengan field **username** + **password**
- Proses login dengan validasi & verifikasi password (SHA-256)
- Manajemen session (set session saat login, destroy saat logout)
- Filter proteksi route privat (redirect ke `/login` jika belum login)
- Tombol/menu Logout di sidebar
- Penyimpanan log aktivitas ke tabel `logsystem`

**Di luar scope fitur ini (tidak dikerjakan):**
- Register akun baru (backoffice tidak ada registrasi)
- Login/Register client side dengan NPM (akan dikerjakan terpisah nanti, setelah backend backoffice selesai)

---

## 2. Aturan & Validasi

### 2.1 Field Username

| Rule         | Keterangan                        |
|--------------|-----------------------------------|
| `required`   | Wajib diisi                       |
| `max_length[75]` | Maksimal 75 karakter (sesuai skema tabel) |

Tidak ada validasi format (bebas). Berbeda dengan client side nanti yang akan pakai NPM 9 digit.

### 2.2 Field Password

| Rule       | Keterangan          |
|------------|---------------------|
| `required` | Wajib diisi         |

### 2.3 Verifikasi Kredensial

1. Cari user di tabel `user` dengan `username` yang diinput DAN `login_type = 'backoffice'`
2. Hash password input dengan SHA-256
3. Bandingkan hash dengan `password` di DB menggunakan `hash_equals()` (cegah timing attack)
4. Jika user tidak ditemukan ATAU password tidak match → tampilkan pesan yang sama: **"Username atau password salah."** (cegah user enumeration)

---

## 3. File yang Dikerjakan

### 3.1 File Baru

#### `app/Controllers/Auth.php`
Controller utama autentikasi. Method yang tersedia:
- `login()` — menampilkan halaman login
- `loginProcess()` — memproses form login (validasi → cek kredensial → set session → log)
- `logout()` — destroy session + log aktivitas logout

**Catatan:** Tidak ada method `register()` atau `registerProcess()` — backoffice tidak menyediakan registrasi.

#### `app/Models/UserModel.php`
Model untuk tabel `user`. Method utama:
- `findByUsername(string $username)` — ambil user backoffice by username (filter `login_type = 'backoffice'`)
- `findByNpm(string $npm)` — ambil user mahasiswa by NPM (filter `login_type = 'mahasiswa'`). **Disiapkan untuk client side nanti**, belum dipakai di scope ini.

#### `app/Filters/AuthFilter.php`
Filter CodeIgniter untuk proteksi route privat. Cek session `isLoggedIn`:
- Jika `true` → lanjut ke route
- Jika `false` → redirect ke `/login` dengan flash message "Silakan login terlebih dahulu."

#### `app/Views/Auth/Login.php`
View halaman login. Layout full-page (tanpa sidebar), Bootstrap 5 sesuai template Kantin, gradient background ungu dengan card putih di tengah. Komponen:
- Field Username (input text, max 75 char)
- Field Password (input password, dengan tombol show/hide)
- Flash message untuk error/success
- Badge "Backoffice" untuk membedakan dari login client side nanti

#### `app/Database/Migrations/2026-07-02-100000_AlterUserAddNpmAndLoginType.php`
Migration untuk menambah 2 kolom baru ke tabel `user`:
- `npm` VARCHAR(15) NULL — untuk menyimpan NPM mahasiswa (client side nanti)
- `login_type` ENUM('backoffice', 'mahasiswa') — untuk membedakan tipe user

Tujuan: tabel `user` sudah disiapkan supaya bisa **menampung kedua tipe user** (backoffice pakai username, mahasiswa pakai NPM) tanpa perlu 2 tabel terpisah.

### 3.2 File yang Dimodifikasi

#### `app/Config/Routes.php`
| Route | Method | Filter | Keterangan |
|-------|--------|--------|------------|
| `/` | GET | `auth` | Dashboard (butuh login) |
| `/sample` | GET | `auth` | Halaman sample (butuh login) |
| `/login` | GET, POST | - | Halaman & proses login |
| `/logout` | GET | - | Logout |

#### `app/Config/Filters.php`
Menambahkan alias filter:
```php
'auth' => \App\Filters\AuthFilter::class,
```

#### `app/Controllers/Home.php`
Method `index()` sekarang melewatkan data session (`username`, `role`) ke view untuk ditampilkan di dashboard.

#### `app/Views/Home.php`
Dashboard menampilkan:
- Welcome message dengan username & role dari session
- Flash messages (success/error)
- Icon & deskripsi singkat

#### `app/Views/Layout/Header.php`
- Title sekarang dinamis: `{Page Title} - {App Name}`
- Bersihkan tag `</script>` ganda yang ada di template asli

#### `app/Views/Layout/Menu.php`
- Tambah menu **Logout** di sidebar (desktop & mobile offcanvas)
- Tidak mengubah struktur atau styling lain yang sudah ada

#### `app/Database/Seeds/UserSeeder.php`
Insert 1 akun admin backoffice:
- Username: `Admin`
- Password: `Kantin123456UPB` (SHA-256 hashed)
- Role: `Admin`
- Login type: `backoffice`
- NPM: `NULL`

Seeder aman dijalankan ulang (cek duplikasi sebelum insert).

---

## 4. Skema Database

### 4.1 Tabel `user` (setelah migration)

```sql
CREATE TABLE user (
    id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    username VARCHAR(75) NOT NULL DEFAULT '',       -- backoffice: username bebas; mahasiswa: NULL/kosong
    npm VARCHAR(15) NULL DEFAULT NULL,              -- backoffice: NULL; mahasiswa: 9 digit angka
    password CHAR(64) NOT NULL DEFAULT '',           -- SHA-256 hash (64 char hex)
    role ENUM('Admin', 'Penjual', 'Pembeli') NOT NULL DEFAULT 'Pembeli',
    login_type ENUM('backoffice', 'mahasiswa') NOT NULL DEFAULT 'backoffice',
    createdby INT(10) UNSIGNED NULL DEFAULT 0,
    createdat DATETIME NULL,
    updatedby INT(10) UNSIGNED NULL DEFAULT 0,
    updatedat DATETIME NULL,
    PRIMARY KEY (id),
    UNIQUE INDEX idx_user_npm (npm)
);
```

### 4.2 Cara Kerja Dua Tipe User

| Tipe | `username` | `npm` | `login_type` | Field login |
|------|------------|-------|--------------|-------------|
| Backoffice (Admin/Penjual) | bebas (mis. `Admin`) | `NULL` | `backoffice` | `username` |
| Mahasiswa (client side, nanti) | `NULL`/kosong | 9 digit (mis. `231510019`) | `mahasiswa` | `npm` |

Unique index pada `npm` memastikan tidak ada 2 mahasiswa dengan NPM yang sama. Untuk backoffice, `npm` NULL tidak dihitung unique, jadi boleh banyak user dengan `npm = NULL`.

### 4.3 Tabel `logsystem` (sudah ada, tidak diubah)

Digunakan untuk menyimpan log aktivitas login & logout:
```sql
INSERT INTO logsystem (iduser, module, level, aksi, deskripsi, createdby, createdat)
VALUES (?, 'Auth', ?, 'login'/'logout', ?, ?, NOW());
```

---

## 5. Alur Login Backoffice

```
[User akses /]
       │
       ▼
[AuthFilter: cek session isLoggedIn]
       │
       ├── false ──► [redirect /login]
       │                    │
       │                    ▼
       │              [User input username + password]
       │                    │
       │                    ▼
       │              [POST /login → Auth::loginProcess]
       │                    │
       │                    ▼
       │              [Validasi: username required max 75, password required]
       │                    │
       │              ┌─────┴─────┐
       │              │           │
       │           valid       invalid
       │              │           │
       │              ▼           ▼
       │    [Cari user: WHERE username = ?  [redirect back + errors]
       │                    AND login_type = 'backoffice']
       │              │
       │         ┌────┴────┐
       │         │         │
       │      found    not found
       │         │         │
       │         ▼         ▼
       │  [Cek password   [redirect back:
       │   SHA-256 match   "Username atau password
       │   via hash_equals]  salah"]
       │         │
       │    ┌────┴────┐
       │    │         │
       │ match      no match
       │    │         │
       │    ▼         ▼
       │ [Set session:    [redirect back:
       │  id, username,    "Username atau password
       │  role, login_type,  salah"]
       │  isLoggedIn=true]
       │    │
       │    ▼
       │ [Insert logsystem: aksi=login]
       │    │
       │    ▼
       │ [redirect / + success flash]
       │              │
       └── true ─────┘
              │
              ▼
       [Render Home::index → Dashboard]
```

---

## 6. Alur Logout

```
[User klik menu Logout atau akses /logout]
       │
       ▼
[Auth::logout]
       │
       ▼
[Insert logsystem: aksi=logout, deskripsi="User X logout"]
       │
       ▼
[session()->destroy()]
       │
       ▼
[redirect /login + flash: "Anda telah berhasil logout."]
```
