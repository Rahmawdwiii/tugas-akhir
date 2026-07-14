<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class LokasiSeeder extends Seeder
{
    public function run()
    {
        $created = date('Y-m-d H:i:s');
        $updated = date('Y-m-d H:i:s');

        $data = [
            // ============================================================
            // A. JURUSAN (ID 1-10)
            // ============================================================
            
            // 1. Teknik Sipil
            ['id_unit' => 1, 'gedung' => 'Gedung Teknik Sipil', 'lantai' => 'Lantai 1', 'ruangan' => 'Ruang Ketua Jurusan Teknik Sipil', 'kampus' => 'Kampus Utama', 'created_at' => $created, 'updated_at' => $updated],
            ['id_unit' => 1, 'gedung' => 'Gedung Teknik Sipil', 'lantai' => 'Lantai 1', 'ruangan' => 'Lab Uji Tanah', 'kampus' => 'Kampus Utama', 'created_at' => $created, 'updated_at' => $updated],
            ['id_unit' => 1, 'gedung' => 'Gedung Teknik Sipil', 'lantai' => 'Lantai 2', 'ruangan' => 'Studio Gambar Sipil', 'kampus' => 'Kampus Utama', 'created_at' => $created, 'updated_at' => $updated],

            // 2. Teknik Mesin
            ['id_unit' => 2, 'gedung' => 'Gedung Teknik Mesin', 'lantai' => 'Lantai 1', 'ruangan' => 'Ruang Ketua Jurusan Teknik Mesin', 'kampus' => 'Kampus Utama', 'created_at' => $created, 'updated_at' => $updated],
            ['id_unit' => 2, 'gedung' => 'Gedung Teknik Mesin', 'lantai' => 'Lantai 1', 'ruangan' => 'Bengkel Produksi', 'kampus' => 'Kampus Utama', 'created_at' => $created, 'updated_at' => $updated],
            ['id_unit' => 2, 'gedung' => 'Gedung Teknik Mesin', 'lantai' => 'Lantai 2', 'ruangan' => 'Lab CNC', 'kampus' => 'Kampus Utama', 'created_at' => $created, 'updated_at' => $updated],

            // 3. Teknik Elektro
            ['id_unit' => 3, 'gedung' => 'Gedung Teknik Elektro', 'lantai' => 'Lantai 1', 'ruangan' => 'Ruang Ketua Jurusan Teknik Elektro', 'kampus' => 'Kampus Utama', 'created_at' => $created, 'updated_at' => $updated],
            ['id_unit' => 3, 'gedung' => 'Gedung Teknik Elektro', 'lantai' => 'Lantai 2', 'ruangan' => 'Lab Listrik Dasar', 'kampus' => 'Kampus Utama', 'created_at' => $created, 'updated_at' => $updated],
            ['id_unit' => 3, 'gedung' => 'Gedung Teknik Elektro', 'lantai' => 'Lantai 2', 'ruangan' => 'Lab Elektronika', 'kampus' => 'Kampus Utama', 'created_at' => $created, 'updated_at' => $updated],

            // 4. Teknik Kimia
            ['id_unit' => 4, 'gedung' => 'Gedung Teknik Kimia', 'lantai' => 'Lantai 1', 'ruangan' => 'Ruang Ketua Jurusan Teknik Kimia', 'kampus' => 'Kampus Utama', 'created_at' => $created, 'updated_at' => $updated],
            ['id_unit' => 4, 'gedung' => 'Gedung Teknik Kimia', 'lantai' => 'Lantai 1', 'ruangan' => 'Lab Operasi Teknik Kimia', 'kampus' => 'Kampus Utama', 'created_at' => $created, 'updated_at' => $updated],

            // 5. Akuntansi
            ['id_unit' => 5, 'gedung' => 'Gedung Akuntansi', 'lantai' => 'Lantai 1', 'ruangan' => 'Ruang Ketua Jurusan Akuntansi', 'kampus' => 'Kampus Utama', 'created_at' => $created, 'updated_at' => $updated],
            ['id_unit' => 5, 'gedung' => 'Gedung Akuntansi', 'lantai' => 'Lantai 2', 'ruangan' => 'Lab Komputer Akuntansi 1', 'kampus' => 'Kampus Utama', 'created_at' => $created, 'updated_at' => $updated],
            ['id_unit' => 5, 'gedung' => 'Gedung Akuntansi', 'lantai' => 'Lantai 2', 'ruangan' => 'Mini Bank', 'kampus' => 'Kampus Utama', 'created_at' => $created, 'updated_at' => $updated],

            // 6. Administrasi Bisnis
            ['id_unit' => 6, 'gedung' => 'Gedung AB', 'lantai' => 'Lantai 1', 'ruangan' => 'Ruang Ketua Jurusan Administrasi Bisnis', 'kampus' => 'Kampus Utama', 'created_at' => $created, 'updated_at' => $updated],
            ['id_unit' => 6, 'gedung' => 'Gedung AB', 'lantai' => 'Lantai 1', 'ruangan' => 'Lab Perkantoran Digital', 'kampus' => 'Kampus Utama', 'created_at' => $created, 'updated_at' => $updated],

            // 7. Teknik Komputer
            ['id_unit' => 7, 'gedung' => 'Gedung Teknik Komputer', 'lantai' => 'Lantai 1', 'ruangan' => 'Ruang Ketua Jurusan Teknik Komputer', 'kampus' => 'Kampus Utama', 'created_at' => $created, 'updated_at' => $updated],
            ['id_unit' => 7, 'gedung' => 'Gedung Teknik Komputer', 'lantai' => 'Lantai 2', 'ruangan' => 'Lab Hardware & Embedded', 'kampus' => 'Kampus Utama', 'created_at' => $created, 'updated_at' => $updated],
            ['id_unit' => 7, 'gedung' => 'Gedung Teknik Komputer', 'lantai' => 'Lantai 2', 'ruangan' => 'Lab Jaringan Komputer', 'kampus' => 'Kampus Utama', 'created_at' => $created, 'updated_at' => $updated],

            // 8. Manajemen Informatika
            ['id_unit' => 8, 'gedung' => 'Gedung MI', 'lantai' => 'Lantai 2', 'ruangan' => 'Ruang Ketua Jurusan Manajemen Informatika', 'kampus' => 'Kampus Utama', 'created_at' => $created, 'updated_at' => $updated],
            ['id_unit' => 8, 'gedung' => 'Gedung MI', 'lantai' => 'Lantai 3', 'ruangan' => 'Lab 1 (Basis Data)', 'kampus' => 'Kampus Utama', 'created_at' => $created, 'updated_at' => $updated],
            ['id_unit' => 8, 'gedung' => 'Gedung MI', 'lantai' => 'Lantai 3', 'ruangan' => 'Lab 2 (Pemrograman)', 'kampus' => 'Kampus Utama', 'created_at' => $created, 'updated_at' => $updated],
            ['id_unit' => 8, 'gedung' => 'Gedung MI', 'lantai' => 'Lantai 3', 'ruangan' => 'Lab 3 (Multimedia)', 'kampus' => 'Kampus Utama', 'created_at' => $created, 'updated_at' => $updated],

            // 9. Bahasa dan Pariwisata
            ['id_unit' => 9, 'gedung' => 'Gedung Bahasa', 'lantai' => 'Lantai 1', 'ruangan' => 'Ruang Ketua Jurusan Bahasa dan Pariwisata', 'kampus' => 'Kampus Utama', 'created_at' => $created, 'updated_at' => $updated],
            ['id_unit' => 9, 'gedung' => 'Gedung Bahasa', 'lantai' => 'Lantai 2', 'ruangan' => 'Lab Bahasa Multimedia', 'kampus' => 'Kampus Utama', 'created_at' => $created, 'updated_at' => $updated],
            ['id_unit' => 9, 'gedung' => 'Gedung Bahasa', 'lantai' => 'Lantai 3', 'ruangan' => 'Kitchen Hotel (Praktek)', 'kampus' => 'Kampus Utama', 'created_at' => $created, 'updated_at' => $updated],

            // 10. Rekayasa Teknologi Pertanian
            ['id_unit' => 10, 'gedung' => 'Gedung Pertanian', 'lantai' => 'Lantai 1', 'ruangan' => 'Ruang Ketua Jurusan RTBP', 'kampus' => 'Kampus B', 'created_at' => $created, 'updated_at' => $updated],
            ['id_unit' => 10, 'gedung' => 'Gedung Pertanian', 'lantai' => 'Lantai 1', 'ruangan' => 'Greenhouse', 'kampus' => 'Kampus B', 'created_at' => $created, 'updated_at' => $updated],

            // ============================================================
            // B. ADMINISTRASI (ID 11-12)
            // ============================================================

            // 11. Bagian Akademik Dan Kemahasiswaan
            ['id_unit' => 11, 'gedung' => 'Gedung KPA (Pusat)', 'lantai' => 'Lantai 1', 'ruangan' => 'Ruang Koordinator Akademik & Kemahasiswaan', 'kampus' => 'Kampus Utama', 'created_at' => $created, 'updated_at' => $updated],
            ['id_unit' => 11, 'gedung' => 'Gedung KPA (Pusat)', 'lantai' => 'Lantai 1', 'ruangan' => 'Loket Pelayanan Mahasiswa', 'kampus' => 'Kampus Utama', 'created_at' => $created, 'updated_at' => $updated],

            // 12. Bagian Umum, Keuangan, Dan Kepegawaian
            ['id_unit' => 12, 'gedung' => 'Gedung KPA (Pusat)', 'lantai' => 'Lantai 1', 'ruangan' => 'Ruang Koordinator Umum & Keuangan', 'kampus' => 'Kampus Utama', 'created_at' => $created, 'updated_at' => $updated],
            ['id_unit' => 12, 'gedung' => 'Gedung Graha Pendidikan', 'lantai' => 'Lantai 1', 'ruangan' => 'Hall Utama', 'kampus' => 'Kampus Utama', 'created_at' => $created, 'updated_at' => $updated],
            ['id_unit' => 12, 'gedung' => 'Gedung Kuliah Bersama', 'lantai' => 'Lantai 5', 'ruangan' => 'Aula Pertemuan', 'kampus' => 'Kampus Utama', 'created_at' => $created, 'updated_at' => $updated],

            // ============================================================
            // C. PENUNJANG AKADEMIK / UPA (ID 13-19)
            // ============================================================

            // 13. UPA. Perpustakaan
            ['id_unit' => 13, 'gedung' => 'Gedung Perpustakaan', 'lantai' => 'Lantai 1', 'ruangan' => 'Ruang Kepala UPA Perpustakaan', 'kampus' => 'Kampus Utama', 'created_at' => $created, 'updated_at' => $updated],
            ['id_unit' => 13, 'gedung' => 'Gedung Perpustakaan', 'lantai' => 'Lantai 2', 'ruangan' => 'Ruang Baca Digital', 'kampus' => 'Kampus Utama', 'created_at' => $created, 'updated_at' => $updated],

            // 14. UPA. Bahasa
            ['id_unit' => 14, 'gedung' => 'Gedung Bahasa', 'lantai' => 'Lantai 1', 'ruangan' => 'Ruang Kepala UPA Bahasa', 'kampus' => 'Kampus Utama', 'created_at' => $created, 'updated_at' => $updated],
            
            // 15. UPA. Teknologi Informasi Dan Komunikasi
            ['id_unit' => 15, 'gedung' => 'Gedung UPA-TIK', 'lantai' => 'Lantai 1', 'ruangan' => 'Ruang Kepala UPA TIK', 'kampus' => 'Kampus Utama', 'created_at' => $created, 'updated_at' => $updated],
            ['id_unit' => 15, 'gedung' => 'Gedung UPA-TIK', 'lantai' => 'Lantai 2', 'ruangan' => 'Data Center / Server Room', 'kampus' => 'Kampus Utama', 'created_at' => $created, 'updated_at' => $updated],

            // 16. UPA. Perawatan dan Perbaikan
            ['id_unit' => 16, 'gedung' => 'Gedung Workshop Pusat', 'lantai' => 'Lantai 1', 'ruangan' => 'Ruang Kepala UPA Perawatan & Perbaikan', 'kampus' => 'Kampus Utama', 'created_at' => $created, 'updated_at' => $updated],
            ['id_unit' => 16, 'gedung' => 'Gedung Workshop Pusat', 'lantai' => 'Lantai 1', 'ruangan' => 'Bengkel Perbaikan Umum', 'kampus' => 'Kampus Utama', 'created_at' => $created, 'updated_at' => $updated],

            // 17. UPA. Layanan Uji Kompetensi
            ['id_unit' => 17, 'gedung' => 'Gedung KPA (Pusat)', 'lantai' => 'Lantai 3', 'ruangan' => 'Ruang Kepala UPA Layanan Uji Kompetensi', 'kampus' => 'Kampus Utama', 'created_at' => $created, 'updated_at' => $updated],
            ['id_unit' => 17, 'gedung' => 'Gedung Serbaguna', 'lantai' => 'Lantai 2', 'ruangan' => 'Ruang Ujian Kompetensi', 'kampus' => 'Kampus Utama', 'created_at' => $created, 'updated_at' => $updated],

            // 18. UPA. Pengembangan Karir Dan Kewirausahaan
            ['id_unit' => 18, 'gedung' => 'Gedung KPA (Pusat)', 'lantai' => 'Lantai 1', 'ruangan' => 'Ruang Kepala UPA Pengembangan Karir', 'kampus' => 'Kampus Utama', 'created_at' => $created, 'updated_at' => $updated],
            ['id_unit' => 18, 'gedung' => 'Gedung KPA (Pusat)', 'lantai' => 'Lantai 1', 'ruangan' => 'Inkubator Bisnis Mahasiswa', 'kampus' => 'Kampus Utama', 'created_at' => $created, 'updated_at' => $updated],

            // 19. UPA. Pengembangan Teknologi dan Produk Unggulan
            ['id_unit' => 19, 'gedung' => 'Gedung KPA (Pusat)', 'lantai' => 'Lantai 2', 'ruangan' => 'Ruang Kepala UPA Pengembangan Teknologi', 'kampus' => 'Kampus Utama', 'created_at' => $created, 'updated_at' => $updated],
            ['id_unit' => 19, 'gedung' => 'Gedung Workshop Pusat', 'lantai' => 'Lantai 2', 'ruangan' => 'Lab Riset Produk Unggulan', 'kampus' => 'Kampus Utama', 'created_at' => $created, 'updated_at' => $updated],
        ];

        // Insert Batch
        $this->db->table('tb_master_lokasi')->ignore(true)->insertBatch($data);
    }
}