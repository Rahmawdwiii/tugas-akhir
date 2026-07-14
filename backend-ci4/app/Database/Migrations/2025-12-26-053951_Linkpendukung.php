<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Linkpendukung extends Migration
{
    public function up()
    {
        // Menambahkan kolom 'link_pendukung' ke tabel 'tb_laporan'
        $fields = [
            'link_pendukung' => [
                'type'       => 'TEXT',       // Gunakan TEXT agar muat link panjang
                'null'       => true,         // Wajib TRUE (karena opsional)
                'default'    => null,
                'after'      => 'path_foto_bukti' // Posisi kolom (setelah foto)
            ],
        ];

        // Pastikan nama tabelnya sesuai dengan database Anda (tb_laporan)
        $this->forge->addColumn('tb_laporan', $fields);
    }

    public function down()
    {
        // Menghapus kolom jika migration di-rollback
        $this->forge->dropColumn('tb_laporan', 'link_pendukung');
    }
}