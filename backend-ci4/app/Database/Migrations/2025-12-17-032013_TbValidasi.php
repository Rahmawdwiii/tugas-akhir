<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TbValidasi extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_validasi' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],

            'id_laporan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],

            'id_perbaikan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],

            'jenis_validasi' => [
                'type'       => 'ENUM',
                'constraint' => ['PELAPOR', 'ADMIN'],
            ],

            'hasil_validasi' => [
                'type'       => 'ENUM',
                'constraint' => ['DISETUJUI', 'DITOLAK'],
            ],

            'catatan' => [
                'type' => 'TEXT',
                'null' => true,
            ],

            // hanya untuk pelapor
            'rating' => [
                'type'       => 'INT',
                'constraint' => 1,
                'null'       => true,
            ],

            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);

        $this->forge->addKey('id_validasi', true);

        $this->forge->addForeignKey(
            'id_laporan',
            'tb_laporan',
            'id_laporan',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->addForeignKey(
            'id_perbaikan',
            'tb_perbaikan',
            'id_perbaikan',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->createTable('tb_validasi');
    }

    public function down()
    {
        $this->forge->dropTable('tb_validasi');
    }
}
