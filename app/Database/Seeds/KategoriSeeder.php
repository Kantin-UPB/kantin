<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'nama_kategori' => 'Makanan Berat',
            ],
            [
                'nama_kategori' => 'Makanan Ringan',
            ],
            [
                'nama_kategori' => 'Minuman',
            ],
            [
                'nama_kategori' => 'Dessert',
            ],
            [
                'nama_kategori' => 'Cemilan',
            ],
            [
                'nama_kategori' => 'Kopi & Teh',
            ],
        ];

        $this->db->table('kategori')->insertBatch($data);
    }
}
