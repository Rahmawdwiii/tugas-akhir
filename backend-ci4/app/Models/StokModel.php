<?php

namespace App\Models;

use CodeIgniter\Model;

class StokModel extends Model
{
    protected $table            = 'tb_master_stok';
    protected $primaryKey       = 'id_stok';
    protected $useAutoIncrement = true;

    // SESUAIKAN DENGAN KOLOM BARU
    protected $allowedFields    = ['nomor_inventaris', 'nama_barang', 'jumlah'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $useSoftDeletes = true;
    protected $deletedField  = 'deleted_at';

    // FUNGSI INI KINI SANGAT SEDERHANA KARENA TIDAK PERLU JOIN LAGI
    public function getStokLengkap()
    {
        return $this->orderBy('nama_barang', 'ASC')->findAll();
    }
}