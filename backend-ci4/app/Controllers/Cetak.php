<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Dompdf\Dompdf;
use Dompdf\Options;

class Cetak extends BaseController
{
    public function perbaikan()
    {
        return view('perbaikan');
    }

    protected function imageToBase64($path)
    {
        if (!file_exists($path)) {
            return '';
        }
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }

    private function getLaporanDataByNomor($nomor)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('tb_laporan l');
        
        // JOIN ke jadwal & user agar kita dapat nama teknisi dari DB
        $builder->select('l.*, p.status_kerusakan, u.nama as nama_teknisi, u.jabatan as jabatan_teknisi');
        $builder->join('tb_jadwal_perbaikan jp', 'jp.id_laporan = l.id_laporan', 'left');
        $builder->join('tb_perbaikan p', 'p.id_jadwal = jp.id_jadwal', 'left');
        $builder->join('tb_user u', 'u.id_user = jp.id_teknisi', 'left'); // Join ke user
        
        $builder->where('l.nomor_laporan', urldecode($nomor));
        return $builder->get()->getRowArray();
    }

    public function cetakLaporanPdf($nomorLaporan)
    {
        // 1. Ambil data laporan
        $laporanData = $this->getLaporanDataByNomor($nomorLaporan);

        if (empty($laporanData)) {
            return $this->response->setStatusCode(404)->setBody('Laporan tidak ditemukan.');
        }

        // 2. Logika teknisi
        // Hapus semua logika if-else strpos yang panjang itu, ganti dengan:
        $namaTeknisi = $laporanData['nama_teknisi'] ?? 'Teknisi UPAPP';
        $jabatanTeknisi = $laporanData['jabatan_teknisi'] ?? 'Teknisi';
        
        // Simpan ke array data
        $data['nama_teknisi_polsri'] = $namaTeknisi;
        $data['jabatan_teknisi_polsri'] = $jabatanTeknisi;

        // 3. Susun data
        $data['laporan'] = $laporanData;
        $data['nama_teknisi_polsri'] = $namaTeknisi;
        $data['jabatan_teknisi_polsri'] = $jabatanTeknisi;

        // 4. Konversi logo ke Base64
        $logoPathPolsri = FCPATH . 'images/polsri.jpg';
        $logoPathIso    = FCPATH . 'images/iso.jpg';
        $data['logo_polsri'] = $this->imageToBase64($logoPathPolsri);
        $data['logo_iso']    = $this->imageToBase64($logoPathIso);

        // 5. Konversi foto kerusakan
        $rawFoto  = $laporanData['path_foto_bukti'] ?? null;
        $fotoData = !empty($rawFoto) ? explode(',', $rawFoto) : [];
        $data['foto_list'] = [];

        if (!empty($fotoData)) {
            foreach ($fotoData as $namaFileString) {
                $namaFileString = trim($namaFileString);
                if (empty($namaFileString)) continue;

                $fotoPath = FCPATH . 'uploads/laporan/' . $namaFileString;

                if (file_exists($fotoPath)) {
                    $sizeInfo = getimagesize($fotoPath);
                    if ($sizeInfo) {
                        $data['foto_list'][] = [
                            'base64'         => $this->imageToBase64($fotoPath),
                            'width_px'       => $sizeInfo[0],
                            'height_px'      => $sizeInfo[1],
                            'page_height_px' => $sizeInfo[1] + 50
                        ];
                    }
                }
            }
        }

        // 6. Render view
        $finalHtml = view('cetak/cetak_laporan', $data);

        // 7. Render dengan Dompdf
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($finalHtml);
        $dompdf->render();

        // 8. Bersihkan output buffer lalu stream PDF
        if (ob_get_level()) {
            ob_end_clean();
        }

        $filename = 'Laporan_Kerusakan_dan_Perbaikan_' . $nomorLaporan . '.pdf';
        $dompdf->stream($filename, ["Attachment" => 0]);
        exit;
    }

    public function cetakFilterPdf()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('tb_laporan');

        $builder->select('tb_laporan.*, tb_perbaikan.status_kerusakan');
        $builder->join('tb_jadwal_perbaikan', 'tb_jadwal_perbaikan.id_laporan = tb_laporan.id_laporan', 'left');
        $builder->join('tb_perbaikan', 'tb_perbaikan.id_jadwal = tb_jadwal_perbaikan.id_jadwal', 'left');

        $daterange = $this->request->getGet('daterange');
        $bulan     = $this->request->getGet('bulan');
        $tahun     = $this->request->getGet('tahun');
        $status    = $this->request->getGet('status');
        $unit      = $this->request->getGet('unit');

        if (!empty($daterange)) {
            $dates = explode(' s/d ', $daterange);
            if (count($dates) == 2) {
                $builder->where('DATE(tb_laporan.tanggal_laporan) >=', trim($dates[0]));
                $builder->where('DATE(tb_laporan.tanggal_laporan) <=', trim($dates[1]));
            }
        }
        if (!empty($bulan)) {
            $builder->where('MONTH(tb_laporan.tanggal_laporan)', $bulan);
        }
        if (!empty($tahun)) {
            $builder->where('YEAR(tb_laporan.tanggal_laporan)', $tahun);
        }
        if (!empty($unit)) {
            $builder->where('tb_laporan.unit', $unit);
        }
        if (!empty($status)) {
            if (strtoupper($status) === 'SELESAI') {
                $builder->where('tb_laporan.status_laporan', 'SELESAI');
            } else {
                $builder->where('tb_perbaikan.status_kerusakan', $status);
            }
        }

        $dataLaporan = $builder->get()->getResultArray();

        foreach ($dataLaporan as $key => $row) {
            $namaAlat = strtoupper((string)($row['nama_alat'] ?? ''));
            $namaTeknisi = "Teknisi UPAPP";
            $jabatanTeknisi = "Teknisi";

            if (strpos($namaAlat, 'AIR CONDITIONER') !== false || strpos($namaAlat, 'AC') !== false) {
                $namaTeknisi = "Cipto";
                $jabatanTeknisi = "Teknisi AC";
            } elseif (strpos($namaAlat, 'KOMPUTER') !== false || strpos($namaAlat, 'PC') !== false || strpos($namaAlat, 'ABSENSI') !== false) {
                $namaTeknisi = "M. Karison";
                $jabatanTeknisi = "Teknisi Komputer";
            } elseif (strpos($namaAlat, 'LISTRIK') !== false || strpos($namaAlat, 'LAMPU') !== false) {
                $namaTeknisi = "Riadi Putra";
                $jabatanTeknisi = "Teknisi Kelistrikan";
            } elseif (strpos($namaAlat, 'ELEKTRONIKA') !== false || strpos($namaAlat, 'ACCESS POINT') !== false) {
                $namaTeknisi = "Edial Salmes";
                $jabatanTeknisi = "Teknisi Elektronika";
            }

            $dataLaporan[$key]['nama_teknisi_polsri'] = $namaTeknisi;
            $dataLaporan[$key]['jabatan_teknisi_polsri'] = $jabatanTeknisi;
        }

        $teksPeriode = "Semua Waktu";
        if (!empty($daterange)) {
            $teksPeriode = $daterange;
        } elseif (!empty($bulan) || !empty($tahun)) {
            $namaBulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            $teksBulan = !empty($bulan) ? $namaBulan[(int)$bulan] : '';
            $teksPeriode = trim("Bulan $teksBulan Tahun $tahun");
        }

        $data = [
            'laporan_list'   => $dataLaporan,
            'filter_periode' => $teksPeriode,
            'filter_unit'    => $unit ?: 'Semua Unit',
            'filter_status'  => $status ?: 'Semua Status',
            'logo_polsri'    => $this->imageToBase64(FCPATH . 'images/polsri.jpg'),
            'logo_iso'       => $this->imageToBase64(FCPATH . 'images/iso.jpg'),
        ];

        $finalHtml = view('cetak/cetak_laporan_global', $data);

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($finalHtml);
        $dompdf->setPaper('legal', 'landscape');
        $dompdf->render();

        if (ob_get_level()) {
            ob_end_clean();
        }

        $filename = 'Rekap_Laporan_Perbaikan_' . date('Ymd') . '.pdf';
        $dompdf->stream($filename, ["Attachment" => 0]);
        exit;
    }
}