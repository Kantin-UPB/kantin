# Fitur Urutkan Harga — Kantin UPB

Dokumen ini menjelaskan fitur baru: **urutkan menu dari harga termurah ke termahal (atau sebaliknya)**. Fitur ini ada di 2 tempat: halaman pemesanan (client) dan halaman kelola menu (admin).

## File Apa Saja yang Diubah?

Cuma **2 file**, keduanya file tampilan (bukan file database/logic inti):

| File | Untuk Apa | Yang Ditambahkan |
|------|-----------|-------------------|
| `app/Views/client/pesan.php` | Halaman pemesanan (yang dilihat pembeli) | Dropdown pilihan urutan harga di atas daftar menu |
| `app/Views/menu/index.php` | Halaman kelola menu (yang dilihat admin) | Dropdown yang sama, di sebelah tombol "Add Menu" |

Tidak ada file lain yang disentuh. Controller, model, database, tombol lain — semua tetap sama seperti sebelumnya.

## Apa yang Bisa Dilakukan User?

Ada dropdown dengan 3 pilihan:

- **Default** — urutan menu seperti biasa (tidak diubah)
- **Termurah → Termahal** — menu dengan harga paling murah muncul duluan
- **Termahal → Termurah** — menu dengan harga paling mahal muncul duluan

## Cara Kerjanya (Versi Simpel)

**Di halaman pemesanan (client):**
Waktu user pilih salah satu opsi di dropdown, sistem akan mengurutkan ulang daftar menu berdasarkan harga, LALU baru dibagi ke halaman-halaman (pagination). Jadi urutan halaman 1, 2, 3 dst juga ikut berubah sesuai pilihan sort. Ini tetap jalan bareng fitur pindah kategori (Makanan/Minuman/Dessert) yang sudah ada — tidak saling ganggu.

**Di halaman admin:**
Cara kerjanya sedikit beda. Di sini, sistem cuma **menyusun ulang posisi kartu menu yang sudah tampil di layar** — tidak mengambil data baru dari database. Jadi lebih ringan dan cepat, dan tidak mengganggu fitur filter status menu (Active/Pending/Cancelled) yang mungkin sudah ada.

## Kenapa Dibuat Beda Cara di 2 Tempat?

- Di **client**, semua data menu memang sudah diambil dan disimpan di JavaScript, jadi sorting + pagination bisa digabung sekaligus di situ.
- Di **admin**, data menu di-render langsung dari server (PHP). Supaya tidak perlu mengubah kode query database (yang berisiko bikin error di fitur lain), sorting-nya cukup dilakukan di browser dengan menyusun ulang kartu yang sudah ada.

Intinya: dua-duanya sama-sama aman, cuma caranya disesuaikan dengan struktur kode yang sudah ada di masing-masing halaman.

## Apakah Ini Mengubah Database?

**Tidak.** Tidak ada kolom baru, tidak ada tabel baru, tidak perlu jalankan migration tambahan. Fitur ini murni di tampilan.

## Apakah Perlu Install Sesuatu?

**Tidak.** Tidak ada library atau dependency baru. Semua pakai JavaScript biasa yang sudah include di project.

## Sudah Dicek Apa Belum?

Saya sudah cek kodenya secara manual (baca ulang logika-nya satu-satu), dan hasilnya aman — tidak ada bagian yang bentrok dengan fitur lain seperti pagination, tab kategori, atau tombol Edit/Hapus di admin.

Tapi karena saya tidak punya akses buka browser & database langsung di sini, saya **belum sempat coba klik-klik langsung di aplikasi**. Jadi sebelum digabung ke branch utama, tolong dicoba dulu manual:

1. Buka halaman pemesanan → coba pilih tiap opsi sort → cek urutan menu berubah dengan benar
2. Buka halaman admin menu → coba pilih tiap opsi sort → cek kartu menu ikut tersusun ulang
3. Coba gabung sama fitur lain (ganti kategori, pindah halaman pagination) → pastikan tidak error

## Catatan Tambahan

- Kalau nanti mau tambah fitur lain seperti pencarian nama menu, itu bisa digabung dengan fitur sort ini tanpa perlu bongkar ulang — karena logic sort-nya sudah dipisah rapi di satu tempat.
- Kalau suatu saat halaman admin diubah supaya datanya diambil pakai AJAX (bukan langsung dari server), bagian sort di admin perlu disesuaikan sedikit.
