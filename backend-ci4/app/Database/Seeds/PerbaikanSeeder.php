<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PerbaikanSeeder extends Seeder
{
    public function run()
    {
        // Tidak ada data default untuk tb_perbaikan.
        // Data perbaikan harus dibuat sesudah pelapor mengirim laporan,
        // teknisi menentukan status kerusakan, dan admin membuat jadwal perbaikan.
    }
}
