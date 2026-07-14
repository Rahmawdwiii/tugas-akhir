<?php

namespace App\Services;

use App\Models\ValidasiModel;
use App\Models\LaporanModel;
use App\Models\PerbaikanModel;
use App\Models\JadwalPerbaikanModel;
use CodeIgniter\Database\Exceptions\DatabaseException;

class PelaporService
{
    protected ValidasiModel $validasiModel;
    protected LaporanModel $laporanModel;
    protected PerbaikanModel $perbaikanModel;
    protected JadwalPerbaikanModel $jadwalModel;

    public function __construct()
    {
        $this->validasiModel  = new ValidasiModel();
        $this->laporanModel   = new LaporanModel();
        $this->perbaikanModel = new PerbaikanModel();
        $this->jadwalModel    = new JadwalPerbaikanModel();
    }

    /**
     * Pelapor memvalidasi hasil perbaikan
     */
    public function validasi(
        int $idPerbaikan,
        string $hasil,
        ?string $catatan = null,
        ?int $rating = null
    ): void {
        if (!in_array($hasil, ['DISETUJUI', 'DITOLAK'], true)) {
            throw new DatabaseException('Hasil validasi tidak valid');
        }

        $perbaikan = $this->perbaikanModel->find($idPerbaikan);
        if (!$perbaikan) {
            throw new DatabaseException('Perbaikan tidak ditemukan');
        }

        if ($perbaikan['status_perbaikan'] !== 'SELESAI') {
            throw new DatabaseException('Perbaikan belum selesai');
        }

        // ❗ Cegah validasi dobel
        $existing = $this->validasiModel
            ->where('id_perbaikan', $idPerbaikan)
            ->where('jenis_validasi', 'PELAPOR')
            ->first();

        if ($existing) {
            throw new DatabaseException('Perbaikan sudah divalidasi');
        }

        $jadwal = $this->jadwalModel->find($perbaikan['id_jadwal']);
        if (!$jadwal || !isset($jadwal['id_laporan'])) {
            throw new DatabaseException('Relasi laporan rusak');
        }

        $idLaporan = (int) $jadwal['id_laporan'];

        $db = \Config\Database::connect();
        $db->transStart();

        // Insert validasi
        $this->validasiModel->insert([
            'id_laporan'     => $idLaporan,
            'id_perbaikan'   => $idPerbaikan,
            'jenis_validasi' => 'PELAPOR',
            'hasil_validasi' => $hasil,
            'catatan'        => $catatan,
            'rating'         => $hasil === 'DISETUJUI' ? $rating : null,
            'created_at'     => date('Y-m-d H:i:s'),
        ]);

        if ($hasil === 'DISETUJUI') {
            // Final lock
            $this->laporanModel->update($idLaporan, [
                'status_laporan' => 'SELESAI',
                'rating_pelapor' => $rating,
            ]);

            $this->perbaikanModel->update($idPerbaikan, [
                'status_perbaikan' => 'TERVALIDASI',
            ]);
        } else {
            $this->laporanModel->update($idLaporan, [
                'status_laporan' => 'DITOLAK',
            ]);
        }

        $db->transComplete();

        if (!$db->transStatus()) {
            throw new DatabaseException('Gagal menyimpan validasi');
        }
    }
}
