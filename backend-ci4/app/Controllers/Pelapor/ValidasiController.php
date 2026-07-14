<?php

namespace App\Controllers\Pelapor;

use App\Controllers\BaseController;
use App\Services\PelaporService;

class ValidasiController extends BaseController
{
    public function submit($idPerbaikan)
    {
        $hasil   = $this->request->getPost('hasil');   // DISETUJUI / DITOLAK
        $catatan = $this->request->getPost('catatan');
        $rating  = $this->request->getPost('rating');

        $service = new PelaporService();
        $service->validasi(
            (int)$idPerbaikan,
            $hasil,
            $catatan,
            $rating ? (int)$rating : null
        );

        return response()->setJSON(['status' => 'ok']);
    }
}
