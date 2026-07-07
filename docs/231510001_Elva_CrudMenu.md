# CRUD MENU
_Documentation by Elva Gracia 231510001_

---

## I. Apa aja yang dikerjakan
- Create Menu
- Read Menu
- Update Menu
- Delete Menu
- Status Menu

## II. Tabel Menu
Dalam pembuatan CRUD Menu, ada dibuat Tabel `Menu` <p>
|Column | Type  | Note |
|------ |-------|------|
| `id`	| int(11) Auto Increment	| id menu, Primary Key |
| `id_kategori`	| int(11) unsigned	| Foreign Key ke Tabel Kategori |
| `nama`	| varchar(255)	| Nama Menu |
| `deskripsi`	| text	| Deskripsi Menu |
| `harga`	| decimal(10,0)	| harga menu |
| `status_id` |	int(2)	| Status Menu. Cth: 1 Pending, 5 Active, 8 Cancel, 20 Terposting |
| `url_gambar` |	varchar(255)	| untuk menyimpan link/path foto menu |
| `created_by` |	int(11)	| menyimpan ID user yang membuat data ini |
| `created_at` |	timestamp [current_timestamp()]	| agar mencatat waktu pembuatan secara otomatis. |

**NOTES <p>**
Setting untuk Foreign Key id_kategori <p>
`ON DELETE = "RESTRICT"`. Tidak bisa menghapus kategori jika masih ada menu yang menggunakan kategori tersebut. <p>
`ON UPDATE ="CASCADE"`. Kalau id_kategori berubah, maka nilai di tabel menu ikut berubah.

## III. Diagram of CRUD Menu

```mermaid
graph
  1[Login] --> 2[Dashboard]
  2 --> 3[menu]
  3 --> 4[Add Menu]
  3 --> 5[Pending Menus]
  3 --> 6[Cancelled Menus]
  3 --> 7[Existing Menus' Profile]

  4 --> 8[Simpan]
  4 --> 9[Batal]
  5 --> 10[Detail]
  5 --> 11[Edit]
  5 --> 12[Activate]
  5 --> 13[Cancel]
  6 --> 9
  6 --> 10
  6 --> 14[Hapus Permanen]
  7 --> 10
  7 --> 11
  7 --> 15[Set to Draft]
  7 --> 13

  8 --> 16[Menu Berhasil ditambahkan ke Daftar Pending Menus]
  9 --> 17[Menu tidak di simpan] 
  10 --> 11
  10 --> 18[Kembali]  
  11 --> 19[Update]
  11 --> 20[Batal]
  12 --> 21[Menu Berhasil diaktifkan ke Daftar Menu Aktif]
  13 --> 22[Menu Dicancel, Dipindahkan ke Cancelled Menus]
  14 --> 23[Menu Dihapus]
  15 --> 24[Menu dipindahkan ke drafts / Pending Menus]

  16 --> 2
  17 --> 2
  18 --> 2
  19 --> 25[Menu Berhasil diupdate]
  20 --> 26[Menu Tidak Berubah, Edit tidak disimpan]
  21 --> 2
  22 --> 2
  23 --> 2
  24 --> 2

  25 --> 2
  26 --> 2
``` 

---

>### ADDITIONAL NOTES
>1. FEATURE TEST<p>
> Untuk tes fitur. untuk Sementara, karena scope Kategori belum ada di dalam git proyek, bisa di tambahkan dulu manual Kategori di database, baru tes kembali.
>2. ERROR TO BE FIXED<p>
> *ada masalah navigasi ketika klik set to draft, (will be fixed soon)<p>
> *tambah kategori untuk sementara juga belom bisa karena scope kategori blom selesai (will be fixed soon)



















