<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePaketBundlingItem extends Migration
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
            'id_paket' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'id_menu' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'qty' => [
                'type'       => 'INT',
                'constraint' => 5,
                'unsigned'   => true,
                'default'    => 1,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id_paket', 'paket_bundling', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_menu', 'menu', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('paket_bundling_item');
    }

    public function down()
    {
        $this->forge->dropTable('paket_bundling_item');
    }
}
