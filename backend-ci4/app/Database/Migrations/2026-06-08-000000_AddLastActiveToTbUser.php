<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLastActiveToTbUser extends Migration
{
    public function up()
    {
        $fields = [
            'last_active' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'is_online',
            ],
        ];

        $this->forge->addColumn('tb_user', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('tb_user', 'last_active');
    }
}
