<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AlatSeeder extends Seeder
{
    public function run()
    {
        $timestamp = date('Y-m-d H:i:s');

        $data = [
            // ASET (ID 1-23)
            ['id_alat' => 1,  'nomor_inventaris' => 'PC-004-LAB1',   'nama_alat' => 'Komputer/PC',         'kategori' => 'Aset', 'id_teknisi' => 3, 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_alat' => 2,  'nomor_inventaris' => 'LTP-012-ADM',   'nama_alat' => 'Laptop',              'kategori' => 'Aset', 'id_teknisi' => 3, 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_alat' => 3,  'nomor_inventaris' => 'PRN-002-ADM',   'nama_alat' => 'Printer',             'kategori' => 'Aset', 'id_teknisi' => 3, 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_alat' => 4,  'nomor_inventaris' => 'AP-015-GKU',    'nama_alat' => 'ACCESS POINT',        'kategori' => 'Aset', 'id_teknisi' => 3, 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_alat' => 5,  'nomor_inventaris' => 'FO-001-JAR',    'nama_alat' => 'KABEL FIBER OPTIC',   'kategori' => 'Aset', 'id_teknisi' => 3, 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_alat' => 6,  'nomor_inventaris' => 'ABS-001-ADM',   'nama_alat' => 'Absensi',             'kategori' => 'Aset', 'id_teknisi' => 3, 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_alat' => 7,  'nomor_inventaris' => 'LMP-021-GKU',   'nama_alat' => 'LAMPU PENERANGAN',    'kategori' => 'Aset', 'id_teknisi' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_alat' => 8,  'nomor_inventaris' => 'MCB-001-PNL',   'nama_alat' => 'MCB',                 'kategori' => 'Aset', 'id_teknisi' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_alat' => 9,  'nomor_inventaris' => 'SK-105-KLS',    'nama_alat' => 'STOP KONTAK',         'kategori' => 'Aset', 'id_teknisi' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_alat' => 10, 'nomor_inventaris' => 'LPJU-030-AREA', 'nama_alat' => 'LPJU',                'kategori' => 'Aset', 'id_teknisi' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_alat' => 11, 'nomor_inventaris' => 'PSU-001-LAB',   'nama_alat' => 'POWERSUPPLY',         'kategori' => 'Aset', 'id_teknisi' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_alat' => 12, 'nomor_inventaris' => 'CCTV-008-GKU',  'nama_alat' => 'CCTV',                'kategori' => 'Aset', 'id_teknisi' => 5, 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_alat' => 13, 'nomor_inventaris' => 'TV-003-RPT',    'nama_alat' => 'TV/SMART TV',         'kategori' => 'Aset', 'id_teknisi' => 5, 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_alat' => 14, 'nomor_inventaris' => 'VDT-001-GKU',   'nama_alat' => 'VIDEOTRONE',          'kategori' => 'Aset', 'id_teknisi' => 5, 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_alat' => 15, 'nomor_inventaris' => 'SND-001-AUD',   'nama_alat' => 'SOUNSYSTEM/PORTABLE', 'kategori' => 'Aset', 'id_teknisi' => 5, 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_alat' => 16, 'nomor_inventaris' => 'TLP-005-ADM',   'nama_alat' => 'TELPON',              'kategori' => 'Aset', 'id_teknisi' => 5, 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_alat' => 17, 'nomor_inventaris' => 'AC-001-GK5',    'nama_alat' => 'AIR CONDITIONER',     'kategori' => 'Aset', 'id_teknisi' => 6, 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_alat' => 18, 'nomor_inventaris' => 'PMP-002-GKU',   'nama_alat' => 'POMPA AIR',           'kategori' => 'Aset', 'id_teknisi' => 6, 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_alat' => 19, 'nomor_inventaris' => 'KLK-001-PNT',   'nama_alat' => 'KULKAS',              'kategori' => 'Aset', 'id_teknisi' => 6, 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_alat' => 20, 'nomor_inventaris' => 'DSP-003-PNT',   'nama_alat' => 'DISPENSER',           'kategori' => 'Aset', 'id_teknisi' => 6, 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_alat' => 21, 'nomor_inventaris' => 'GEN-001-GKU',   'nama_alat' => 'GENSET',              'kategori' => 'Aset', 'id_teknisi' => 6, 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_alat' => 22, 'nomor_inventaris' => 'KPS-010-KLS',   'nama_alat' => 'KIPAS ANGIN',         'kategori' => 'Aset', 'id_teknisi' => 6, 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_alat' => 23, 'nomor_inventaris' => 'IPF-001-GKU',   'nama_alat' => 'IP POGGING',          'kategori' => 'Aset', 'id_teknisi' => 6, 'created_at' => $timestamp, 'updated_at' => $timestamp],
            
            // BHP (ID 24-46)
            ['id_alat' => 24, 'nomor_inventaris' => 'SPR-SK-001',    'nama_alat' => 'Stop Kontak 3 Lubang',         'kategori' => 'BHP', 'id_teknisi' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_alat' => 25, 'nomor_inventaris' => 'SPR-SK-002',    'nama_alat' => 'Stop Kontak 4 Lubang + Kabel', 'kategori' => 'BHP', 'id_teknisi' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_alat' => 26, 'nomor_inventaris' => 'SPR-MCB-001',   'nama_alat' => 'MCB 6 Ampere',                 'kategori' => 'BHP', 'id_teknisi' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_alat' => 27, 'nomor_inventaris' => 'SPR-ISO-001',   'nama_alat' => 'Isolasi Listrik Hitam',        'kategori' => 'BHP', 'id_teknisi' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_alat' => 28, 'nomor_inventaris' => 'TOOL-TS-001',   'nama_alat' => 'Tespen Bunyi',                 'kategori' => 'BHP', 'id_teknisi' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_alat' => 29, 'nomor_inventaris' => 'SPR-KBL-001',   'nama_alat' => 'Kabel UTP Cat6 (Roll)',        'kategori' => 'BHP', 'id_teknisi' => 3, 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_alat' => 30, 'nomor_inventaris' => 'SPR-CON-001',   'nama_alat' => 'Connector RJ45 (Box)',         'kategori' => 'BHP', 'id_teknisi' => 3, 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_alat' => 31, 'nomor_inventaris' => 'SPR-BAT-001',   'nama_alat' => 'Baterai CMOS',                 'kategori' => 'BHP', 'id_teknisi' => 3, 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_alat' => 32, 'nomor_inventaris' => 'TOOL-TC-001',   'nama_alat' => 'Tang Crimping',                'kategori' => 'BHP', 'id_teknisi' => 3, 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_alat' => 33, 'nomor_inventaris' => 'TOOL-LT-001',   'nama_alat' => 'LAN Tester',                   'kategori' => 'BHP', 'id_teknisi' => 3, 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_alat' => 34, 'nomor_inventaris' => 'SPR-FRE-001',   'nama_alat' => 'Freon R32 (Tabung)',           'kategori' => 'BHP', 'id_teknisi' => 6, 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_alat' => 35, 'nomor_inventaris' => 'SPR-PIP-001',   'nama_alat' => 'Pipa Tembaga AC (Roll)',       'kategori' => 'BHP', 'id_teknisi' => 6, 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_alat' => 36, 'nomor_inventaris' => 'SPR-CAP-001',   'nama_alat' => 'Kapasitor AC',                 'kategori' => 'BHP', 'id_teknisi' => 6, 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_alat' => 37, 'nomor_inventaris' => 'TOOL-OBG-001',  'nama_alat' => 'Obeng Set Lengkap',            'kategori' => 'BHP', 'id_teknisi' => 1, 'created_at' => $timestamp, 'updated_at' => '2025-12-01 16:34:15'],
            ['id_alat' => 39, 'nomor_inventaris' => 'SPR-BAT-AA',    'nama_alat' => 'Baterai AA (Box)',             'kategori' => 'BHP', 'id_teknisi' => 1, 'created_at' => $timestamp, 'updated_at' => $timestamp],
            
            // DATA BARU (Tanpa Kategori di Data Asli)
            ['id_alat' => 44, 'nomor_inventaris' => 'TOOL-PLU-001',  'nama_alat' => 'Palu',    'kategori' => '', 'id_teknisi' => 1, 'created_at' => '2025-11-28 19:51:54', 'updated_at' => '2025-11-28 19:51:54'],
            ['id_alat' => 45, 'nomor_inventaris' => 'TOOL-LMP-002',  'nama_alat' => 'Lampu',   'kategori' => '', 'id_teknisi' => 1, 'created_at' => '2025-12-01 15:06:29', 'updated_at' => '2025-12-01 15:06:29'],
            ['id_alat' => 46, 'nomor_inventaris' => 'TOOL-KBL-003',  'nama_alat' => 'Kabel',   'kategori' => '', 'id_teknisi' => 1, 'created_at' => '2025-12-01 15:06:29', 'updated_at' => '2025-12-01 16:33:53'],
        ];

        // Insert Batch
        $this->db->table('tb_master_alat')->ignore(true)->insertBatch($data);
    }
}