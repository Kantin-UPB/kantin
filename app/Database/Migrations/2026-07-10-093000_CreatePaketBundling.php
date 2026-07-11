<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePaketBundling extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_paket' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'harga_paket' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0,
                'comment'    => 'Harga jual paket bundling (harga spesial)',
            ],
            'status_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 1,
                'comment'    => '1=Pending, 5=Active, 8=Cancel',
            ],
            'url_gambar' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('paket_bundling');
    }

    public function down()
    {
        $this->forge->dropTable('paket_bundling');
    }
}
