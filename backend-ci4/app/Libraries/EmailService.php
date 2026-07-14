<?php

namespace App\Libraries;

class EmailService
{
    protected $email;

    public function __construct()
    {
        $this->email = service('email');
    }

    public function kirim($tujuan, $subject, $view, $data = [])
    {
        $this->email->clear();

        $this->email->setFrom(
            env('email.fromEmail'),
            env('email.fromName')
        );

        $this->email->setTo($tujuan);

        $this->email->setSubject($subject);

        // otomatis membuat timeline
        if (isset($data['nomor_laporan'])) {
            $data['timeline'] = $this->buatTimeline($data['nomor_laporan']);
        }

        $this->email->setMessage(
            view($view, $data)
        );

        return $this->email->send();
    }

    public function kirimLaporanBaru($email, $data)
    {
        return $this->kirim(

            $email,

            'UPA PP | Laporan Berhasil Dibuat',

            'email/notifikasi',

            $data

        );
    }

    public function kirimMulaiPerbaikan($email, $data)
    {
        return $this->kirim(
            $email,
            'UPA PP | Perbaikan Sedang Berlangsung',
            'email/notifikasi',
            $data
        );
    }

    public function kirimPending($email, $data)
    {
        return $this->kirim(
            $email,
            'UPA PP | Perbaikan Ditunda',
            'email/notifikasi',
            $data
        );
    }

    public function kirimSelesai($email, $data)
    {
        return $this->kirim(
            $email,
            'UPA PP | Perbaikan Selesai',
            'email/notifikasi',
            $data
        );
    }

    public function kirimTidakDapatDiperbaiki($email, $data)
    {
        return $this->kirim(
            $email,
            'UPA PP | Peralatan Tidak Dapat Diperbaiki',
            'email/notifikasi',
            $data
        );
    }

    public function kirimPenugasanTeknisi($email, $data)
    {
        return $this->kirim(
            $email,
            'UPA PP | Jadwal Perbaikan Dibuat',
            'email/notifikasi',
            $data
        );
    }

    public function kirimLanjutkanPerbaikan($email, $data)
    {
        return $this->kirim(
            $email,
            'UPA PP | Perbaikan Dilanjutkan',
            'email/notifikasi',
            $data
        );
    }

    public function getError()
    {
        return $this->email->printDebugger(['headers']);
    }

    protected function buatTimeline($nomorLaporan)
    {
        $db = \Config\Database::connect();

        $data = $db->table('tb_laporan l')
            ->select("
                l.created_at,
                jp.tanggal_perbaikan,

                p.status_kerusakan,
                p.alasan_pending,
                p.diagnosa_rusak,
                p.hasil_perbaikan,

                p.waktu_cek_kerusakan,
                p.waktu_mulai,
                p.waktu_pending,
                p.waktu_dilanjutkan,
                p.waktu_selesai
            ")
            ->join('tb_jadwal_perbaikan jp', 'jp.id_laporan=l.id_laporan')
            ->join('tb_perbaikan p', 'p.id_jadwal=jp.id_jadwal', 'left')
            ->where('l.nomor_laporan', $nomorLaporan)
            ->get()
            ->getRowArray();
        if (!$data) {
            return [];
        }

        $timeline = [];

        if (!empty($data['created_at'])) {
            $timeline[] = [
                'status' => '📝 Laporan berhasil dibuat',
                'waktu' => date('d M Y H:i', strtotime($data['created_at']))
            ];
        }

        if (!empty($data['waktu_cek_kerusakan'])) {

            $statusKerusakan = strtoupper($data['status_kerusakan'] ?? '-');

            $timeline[] = [

                'status' => '🔍 Status kerusakan ditentukan (' . $statusKerusakan . ')',

                'waktu' => date('d M Y H:i', strtotime($data['waktu_cek_kerusakan']))
            ];
        }

        if (!empty($data['tanggal_perbaikan'])) {

            $timeline[] = [

                'status' => '📅 Perbaikan dijadwalkan',

                'waktu' => date('d M Y', strtotime($data['tanggal_perbaikan']))
            ];
        }

        if (!empty($data['waktu_mulai'])) {
            $timeline[] = [
                'status' => '🔧 Teknisi mulai melakukan perbaikan',
                'waktu' => date('d M Y H:i', strtotime($data['waktu_mulai']))
            ];
        }

        if (!empty($data['waktu_pending'])) {

            $statusPending = "⏸ Perbaikan ditunda";

            if (!empty($data['alasan_pending'])) {
                $statusPending .= " (" . $data['alasan_pending'] . ")";
            }

            $timeline[] = [
                'status' => $statusPending,
                'waktu' => date('d M Y H:i', strtotime($data['waktu_pending']))
            ];
        }

        if (!empty($data['waktu_dilanjutkan'])) {
            $timeline[] = [
                'status' => '▶ Perbaikan dilanjutkan',
                'waktu' => date('d M Y H:i', strtotime($data['waktu_dilanjutkan']))
            ];
        }

        if (!empty($data['waktu_selesai'])) {

            $status = "✅ Perbaikan selesai";

            if ($data['hasil_perbaikan'] == "RUSAK TOTAL") {

                $status = "❌ Peralatan dinyatakan Rusak Total";
            }

            $timeline[] = [

                'status' => $status,

                'waktu' => date('d M Y H:i', strtotime($data['waktu_selesai']))
            ];
        }

        return $timeline;
    }
}