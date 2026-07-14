<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TbJadwalPerbaikan extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_jadwal' => [
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

            'id_teknisi' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],

            'tanggal_perbaikan' => [
                'type' => 'DATE',
                'null' => true,
            ],

            'jenis_penugasan' => [
                'type'       => 'ENUM',
                'constraint' => ['AUTO', 'MANUAL'],
                'default'    => 'AUTO',
            ],

            'status_perbaikan' => [
                'type'       => 'ENUM',
                'constraint' => ['AKTIF', 'DIGANTI', 'SELESAI', 'MENUNGGU'],
                'default'    => 'AKTIF',
            ],

            'keterangan_admin' => [
                'type' => 'TEXT',
                'null' => true,
            ],

            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);

        $this->forge->addKey('id_jadwal', true);

        $this->forge->addForeignKey(
            'id_laporan',
            'tb_laporan',
            'id_laporan',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->addForeignKey(
            'id_teknisi',
            'tb_user',
            'id_user',
            'RESTRICT',
            'CASCADE'
        );

        $this->forge->createTable('tb_jadwal_perbaikan');
    }

    public function down()
    {
        $this->forge->dropTable('tb_jadwal_perbaikan');
    }
}
