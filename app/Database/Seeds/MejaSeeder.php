<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MejaSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['id_meja' => 'A1', 'status' => 'kosong'],
            ['id_meja' => 'A2', 'status' => 'kosong'],
            ['id_meja' => 'A3', 'status' => 'kosong'],
            ['id_meja' => 'A4', 'status' => 'kosong'],
            ['id_meja' => 'B1', 'status' => 'kosong'],
            ['id_meja' => 'B2', 'status' => 'kosong'],
            ['id_meja' => 'B3', 'status' => 'kosong'],
            ['id_meja' => 'B4', 'status' => 'kosong'],
        ];
             $this->db->table('meja')->insertBatch($data);
    }
}
