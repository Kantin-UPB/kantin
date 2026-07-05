<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Generate SHA-256 hash untuk password admin backoffice
        // Password: Kantin123456UPB (sesuai SETUP.md)
        $adminPassword = 'Kantin123456UPB';
        $adminHash     = hash('sha256', $adminPassword);

        $currentDateTime = date('Y-m-d H:i:s');

        // ===============================================
        // Data admin backoffice
        // ===============================================
        // - username: bebas (sesuai clarification poin 4)
        // - npm: NULL (backoffice tidak punya NPM)
        // - login_type: 'backoffice'
        // - role: 'Admin'
        // ===============================================
        $userData = [
            'username'    => 'Admin',
            'npm'         => null,
            'password'    => $adminHash,
            'role'        => 'Admin',
            'login_type'  => 'backoffice',
            'createdby'   => 0,
            'createdat'   => $currentDateTime,
            'updatedby'   => 0,
            'updatedat'   => $currentDateTime,
        ];

        // Cek apakah user sudah ada (hindari duplikasi saat seeder dijalankan ulang)
        $existing = $this->db->table('user')
            ->where('username', $userData['username'])
            ->where('login_type', 'backoffice')
            ->countAllResults();

        if ($existing === 0) {
            $this->db->table('user')->insert($userData);
        }
    }
}
