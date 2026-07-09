<?php

// Lokasi file ini di project: app/Database/Seeds/PoinSeeder.php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PoinSeeder extends Seeder
{
    public function run()
    {
        // Kosongkan dulu supaya seeder aman dijalankan berkali-kali (idempotent)
        $this->db->table('pengaturan_poin')->truncate();

        $data = [
            [
                'nama_aturan'       => 'Poin Reguler',
                'rasio_rupiah'      => 1000,
                'minimal_transaksi' => 10000,
                'status_aktif'      => 'aktif',
                'created_at'        => date('Y-m-d H:i:s'),
                'updated_at'        => date('Y-m-d H:i:s'),
            ],
            [
                'nama_aturan'       => 'Poin Promo Weekend (contoh, nonaktif)',
                'rasio_rupiah'      => 500,
                'minimal_transaksi' => 15000,
                'status_aktif'      => 'nonaktif',
                'created_at'        => date('Y-m-d H:i:s'),
                'updated_at'        => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('pengaturan_poin')->insertBatch($data);
    }
}