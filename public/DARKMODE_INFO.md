# Dokumentasi Fitur: Dark Mode & User Profile

## Deskripsi
Dokumentasi ini menjelaskan penambahan fitur *Dark Mode* dan identitas pengguna pada navbar *client-side*.

## File yang Dimodifikasi
- `app/Views/client/layout.php`

## Detail Perubahan
1. **Dark Mode Toggle**:
   - Menggunakan Bootstrap 5 `form-switch` untuk mengganti tema (Light/Dark).
   - Menggunakan `localStorage` agar tema tetap tersimpan saat halaman dimuat ulang.
   - Atribut utama: `data-bs-theme`.

2. **User Identity**:
   - Menambahkan label nama "Irvansyah" pada navbar.
   - Memastikan tampilan responsif di perangkat mobile maupun desktop.

## Catatan
- Fitur ini diimplementasikan pada branch `fitur-darkmode-irvansyah`.
- Pastikan CDN Bootstrap yang digunakan sudah versi 5.3.3 ke atas agar atribut `data-bs-theme` berfungsi dengan baik.

---
*Dibuat oleh: Irvansyah*