<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterUserAddNpmAndLoginType extends Migration
{
    public function up(): void
    {
        // Tambah kolom npm (nullable - untuk backoffice NULL, untuk mahasiswa 9 digit)
        $this->db->query("ALTER TABLE user
            ADD COLUMN npm VARCHAR(15) NULL DEFAULT NULL AFTER username,
            ADD COLUMN login_type ENUM('backoffice', 'mahasiswa') NOT NULL DEFAULT 'backoffice' AFTER role
        ");

        // Unique index untuk npm (karena NULL tidak dihitung unique, backoffice bisa multiple NULL)
        $this->db->query("ALTER TABLE user
            ADD UNIQUE INDEX idx_user_npm (npm)
        ");
    }

    public function down(): void
    {
        $this->db->query("ALTER TABLE user
            DROP INDEX idx_user_npm
        ");
        $this->db->query("ALTER TABLE user
            DROP COLUMN login_type,
            DROP COLUMN npm
        ");
    }
}
