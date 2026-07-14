<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEmailToTbUser extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tb_user', [
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'after'      => 'akses'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('tb_user', 'email');
    }
}