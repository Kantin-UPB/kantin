<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * AlterUserAddNpmAndLoginType — PATCHED
 *
 * Patch: migration ini dulunya konflik dengan RefactorUserForDualAuth
 * (yang juga menambah kolom npm). Sekarang idempotent — cek dulu
 * apakah kolom sudah ada sebelum alter table. Aman dijalankan
 * berkali-kali.
 */
class AlterUserAddNpmAndLoginType extends Migration
{
    public function up(): void
    {
        $fields = $this->db->getFieldData('user');
        $existingColumns = array_column($fields, 'name');

        $hasNpm       = in_array('npm', $existingColumns, true);
        $hasLoginType = in_array('login_type', $existingColumns, true);

        // 1) Tambah kolom npm kalau belum ada (RefactorUserForDualAuth mungkin sudah tambah)
        if (! $hasNpm) {
            $this->db->query("ALTER TABLE user
                ADD COLUMN npm VARCHAR(15) NULL DEFAULT NULL AFTER username
            ");
        }

        // 2) Tambah kolom login_type kalau belum ada
        if (! $hasLoginType) {
            $this->db->query("ALTER TABLE user
                ADD COLUMN login_type ENUM('backoffice', 'mahasiswa') NOT NULL DEFAULT 'backoffice' AFTER role
            ");
        }

        // 3) Unique index untuk npm — cek dulu apakah index sudah ada
        //    (bisa dinamai uniq_npm dari RefactorUserForDualAuth, atau idx_user_npm dari sini)
        $indexExists = $this->db->query("SHOW INDEX FROM user WHERE Key_name IN ('uniq_npm', 'idx_user_npm')")->getNumRows() > 0;
        if (! $indexExists) {
            $this->db->query("ALTER TABLE user
                ADD UNIQUE INDEX idx_user_npm (npm)
            ");
        }
    }

    public function down(): void
    {
        // Hapus index dulu kalau ada
        $this->db->query("ALTER TABLE user DROP INDEX IF EXISTS idx_user_npm");
        $this->db->query("ALTER TABLE user DROP INDEX IF EXISTS uniq_npm");

        // Hapus kolom login_type kalau ada
        $fields = $this->db->getFieldData('user');
        $existingColumns = array_column($fields, 'name');

        if (in_array('login_type', $existingColumns, true)) {
            $this->db->query("ALTER TABLE user DROP COLUMN login_type");
        }
        // Catatan: kolom npm TIDAK di-drop di down() karena ditangani
        // oleh migration RefactorUserForDualAuth::down(). Kalau di-drop
        // di dua tempat, rollback akan error.
    }
}
