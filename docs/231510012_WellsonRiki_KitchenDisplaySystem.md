# Dokumentasi Fitur: Kitchen Display System (KDS)
**Project:** Kantin UPB (CodeIgniter 4 + Bootstrap 5 + MySQL)
**Modul terkait:** Kitchen Display (baru)
**NPM:** 231510012
**Nama:** Wellson Riki
**Tanggal:** 11 Juli 2026

---

## 1. Ringkasan

Fitur ini menambahkan halaman **Kitchen Display System (KDS)** — layar tampilan dapur yang menampilkan order masuk secara **FIFO** (order yang paling lama menunggu tampil paling atas), menyorot order yang sudah menunggu **lebih dari 15 menit** dengan latar merah, memberi notifikasi suara saat ada order baru masuk, dan memungkinkan order **diterima/diselesaikan per kartu order, per detail item, maupun per qty**.

> ⚠️ **Catatan penting:** Seluruh data order pada fitur ini adalah **mock data (data contoh)** yang di-hardcode di controller. Fitur ini **belum terhubung ke tabel order/transaksi asli di database, dan belum memanggil endpoint API apa pun**. Progress "selesai" per item/qty hanya disimpan di memori JavaScript browser (client-side), sehingga akan reset setiap halaman di-refresh. Detail lengkap ada di bagian [8. Batasan & Catatan Penting](#8-batasan--catatan-penting).

## 2. Tujuan

- Memberi tampilan khusus untuk dapur yang menampilkan antrian order secara FIFO.
- Memberi peringatan visual (latar merah) untuk order yang sudah menunggu terlalu lama (>15 menit).
- Memungkinkan kitchen menandai order selesai secara granular: per kartu order sekaligus, per item, atau per sebagian qty dari satu item.

## 3. Perubahan Database

**Tidak ada.** Fitur ini tidak menambah/mengubah tabel, migration, atau model apa pun. Seluruh data order adalah array PHP hardcoded di controller (lihat bagian 5).

## 4. Akses & Routing

| Method | URL | Handler | Filter | Keterangan |
|---|---|---|---|---|
| GET | `/kitchen` | `Kitchen::index` | `auth` + cek tambahan `session('role') === 'Admin'` | Halaman Kitchen Display |

- Route ditambahkan di `app/Config/Routes.php`.
- Link sidebar baru **"Kitchen Display"** (ikon `bi-fire`) ditambahkan di `app/Views/Layout/Menu.php` (blok sidebar desktop & offcanvas mobile).
- Jika user login tapi bukan role **Admin**, akan di-redirect ke `/` dengan flash error.

## 5. Struktur Data Mock

Data order didefinisikan langsung sebagai array PHP di `Kitchen::index()`, contoh satu order:

```php
[
    'id'    => 1,
    'meja'  => 'Meja 3',
    'waktu' => <unix timestamp order masuk>,
    'items' => [
        ['id' => 101, 'nama' => 'Nasi Goreng', 'qty' => 2, 'qtyDone' => 0],
        ['id' => 102, 'nama' => 'Es Teh',      'qty' => 2, 'qtyDone' => 0],
    ],
]
```

- `qty` = jumlah dipesan, `qtyDone` = jumlah yang sudah ditandai selesai oleh dapur.
- Array ini di-sort ascending berdasarkan `waktu` (FIFO), lalu dikirim ke view sebagai JSON (`json_encode($orders)`).
- Di browser, JSON ini dimuat ke variabel JavaScript `dataOrder` dan seluruh interaksi (terima item, terima qty, selesaikan semua) memodifikasi variabel ini langsung di memori — **tidak ada request ke server**.

## 6. Alur & Logika Fitur

### 6.1 Urutan FIFO
Order diurutkan berdasarkan waktu order masuk (`waktu`) secara ascending, sehingga order yang paling lama menunggu selalu tampil di posisi paling awal grid.

### 6.2 Highlight Delay > 15 Menit
Setiap detik, JavaScript menghitung selisih waktu sekarang dengan waktu order masuk. Begitu selisihnya ≥ 15 menit, kartu order otomatis mendapat class `.order-delayed` (latar merah) — tanpa perlu reload halaman.

### 6.3 Notifikasi Suara Order Baru
Saat halaman pertama kali dimuat, jika ada order yang usianya kurang dari 90 detik, browser membunyikan beep singkat. Suara di-generate langsung lewat Web Audio API (oscillator), tanpa file audio eksternal.

### 6.4 Terima Order — Per Kartu, Per Item, Per Qty
- **Item dengan qty = 1:** klik item → dialog konfirmasi → item langsung ditandai selesai.
- **Item dengan qty > 1:** klik item → muncul modal stepper (`-` / `+` / Maksimal) untuk memilih berapa qty yang mau ditandai selesai sekarang (mendukung penyelesaian sebagian/partial).
- **Selesai Semua (per kartu):** tombol di bagian bawah tiap kartu order, menandai seluruh item dalam order tersebut selesai sekaligus.
- Kartu order otomatis hilang dari grid begitu semua item di dalamnya sudah `qtyDone >= qty`.

### 6.5 Pilihan Jumlah Kolom Grid
Combo box "Grid" di topbar (pilihan 2/3/4/5/6 kolom, default 4) langsung mengganti class Bootstrap `row-cols-N` pada container grid, tanpa reload halaman.

### 6.6 Layout Kiosk (Fullscreen, Tanpa Scroll Halaman)
- Tombol **Fullscreen** memakai Fullscreen API bawaan browser.
- Halaman dikunci agar **tidak pernah scroll di level dokumen** — topbar (jam, pilihan grid, fullscreen, tombol kembali ke dashboard) selalu tetap terlihat; hanya area grid order yang scroll secara internal saat jumlah order banyak.
- Daftar item di dalam satu kartu juga punya area scroll sendiri, supaya tinggi kartu tetap konsisten dalam satu baris grid meskipun jumlah item per order berbeda-beda.

## 7. Struktur File yang Terdampak

```
app/
├── Config/
│   └── Routes.php                 [EDIT] tambah route GET /kitchen
├── Controllers/
│   └── Kitchen.php                [BARU]
└── Views/
    ├── Layout/Menu.php            [EDIT] tambah link sidebar "Kitchen Display"
    └── kitchen/
        └── index.php              [BARU]
```

## 8. Alur Penggunaan

1. Login sebagai user dengan role **Admin**.
2. Klik menu **Kitchen Display** di sidebar (atau akses langsung `/kitchen`).
3. Order mock tampil terurut FIFO; order yang sudah menunggu >15 menit otomatis berlatar merah.
4. Klik salah satu item untuk menandainya selesai (langsung, atau lewat modal stepper qty jika qty > 1), atau klik **Selesai Semua** untuk menyelesaikan satu order sekaligus.
5. Kartu yang seluruh itemnya sudah selesai otomatis hilang dari grid.
6. (Opsional) Ubah jumlah kolom lewat combo box **Grid**, atau klik **Fullscreen** untuk mode layar penuh ala kiosk dapur.

## 9. Batasan & Catatan Penting

- **Seluruh data order bersifat mock/dummy**, di-hardcode langsung di `Kitchen::index()`. **Belum ada tabel order/transaksi asli di database**, dan **belum ada endpoint API apa pun** yang dipanggil dari halaman ini — tidak ada request AJAX/fetch ke server sama sekali untuk menerima atau menyelesaikan order.
- Progress "diterima/selesai" (per item, per qty, maupun "Selesai Semua") hanya tersimpan di memori JavaScript browser. **Refresh halaman akan mengembalikan data ke kondisi mock awal.**
- Belum ada mekanisme real-time/polling dari server — order baru tidak benar-benar "masuk" secara live selagi halaman terbuka; seluruh order sudah ditentukan di controller saat halaman pertama kali di-load.
- Belum ada status lanjutan seperti "Diproses" (misalnya alur waiting → cooking → selesai) — saat ini hanya status selesai/belum selesai per item.
- Pilihan jumlah kolom grid tidak disimpan (reset ke 4 kolom setiap refresh halaman).
- **Rekomendasi pengembangan lanjutan:** setelah tabel order/transaksi asli tersedia di database, ganti mock data di `Kitchen::index()` dengan query ke database, dan ganti pola "terima/selesaikan order" dari state JavaScript murni menjadi pemanggilan endpoint API sungguhan (mis. `POST /kitchen/complete`) yang benar-benar mengubah data di database — supaya progress tidak hilang saat refresh dan bisa dipantau dari banyak layar/browser sekaligus.
