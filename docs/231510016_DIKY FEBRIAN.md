# Fitur Promo & Paket Bundling — Kantin UPB

> Ringkasan alur fitur **Diskon Menu** dan **Paket Bundling** di sisi **Backoffice** (admin) dan **Client Side** (pelanggan).
> Stack: CodeIgniter 4 + Bootstrap 5 + MySQL.

---

## Daftar Isi
1. [Diskon Menu — Backoffice](#1-diskon-menu--backoffice)
2. [Paket Bundling — Backoffice](#2-paket-bundling--backoffice)
3. [Paket Bundling — Client Side](#3-paket-bundling--client-side)
4. [Status Workflow (Cheat Sheet)](#4-status-workflow-cheat-sheet)
5. [Route Overview](#5-route-overview)

---

## 1. Diskon Menu — Backoffice

Admin kasih diskon persentase ke satu menu tanpa mengubah harga asli.

### Alur
```
[Admin buka Menu → Edit Menu]
            │
            ▼
   Isi field "Diskon (%)"
   (0–100, contoh: 25)
            │
            ▼
   Simpan → sistem validasi 0–100
            │
            ▼
   Tersimpan di kolom menu.diskon
   (harga asli TIDAK diubah)
            │
            ▼
   Tampil di daftar menu:
   ├─ Badge merah "-25%"
   ├─ Harga asli dicoret
   └─ Harga setelah diskon (merah, tebal)
```

### Logika Hitung Harga
```php
// helper: hitung_harga_diskon($harga, $diskon)
$diskon = max(0, min(100, $diskon));   // clamp 0–100
return round($harga - ($harga * $diskon / 100));
```

**Contoh:** Harga `Rp 20.000`, diskon `25%` → tampil `Rp 15.000`.

### Aturan
- `diskon` wajib integer 0–100
- Disimpan terpisah dari kolom `harga` (harga asli tetap utuh)
- Untuk hapus diskon → set `diskon = 0`
- Helper auto-load global (terdaftar di `BaseController::$helpers`)

---

## 2. Paket Bundling — Backoffice

Admin gabungin beberapa menu jadi satu paket dengan harga spesial.

### 2.1 Alur Create Paket
```
[Klik "Tambah Paket Bundling"]
            │
            ▼
   Isi form:
   ├─ Nama paket (wajib, max 150 char)
   ├─ Deskripsi (opsional, max 500 char)
   ├─ Harga paket (wajib, angka ≥ 0)
   ├─ Gambar (opsional, upload file)
   └─ Centang menu aktif + isi qty
            │
            ▼
   JavaScript hitung TOTAL HARGA NORMAL
   (preview real-time di bawah form)
            │
            ▼
   Klik "Simpan" → controller validasi
            │
            ▼
   Insert ke tabel paket_bundling
   Insert batch ke paket_bundling_item
            │
            ▼
   Status default = Pending (1)
   Redirect ke halaman Pending
```

### 2.2 Alur Perhitungan Hemat
Dihitung otomatis di `PaketBundlingModel::withComputedTotals()` setiap kali paket di-fetch:

```
harga_normal  = Σ (harga_menu × qty)   untuk semua item
hemat         = MAX(0, harga_normal - harga_paket)
persen_hemat  = ROUND((hemat / harga_normal) × 100)   jika harga_normal > 0
                0                                     jika harga_normal = 0
```

**Contoh:**
- Isi paket: Nasi Goreng (Rp 15.000 × 1) + Es Teh (Rp 5.000 × 1)
- `harga_normal` = Rp 20.000
- `harga_paket` = Rp 16.000
- `hemat` = Rp 4.000
- `persen_hemat` = 20%

→ Kartu paket tampil: badge **"Hemat 20%"** + harga coret `Rp 20.000` + harga paket `Rp 16.000`.

### 2.3 Aturan Validasi
| Field | Aturan |
|---|---|
| `nama_paket` | wajib, maksimal 150 karakter |
| `deskripsi` | opsional, maksimal 500 karakter |
| `harga_paket` | wajib, numeric, tidak boleh negatif |
| Menu terpilih | minimal 1 menu wajib dicentang |
| Menu yang tersedia | hanya menu dengan `status_id = 5` (Active) |
| `qty` per item | minimal 1 (di-force oleh `saveItems()`) |

---

## 3. Paket Bundling — Client Side

Pelanggan lihat paket aktif di halaman `/pesan`, tab **Promo**.

### 3.1 Alur Tampil
```
[Pelanggan buka /pesan]
            │
            ▼
   Controller Pesan::index()
   ├─ Ambil paket status Active via PaketBundlingModel
   ├─ Transform ke array untuk JavaScript
   │     (id, nama, harga, harga_normal, hemat,
   │      persen_hemat, img, items)
   └─ Pass ke view client/pesan.php
            │
            ▼
   JSON di-inject ke JS: window.paketPromo
            │
            ▼
   Tab "Promo" render kartu:
   ├─ Gambar paket (atau placeholder)
   ├─ Badge "Hemat X%" (jika persen_hemat > 0)
   ├─ Nama paket + deskripsi
   ├─ Daftar isi (qty x nama menu)
   ├─ Harga coret (jika ada hemat)
   ├─ Harga paket (merah, tebal)
   └─ Tombol "Beli Paket"
            │
            ▼
   Pagination: 6 paket per halaman
   Sort: asc/desc berdasarkan harga
```

### 3.2 Alur Beli Paket
```
[Klik "Beli Paket" di kartu]
            │
            ▼
   tambahPaketKeKeranjang(idPaket)
   ├─ Cari paket di paketPromo
   ├─ Jika sudah ada di keranjang → qty += 1
   └─ Jika belum → push {tipe: 'paket', qty: 1}
            │
            ▼
   Update badge keranjang (total qty)
   Alert konfirmasi
            │
            ▼
[Klik "Lihat Keranjang"]
            │
            ▼
   Modal tampilkan:
   ├─ Daftar item (menu & paket)
   ├─ Subtotal per item (harga × qty)
   ├─ Tombol hapus per item
   └─ Total pembayaran
            │
            ▼
[Klik "Langsung Pesan"]
            │
            ▼
   prosesPesan()
   ├─ Alert simulasi sukses
   ├─ Kosongkan keranjangBelanja
   └─ Tutup modal
```

### 3.3 Catatan
- Menu di tab Makanan/Minuman/Dessert **masih dummy JS** (belum terhubung DB)
- Hanya tab **Promo** yang sudah integrasi backend
- Diskon menu individual belum terlihat di client side sampai integrasi menu selesai

---

## 4. Status Workflow (Cheat Sheet)

Konvensi status: `1` = Pending, `5` = Active, `8` = Cancel.

```
                ┌─────────────┐
                │   (create)  │
                └──────┬──────┘
                       │
                       ▼
                ┌─────────────┐
        ┌──────▶│   PENDING   │◀──────┐
        │       │   (1)       │       │
        │       └──────┬──────┘       │
        │              │              │
        │     ┌────────┼────────┐     │
        │     │        │        │     │
        │     ▼        ▼        │     │
        │  activate  cancel     │     │ restore
        │     │        │        │     │
        │     ▼        │        │     │
        │ ┌─────────┐  │        │     │
        │ │ ACTIVE  │  │        │     │
        │ │  (5)    │  │        │     │
        │ └────┬────┘  │        │     │
        │      │       │        │     │
        │   draft      │        │     │
        └──────┘       ▼        │     │
                ┌─────────────┐ │     │
                │   CANCEL    │─┴─────┘
                │   (8)       │
                └──────┬──────┘
                       │
                    delete
                    (permanen)
                       │
                       ▼
                  ┌─────────┐
                  │ DIHAPUS │
                  └─────────┘
```

### Transisi yang Diizinkan
| Dari | Ke | Method | Route |
|---|---|---|---|
| (baru) | Pending (1) | `store` | `POST /paket-bundling/store` |
| Pending (1) | Active (5) | `activate` | `GET /paket-bundling/activate/{id}` |
| Pending (1) | Cancel (8) | `cancel` | `GET /paket-bundling/cancel/{id}` |
| Active (5) | Pending (1) | `draft` | `GET /paket-bundling/draft/{id}` |
| Active (5) | Cancel (8) | `cancel` | `GET /paket-bundling/cancel/{id}` |
| Cancel (8) | Pending (1) | `restore` | `GET /paket-bundling/restore/{id}` |
| Cancel (8) | (dihapus) | `delete` | `GET /paket-bundling/delete/{id}` |

### Aturan Hapus
- Hapus permanen **hanya untuk status Cancel** (dicek di controller)
- Sebelum hapus: file gambar dihapus + baris pivot `paket_bundling_item` dihapus
- Tidak ada soft delete → tidak bisa di-undo

---

## 5. Route Overview

### Backoffice (filter: `auth`)
| Method | URL | Fungsi |
|---|---|---|
| GET | `/paket-bundling` | Daftar paket **Active** |
| GET | `/paket-bundling/pending` | Daftar paket **Pending** |
| GET | `/paket-bundling/cancelled` | Daftar paket **Cancel** |
| GET | `/paket-bundling/create` | Form tambah paket |
| POST | `/paket-bundling/store` | Simpan paket baru |
| GET | `/paket-bundling/{id}` | Detail paket |
| GET | `/paket-bundling/edit/{id}` | Form edit paket |
| POST | `/paket-bundling/update/{id}` | Simpan perubahan |
| GET | `/paket-bundling/activate/{id}` | Set status → Active |
| GET | `/paket-bundling/cancel/{id}` | Set status → Cancel |
| GET | `/paket-bundling/draft/{id}` | Set status → Pending |
| GET | `/paket-bundling/restore/{id}` | Pulihkan Cancel → Pending |
| GET | `/paket-bundling/delete/{id}` | Hapus permanen |

### Client Side (tanpa filter)
| Method | URL | Fungsi |
|---|---|---|
| GET | `/pesan` | Halaman pemesanan + tab Promo |

---

## Skema Database Singkat

```
menu                    (sudah ada + kolom baru: diskon INT(3))
   │
   │ 1
   │
   ▼ N
paket_bundling_item     (pivot: id_paket, id_menu, qty)
   │
   │ N
   │
   ▼ 1
paket_bundling          (nama_paket, harga_paket, status_id, url_gambar, ...)
```

- FK `paket_bundling_item.id_paket` → `paket_bundling.id` (CASCADE)
- FK `paket_bundling_item.id_menu` → `menu.id` (CASCADE)
- `menu.diskon`: INT(3) UNSIGNED, default 0, rentang 0–100

---

## Catatan Keamanan & Rekomendasi

- Operasi status (activate/cancel/draft/restore/delete) masih pakai **GET** → rentan CSRF. Sebaiknya ubah ke **POST + CSRF token**.
- Tidak ada periode berlaku (start/end date) → promo berlaku selamanya sampai di-cancel manual.
- Diskon menu individual **tidak berlaku ganda** dengan paket bundling (harga normal paket pakai harga asli menu).
- Menu yang dihapus dari DB akan tampil sebagai "Menu dihapus" di daftar isi paket (tidak ada auto-nonaktifkan paket).

---
