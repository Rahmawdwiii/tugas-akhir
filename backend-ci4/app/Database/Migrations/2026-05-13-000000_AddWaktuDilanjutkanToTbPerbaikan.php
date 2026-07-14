<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddWaktuDilanjutkanToTbPerbaikan extends Migration
{
    public function up()
    {
        $fields = [
            'waktu_dilanjutkan' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'waktu_mulai',
            ],
        ];

        $this->forge->addColumn('tb_perbaikan', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('tb_perbaikan', 'waktu_dilanjutkan');
    }
}
