<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Alter table `user`: ganti kolom `username` menjadi `npm` (CHAR 9, UNIQUE).
 *
 * Aturan dari tim:
 *  - Login & register menggunakan NPM + password (bukan email / username).
 *  - NPM WAJIB tepat 9 digit angka, tidak boleh kurang / lebih.
 *  - Field di database tetap berada di tabel `user`, namanya `npm`.
 *
 * Migration ini TIDAK menghapus data existing — kolom `username` lama
 * di-rename menjadi `npm` lalu di-cast ke CHAR(9). Record lama yang
 * value-nya bukan 9-digit akan menjadi NULL / string kosong dan harus
 * diperbaiki manual oleh admin. Untuk fresh install, tabel `user`
 * dibuat oleh migration 2026-06-26-191114_CreateUser dengan kolom
 * `username VARCHAR(75)`, lalu migration ini mengubahnya jadi `npm`.
 */
class AlterUserNpm extends Migration
{
    public function up(): void
    {
        // 1) Rename + ubah tipe kolom username -> npm (CHAR 9).
        //    CHAR(9) dipilih agar NPM selalu presisi 9 karakter dan
        //    lebih hemat storage dibanding VARCHAR.
        $this->db->query(
            "ALTER TABLE user
             CHANGE COLUMN username npm CHAR(9) NOT NULL DEFAULT ''"
        );

        // 2) Tambah unique index agar NPM tidak boleh dobel.
        //    Pakai nama index yang familiar buat tim: uniq_npm.
        $this->db->query(
            "ALTER TABLE user ADD UNIQUE KEY uniq_npm (npm)"
        );
    }

    public function down(): void
    {
        // Rollback: balikin kolom jadi `username VARCHAR(75)`.
        $this->db->query("ALTER TABLE user DROP INDEX uniq_npm");
        $this->db->query(
            "ALTER TABLE user
             CHANGE COLUMN npm username VARCHAR(75) NOT NULL DEFAULT ''"
        );
    }
}
