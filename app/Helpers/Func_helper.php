<?php

if (!function_exists('formatRupiah')) {
    /**
     * Format number to Indonesian Rupiah currency
     *
     * @param float|int $amount The amount to format
     * @return string Formatted Rupiah string
     */
    function formatRupiah(float|int $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}

if (!function_exists('writelog')) {
    /**
     * Write activity log to database sesuai instruksi tim
     * * @param int|string $idUser ID user yang melakukan aksi
     * @param string $module Nama menu/modul (misal: 'Home', 'Menu Makanan')
     * @param string $aksi Jenis tindakan (misal: 'View', 'New', 'Edit', 'Delete')
     * @param string $deskripsi Keterangan detail aktivitas
     */
    function writelog($idUser, $module, $aksi, $deskripsi): void
    {
        // 1. Ambil koneksi database bawaan CodeIgniter 4
        $db = \Config\Database::connect();

        // 2. Susun data berdasarkan field persis yang diminta temanmu
        $data = [
            'iduser'    => $idUser,
            'module'    => $module,
            'aksi'      => $aksi,
            'deskripsi' => $deskripsi,
            'createdby' => $idUser,               // Menggunakan ID user yang melakukan aksi
            'createdat' => date('Y-m-d H:i:s'),  // Waktu kejadian sekarang
        ];

        // 3. Masukkan data ke nama tabel sesuai cetak biru kelompok (tabel: logsystem)
        $db->table('logsystem')->insert($data);
    }
}