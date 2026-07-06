# # Dokumentasi Fitur: Log Sistem Pengguna

## 1. Deskripsi Fitur

Fitur ini menyediakan fungsi pembantu global (_helper function_) bernama `writelog()` yang berfungsi untuk mencatat setiap rekam jejak aktivitas pengguna ke dalam database pusat pada tabel `logsystem`.

**Di luar scope fitur ini (tidak dikerjakan):**

- Tampilan antarmuka grafis (UI) untuk melihat daftar log (akan dikerjakan terpisah pada bagian modul Report).

---

## 2. Aturan & Validasi Parameter Fungsi

### 2.1 Struktur Parameter `writelog()`

| Parameter | Tipe Data | Keterangan                                                              |
| :-------- | :-------- | :---------------------------------------------------------------------- |
| `$iduser` | `Integer` | ID Pengguna yang sedang melakukan aksi / modifikasi data.               |
| `$menu`   | `String`  | Nama modul atau halaman yang sedang diakses (contoh: 'Home', 'Produk'). |
| `$ket`    | `String`  | Jenis aksi yang dilakukan (`View`, `New`, `Edit`, `Delete`).            |
| `$solusi` | `String`  | Deskripsi detail atau catatan tambahan mengenai aktivitas tersebut.     |

---

## 3. Implementasi Kode

### 3.1 Kode Helper (`app/Helpers/Func_helper.php`)

```php
function writelog($iduser, $menu, $ket, $solusi)
{
    $db = \Config\Database::connect();

    $data = [
        'iduser'  => $iduser,
        'menu'    => $menu,
        'ket'     => $ket,
        'solusi'  => $solusi
    ];

    $db->table('logsystem')->insert($data);
}
```
