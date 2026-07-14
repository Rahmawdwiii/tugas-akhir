<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUniqueConstraintNomorLaporan extends Migration
{
    public function up()
    {
        // Tambahkan unique constraint ke kolom nomor_laporan
        $this->forge->addUniqueKey('nomor_laporan', 'unik_nomor_laporan');
        $this->forge->processIndexes('tb_laporan');
    }

    public function down()
    {
        // Hapus unique constraint
        $this->forge->dropKey('tb_laporan', 'unik_nomor_laporan', false);
    }
}
