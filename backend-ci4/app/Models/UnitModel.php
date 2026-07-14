<?php

namespace App\Models;

use CodeIgniter\Model;

class UnitModel extends Model
{
    protected $table            = 'tb_master_unit';
    protected $primaryKey       = 'id_unit';
    protected $useAutoIncrement = true;

    // Field yang boleh diisi/diupdate
    protected $allowedFields    = ['nama_unit', 'kategori'];

    // Setting Waktu (Karena di database ada created_at)
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = ''; // Kosongkan jika tidak ada updated_at di tabel unit
}
