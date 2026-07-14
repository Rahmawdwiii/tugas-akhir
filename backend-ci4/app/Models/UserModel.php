<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    // Nama tabel di database
    protected $table = 'tb_user';
    protected $primaryKey = 'id_user';
    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'username',
        'password',
        'nama',
        'jabatan',
        'akses',
        'email',
        'is_online',
        'last_active'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    protected $useSoftDeletes = true;
}
