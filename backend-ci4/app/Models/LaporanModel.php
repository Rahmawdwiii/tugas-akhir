<?php

namespace App\Models;

use CodeIgniter\Model;

class LaporanModel extends Model
{
    protected $table      = 'tb_laporan';
    protected $primaryKey = 'id_laporan';

    protected $allowedFields = [
        'nomor_laporan',
        'tanggal_laporan',
        'id_pelapor',
        'nama_pelapor',
        'nama_alat',
        'nomor_inventaris',
        'lokasi',
        'unit',
        'kerusakan',
        'path_foto_bukti',
        'link_pendukung',
        'komplain',
        'status_laporan',
        'rating_pelapor',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * =====================================================================
     * FUNGSI GENERATOR NOMOR ANTI BENTROK (GLOBAL - TIDAK RESET SETIAP HARI)
     * =====================================================================
     * Fungsi ini akan mencari nomor urut TERAKHIR di database (semua tanggal),
     * ambil 3 digit belakang, lalu increment dari sana.
     * Tanggal prefix akan disesuaikan dengan hari ini.
     */
    public function generateNomorLaporan()
    {
        $today = date('Ymd'); // Contoh: 20260207

        // 1. Cari data dengan nomor laporan TERTINGGI di database (tidak peduli tanggal)
        $lastData = $this->select('nomor_laporan')
                         ->orderBy('nomor_laporan', 'DESC')
                         ->first();

        if ($lastData) {
            // 2. Ambil 3 angka belakang dari nomor terakhir, ubah jadi integer, tambah 1
            // Contoh: 20260208003 -> ambil "003" -> jadi 3 -> 3+1 = 4
            $lastUrutan = intval(substr($lastData['nomor_laporan'], -3));
            $nextUrutan = $lastUrutan + 1;
        } else {
            // 3. Jika belum ada data sama sekali, mulai dari 1
            $nextUrutan = 1;
        }

        // 4. Format ulang dengan tanggal hari ini: 20260207004
        return $today . sprintf('%03d', $nextUrutan);
    }
}
