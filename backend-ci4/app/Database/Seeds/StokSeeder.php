<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class StokSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['id' => 1,  'id_alat' => 24, 'jumlah' => 20, 'created_at' => '2025-11-27 21:35:37', 'updated_at' => '2025-11-27 21:35:37'],
            ['id' => 2,  'id_alat' => 25, 'jumlah' => 15, 'created_at' => '2025-11-27 21:35:37', 'updated_at' => '2025-11-27 21:35:37'],
            ['id' => 3,  'id_alat' => 26, 'jumlah' => 10, 'created_at' => '2025-11-27 21:35:37', 'updated_at' => '2025-11-27 21:35:37'],
            ['id' => 4,  'id_alat' => 27, 'jumlah' => 50, 'created_at' => '2025-11-27 21:35:37', 'updated_at' => '2025-11-27 21:35:37'],
            ['id' => 5,  'id_alat' => 28, 'jumlah' => 5,  'created_at' => '2025-11-27 21:35:37', 'updated_at' => '2025-11-27 21:35:37'],
            ['id' => 6,  'id_alat' => 29, 'jumlah' => 2,  'created_at' => '2025-11-27 21:35:37', 'updated_at' => '2025-11-27 21:35:37'],
            ['id' => 7,  'id_alat' => 30, 'jumlah' => 10, 'created_at' => '2025-11-27 21:35:37', 'updated_at' => '2025-12-01 14:53:05'],
            ['id' => 8,  'id_alat' => 32, 'jumlah' => 3,  'created_at' => '2025-11-27 21:35:37', 'updated_at' => '2025-11-27 21:35:37'],
            ['id' => 9,  'id_alat' => 33, 'jumlah' => 2,  'created_at' => '2025-11-27 21:35:37', 'updated_at' => '2025-11-27 21:35:37'],
            ['id' => 10, 'id_alat' => 34, 'jumlah' => 4,  'created_at' => '2025-11-27 21:35:37', 'updated_at' => '2025-11-27 21:35:37'],
            ['id' => 11, 'id_alat' => 36, 'jumlah' => 8,  'created_at' => '2025-11-27 21:35:37', 'updated_at' => '2025-11-27 21:35:37'],
            ['id' => 12, 'id_alat' => 37, 'jumlah' => 4,  'created_at' => '2025-11-27 21:35:37', 'updated_at' => '2025-11-27 21:35:37'],
            ['id' => 14, 'id_alat' => 38, 'jumlah' => 10, 'created_at' => '2025-11-28 19:07:53', 'updated_at' => '2025-11-28 19:07:53'],
            ['id' => 25, 'id_alat' => 43, 'jumlah' => 5,  'created_at' => '2025-11-28 19:48:50', 'updated_at' => '2025-11-28 19:48:50'],
            ['id' => 26, 'id_alat' => 44, 'jumlah' => 10, 'created_at' => '2025-11-28 19:51:54', 'updated_at' => '2025-11-28 19:51:54'],
            ['id' => 27, 'id_alat' => 45, 'jumlah' => 10, 'created_at' => '2025-12-01 15:06:29', 'updated_at' => '2025-12-01 15:06:29'],
            ['id' => 28, 'id_alat' => 46, 'jumlah' => 10, 'created_at' => '2025-12-01 15:06:29', 'updated_at' => '2025-12-01 15:06:29'],
        ];

        // Insert Batch
        $this->db->table('tb_master_stok')->ignore(true)->insertBatch($data);
    }
}