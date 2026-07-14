<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TbPerbaikan extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_perbaikan' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],

            'id_jadwal' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],

            'status_kerusakan' => [
                'type'       => 'ENUM',
                'constraint' => ['RINGAN', 'SEDANG', 'BERAT', 'RUSAK'],
                'null'       => true,
            ],

            'status_perbaikan' => [
                'type'       => 'ENUM',
                'constraint' => [
                    'MENUNGGU',
                    'DIKERJAKAN',
                    'PENDING',
                    'SELESAI',
                    'DIKEMBALIKAN'
                ],
                'default' => 'MENUNGGU',
            ],

            'catatan_teknisi' => [
                'type' => 'TEXT',
                'null' => true,
            ],

            'foto_bukti' => [
                'type' => 'TEXT',
                'null' => true,
            ],

            'waktu_mulai' => [
                'type' => 'DATETIME',
                'null' => true,
            ],

            'waktu_selesai' => [
                'type' => 'DATETIME',
                'null' => true,
            ],

            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);

        $this->forge->addKey('id_perbaikan', true);

        $this->forge->addForeignKey(
            'id_jadwal',
            'tb_jadwal_perbaikan',
            'id_jadwal',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->createTable('tb_perbaikan');
    }

    public function down()
    {
        $this->forge->dropTable('tb_perbaikan');
    }
}

