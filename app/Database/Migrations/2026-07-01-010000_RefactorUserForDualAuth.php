<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Refactor tabel `user` agar bisa menampung 2 tipe user:
 *
 *   1. Backoffice (Admin / Penjual) — login pakai `username` (bebas).
 *      Kolom `npm` dibiarkan NULL.
 *
 *   2. Client side / Mahasiswa — login pakai `npm` (9 digit angka).
 *      Kolom `username` dibiarkan kosong string ''. Field `npm` CHAR(9) UNIQUE.
 *      Akan dipakai nanti setelah backend client side selesai.
 *
 * Migration ini adalah kebalikan dari migration `AlterUserNpm` sebelumnya
 * (yang sempat mengganti kolom `username` → `npm` total). Setelah diskusi
 * dengan reviewer, struktur disesuaikan jadi: kolom `username` dikembalikan,
 * kolom `npm` ditambahkan sebagai opsional (nullable, UNIQUE).
 *
 * Catatan: login backoffice saat ini HANYA pakai `username`. Client side
 * (login + register dengan NPM) belum diimplementasi — nunggu backend-nya
 * siap dulu supaya ada data yang bisa dipakai.
 */
class RefactorUserForDualAuth extends Migration
{
    public function up(): void
    {
        // 1) Kalau kolom `npm` sudah ada (dari migration sebelumnya),
        //    rename balik jadi `username VARCHAR(75)`.
        //    Kalau belum ada, lewati.
        $cols = $this->db->getFieldData('user');
        $hasNpm = false;
        $hasUsername = false;
        foreach ($cols as $col) {
            if ($col->name === 'npm')  $hasNpm = true;
            if ($col->name === 'username') $hasUsername = true;
        }

        if ($hasNpm && ! $hasUsername) {
            // Kasus migration AlterUserNpm sudah jalan — rename npm -> username VARCHAR(75)
            $this->db->query(
                "ALTER TABLE user
                 CHANGE COLUMN npm username VARCHAR(75) NOT NULL DEFAULT ''"
            );
            // Hapus unique index lama (uniq_npm) kalau ada
            $this->db->query(
                "ALTER TABLE user DROP INDEX IF EXISTS uniq_npm"
            );
        }

        // 2) Tambah kolom `npm CHAR(9) NULL UNIQUE` kalau belum ada.
        //    Nullable supaya admin backoffice tidak wajib punya NPM.
        if (! $hasNpm) {
            $this->db->query(
                "ALTER TABLE user
                 ADD COLUMN npm CHAR(9) NULL DEFAULT NULL AFTER username"
            );
        }
        // Tambah unique index untuk npm (kalau belum ada). Kalau ada row
        // dengan npm=NULL (admin), tidak akan konflik karena UNIQUE index
        // di MySQL memperbolehkan multiple NULLs.
        $this->db->query(
            "ALTER TABLE user
             ADD UNIQUE INDEX uniq_npm (npm)"
        );
    }

    public function down(): void
    {
        // Rollback: hapus kolom npm, kembalikan tabel ke kondisi awal
        // (cuma ada username, password, role, dll).
        $this->db->query("ALTER TABLE user DROP INDEX IF EXISTS uniq_npm");
        $this->db->query("ALTER TABLE user DROP COLUMN IF EXISTS npm");
    }
}
