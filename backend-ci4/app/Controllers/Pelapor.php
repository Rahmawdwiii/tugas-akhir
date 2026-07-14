<?php

namespace App\Controllers;

use App\Models\LaporanModel;
use App\Models\AlatModel;
use App\Models\JadwalPerbaikanModel;
use App\Libraries\EmailService;
use App\Models\UserModel;

class Pelapor extends BaseController
{
    /* =========================
       DASHBOARD
    ========================== */
    public function dashboard()
    {
        return view('pelapor/dashboard');
    }

    /* =========================
       GET LAPORAN (DASHBOARD)
    ========================== */
    public function get_laporan($filter = 'all')
    {
        $model = new LaporanModel();

        // Coba ambil 'id' (sesuai Auth), jika tidak ada baru coba 'id_user'
        $idPelapor = $this->session->get('id_user');

        // Debugging: Cek apakah ID benar-benar terambil
        if (empty($idPelapor)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'DEBUG: Session ID tidak ditemukan. Anda login sebagai siapa?'
            ]);
        }

        $builder = $model
            ->select(
                'tb_laporan.id_laporan, tb_laporan.nomor_laporan, tb_laporan.nama_alat, tb_laporan.lokasi, tb_laporan.unit, tb_laporan.status_laporan, tb_laporan.created_at, tb_laporan.tanggal_laporan, latest_jp.status_perbaikan, p.status_kerusakan,
                p.hasil_perbaikan'
            )
            ->join("(SELECT id_laporan, id_jadwal, status_perbaikan FROM tb_jadwal_perbaikan WHERE id_jadwal IN (SELECT MAX(id_jadwal) FROM tb_jadwal_perbaikan GROUP BY id_laporan)) latest_jp", 'latest_jp.id_laporan = tb_laporan.id_laporan', 'left')
            ->join('tb_perbaikan p', 'p.id_jadwal = latest_jp.id_jadwal', 'left')
            ->join(
                'tb_validasi vp',
                'vp.id_laporan = tb_laporan.id_laporan
                AND vp.jenis_validasi = "PELAPOR"',
                'left'
            )
            ->where('tb_laporan.id_pelapor', $idPelapor);

        if ($filter === 'all') {
            $builder->whereNotIn('tb_laporan.status_laporan', ['SELESAI']);
        } elseif ($filter === 'proses') {
            $builder->whereIn('latest_jp.status_perbaikan', ['PROSES', 'PENDING']);
        } elseif ($filter === 'validasi') {
            $builder->where('tb_laporan.status_laporan', 'MENUNGGU KONFIRMASI');
        } elseif ($filter === 'selesai') {
            $builder
                ->where('vp.jenis_validasi', 'PELAPOR')
                ->where('vp.rating IS NOT NULL', null, false)
                ->where('TRIM(vp.ulasan) <>', '');
        }

        return $this->response->setJSON(
            $builder->orderBy('tb_laporan.id_laporan', 'DESC')->findAll()
        );
    }

    /* =========================
       DETAIL LAPORAN
    ========================== */
    public function get_detail($id)
    {
        $db = \Config\Database::connect();

        $data = $db->table('tb_laporan l')
            ->select('
        l.id_laporan,
        l.nomor_laporan,
        l.tanggal_laporan,
        l.status_laporan,
        l.nama_alat,
        l.nomor_inventaris,
        l.lokasi,
        l.unit,
        l.kerusakan,
        l.validasi_kepala,
        l.path_foto_bukti,

        jp.id_jadwal,
        jp.tanggal_perbaikan AS tgl_dijadwalkan,
        jp.waktu_dijadwalkan,
        jp.status_perbaikan,

        p.status_kerusakan,
    
        p.hasil_perbaikan,
        p.catatan_teknisi,
        p.alasan_pending,
        p.diagnosa_rusak,
        p.waktu_cek_kerusakan,
        p.waktu_mulai,
        p.waktu_pending,
        p.waktu_dilanjutkan,
        p.waktu_selesai,
        p.foto_bukti,

        u.nama AS nama_teknisi,
        v.rating,
        v.ulasan
    ')
            ->join(
                // ⬇️ AMBIL JADWAL TERBARU SAJA
                '(SELECT * FROM tb_jadwal_perbaikan 
          WHERE id_laporan = ' . (int) $id . ' 
          ORDER BY id_jadwal DESC 
          LIMIT 1) jp',
                'jp.id_laporan = l.id_laporan',
                'left'
            )
            ->join(
                // ⬇️ PERBAIKAN HANYA UNTUK JADWAL INI
                'tb_perbaikan p',
                'p.id_jadwal = jp.id_jadwal',
                'left'
            )
            ->join('tb_user u', 'u.id_user = jp.id_teknisi', 'left')
            ->join(
                'tb_validasi v',
                'v.id_laporan = l.id_laporan AND v.jenis_validasi = "PELAPOR"',
                'left'
            )
            ->where('l.id_laporan', $id)
            ->get()
            ->getRowArray();

        return $this->response->setJSON($data);
    }

    /* =========================
       FORM LAPORAN
    ========================== */
    public function form_laporan()
    {
        $alatModel = new AlatModel();
        $db = \Config\Database::connect();

        $data['daftar_alat'] = $alatModel
            ->select('tb_master_alat.*, tb_user.nama AS nama_teknisi')
            ->join('tb_user', 'tb_user.id_user = tb_master_alat.id_teknisi', 'left')
            ->orderBy('tb_master_alat.nama_alat', 'ASC')
            ->findAll();

        $queryLokasi = $db->table('tb_master_lokasi')
            ->select('tb_master_lokasi.*, tb_master_unit.nama_unit')
            ->join('tb_master_unit', 'tb_master_unit.id_unit = tb_master_lokasi.id_unit')
            ->get()
            ->getResultArray();

        $lokasi_per_jurusan = [];
        foreach ($queryLokasi as $row) {
            $unit = $row['nama_unit'];
            $lokasi_per_jurusan[$unit][] =
                "{$row['gedung']}, {$row['lantai']}, {$row['ruangan']}";
        }

        $data['lokasi_per_jurusan'] = $lokasi_per_jurusan;

        return view('pelapor/form_laporan', $data);
    }

    /* =========================
       SUBMIT LAPORAN
    ========================== */
    public function submit()
    {
        $laporanModel = new LaporanModel();
        $alatModel = new AlatModel();
        date_default_timezone_set('Asia/Jakarta');

        try {
            // Validasi Sesi
            if ($this->session->get('role') !== 'pelapor') {
                throw new \Exception('Akses ditolak. Anda bukan pelapor.');
            }
            $idPelapor = $this->session->get('id_user');
            if (empty($idPelapor)) {
                throw new \Exception('Sesi Anda berakhir. Silakan Login ulang.');
            }

            // 1. Validasi Nama Pelapor
            $namaPelapor = trim($this->request->getPost('nama_pelapor'));
            if ($namaPelapor === '') {
                throw new \Exception('Nama Pelapor wajib diisi.');
            }

            // 2. Validasi Nomor Inventaris
            $nomorInventaris = trim($this->request->getPost('nomor_inventaris'));
            if ($nomorInventaris === '') {
                throw new \Exception('Nomor Inventaris wajib terisi (Pilih alat terlebih dahulu).');
            }

            // 3. Validasi Nama Alat
            $namaAlat = $this->request->getPost('nama_alat');
            if (empty($namaAlat)) {
                throw new \Exception('Nama Alat wajib dipilih.');
            }

            // 4. Validasi Lokasi & Unit
            $lokasi = $this->request->getPost('lokasi_alat');
            $unit = $this->request->getPost('unit');
            if (empty($lokasi) || empty($unit)) {
                throw new \Exception('Lokasi dan Unit/Jurusan wajib dipilih.');
            }

            // 5. Validasi Keluhan
            $keluhan = trim($this->request->getPost('kerusakan_keluhan'));
            if ($keluhan === '') {
                throw new \Exception('Detail kerusakan wajib diisi.');
            }

            // 6. Validasi Foto (WAJIB)
            $files = $this->request->getFileMultiple('foto');
            $uploadedFiles = [];
            $hasValidFile = false;

            if ($files) {
                foreach ($files as $file) {
                    if ($file->isValid() && !$file->hasMoved()) {
                        $name = $file->getRandomName();
                        $file->move(FCPATH . 'uploads/laporan', $name);
                        $uploadedFiles[] = $name;
                        $hasValidFile = true;
                    }
                }
            }

            if (!$hasValidFile) {
                throw new \Exception('Bukti Foto wajib diupload minimal satu.');
            }

            // Cek Teknisi
            $alat = $alatModel->where('nama_alat', $namaAlat)->first();
            if (!$alat || !$alat['id_teknisi']) {
                throw new \Exception('Alat belum memiliki teknisi penanggung jawab.');
            }

            // ========================================
            // RETRY LOGIC (Anti Race Condition)
            // ========================================
            $maxRetries = 3;
            $attempt = 0;
            $success = false;
            $lastError = '';
            $db = \Config\Database::connect();

            while ($attempt < $maxRetries && !$success) {
                $attempt++;

                // 1. Generate Nomor Laporan (Panggil Fungsi Sakti di Model)
                $nomorBaru = $laporanModel->generateNomorLaporan();

                $db->transStart();
                try {
                    // 2. Insert Database
                    $idLaporan = $laporanModel->insert([
                        'id_pelapor' => $idPelapor,
                        'nomor_laporan' => $nomorBaru,
                        'tanggal_laporan' => date('Y-m-d H:i:s'),
                        'nama_pelapor' => $namaPelapor,
                        'nama_alat' => $namaAlat,
                        'nomor_inventaris' => $nomorInventaris,
                        'lokasi' => $lokasi,
                        'unit' => $unit,
                        'kerusakan' => $keluhan,
                        'path_foto_bukti' => implode(',', $uploadedFiles),
                        'link_pendukung' => $this->request->getPost('link_pendukung'),
                        'status_laporan' => 'BARU',
                        'created_at' => date('Y-m-d H:i:s')
                    ]);

                    (new JadwalPerbaikanModel())->insert([
                        'id_laporan' => $idLaporan,
                        'id_teknisi' => $alat['id_teknisi'],
                        'jenis_penugasan' => 'AUTO',
                        'status_perbaikan' => 'MENUNGGU',
                    ]);

                    $db->transComplete();

                    if ($db->transStatus() === FALSE) {
                        $errorDB = $db->error();
                        throw new \Exception($errorDB['message']);
                    }

                    $success = true;
                } catch (\Exception $e) {
                    $db->transRollback();
                    $lastError = $e->getMessage();

                    // Hanya ulangi jika error bentrok nomor
                    if (strpos($lastError, 'Duplicate entry') !== false) {
                        continue;
                    }
                    // Jika error lain, BERHENTI dan tampilkan error
                    break;
                }
            }

            if ($success) {

                // ============================================
                // KIRIM EMAIL NOTIFIKASI
                // ============================================

                try {

                    $userModel = new UserModel();

                    $emailService = new EmailService();

                    $pelapor = $userModel->find($idPelapor);

                    $teknisi = $userModel->find($alat['id_teknisi']);

                    $admins = $userModel
                        ->where('akses', 'admin')
                        ->findAll();

                    if (!empty($pelapor['email'])) {

                        // kirimLaporanBaru expects 2 arguments: recipient and data array
                        $emailService->kirimLaporanBaru(
                            $pelapor['email'],
                            [
                                'judul' => 'Laporan Berhasil Dibuat',
                                'pesan' => 'Laporan Anda telah berhasil diterima dan sedang menunggu proses penanganan.',
                                'warna' => '#0d6efd',
                                'status' => 'BARU',

                                'nama_pelapor' => $namaPelapor,
                                'nomor_laporan' => $nomorBaru,
                                'tanggal' => date('d F Y H:i'),
                                'nama_alat' => $namaAlat,
                                'lokasi' => $lokasi,
                                'keluhan' => $keluhan
                            ]
                        );
                    }

                    if (!empty($teknisi['email'])) {

                        $emailService->kirimLaporanBaru(
                            $teknisi['email'],
                            [
                                'judul' => 'Penugasan Baru',
                                'pesan' => 'Anda mendapatkan laporan kerusakan baru yang perlu ditindaklanjuti.',
                                'warna' => '#0d6efd',
                                'status' => 'BARU',

                                'nama_pelapor' => $namaPelapor,
                                'nomor_laporan' => $nomorBaru,
                                'tanggal' => strtotime(date('Y-m-d H:i:s')),
                                'nama_alat' => $namaAlat,
                                'lokasi' => $lokasi,
                                'keluhan' => $keluhan
                            ]
                        );

                    }

                    foreach ($admins as $admin) {

                        if (!empty($admin['email'])) {

                            $emailService->kirimLaporanBaru(
                                $admin['email'],
                                [
                                    'judul' => 'Laporan Baru',
                                    'pesan' => 'Terdapat laporan kerusakan baru yang masuk ke sistem.',
                                    'warna' => '#0d6efd',
                                    'status' => 'BARU',

                                    'nama_pelapor' => $namaPelapor,
                                    'nomor_laporan' => $nomorBaru,
                                    'tanggal' => date('d F Y H:i'),
                                    'nama_alat' => $namaAlat,
                                    'lokasi' => $lokasi,
                                    'keluhan' => $keluhan
                                ]
                            );

                        }

                    }

                } catch (\Throwable $e) {

                    log_message(
                        'error',
                        'Email gagal dikirim : ' . $e->getMessage()
                    );

                }

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Laporan berhasil dikirim',
                    'nomor' => $nomorBaru
                ]);

            } else {
                throw new \Exception('Gagal: ' . $lastError);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
    // Tambahkan method ini di App\Controllers\Pelapor.php

    public function get_counters()
    {
        // Pastikan hanya request AJAX yang diterima (opsional tapi aman)
        if (!$this->request->isAJAX()) {
            // return $this->response->setStatusCode(404); 
        }

        $model = new \App\Models\LaporanModel();
        $idPelapor = session()->get('id_user');

        if (!$idPelapor) {
            // Jika sesi habis, kembalikan 0 semua
            return $this->response->setJSON([
                'all' => 0,
                'proses' => 0,
                'validasi' => 0,
                'selesai' => 0
            ]);
        }

        // 1. Hitung Laporan Aktif (Semua kecuali SELESAI)
        $countAll = $model->where('id_pelapor', $idPelapor)
            ->whereNotIn('status_laporan', ['SELESAI'])
            ->countAllResults();

        // 2. Hitung Sedang Proses berdasarkan status terbaru jadwal teknisi
        $countProses = (new \App\Models\LaporanModel())
            ->join("(SELECT id_laporan, status_perbaikan FROM tb_jadwal_perbaikan WHERE id_jadwal IN (SELECT MAX(id_jadwal) FROM tb_jadwal_perbaikan GROUP BY id_laporan)) latest_jp", 'latest_jp.id_laporan = tb_laporan.id_laporan', 'left')
            ->where('tb_laporan.id_pelapor', $idPelapor)
            ->whereIn('latest_jp.status_perbaikan', ['PROSES', 'PENDING'])
            ->countAllResults();

        // 3. Hitung Perlu Validasi
        $countValidasi = (new \App\Models\LaporanModel())
            ->where('id_pelapor', $idPelapor)
            ->where('status_laporan', 'MENUNGGU KONFIRMASI')
            ->countAllResults();

        // 4. Hitung Selesai
        $countSelesai = $model->where('id_pelapor', $idPelapor)
            ->where('status_laporan', 'SELESAI')
            ->countAllResults();

        return $this->response->setJSON([
            'all' => $countAll,
            'proses' => $countProses,
            'validasi' => $countValidasi,
            'selesai' => $countSelesai
        ]);
    }

    public function get_dashboard_charts()
    {
        $db = \Config\Database::connect();
        $idPelapor = session()->get('id_user');

        if (!$idPelapor) {
            return $this->response->setJSON([
                'cards' => [
                    'all' => 0,
                    'proses' => 0,
                    'validasi' => 0,
                    'selesai' => 0,
                    'rusak' => 0
                ],
                'charts' => [
                    'unit' => ['labels' => [], 'data' => []],
                    'alat' => ['labels' => [], 'data' => []],
                    'severity' => ['labels' => ['Ringan', 'Sedang', 'Berat'], 'data' => [0, 0, 0]],
                    'status' => ['labels' => ['BARU', 'DIPROSES', 'MENUNGGU KONFIRMASI', 'SELESAI'], 'data' => [0, 0, 0, 0]]
                ]
            ]);
        }

        $query = $db->table('tb_laporan l')
            ->select('l.nomor_laporan, l.status_laporan, l.unit, l.nama_alat, p.status_kerusakan, p.hasil_perbaikan, latest_jp.status_perbaikan')
            ->join("(SELECT id_laporan, id_jadwal, status_perbaikan FROM tb_jadwal_perbaikan WHERE id_jadwal IN (SELECT MAX(id_jadwal) FROM tb_jadwal_perbaikan GROUP BY id_laporan)) latest_jp", 'latest_jp.id_laporan = l.id_laporan', 'left')
            ->join('tb_perbaikan p', 'p.id_jadwal = latest_jp.id_jadwal', 'left')
            ->where('l.id_pelapor', $idPelapor)
            ->get()
            ->getResultArray();

        $cards = [
            'all' => 0,
            'proses' => 0,
            'validasi' => 0,
            'selesai' => 0,
            'rusak' => 0
        ];

        $statusCounts = [
            'MENUNGGU' => 0,
            'PROSES' => 0,
            'PENDING' => 0,
            'SELESAI' => 0,
            'RUSAK' => 0
        ];
        $severityCounts = ['Ringan' => 0, 'Sedang' => 0, 'Berat' => 0];
        $unitCounts = [];
        $alatCounts = [];

        foreach ($query as $row) {
            $statusPerbaikan = strtoupper(trim($row['status_perbaikan'] ?? 'MENUNGGU'));
            if (!isset($statusCounts[$statusPerbaikan])) {
                $statusCounts[$statusPerbaikan] = 0;
            }
            $statusCounts[$statusPerbaikan]++;

            if ($row['status_laporan'] !== 'SELESAI') {
                $cards['all']++;
            }
            if (in_array($statusPerbaikan, ['PROSES', 'PENDING'])) {
                $cards['proses']++;
            }
            if ($row['status_laporan'] === 'MENUNGGU KONFIRMASI') {
                $cards['validasi']++;
            }
            if ($row['status_laporan'] === 'SELESAI') {
                $cards['selesai']++;
            }

            $unit = trim($row['unit'] ?? 'Lainnya');
            if ($unit !== '') {
                $unitCounts[$unit] = ($unitCounts[$unit] ?? 0) + 1;
            }

            $alat = trim($row['nama_alat'] ?? 'Tanpa Nama');
            if ($alat !== '') {
                $alatCounts[$alat] = ($alatCounts[$alat] ?? 0) + 1;
            }

            $statusKerusakan = strtolower(trim($row['status_kerusakan'] ?? ''));
            if (strpos($statusKerusakan, 'ringan') !== false) {
                $severityCounts['Ringan']++;
            } elseif (strpos($statusKerusakan, 'sedang') !== false) {
                $severityCounts['Sedang']++;
            } elseif (strpos($statusKerusakan, 'berat') !== false || strpos($statusKerusakan, 'rusak') !== false) {
                $severityCounts['Berat']++;
                $cards['rusak']++;
            }
        }

        arsort($unitCounts);
        arsort($alatCounts);

        return $this->response->setJSON([
            'cards' => $cards,
            'charts' => [
                'unit' => [
                    'labels' => array_slice(array_keys($unitCounts), 0, 10),
                    'data' => array_slice(array_values($unitCounts), 0, 10)
                ],
                'alat' => [
                    'labels' => array_slice(array_keys($alatCounts), 0, 10),
                    'data' => array_slice(array_values($alatCounts), 0, 10)
                ],
                'severity' => [
                    'labels' => array_keys($severityCounts),
                    'data' => array_values($severityCounts)
                ],
                'status' => [
                    'labels' => array_keys($statusCounts),
                    'data' => array_values($statusCounts)
                ]
            ]
        ]);
    }

    /* =========================
       SELESAIKAN LAPORAN & RATING
    ========================== */
    public function selesaikan_laporan()
    {
        if (!$this->request->isAJAX())
            return $this->response->setStatusCode(404);

        $nomor = $this->request->getPost('nomor_laporan');
        $rating = $this->request->getPost('rating');
        $inputUlasan = $this->request->getPost('ulasan');

        $db = \Config\Database::connect();

        try {
            $laporan = $db->table('tb_laporan')->where('nomor_laporan', $nomor)->get()->getRowArray();
            if (!$laporan)
                throw new \Exception("Laporan tidak ditemukan");

            // Cari ID Perbaikan (Karena wajib diisi di tb_validasi)
            $perbaikan = $db->table('tb_perbaikan p')
                ->join('tb_jadwal_perbaikan jp', 'jp.id_jadwal = p.id_jadwal')
                ->where('jp.id_laporan', $laporan['id_laporan'])
                ->orderBy('p.id_perbaikan', 'DESC')
                ->get()->getRowArray();

            $idPerbaikan = $perbaikan ? $perbaikan['id_perbaikan'] : 0;

            // =========================================
            // Mulai Transaction
            // =========================================
            $db->transStart();

            // =========================================
            // 1. Update Status Laporan
            // =========================================
            $db->table('tb_laporan')
                ->where('id_laporan', $laporan['id_laporan'])
                ->update([
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

            // =========================================
            // 2. Simpan Rating & Ulasan Pelapor
            // =========================================
            $db->table('tb_validasi')->insert([
                'id_laporan' => $laporan['id_laporan'],
                'id_perbaikan' => $idPerbaikan,
                'jenis_validasi' => 'PELAPOR',
                'hasil_validasi' => null,
                'rating' => $rating,
                'ulasan' => $inputUlasan,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            // =========================================
            // Selesai Transaction
            // =========================================
            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception("Gagal menyimpan data validasi.");
            }

            return $this->response->setJSON([
                'status' => 'success'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /* =========================
       KOMPLAIN (MASIH RUSAK)
    ========================== */
    public function komplain()
    {
        if (!$this->request->isAJAX())
            return $this->response->setStatusCode(404);

        $nomor = $this->request->getPost('nomor_laporan');
        $alasan = $this->request->getPost('alasan');
        $idPelapor = session()->get('id_user');

        $db = \Config\Database::connect();

        try {
            $laporan = $db->table('tb_laporan')->where('nomor_laporan', $nomor)->get()->getRowArray();
            if (!$laporan)
                throw new \Exception("Laporan tidak ditemukan");

            // Cari ID Perbaikan 
            $perbaikan = $db->table('tb_perbaikan p')
                ->join('tb_jadwal_perbaikan jp', 'jp.id_jadwal = p.id_jadwal')
                ->where('jp.id_laporan', $laporan['id_laporan'])
                ->orderBy('p.id_perbaikan', 'DESC')
                ->get()->getRowArray();

            $idPerbaikan = $perbaikan ? $perbaikan['id_perbaikan'] : 0;

            // 1. Komplain hanya dicatat; status laporan tidak berubah
            // 2. Insert Komplain ke tb_validasi
            $cekInsert = $db->table('tb_validasi')->insert([
                'id_laporan' => $laporan['id_laporan'],
                'id_perbaikan' => $idPerbaikan,
                'jenis_validasi' => 'PELAPOR',
                'hasil_validasi' => NULL,
                'ulasan' => "KOMPLAIN: " . $alasan, // <--- SUDAH DISESUAIKAN MENJADI 'ulasan'
                'created_at' => date('Y-m-d H:i:s')
            ]);

            if (!$cekInsert) {
                $err = $db->error();
                throw new \Exception("Gagal Insert Tabel Validasi: " . $err['message']);
            }

            return $this->response->setJSON(['status' => 'success']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /* =========================
   GET NOTIFIKASI PELAPOR
========================== */
    public function get_notifikasi()
    {
        $db = \Config\Database::connect();

        $idPelapor = session()->get('id_user');

        if (!$idPelapor) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Sesi habis'
            ]);
        }

        try {

            $notifikasi = $db->table('tb_laporan l')
                ->select("
                l.id_laporan,
                l.nomor_laporan,
                l.nama_alat,
                l.kerusakan,
                l.status_laporan,
                l.tanggal_laporan,
                l.updated_at,
                u.nama AS teknisi
            ")
                ->join('tb_jadwal_perbaikan jp', 'jp.id_laporan=l.id_laporan', 'left')
                ->join('tb_user u', 'u.id_user=jp.id_teknisi', 'left')
                ->where('l.id_pelapor', $idPelapor)
                ->orderBy('l.updated_at', 'DESC')
                ->limit(10)
                ->get()
                ->getResultArray();

            foreach ($notifikasi as &$item) {

                if ($item['status_laporan'] == 'BARU') {

                    $item['pesan'] = 'Laporan berhasil dikirim';

                } else if ($item['status_laporan'] == 'DIJADWALKAN') {

                    $item['pesan'] = 'Perbaikan telah dijadwalkan';

                } else if ($item['status_laporan'] == 'DIPROSES') {

                    $item['pesan'] = 'Teknisi sedang melakukan perbaikan';

                } else if ($item['status_laporan'] == 'PENDING') {

                    $item['pesan'] = 'Perbaikan sementara ditunda';

                } else if ($item['status_laporan'] == 'MENUNGGU KONFIRMASI') {

                    $item['pesan'] = 'Menunggu konfirmasi Anda';

                } else if ($item['status_laporan'] == 'SELESAI') {

                    $item['pesan'] = 'Laporan telah selesai';

                } else {
                    $item['pesan'] = '-';
                }

            }

            return $this->response->setJSON([
                'status' => 'success',
                'count' => count($notifikasi),
                'data' => $notifikasi
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function riwayat()
    {
        $idPelapor = session()->get('id_user');

        $db = \Config\Database::connect();
        $builder = $db->table('tb_laporan l')
            ->select('
                l.*,
                jp.status_perbaikan,
                jp.tanggal_perbaikan,
                p.status_kerusakan,
                p.hasil_perbaikan,
                p.catatan_teknisi,
                p.foto_bukti,
                u.nama as nama_teknisi
            ')
            ->join('tb_jadwal_perbaikan jp', 'jp.id_laporan = l.id_laporan', 'left')
            ->join('tb_perbaikan p', 'p.id_jadwal = jp.id_jadwal', 'left')
            ->join('tb_user u', 'u.id_user = jp.id_teknisi', 'left')
            ->where('l.id_pelapor', $idPelapor)
            ->where('l.status_laporan', 'SELESAI')
            ->orderBy('l.created_at', 'DESC');

        $riwayatList = $builder->get()->getResultArray();

        return view('pelapor/riwayat', ['riwayatList' => $riwayatList]);
    }

    public function hapus_laporan()
    {
        $nomor = $this->request->getPost('nomor_laporan');

        $db = \Config\Database::connect();

        $laporan = $db->table('tb_laporan')
            ->where('nomor_laporan', $nomor)
            ->get()
            ->getRowArray();

        if (!$laporan) {

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Laporan tidak ditemukan.'
            ]);

        }

        $db->table('tb_laporan')
            ->where('nomor_laporan', $nomor)
            ->delete();

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Laporan berhasil dihapus.'
        ]);
    }
}