<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Array Penampung Semua Data
        $data = [];

        // ==========================================
        // 1. DATA LEGACY (ID 1 - 7) - TETAP
        // ==========================================
        $passLegacy = password_hash('Polsri2020#', PASSWORD_DEFAULT);

        $legacyUsers = [
            [1, 'sukri',     'Sukri',            'Administrasi',        'admin'],
            [2, 'harba',     'Harba Ario Sukha', 'Kepala Unit',         'admin'],
            [3, 'icon',      'M. Karison',       'Teknisi Komputer',    'teknisi'],
            [4, 'riput',     'Riadi Putra',      'Teknisi Kelistrikan', 'teknisi'],
            [5, 'edial',     'Edial Salmes',     'Teknisi Elektronika', 'teknisi'],
            [6, 'cipto',     'Cipto',            'Teknisi AC',          'teknisi'],
            [7, 'sairespen', 'Sairespen',        'Teknisi AC',          'teknisi'],
        ];

        foreach ($legacyUsers as $u) {
            $data[] = [
                'id'       => $u[0],
                'username' => $u[1],
                'password' => $passLegacy,
                'nama'     => $u[2],
                'jabatan'  => $u[3],
                'akses'    => $u[4]
            ];
        }

        // ==========================================
        // 2. DATA SATPAM & UNIT BARU (ID 8 dst)
        // ==========================================
        
        $currentId = 8; 

        // Format Raw Data: [Username, Password Asli, Nama Unit, Jabatan, Akses]
        $newUsers = [
            // --- A. SATPAM (DATA LAMA DIPERTAHANKAN) ---
            ['satpam_elektro',   'JagaElektro#2025!', 'Satpam Teknik Elektro', 'Keamanan', 'pelapor'],
            ['satpam_kimia',     'JagaKimia#2025!',   'Satpam Teknik Kimia',   'Keamanan', 'pelapor'],
            ['satpam_akuntansi', 'JagaAkun#2025!',    'Satpam Akuntansi',      'Keamanan', 'pelapor'],
            ['satpam_ab',        'JagaAB#2025!',      'Satpam Admin Bisnis',   'Keamanan', 'pelapor'],
            ['satpam_tekkom',    'JagaTekkom#2025!',  'Satpam Teknik Komputer','Keamanan', 'pelapor'],
            ['satpam_pertanian', 'JagaTani#2025!',    'Satpam Gedung RTBP',    'Keamanan', 'pelapor'],
            ['satpam_perpus',    'JagaPerpus#2025!',  'Satpam Perpustakaan',   'Keamanan', 'pelapor'],
            ['satpam_tik',       'JagaTik#2025!',     'Satpam UPA-TIK',        'Keamanan', 'pelapor'],
            ['satpam_p3m',       'JagaP3m#2025!',     'Satpam Gedung P3M',     'Keamanan', 'pelapor'],
            ['satpam_mibahasa',  'JagaMIBahasa#2025!','Satpam Gedung MI & BAHASA', 'Keamanan', 'pelapor'],

            // --- B. JURUSAN (DATA BARU) ---
            ['admin_sipil',      'Sipil#2025!',       'Jurusan Teknik Sipil',                    'Admin Jurusan', 'pelapor'],
            ['admin_mesin',      'Mesin#2025!',       'Jurusan Teknik Mesin',                    'Admin Jurusan', 'pelapor'],
            ['admin_elektro',    'Elektro#2025!',     'Jurusan Teknik Elektro',                  'Admin Jurusan', 'pelapor'],
            ['admin_kimia',      'Kimia#2025!',       'Jurusan Teknik Kimia',                    'Admin Jurusan', 'pelapor'],
            ['admin_akuntansi',  'Akun#2025!',        'Jurusan Akuntansi',                       'Admin Jurusan', 'pelapor'],
            ['admin_ab',         'Bisnis#2025!',      'Jurusan Administrasi Bisnis',             'Admin Jurusan', 'pelapor'],
            ['admin_tekkom',     'Tekkom#2025!',      'Jurusan Teknik Komputer',                 'Admin Jurusan', 'pelapor'],
            ['admin_mi',         'Mi#2025!',          'Jurusan Manajemen Informatika',           'Admin Jurusan', 'pelapor'],
            ['admin_bahasa',     'Bahasa#2025!',      'Jurusan Bahasa dan Pariwisata',           'Admin Jurusan', 'pelapor'],
            ['admin_pertanian',  'Tani#2025!',        'Jurusan Rekayasa Teknologi Pertanian',    'Admin Jurusan', 'pelapor'],

            // --- C. ADMINISTRASI PUSAT (DATA BARU) ---
            ['admin_baak',       'Baak#2025!',        'Bagian Akademik Dan Kemahasiswaan',       'Admin Pusat',   'pelapor'],
            ['admin_bauk',       'Bauk#2025!',        'Bagian Umum, Keuangan, Dan Kepegawaian',  'Admin Pusat',   'pelapor'],

            // --- D. PENUNJANG AKADEMIK / UPA (DATA BARU) ---
            ['upa_perpus',       'Perpus#2025!',      'UPA. Perpustakaan',                       'Admin UPA',     'pelapor'],
            ['upa_bahasa',       'UpaBahasa#2025!',   'UPA. Bahasa',                             'Admin UPA',     'pelapor'],
            ['upa_tik',          'UpaTik#2025!',      'UPA. Teknologi Informasi Dan Komunikasi', 'Admin UPA',     'pelapor'],
            ['upa_perbaikan',    'UpaRepair#2025!',   'UPA. Perawatan dan Perbaikan',            'Admin UPA',     'pelapor'],
            ['upa_kompetensi',   'UpaUji#2025!',      'UPA. Layanan Uji Kompetensi',             'Admin UPA',     'pelapor'],
            ['upa_karir',        'UpaKarir#2025!',    'UPA. Pengembangan Karir & Kewirausahaan', 'Admin UPA',     'pelapor'],
            ['upa_unggulan',     'UpaProduk#2025!',   'UPA. Pengembangan Teknologi & Produk',    'Admin UPA',     'pelapor'],
        ];

        foreach ($newUsers as $u) {
            $data[] = [
                'id'       => $currentId++, // ID Manual: 8, 9, 10, dst...
                'username' => $u[0],
                'password' => password_hash($u[1], PASSWORD_DEFAULT), // Hash password
                'nama'     => $u[2],
                'jabatan'  => $u[3],
                'akses'    => $u[4]
            ];
        }

        // Eksekusi Insert Sekaligus
        // Menggunakan ignore(true) untuk keamanan duplikasi ID
        $this->db->table('tb_user')->ignore(true)->insertBatch($data);
    }
}