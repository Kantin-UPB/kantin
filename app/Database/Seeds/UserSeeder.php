<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Generate SHA-256 hash for the password (sama dengan kontrak
        // existing project Kantin: hash('sha256', $plaintext)).
        $passwordSalt = 'Kantin123456UPB'; // 'Kantin' . '123456' . 'UPB'
        $hashedPassword = hash('sha256', $passwordSalt);

        // Current timestamp in the configured format
        $currentDateTime = date('Y-m-d H:i:s');

        // User data — admin memakai NPM placeholder 9-digit "123456789"
        // karena seluruh login (termasuk admin) wajib pakai NPM 9 digit.
        // Admin asli di kampus tidak punya NPM mahasiswa, jadi NPM ini
        // hanya sebagai identifier login akun admin default.
        $userData = [
            'npm'        => '123456789',
            'password'   => $hashedPassword,
            'role'       => 'Admin',
            'createdby'  => 0,
            'createdat'  => $currentDateTime,
            'updatedby'  => 0,
            'updatedat'  => $currentDateTime,
        ];

        // Insert using Query Builder
        $this->db->table('user')->insert($userData);
    }
}
