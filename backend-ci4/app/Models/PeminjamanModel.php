<?php

namespace App\Models;

use CodeIgniter\Model;

class PeminjamanModel extends Model
{
    // Sesuaikan dengan nama tabel di database Anda
    protected $table            = 'tb_peminjaman'; 
    protected $primaryKey       = 'id_peminjaman';
    protected $useAutoIncrement = true;
    
    // Pastikan semua nama kolom input form ada di sini agar bisa disimpan
    protected $allowedFields    = [
        'nomor', 
        'id_unit', // Atau 'unit' sesuai nama kolom di DB
        'kegiatan', 
        'lokasi', 
        'tanggal_mulai', // Sesuaikan dengan nama kolom di DB (mulai)
        'tanggal_selesai', // Sesuaikan dengan nama kolom di DB (selesai)
        'keterangan', 
        'identitas', 
        'peminjam', 
        'handphone', 
        'lampiran'
    ];

    // Aktifkan timestamp jika tabel memiliki kolom created_at dan updated_at
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}