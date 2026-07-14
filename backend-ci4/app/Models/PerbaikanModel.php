<?php

namespace App\Models;

use CodeIgniter\Model;

class PerbaikanModel extends Model
{
    protected $table      = 'tb_perbaikan';
    protected $primaryKey = 'id_perbaikan';

    protected $allowedFields = [
        'id_jadwal',
        'status_kerusakan',
        'status_perbaikan',
        'catatan_teknisi',
        'foto_bukti',
        'waktu_mulai',
        'waktu_dilanjutkan',
        'waktu_selesai',
        'created_at',
    ];

    protected $useTimestamps = false;
}
