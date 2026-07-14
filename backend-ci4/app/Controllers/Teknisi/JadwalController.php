<?php

namespace App\Controllers\Teknisi;

use App\Controllers\BaseController;
use App\Services\JadwalService;

class JadwalController extends BaseController
{
    // --- TAMBAHKAN FUNGSI INI DI DALAM class Teknisi ---
    public function mulai($nomorLaporan)
    {
        $this->response->setHeader('Content-Type', 'application/json');
        $db = \Config\Database::connect();

        try {
            // 1. Cari ID Laporan & ID Jadwal berdasarkan Nomor Laporan
            $data = $db->table('tb_jadwal_perbaikan jp')
                ->select('jp.id_jadwal, jp.id_laporan')
                ->join('tb_laporan l', 'l.id_laporan = jp.id_laporan')
                ->where('l.nomor_laporan', $nomorLaporan)
                ->get()->getRowArray();

            if (!$data) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Jadwal tidak ditemukan.']);
            }

            $idJadwal = $data['id_jadwal'];
            $idLaporan = $data['id_laporan'];

            // 2. Cek apakah row perbaikan sudah ada
            // (Meskipun harusnya sudah ada saat teknisi update status kerusakan)
            $cekPerbaikan = $db->table('tb_perbaikan')->where('id_jadwal', $idJadwal)->countAllResults();

            if ($cekPerbaikan == 0) {
                $db->table('tb_perbaikan')->insert([
                    'id_jadwal' => $idJadwal,
                    // status_kerusakan dibiarkan NULL hingga teknisi memeriksa
                    'created_at' => date('Y-m-d H:i:s')
                ]);            
            } else {
                // Jika sudah ada, update waktu mulai HANYA jika masih kosong
                // Tapi status kerusakannya JANGAN di-reset ke 'Belum Dicek'
                $db->table('tb_perbaikan')
                    ->where('id_jadwal', $idJadwal)
                    ->groupStart() // Update hanya jika waktu_mulai null atau kosong
                    ->where('waktu_mulai', null)
                    ->orWhere('waktu_mulai', '0000-00-00 00:00:00')
                    ->groupEnd()
                    ->update(['waktu_mulai' => date('Y-m-d H:i:s')]);
            }

            // 3. Update Status Jadwal menjadi 'PROSES'
            $db->table('tb_jadwal_perbaikan')
                ->where('id_jadwal', $idJadwal)
                ->update(['status_perbaikan' => 'PROSES']);

            // 4. Update Status Laporan menjadi 'DIPROSES'
            $db->table('tb_laporan')
                ->where('id_laporan', $idLaporan)
                ->update(['status_laporan' => 'DIPROSES']);

            return $this->response->setJSON(['status' => 'success', 'message' => 'Pekerjaan dimulai!']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    // --- TAMBAHKAN FUNGSI INI DI DALAM class JadwalController ---
    public function pending()
    {
        $this->response->setHeader('Content-Type', 'application/json');
        
        $nomorLaporan = $this->request->getPost('nomor_laporan');
        $alasan       = $this->request->getPost('alasan');

        if (!$nomorLaporan || !$alasan) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak lengkap.']);
        }

        $db = \Config\Database::connect();

        try {
            // 1. Cari ID Jadwal berdasarkan Nomor Laporan
            $data = $db->table('tb_jadwal_perbaikan jp')
                ->select('jp.id_jadwal')
                ->join('tb_laporan l', 'l.id_laporan = jp.id_laporan')
                ->where('l.nomor_laporan', $nomorLaporan)
                ->get()->getRowArray();

            if (!$data) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Jadwal tidak ditemukan.']);
            }

            $idJadwal = $data['id_jadwal'];

            // 2. Update Status Jadwal menjadi 'PENDING'
            $db->table('tb_jadwal_perbaikan')
                ->where('id_jadwal', $idJadwal)
                ->update(['status_perbaikan' => 'PENDING']);

            // 3. Simpan Alasan Penundaan di tb_perbaikan (Kolom catatan_teknisi)
            // Kita gunakan 'alasan_pending' jika ada kolomnya, atau 'catatan_teknisi'
            $db->table('tb_perbaikan')
                ->where('id_jadwal', $idJadwal)
                ->update(['catatan_teknisi' => $alasan]);

            return $this->response->setJSON(['status' => 'success']);

        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
