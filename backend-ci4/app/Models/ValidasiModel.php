<?php

namespace App\Models;

use CodeIgniter\Model;

class ValidasiModel extends Model
{
    protected $table      = 'tb_validasi';
    protected $primaryKey = 'id_validasi';

    protected $allowedFields = [
        'id_laporan',
        'id_perbaikan',
        'jenis_validasi',
        'hasil_validasi',
        'catatan',
        'rating',
        'created_at',
    ];

    protected $useTimestamps = false; // WAJIB
}
