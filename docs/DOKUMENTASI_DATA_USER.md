# Dokumentasi Modul Data User — Kantin UPB

Dokumen ini menjelaskan struktur data, fitur, alur (flow), dan logic dari modul **User** pada project `Kantin-UPB/kantin`, disusun berdasarkan isi source code aktual (migration, model, controller, filter, dan routes).

---

## 1. Ringkasan

Modul user pada project ini dirancang untuk menampung **dua tipe user** dalam satu tabel (`user`), dibedakan lewat kolom `login_type`:

| Tipe User | `login_type` | Login Pakai | Role Default | Bisa Register Sendiri? |
|---|---|---|---|---|
| Backoffice (Admin/Penjual) | `backoffice` | `username` (bebas) | `Admin` / `Penjual` | ❌ Tidak, akun dibuat manual (seeder/DB) |
| Mahasiswa (Client Side) | `mahasiswa` | `npm` (9 digit angka) | `Pembeli` | ✅ Ya, lewat halaman register |

Kedua alur login **terpisah total** — controller, filter, session guard, dan halaman redirect-nya berbeda, meskipun sama-sama membaca/menulis ke tabel `user` yang sama.

---

## 2. Skema Database — Tabel `user`

Struktur final tabel `user` adalah hasil dari **3 migration** yang berjalan berurutan (lihat riwayat di bagian 7). Struktur akhirnya:

| Kolom | Tipe | Null | Default | Keterangan |
|---|---|---|---|---|
| `id` | `INT(11) UNSIGNED` | No | Auto Increment | Primary key |
| `username` | `VARCHAR(75)` | No | `''` | Login backoffice. Diisi string kosong `''` untuk akun mahasiswa |
| `npm` | `VARCHAR(15)` *(lihat catatan)* | Yes | `NULL` | Nomor Pokok Mahasiswa, 9 digit angka. `NULL` untuk akun backoffice. **UNIQUE** |
| `password` | `CHAR(64)` | No | `''` | Hash **SHA-256** dari password plaintext |
| `role` | `ENUM('Admin','Penjual','Pembeli')` | No | `'Pembeli'` | Hak akses/peran user |
| `login_type` | `ENUM('backoffice','mahasiswa')` | No | `'backoffice'` | Menentukan jalur autentikasi mana yang berlaku |
| `createdby` | `INT(10) UNSIGNED` | Yes | `0` | ID user yang membuat baris ini |
| `createdat` | `DATETIME` | Yes | — | Timestamp dibuat |
| `updatedby` | `INT(10) UNSIGNED` | Yes | `0` | ID user yang terakhir mengubah |
| `updatedat` | `DATETIME` | Yes | — | Timestamp terakhir diubah |

**Index:** `PRIMARY KEY (id)`, `UNIQUE INDEX` pada `npm` (nama index `uniq_npm` atau `idx_user_npm`, tergantung migration mana yang jalan duluan — lihat bagian 7).

> ⚠️ **Catatan tipe kolom `npm`:** ada inkonsistensi kecil antar migration — migration `RefactorUserForDualAuth` membuatnya `CHAR(9)`, sedangkan migration `AlterUserAddNpmAndLoginType` (yang jalan setelahnya, idempotent) membuatnya `VARCHAR(15)` **jika kolom belum ada**. Validasi aplikasi (regex `^[0-9]{9}$`) tetap memaksa panjang 9 digit, jadi secara fungsional tetap konsisten walau tipe kolom di DB bisa berbeda tergantung urutan migration yang dijalankan.

Password **tidak pernah** disimpan plaintext — selalu `hash('sha256', $password)`, dan dibandingkan pakai `hash_equals()` (mencegah timing attack).

---

## 3. File-File Terkait

| File | Peran |
|---|---|
| `app/Database/Migrations/2026-06-26-191114_CreateUser.php` | Migration awal, buat tabel `user` dengan `username` |
| `app/Database/Migrations/2026-07-01-010000_RefactorUserForDualAuth.php` | Refactor jadi dual-auth: kembalikan `username`, tambah `npm` opsional |
| `app/Database/Migrations/2026-07-02-100000_AlterUserAddNpmAndLoginType.php` | Tambah kolom `login_type`, pastikan `npm` + unique index ada (idempotent) |
| `app/Database/Seeds/UserSeeder.php` | Seed 1 akun admin default |
| `app/Models/UserModel.php` | Model Eloquent-style CI4, validasi, method pencarian user |
| `app/Controllers/Auth.php` | Login/logout **backoffice** (Admin, Penjual) |
| `app/Controllers/ClientAuth.php` | Login/register/logout **mahasiswa** |
| `app/Filters/AuthFilter.php` | Guard halaman backoffice |
| `app/Filters/ClientAuthFilter.php` | Guard halaman client/mahasiswa |
| `app/Config/Filters.php` | Registrasi alias filter `auth` dan `clientauth` |
| `app/Config/Routes.php` | Definisi semua route auth |
| `app/Models/MenuModel.php` | Contoh consumer: join ke `user` untuk ambil `username` pembuat menu |

---

## 4. Model — `UserModel`

```php
protected $table            = 'user';
protected $primaryKey       = 'id';
protected $returnType       = 'array';
protected $useSoftDeletes   = false;
protected $useTimestamps    = false; // timestamp diisi manual, bukan otomatis oleh CI4

protected $allowedFields = [
    'username', 'npm', 'password', 'role', 'login_type',
    'createdby', 'createdat', 'updatedby', 'updatedat',
];
```

### Validation rules bawaan model

| Field | Rule | Pesan Custom |
|---|---|---|
| `username` | `permit_empty\|max_length[75]\|is_unique[user.username,id,{id}]` | Max 75 karakter, harus unik |
| `password` | `required\|min_length[6]` | Wajib, minimal 6 karakter |
| `login_type` | `required\|in_list[backoffice,mahasiswa]` | Wajib salah satu dari 2 nilai |
| `role` | `required\|in_list[Admin,Penjual,Pembeli]` | Wajib salah satu dari 3 role |

> Rule NPM (9 digit, unik) **tidak** didefinisikan di model — divalidasi manual di level controller (`ClientAuth`), karena hanya berlaku untuk alur mahasiswa.

### Method khusus

```php
findByUsername(string $username): ?array   // where username = ? AND login_type = 'backoffice'
findByNpm(string $npm): ?array              // where npm = ? AND login_type = 'mahasiswa'
```

Kedua method ini adalah **satu-satunya** cara controller mengambil data user untuk proses login — masing-masing dibatasi ketat pada `login_type` yang sesuai, jadi akun backoffice tidak akan pernah ketemu lewat `findByNpm()` dan sebaliknya.

---

## 5. Fitur & Flow

### 5.1 Login Backoffice (`Auth::login` / `Auth::loginProcess`)

**Route:** `GET /login`, `POST /login`
**Field form:** `username`, `password`

```
User buka /login
  └─ Jika sudah login (session isLoggedIn=true) → redirect ke "/"
  └─ Tampilkan form login

User submit POST /login
  ├─ Validasi: username required & max 75 char, password required
  │    └─ Gagal → redirect back + withInput + flash "errors"
  ├─ Cari user: UserModel->findByUsername($username)
  │    └─ Tidak ketemu → redirect back + flash error
  │        "Username atau password salah."  (pesan generik, anti user-enumeration)
  ├─ Hash input password (SHA-256) & bandingkan pakai hash_equals()
  │    └─ Tidak cocok → redirect back + flash error yang SAMA seperti di atas
  ├─ Sukses:
  │    ├─ Set session: id, username, role, login_type, isLoggedIn=true
  │    ├─ Insert log ke tabel `logsystem` (best-effort — kalau gagal,
  │    │   login tetap lanjut, hanya di-log sebagai warning)
  │    └─ Redirect ke "/" + flash success "Selamat datang, {username}!"
```

### 5.2 Login Mahasiswa (`ClientAuth::login` / `ClientAuth::loginProcess`)

**Route:** `GET /mahasiswa/login`, `POST /mahasiswa/login`
**Field form:** `npm`, `password`

```
User buka /mahasiswa/login
  └─ Jika sudah login → redirect ke "/pesan"
  └─ Tampilkan form login

User submit POST /mahasiswa/login
  ├─ Validasi: npm wajib match regex ^[0-9]{9}$, password required
  │    └─ Gagal → redirect back + withInput + flash "errors"
  ├─ Cari user: UserModel->findByNpm($npm)
  │    └─ Tidak ketemu → flash error "NPM atau password salah."
  ├─ Hash & bandingkan password (SHA-256 + hash_equals)
  │    └─ Tidak cocok → flash error sama seperti di atas
  ├─ Sukses:
  │    ├─ Set session: id, npm, role, login_type, isLoggedIn=true
  │    ├─ Insert log ke `logsystem` (best-effort)
  │    └─ Redirect ke "/pesan" + flash success
```

### 5.3 Register Mahasiswa (`ClientAuth::register` / `ClientAuth::registerProcess`)

**Route:** `GET /mahasiswa/register`, `POST /mahasiswa/register`
**Field form:** `npm`, `password`, `password_confirm`

```
User submit POST /mahasiswa/register
  ├─ Validasi:
  │    ├─ npm: required, regex 9 digit, is_unique[user.npm]
  │    ├─ password: required, min_length 8
  │    └─ password_confirm: required, harus matches[password]
  │    └─ Gagal salah satu → redirect back + flash "errors"
  ├─ Insert user baru:
  │    username    = ''             (kosong, field ini punya backoffice)
  │    npm         = input
  │    password    = hash('sha256', input)
  │    role        = 'Pembeli'      (selalu, tidak bisa dipilih user)
  │    login_type  = 'mahasiswa'
  │    createdby/updatedby = 0
  │    createdat/updatedat = now()
  ├─ Insert log ke `logsystem` (aksi: "register", best-effort)
  └─ Redirect ke /mahasiswa/login + flash success
      "Registrasi berhasil. Silakan login dengan NPM & password Anda."
```

> Tidak ada endpoint register untuk backoffice — akun Admin/Penjual dibuat manual lewat seeder atau query database langsung.

### 5.4 Logout (Backoffice & Mahasiswa)

**Route:** `GET /logout` (backoffice) · `GET /mahasiswa/logout` (mahasiswa)

Kedua logout **tidak** memakai `session()->destroy()` penuh — supaya flashdata yang di-set setelah redirect tidak ikut hilang. Alurnya:

```
├─ (Best-effort) Insert log "logout" ke logsystem, kalau userId ada di session
├─ Hapus key-key spesifik dari session:
│    id, username/npm, role, login_type, isLoggedIn
├─ session()->regenerate(true)   // cegah session fixation attack
└─ Redirect ke halaman login masing-masing + flash success
```

---

## 6. Proteksi Halaman (Filter / Session Guard)

Ada dua filter terpisah yang didaftarkan di `app/Config/Filters.php`:

| Alias Filter | Class | Dipakai di | Logic |
|---|---|---|---|
| `auth` | `AuthFilter` | Semua route backoffice (`/`, `/kategori`, `/menu`, `/meja`, `/kitchen`, `/paket-bundling`, dll) | Kalau `isLoggedIn` false → redirect `/login`. Kalau `login_type === 'mahasiswa'` → ditolak, redirect ke `/` dengan pesan "Akses backoffice hanya untuk Admin/Penjual." |
| `clientauth` | `ClientAuthFilter` | Route halaman mahasiswa yang butuh login | Kalau `isLoggedIn` false **atau** `login_type !== 'mahasiswa'` → redirect ke `/mahasiswa/login` |

Ini memastikan **cross-access ditolak**: mahasiswa tidak bisa masuk ke dashboard backoffice, dan sebaliknya akun backoffice tidak otomatis punya akses ke halaman client (karena filter `clientauth` mengecek `login_type` secara eksplisit).

> Catatan: route `pesan` (`GET /pesan`) saat ini **tidak** dipasangi filter apapun di `Routes.php` meski `ClientAuth` mengarahkan user ke sana setelah login — perlu dicek apakah ini disengaja atau belum sempat ditambahkan filter `clientauth`.

---

## 7. Data Session Setelah Login

| Key Session | Diisi saat login backoffice | Diisi saat login mahasiswa |
|---|---|---|
| `id` | ✅ id user | ✅ id user |
| `username` | ✅ | ❌ (tidak di-set) |
| `npm` | ❌ | ✅ |
| `role` | ✅ (`Admin`/`Penjual`) | ✅ (`Pembeli`) |
| `login_type` | `backoffice` | `mahasiswa` |
| `isLoggedIn` | `true` | `true` |

---

## 8. Audit Trail — Integrasi dengan `logsystem`

Setiap aksi login, register, dan logout dicatat (best-effort — kegagalan insert log **tidak** membatalkan aksi utama, hanya ditulis ke `log_message('warning', ...)`).

Struktur tabel `logsystem` (dari migration `CreateLogsystem`):

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | INT UNSIGNED | Primary key |
| `iduser` | INT UNSIGNED | ID user yang melakukan aksi |
| `module` | VARCHAR(50) | `'Auth'` atau `'ClientAuth'` |
| `level` | VARCHAR(20) | Role user saat itu |
| `aksi` | VARCHAR(100) | `'login'`, `'logout'`, atau `'register'` |
| `deskripsi` | TEXT | Deskripsi human-readable |
| `createdby` | INT UNSIGNED | Sama dengan `iduser` |
| `createdat` | DATETIME | Timestamp aksi |

---

## 9. Akun Default (dari `UserSeeder`)

Seeder hanya membuat **satu akun backoffice**, dan bersifat idempotent (cek dulu apakah sudah ada sebelum insert):

| Field | Value |
|---|---|
| `username` | `Admin` |
| `npm` | `NULL` |
| `password` | SHA-256 dari `Kantin123456UPB` |
| `role` | `Admin` |
| `login_type` | `backoffice` |

Jalankan dengan:
```bash
php spark db:seed UserSeeder
```

> Dokumen `LOGIN_INFO.md` di repo menyebutkan akun demo NPM `123456789` untuk admin — ini berasal dari draft implementasi lama sebelum refactor dual-auth. Berdasarkan `UserSeeder.php` **saat ini**, admin login memakai `username = Admin`, **bukan** NPM.

---

## 10. Riwayat Perubahan Skema (Kronologi Migration)

Penting untuk dipahami karena ada 2 migration yang saling "memperbaiki" pendekatan sebelumnya:

1. **`CreateUser`** (26 Jun 2026) — desain awal, tabel `user` hanya punya `username` (bebas, tanpa NPM).
2. *(Tidak ada di repo saat ini, tapi disebut di komentar kode)* sempat ada migration `AlterUserNpm` yang **mengganti total** `username` → `npm`, dipakai untuk skema login mahasiswa-only (tercermin di `LOGIN_INFO.md`).
3. **`RefactorUserForDualAuth`** (1 Jul 2026) — pembalik dari langkah di atas. Mengembalikan `username`, menjadikan `npm` sebagai kolom **tambahan** (nullable, unique) supaya tabel bisa menampung backoffice *dan* mahasiswa sekaligus.
4. **`AlterUserAddNpmAndLoginType`** (2 Jul 2026, idempotent) — menambahkan kolom `login_type` untuk secara eksplisit menandai tipe user, plus safety-check supaya migration ini aman dijalankan berulang kali walau kolom `npm`/index unique sudah dibuat migration sebelumnya.

**Kesimpulan:** desain final = 1 tabel `user`, dua jalur autentikasi paralel dibedakan oleh `login_type`, bukan tabel terpisah.

---

## 11. Ringkasan Endpoint

| Method | Path | Controller::Method | Filter | Fungsi |
|---|---|---|---|---|
| GET | `/login` | `Auth::login` | — | Form login backoffice |
| POST | `/login` | `Auth::loginProcess` | — | Proses login backoffice |
| GET | `/logout` | `Auth::logout` | — | Logout backoffice |
| GET | `/mahasiswa/login` | `ClientAuth::login` | — | Form login mahasiswa |
| POST | `/mahasiswa/login` | `ClientAuth::loginProcess` | — | Proses login mahasiswa |
| GET | `/mahasiswa/register` | `ClientAuth::register` | — | Form register mahasiswa |
| POST | `/mahasiswa/register` | `ClientAuth::registerProcess` | — | Proses register mahasiswa |
| GET | `/mahasiswa/logout` | `ClientAuth::logout` | — | Logout mahasiswa |
| GET | `/` dst. (dashboard backoffice) | Beragam | `auth` | Butuh login backoffice |
| GET | `/pesan` | `Pesan::index` | *(belum ada filter)* | Halaman tujuan setelah login mahasiswa |

---

## 12. Catatan / Potensi Isu untuk Ditindaklanjuti

- Route `/pesan` belum dipasangi filter `clientauth`, padahal itu tujuan redirect setelah login mahasiswa — sehingga saat ini bisa diakses tanpa login.
- Tipe kolom `npm` bisa berbeda (`CHAR(9)` vs `VARCHAR(15)`) tergantung urutan migration yang dijalankan pertama kali di environment tertentu — tidak menimbulkan bug fungsional karena validasi regex tetap 9 digit, tapi sebaiknya diseragamkan di migration berikutnya.
- Dokumentasi lama (`LOGIN_INFO.md`) masih mendeskripsikan skema single-auth (NPM-only) yang sudah digantikan oleh dual-auth — berpotensi membingungkan kontributor baru kalau dibaca tanpa konteks migration terbaru.
- Password memakai SHA-256 murni tanpa salt/pepper (bukan `password_hash()`/bcrypt) — ini adalah keputusan desain yang eksplisit disebut sebagai "kontrak existing project", bukan kelalaian, namun perlu diperhatikan dari sisi keamanan jika project berkembang ke production.
