<?php

namespace App\Models;

use CodeIgniter\Model;

class AlatModel extends Model
{
    protected $table            = 'tb_master_alat';
    protected $primaryKey       = 'id_alat';

    // TAMBAHKAN SEMUA KOLOM INI
    protected $allowedFields    = [
        'nomor_inventaris', // Pastikan ejaannya sama dgn di Database!
        'nama_alat',
        'kategori',
        'id_teknisi',       // Tambahkan ini
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $useTimestamps = true;
    protected $useSoftDeletes = true;
    protected $deletedField  = 'deleted_at';
}
