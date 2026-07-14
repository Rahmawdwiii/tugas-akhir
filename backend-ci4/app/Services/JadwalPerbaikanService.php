<?php

namespace App\Services;

use App\Models\JadwalPerbaikanModel;
use CodeIgniter\Database\Exceptions\DatabaseException;

class JadwalPerbaikanService
{
    protected $jadwalModel;

    public function __construct()
    {
        $this->jadwalModel = new JadwalPerbaikanModel();
    }

    /**
     * Admin / Sistem membuat jadwal baru
     */
    public function buatJadwal(
        int $idLaporan,
        int $idTeknisi,
        string $tanggal,
        string $jenis = 'AUTO',
        ?string $keterangan = null
    ): int {
        return $this->jadwalModel->insert([
            'id_laporan'        => $idLaporan,
            'id_teknisi'        => $idTeknisi,
            'tanggal_perbaikan'    => $tanggal,
            'jenis_penugasan'   => $jenis,
            'status_perbaikan'     => 'AKTIF',
            'keterangan_admin'  => $keterangan,
            'created_at'        => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * GANTI teknisi (tidak update, tapi insert baru)
     */
    public function gantiTeknisi(
        int $idLaporan,
        int $idTeknisiBaru,
        string $tanggalBaru,
        ?string $alasan = null
    ): int {
        // tandai jadwal lama sebagai DIGANTI
        $this->jadwalModel
            ->where('id_laporan', $idLaporan)
            ->where('status_perbaikan', 'AKTIF')
            ->set(['status_perbaikan' => 'DIGANTI'])
            ->update();

        // buat jadwal baru
        return $this->buatJadwal(
            $idLaporan,
            $idTeknisiBaru,
            $tanggalBaru,
            'MANUAL',
            $alasan
        );
    }

    /**
     * Ambil jadwal aktif untuk laporan
     */
    public function jadwalAktif(int $idLaporan): ?array
    {
        return $this->jadwalModel
            ->where('id_laporan', $idLaporan)
            ->where('status_perbaikan', 'AKTIF')
            ->first();
    }

    /**
     * Riwayat penugasan teknisi
     */
    public function riwayatJadwal(int $idLaporan): array
    {
        return $this->jadwalModel
            ->where('id_laporan', $idLaporan)
            ->orderBy('created_at', 'ASC')
            ->findAll();
    }
}
