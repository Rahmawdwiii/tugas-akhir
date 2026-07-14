<?php

namespace App\Services;

use App\Models\JadwalPerbaikanModel;
use App\Models\PerbaikanModel;
use App\Models\LaporanModel;
use CodeIgniter\Database\Exceptions\DatabaseException;

class JadwalService
{
    protected JadwalPerbaikanModel $jadwalModel;
    protected PerbaikanModel $perbaikanModel;
    protected LaporanModel $laporanModel;

    public function __construct()
    {
        $this->jadwalModel    = new JadwalPerbaikanModel();
        $this->perbaikanModel = new PerbaikanModel();
        $this->laporanModel   = new LaporanModel();
    }

    /**
     * Teknisi memulai perbaikan
     */
    public function mulaiPerbaikan(int $idJadwal): void
    {
        $jadwal = $this->jadwalModel->find($idJadwal);

        if (!$jadwal) {
            throw new DatabaseException('Jadwal tidak ditemukan');
        }

        if ($jadwal['status_perbaikan'] !== 'AKTIF') {
            throw new DatabaseException('Jadwal tidak dalam status AKTIF');
        }

        // ❗ LOCK: Cegah perbaikan dobel
        // ❗ LOCK: Cegah perbaikan dobel
        $existing = $this->perbaikanModel
            ->where('id_jadwal', $idJadwal)
            ->whereIn('status_perbaikan', ['DIPROSES'])
            ->first();

        if ($existing) {
            throw new DatabaseException('Perbaikan sudah berjalan');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // Update jadwal
        $this->jadwalModel->update($idJadwal, [
            'status_perbaikan' => 'DIPROSES',
        ]);

        // Insert perbaikan
        $this->perbaikanModel->insert([
            'id_jadwal'        => $idJadwal,
            'status_perbaikan' => 'DIPROSES',
            'waktu_mulai'      => date('Y-m-d H:i:s'),
            'created_at'       => date('Y-m-d H:i:s'),
        ]);

        // Update laporan
        $this->laporanModel->update($jadwal['id_laporan'], [
            'status_laporan' => 'DIPROSES',
        ]);

        $db->transComplete();

        if (!$db->transStatus()) {
            throw new DatabaseException('Gagal memulai perbaikan');
        }
    }

    /**
     * Teknisi menyelesaikan perbaikan
     */
    public function selesaiPerbaikan(int $idJadwal, ?string $catatan): void
    {
        $jadwal = $this->jadwalModel->find($idJadwal);

        if (!$jadwal) {
            throw new DatabaseException('Jadwal tidak ditemukan');
        }

        if ($jadwal['status_perbaikan'] !== 'DIPROSES') {
            throw new DatabaseException('Jadwal belum diproses');
        }

        $perbaikan = $this->perbaikanModel
            ->where('id_jadwal', $idJadwal)
            ->where('status_perbaikan', 'DIPROSES')
            ->first();

        if (!$perbaikan) {
            throw new DatabaseException('Perbaikan aktif tidak ditemukan');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // Update perbaikan
        $this->perbaikanModel->update($perbaikan['id_perbaikan'], [
            'status_perbaikan' => 'SELESAI',
            'catatan_teknisi'  => $catatan,
            'waktu_selesai'    => date('Y-m-d H:i:s'),
        ]);

        // Update jadwal
        $this->jadwalModel->update($idJadwal, [
            'status_perbaikan' => 'SELESAI',
        ]);

        // ⛔ JANGAN SELESAIKAN LAPORAN
        $this->laporanModel->update($jadwal['id_laporan'], [
            'status_laporan' => 'MENUNGGU_VALIDASI',
        ]);

        $db->transComplete();

        if (!$db->transStatus()) {
            throw new DatabaseException('Gagal menyelesaikan perbaikan');
        }
    }
}
