<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MasterLokasi extends Migration
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
            'id_unit' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true, // Disamakan unsigned agar bisa berelasi dengan tb_master_unit
            ],
            'nomor_inventaris' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'gedung' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'lantai' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            'ruangan' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'kampus' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
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
        $this->forge->addKey('id_unit'); // Indexing untuk pencarian cepat berdasarkan unit
        $this->forge->createTable('tb_master_lokasi');
    }

    public function down()
    {
        $this->forge->dropTable('tb_master_lokasi');
    }
}