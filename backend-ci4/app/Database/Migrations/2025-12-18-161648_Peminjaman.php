<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Peminjaman extends Migration
{
    public function up()
    {
        // Mendefinisikan kolom
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nomor' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'id_unit' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'kegiatan' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'lokasi' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'tanggal_mulai' => [
                'type' => 'DATE',
            ],
            'tanggal_selesai' => [
                'type' => 'DATE',
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true, // Boleh kosong
            ],
            'identitas' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'peminjam' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'handphone' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            'lampiran' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true, // Boleh kosong
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        // Menentukan Primary Key
        $this->forge->addKey('id', true);

        // Membuat Tabel
        $this->forge->createTable('tb_peminjaman');
    }

    public function down()
    {
        // Menghapus tabel jika rollback
        $this->forge->dropTable('tb_peminjaman');
    }
}