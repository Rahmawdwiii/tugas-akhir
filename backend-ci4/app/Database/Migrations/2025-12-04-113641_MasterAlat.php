<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MasterAlat extends Migration
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
            'nomor_inventaris' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'nama_alat' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'kategori' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => 'Aset', // Default sesuai data mayoritas
            ],
            'id_teknisi' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true, // Relasi ke tb_user (id)
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
        $this->forge->addKey('id_teknisi'); // Index biar loading data teknisi cepat
        $this->forge->createTable('tb_master_alat');
    }

    public function down()
    {
        $this->forge->dropTable('tb_master_alat');
    }
}