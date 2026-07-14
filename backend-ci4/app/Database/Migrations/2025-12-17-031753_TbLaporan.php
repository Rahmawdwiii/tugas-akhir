<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTbLaporan extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_laporan' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],

            'tanggal_laporan' => [
                'type' => 'DATETIME',
                'null' => false,
            ],

            // IDENTITAS PELAPOR (snapshot, tidak ikut berubah)
            'nama_pelapor' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],

            // DETAIL OBJEK LAPORAN
            'nama_alat' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],

            'nomor_inventaris' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],

            'lokasi' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],

            'unit' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],

            // DESKRIPSI KERUSAKAN AWAL
            'kerusakan' => [
                'type' => 'TEXT',
            ],

            // FOTO BUKTI AWAL
            'path_foto_bukti' => [
                'type' => 'TEXT',
                'null' => true,
            ],

            /**
             * STATUS GLOBAL LAPORAN
             * BUKAN status teknis / proses detail
             *
             * BARU     : laporan baru masuk
             * AKTIF    : sedang diproses (apa pun kondisinya)
             * SELESAI  : teknisi menyatakan selesai, menunggu finalisasi
             * DITUTUP  : pelapor & admin sudah validasi
             */
            'status_laporan' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'BARU',
            ],

            // RATING FINAL DARI PELAPOR
            'rating_pelapor' => [
                'type'       => 'INT',
                'constraint' => 1,
                'null'       => true,
            ],

            // TIMESTAMP
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],

            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id_laporan', true);
        $this->forge->createTable('tb_laporan');
    }

    public function down()
    {
        $this->forge->dropTable('tb_laporan');
    }
}
