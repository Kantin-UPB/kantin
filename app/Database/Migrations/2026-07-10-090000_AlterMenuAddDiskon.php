<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterMenuAddDiskon extends Migration
{
    public function up()
    {
        $this->forge->addColumn('menu', [
            'diskon' => [
                'type'       => 'INT',
                'constraint' => 3,
                'unsigned'   => true,
                'default'    => 0,
                'null'       => false,
                'after'      => 'harga',
                'comment'    => 'Persentase diskon menu, 0-100',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('menu', 'diskon');
    }
}
