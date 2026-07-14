<?php

namespace App\Services;

use App\Models\PerbaikanModel;

class PerbaikanService
{
    protected $model;

    public function __construct()
    {
        $this->model = new PerbaikanModel();
    }

    public function mulaiPerbaikan(int $idJadwal, ?string $statusKerusakan = null, ?string $catatan = null)
    {
        return $this->model->insert([
            'id_jadwal'       => $idJadwal,
            'status_perbaikan' => 'DIKERJAKAN',
            'status_kerusakan' => $statusKerusakan,
            'catatan_teknisi' => $catatan,
            'waktu_mulai'     => date('Y-m-d H:i:s')
        ]);
    }

    public function selesaiPerbaikan(int $idPerbaikan, ?string $catatan = null, ?string $fotoBukti = null)
    {
        return $this->model->update($idPerbaikan, [
            'status_perbaikan' => 'SELESAI',
            'catatan_teknisi' => $catatan,
            'foto_bukti'      => $fotoBukti,
            'waktu_selesai'   => date('Y-m-d H:i:s')
        ]);
    }

    public function getPerbaikanByJadwal(int $idJadwal)
    {
        return $this->model->where('id_jadwal', $idJadwal)->findAll();
    }
}
