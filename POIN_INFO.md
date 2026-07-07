# Manage Sistem Poin — Kantin UPB

Dokumentasi fitur **Manage Sistem Poin** (Back Office — Admin & Penjual) yang ditambahkan ke project Kantin UPB.

**Dikerjakan oleh:** Delon Kent (NPM 231510020)

## Yang Ditambahkan

| File | Status | Keterangan |
|------|--------|------------|
| `app/Database/Migrations/2026-07-05-000000_CreatePoinTables.php` | **NEW** | Buat tabel `pengaturan_poin` dan `riwayat_poin`. Tambah kolom `saldo_poin` (default 0) ke tabel `user`. |
| `app/Models/PoinModel.php` | **NEW** | Model untuk tabel `pengaturan_poin`. Ada method `getAturanAktif()` dan `hitungPoin(int $nominalTransaksi)`. |
| `app/Models/RiwayatPoinModel.php` | **NEW** | Model untuk tabel `riwayat_poin`. Ada method `getRiwayatByUser()`, `tambahPoin()`, `kurangiPoin()`. |
| `app/Controllers/ManagePoin.php` | **NEW** | Controller CRUD aturan poin + halaman riwayat. Pakai pola `renderPage()` (Header + Menu + konten + Footer) sesuai konvensi project (lihat `Controllers/Menu.php`). |
| `app/Views/Poin/Index.php` | **NEW** | View daftar aturan poin (tabel + tombol tambah/edit/hapus). |
| `app/Views/Poin/Form.php` | **NEW** | View form tambah/edit aturan poin. |
| `app/Views/Poin/Riwayat.php` | **NEW** | View daftar riwayat poin semua user. |
| `app/Config/Routes.php` | **MODIFIED** | Tambah grup route `manage-poin` dengan filter `auth`. |

## Aturan yang Diterapkan

1. Admin/penjual bisa atur **rasio poin** (contoh: Rp1.000 = 1 poin) lewat form, bukan hardcode.
2. Ada **minimal nominal transaksi** supaya dapat poin (bisa diset ke 0 kalau tidak ada minimal).
3. Aturan poin punya status **aktif/nonaktif** — perhitungan poin transaksi memakai aturan yang sedang `aktif`.
4. Setiap perubahan saldo poin (dapat/pakai) dicatat di tabel `riwayat_poin`, dengan jenis `masuk` atau `keluar`, supaya bisa diaudit/dilacak.
5. Perhitungan poin (`hitungPoin()`) murni fungsi backend — nominal transaksi masuk, poin keluar dihitung server-side (selaras dengan aturan project: perhitungan ulang wajib di backend, tidak boleh percaya angka dari frontend).

## Setup & Run

Ikuti setup umum project ini (lihat `SETUP.md`), lalu tambahan berikut:

### 1. Jalankan Migration

```bash
php spark migrate
```

Ini akan membuat tabel `pengaturan_poin`, `riwayat_poin`, dan menambah kolom `saldo_poin` ke tabel `user` (default `0`).

### 2. Akses Halaman

Setelah login (lihat `LOGIN_INFO.md` untuk cara login), buka:

```
http://localhost:8080/manage-poin
```

## Rute

| Method | Path | Controller | Keterangan |
|--------|------|------------|------------|
| GET | `/manage-poin` | `ManagePoin::index` | Daftar aturan poin |
| GET | `/manage-poin/create` | `ManagePoin::create` | Form tambah aturan |
| POST | `/manage-poin/store` | `ManagePoin::store` | Simpan aturan baru |
| GET | `/manage-poin/edit/(:num)` | `ManagePoin::edit` | Form edit aturan |
| POST | `/manage-poin/update/(:num)` | `ManagePoin::update` | Simpan perubahan aturan |
| GET | `/manage-poin/delete/(:num)` | `ManagePoin::delete` | Hapus aturan |
| GET | `/manage-poin/riwayat` | `ManagePoin::riwayat` | Lihat riwayat poin semua user |

Semua rute di atas memakai filter `auth` (wajib login).

## Alur Validasi (Create/Update Aturan Poin)

1. `nama_aturan` wajib diisi, minimal 3 karakter, maksimal 100 karakter.
2. `rasio_rupiah` wajib diisi, harus angka, harus lebih besar dari 0.
3. `minimal_transaksi` boleh kosong (default 0), kalau diisi harus angka ≥ 0.
4. Gagal validasi → redirect kembali ke form dengan flashdata `errors` berisi pesan per field.
5. Sukses → redirect ke `/manage-poin` dengan flashdata `success`.

## Logika Perhitungan Poin

Method `PoinModel::hitungPoin(int $nominalTransaksi): int`:

1. Ambil aturan yang sedang `status_aktif = 'aktif'` (aturan terbaru kalau ada lebih dari satu, diurutkan berdasarkan `id` menurun).
2. Kalau tidak ada aturan aktif → return `0`.
3. Kalau nominal transaksi di bawah `minimal_transaksi` → return `0`.
4. Kalau rasio valid (`> 0`) → poin = `intdiv($nominalTransaksi, $rasio_rupiah)`.

Contoh: rasio `1000`, minimal transaksi `10000`, belanja `Rp25.000` → dapat `25` poin. Belanja `Rp5.000` (di bawah minimal) → dapat `0` poin.

## Yang BELUM Selesai / Perlu Dikerjakan Lanjut

- **Pembatasan role**: saat ini halaman `/manage-poin` hanya memakai filter `auth` (cek sudah login atau belum). Belum ada pembatasan spesifik supaya hanya role `Admin` dan `Penjual` yang bisa akses (role `Pembeli` seharusnya tidak boleh masuk ke sini). Perlu koordinasi dengan tim Auth soal filter role.
- **Integrasi ke Transaksi**: `RiwayatPoinModel::tambahPoin()` dan `kurangiPoin()` sudah siap dipanggil, tapi belum terhubung ke modul Transaksi (menunggu modul Transaksi selesai). Rencana integrasi:
  - Setelah transaksi sukses (status `5 = Active` / `20 = Terposting`) → panggil `tambahPoin()` berdasarkan `PoinModel::hitungPoin()`.
  - Saat transaksi dibatalkan (status `8 = Cancel`) → rollback poin yang sudah didapat/dipakai.
  - Saat pembeli pakai poin untuk Multipayment (potong saldo bonus) → panggil `kurangiPoin()`.
- **Client Side (Integrasi Poin saat checkout)**: dikerjakan bersama Philip, menunggu route `/mahasiswa/*` dan modul login pembeli.
- **Tampilan saldo poin di halaman profil pembeli**: belum ada, menunggu halaman Profile (Faizal) tersedia.

## Catatan untuk Tim

- Halaman ini memakai pola `renderPage()` (Header + Menu + konten + Footer), **bukan** `extend()`/`section()` bawaan CodeIgniter — supaya konsisten dengan halaman Menu yang sudah ada. Kalau bikin halaman baru di project ini, pola ini yang dipakai, bukan `extend/section`.
- Struktur tabel `pengaturan_poin` dan `riwayat_poin` independen dari tabel lain, jadi migration ini aman dijalankan tanpa mempengaruhi tabel yang sudah ada, kecuali penambahan kolom `saldo_poin` di tabel `user`.
