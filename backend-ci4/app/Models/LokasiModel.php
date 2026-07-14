<?php

namespace App\Models;

use CodeIgniter\Model;

class LokasiModel extends Model
{
    protected $table            = 'tb_master_lokasi';
    protected $primaryKey       = 'id_lokasi';
    protected $useAutoIncrement = true;
    
    // PERBAIKAN 1: Aktifkan Timestamps agar created_at & updated_at terisi otomatis
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    // PERBAIKAN 2: Aktifkan Soft Deletes (Agar data tidak langsung hilang permanen saat dihapus, tapi masuk ke deleted_at)
    // Sesuaikan ini dengan struktur tabel Anda. Jika kolom deleted_at ada, aktifkan ini.
    protected $useSoftDeletes   = true; 
    protected $deletedField     = 'deleted_at';

    protected $allowedFields    = ['id_unit', 'gedung', 'lantai', 'ruangan', 'kampus'];

    // PERBAIKAN: 
    // 1. Tambahkan parameter ($keyword = null)
    // 2. Hapus ->findAll() di akhir
    // 3. Tambahkan logika pencarian (groupStart ... groupEnd)
    public function getLokasiLengkap($keyword = null)
    {
        // Query Dasar
        $this->select('tb_master_lokasi.*, tb_master_unit.nama_unit')
             ->join('tb_master_unit', 'tb_master_unit.id_unit = tb_master_lokasi.id_unit', 'left')
             ->orderBy('tb_master_lokasi.id_lokasi', 'DESC');

        // Logika Pencarian
        if ($keyword) {
            $this->groupStart()
                 ->like('tb_master_unit.nama_unit', $keyword)
                 ->orLike('tb_master_lokasi.gedung', $keyword)
                 ->orLike('tb_master_lokasi.lantai', $keyword)
                 ->orLike('tb_master_lokasi.ruangan', $keyword)
                 ->orLike('tb_master_lokasi.kampus', $keyword)
                 ->groupEnd();
        }

        // KUNCI PERBAIKAN:
        // Kembalikan $this (Query Builder), JANGAN kembalikan findAll() (Array)
        // Agar controller bisa melakukan ->paginate()
        return $this;
    }
}