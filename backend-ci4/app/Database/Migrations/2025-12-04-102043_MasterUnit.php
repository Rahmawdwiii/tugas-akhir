<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MasterUnit extends Migration
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
            'nama_unit' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'kategori' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => 'Unit/Lembaga', // Default value sesuai SQL
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('tb_master_unit');
    }

    public function down()
    {
        $this->forge->dropTable('tb_master_unit');
    }
}
