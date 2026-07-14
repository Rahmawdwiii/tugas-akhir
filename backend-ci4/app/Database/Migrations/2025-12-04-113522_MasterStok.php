<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MasterStok extends Migration
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
            'id_alat' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true, // Disamakan unsigned agar bisa direlasikan nanti
            ],
            'jumlah' => [
                'type'       => 'INT',
                'constraint' => 11,
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
        // Opsional: Menambahkan Key index untuk id_alat agar pencarian lebih cepat
        $this->forge->addKey('id_alat'); 
        
        $this->forge->createTable('tb_master_stok');
    }

    public function down()
    {
        $this->forge->dropTable('tb_master_stok');
    }
}