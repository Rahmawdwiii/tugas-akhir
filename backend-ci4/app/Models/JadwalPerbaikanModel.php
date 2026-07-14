<?php

namespace App\Models;

use CodeIgniter\Model;
class JadwalPerbaikanModel extends Model
{
    protected $table      = 'tb_jadwal_perbaikan';
    protected $primaryKey = 'id_jadwal';

    protected $allowedFields = [
        'id_laporan',
        'id_teknisi',
        'tanggal_perbaikan',
        'jenis_penugasan',
        'status_perbaikan',
        'keterangan_admin',
        'created_at'
    ];

    protected $useTimestamps = false;
    protected $createdField = 'created_at';
}
