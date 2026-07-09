<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMejaTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_meja' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'kosong',
            ],
        ]);
        
        $this->forge->addKey('id_meja', true);
        
        $this->forge->createTable('meja');
    }

    public function down()
    {
        $this->forge->dropTable('meja');
    }
}
