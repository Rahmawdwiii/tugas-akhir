<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run()
    {
        $timestamp = date('Y-m-d H:i:s');

        $data = [
            // JURUSAN (ID 1–10)
            ['id' => 1,  'nama_unit' => 'Teknik Sipil',                            'kategori' => 'Jurusan',        'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id' => 2,  'nama_unit' => 'Teknik Mesin',                            'kategori' => 'Jurusan',        'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id' => 3,  'nama_unit' => 'Teknik Elektro',                          'kategori' => 'Jurusan',        'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id' => 4,  'nama_unit' => 'Teknik Kimia',                            'kategori' => 'Jurusan',        'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id' => 5,  'nama_unit' => 'Akuntansi',                               'kategori' => 'Jurusan',        'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id' => 6,  'nama_unit' => 'Administrasi Bisnis',                     'kategori' => 'Jurusan',        'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id' => 7,  'nama_unit' => 'Teknik Komputer',                         'kategori' => 'Jurusan',        'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id' => 8,  'nama_unit' => 'Manajemen Informatika',                   'kategori' => 'Jurusan',        'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id' => 9,  'nama_unit' => 'Bahasa dan Pariwisata',                   'kategori' => 'Jurusan',        'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id' => 10, 'nama_unit' => 'Rekayasa Teknologi dan Bisnis Pertanian', 'kategori' => 'Jurusan',        'created_at' => $timestamp, 'updated_at' => $timestamp],

            // UNIT / LEMBAGA (ID 11–24) — DIBUAT TANPA DUPE
            ['id' => 11, 'nama_unit' => 'BPM',                                     'kategori' => 'Unit/Lembaga',   'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id' => 12, 'nama_unit' => 'Humas',                                   'kategori' => 'Unit/Lembaga',   'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id' => 13, 'nama_unit' => 'Poliklinik',                              'kategori' => 'Unit/Lembaga',   'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id' => 14, 'nama_unit' => 'UPA Perpustakaan',                        'kategori' => 'Unit/Lembaga',   'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id' => 15, 'nama_unit' => 'UPA Bahasa',                              'kategori' => 'Unit/Lembaga',   'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id' => 16, 'nama_unit' => 'UPA Teknologi Informasi dan Komunikasi',  'kategori' => 'Unit/Lembaga',   'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id' => 17, 'nama_unit' => 'UPA Perawatan dan Perbaikan',             'kategori' => 'Unit/Lembaga',   'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id' => 18, 'nama_unit' => 'UPA Layanan Uji Kompetensi',              'kategori' => 'Unit/Lembaga',   'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id' => 19, 'nama_unit' => 'UPA Pengembangan Karir dan Kewirausahaan','kategori' => 'Unit/Lembaga',   'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id' => 20, 'nama_unit' => 'UPA Pengembangan Teknologi dan Produk Unggulan','kategori' => 'Unit/Lembaga', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id' => 21, 'nama_unit' => 'PusBangJar',                               'kategori' => 'Unit/Lembaga',   'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id' => 22, 'nama_unit' => 'P3M',                                      'kategori' => 'Unit/Lembaga',   'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id' => 23, 'nama_unit' => 'BAAK',                                     'kategori' => 'Unit/Lembaga',   'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id' => 24, 'nama_unit' => 'BAUK',                                     'kategori' => 'Unit/Lembaga',   'created_at' => $timestamp, 'updated_at' => $timestamp],
        ];

        $this->db->table('tb_master_unit')->ignore(true)->insertBatch($data);
    }
}
