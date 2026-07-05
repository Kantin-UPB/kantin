<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLogsystemTable extends Migration
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
            'user_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'null'           => true, // null jika aksi dilakukan oleh guest/belum login
            ],
            'activity' => [
                'type'       => 'VARCHAR',
                'constraint' => '255', // Contoh: "Melakukan Pemesanan Makanan", "Logout"
            ],
            'description' => [
                'type' => 'TEXT', // Detail log: "User memesan Nasi Goreng (x2)"
                'null' => true,
            ],
            'ip_address' => [
                'type'       => 'VARCHAR',
                'constraint' => '45', // Mendukung IPv4 & IPv6
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('log_system'); //
    }

    public function down()
    {
    $this->forge->dropTable('log_system');
    }
}
