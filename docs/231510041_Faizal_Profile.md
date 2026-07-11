# Dokumentasi Modul Profile Client Side — Kantin UPB

Dokumen ini menjelaskan implementasi fitur **Profile** pada halaman Client Side (Mahasiswa/Pembeli), disusun berdasarkan tugas koding aktual atas nama Faizal (NPM: 231510041).

---

## 1. Ringkasan Fitur

Fitur Profile dirancang khusus untuk sisi pembeli (mahasiswa) agar dapat melihat detail informasi akun mereka secara aman setelah berhasil melewati proses autentikasi (login). Tombol akses profil diletakkan langsung di bagian dashboard utama pemesanan pelanggan untuk kemudahan navigasi.

## 2. Struktur File Terkait

Implementasi fitur ini menggunakan pola MVC standar CodeIgniter 4:

- `app/Controllers/Profile.php` — Menangani logika pengecekan session dan pengambilan data user.
- `app/Views/client/profile.php` — Menampilkan antarmuka data diri mahasiswa dengan framework Bootstrap.
- `app/Views/client/pesan.php` — Integrasi tombol navigasi akses "Profil Saya" pada dashboard pemesanan.
- `app/Config/Routes.php` — Mendaftarkan endpoint URL dengan proteksi filter keamanan.

## 3. Alur Logic & Proteksi Halaman

- Sistem melakukan pengecekan session aktif (`isLoggedIn` dan `login_type === 'mahasiswa'`).
- Jika tidak memenuhi syarat, filter `clientauth` secara otomatis menolak akses dan melempar pengguna ke halaman `/mahasiswa/login`.
- Data profil diambil secara dinamis dari tabel `user` berdasarkan `id` unik yang tersimpan pada data session login.
