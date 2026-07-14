<?php

namespace App\Controllers;

use Config\Database;

class Teknisi extends BaseController
{
    public function dashboard()
    {
        // 1. Cek Login
        if (session()->get('role') !== 'teknisi') {
            return redirect()->to('/');
        }

        $idTeknisi = session()->get('id_user');
        $db = \Config\Database::connect();

        // ---------------------------------------------------------
        // [TAMBAHAN 1] AMBIL STATUS ONLINE TERBARU DARI DATABASE
        // ---------------------------------------------------------
        // Kita panggil UserModel untuk mengecek kolom 'is_online' saat ini
        $userModel = new \App\Models\UserModel();
        $dataUser = $userModel->find($idTeknisi);

        // Ambil nilainya (default 0 jika tidak ketemu)
        $statusTerbaru = $dataUser['is_online'] ?? 0;

        // Opsional: Update session biar sinkron
        session()->set('is_online', $statusTerbaru);
        // ---------------------------------------------------------


        // 2. Query Database (Sama seperti di jadwal_perbaikan)
        // Mengambil semua tugas milik teknisi ini
        $dataPekerjaan = $db->table('tb_jadwal_perbaikan')
            ->select('tb_laporan.*, tb_jadwal_perbaikan.tanggal_perbaikan as tgl_perbaikan, tb_jadwal_perbaikan.status_perbaikan')
            ->join('tb_laporan', 'tb_laporan.id_laporan = tb_jadwal_perbaikan.id_laporan')
            ->where('tb_jadwal_perbaikan.id_teknisi', $idTeknisi)
            // Ambil semua status agar dashboard bisa memilah ke tab Baru/Proses/Riwayat
            ->whereIn('tb_jadwal_perbaikan.status_perbaikan', [
                'MENUNGGU',
                'PROSES',
                'PENDING',
                'SELESAI',
                'RUSAK'
            ])
            ->orderBy('tb_laporan.created_at', 'DESC')
            ->get()
            ->getResultArray();

        // 3. Kirim data ke View
        $data = [
            'title' => 'Dashboard Teknisi',
            'jadwalList' => $dataPekerjaan,

            // [TAMBAHAN 2] Kirim status ini ke View
            'is_online' => $statusTerbaru
        ];

        return view('teknisi/dashboard', $data);
    }

    // ===================================================================
    // FUNGSI BARU: UPDATE STATUS ONLINE / OFFLINE TEKNISI
    // ===================================================================
    public function update_status_online()
    {
        // Tolak jika bukan request dari Javascript (AJAX)
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Akses ditolak']);
        }

        // Ambil kiriman angka 1 (Online) atau 0 (Offline)
        $isOnline = $this->request->getPost('is_online');
        $idUser = session()->get('id_user');

        $db = \Config\Database::connect();

        try {
            // 1. Update ke Database (Tabel tb_user)
            // Set last_active jika kolom tersedia. Gunakan try/catch untuk fallback.
            $now = date('Y-m-d H:i:s');
            try {
                $db->table('tb_user')
                    ->where('id_user', $idUser)
                    ->update(['is_online' => $isOnline, 'last_active' => $now]);
            } catch (\Throwable $e) {
                // Jika kolom last_active tidak ada, fallback hanya update is_online
                $db->table('tb_user')
                    ->where('id_user', $idUser)
                    ->update(['is_online' => $isOnline]);
            }

            // 2. Update Session (Penting! Agar saat web direfresh, toggle tidak balik ke awal)
            session()->set('is_online', $isOnline);
            session()->set('last_active', $now);

            return $this->response->setJSON(['status' => 'success']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function jadwal()
    {
        // 1. Tentukan lokasi file sumber (file utama yang berisi semua laporan)
        $filePath = WRITEPATH . 'data/jadwal_teknisi.json';
        $jadwalList = [];

        if (file_exists($filePath)) {
            $jadwalList = json_decode(file_get_contents($filePath), true) ?? [];

            if (is_array($jadwalList)) {
                // Optional: Balik urutan agar laporan terbaru ada di atas
                $jadwalList = array_reverse($jadwalList);
            }
        }

        // 2. Kirim data laporan LENGKAP ke view jadwal
        $data['jadwalList'] = $jadwalList;

        // 3. (Anda perlu menghitung counts di sini jika cards ringkasan masih ada)
        // ... logic penghitungan cards ...

        // Pastikan view di sini menggunakan nama file dashboard Anda
        return view('teknisi/jadwal', $data);
    }

    public function update_status()
    {
        $nomor = $this->request->getPost('nomor_laporan');
        $statusKerusakan = $this->request->getPost('status_kerusakan');
        if ($statusKerusakan) {
            $statusKerusakan = strtoupper($statusKerusakan);
        }

        $db = \Config\Database::connect();

        $jadwal = $db->table('tb_jadwal_perbaikan jp')
            ->select('jp.id_jadwal')
            ->join('tb_laporan l', 'l.id_laporan = jp.id_laporan')
            ->where('l.nomor_laporan', $nomor)
            ->get()->getRowArray();

        if (!$jadwal) {
            return $this->response->setJSON(['status' => 'error']);
        }

        // ⬇️ hanya simpan HASIL CEK
        $exists = $db->table('tb_perbaikan')
            ->where('id_jadwal', $jadwal['id_jadwal'])
            ->countAllResults();

        if ($exists) {
            $db->table('tb_perbaikan')
                ->where('id_jadwal', $jadwal['id_jadwal'])
                ->update(['status_kerusakan' => $statusKerusakan, 'waktu_cek_kerusakan' => date('Y-m-d H:i:s')]);
        } else {
            $db->table('tb_perbaikan')->insert([
                'id_jadwal' => $jadwal['id_jadwal'],
                'status_kerusakan' => $statusKerusakan,
                'waktu_cek_kerusakan' => date('Y-m-d H:i:s')
            ]);
        }

        return $this->response->setJSON(['status' => 'success']);
    }

    public function jadwal_perbaikan()
    {
        // 1. Cek apakah yang login benar-benar teknisi? (Opsional, tapi aman)
        if (session()->get('role') !== 'teknisi') {
            return redirect()->to('/'); // Tendang jika bukan teknisi
        }

        // 2. Ambil ID Teknisi dari Session Login
        $idTeknisi = session()->get('id_user');

        // 3. Koneksi Database
        $db = \Config\Database::connect();

        // 4. Query Data: Gabungkan Jadwal + Laporan
        // Logic: Ambil semua jadwal dimana id_teknisi = SAYA
        $dataPekerjaan = $db->table('tb_jadwal_perbaikan')
            ->select('tb_jadwal_perbaikan.*, tb_laporan.nomor_laporan, tb_laporan.nama_alat, tb_laporan.lokasi, tb_laporan.kerusakan, tb_laporan.status_laporan, tb_laporan.tanggal_laporan, tb_laporan.nama_pelapor')
            ->join('tb_laporan', 'tb_laporan.id_laporan = tb_jadwal_perbaikan.id_laporan')
            ->where('tb_jadwal_perbaikan.id_teknisi', $idTeknisi)
            // Filter Status: Tampilkan yang belum selesai saja di dashboard utama
            ->whereIn('tb_jadwal_perbaikan.status_perbaikan', [
                'MENUNGGU',
                'PROSES',
                'PENDING',
                'SELESAI',
                'RUSAK'
            ])
            ->orderBy('tb_laporan.created_at', 'DESC')
            ->get()
            ->getResultArray();

        $data = [
            'title' => 'Jadwal Perbaikan Teknisi',
            'daftar_tugas' => $dataPekerjaan
        ];

        return view('teknisi/jadwal_perbaikan', $data);
    }

    // File: app/Controllers/Teknisi.php
    public function get_tugas_json($kategori)
    {
        // 1. Ambil ID Teknisi yang Login
        $idTeknisi = session()->get('id_user');

        $db = \Config\Database::connect();
        $builder = $db->table('tb_jadwal_perbaikan jp');

        // 2. Select Data yang dibutuhkan JS
        $builder->select('
        l.id_laporan,
        l.nomor_laporan,
        l.tanggal_laporan,
        l.nama_alat,
        l.unit,
        l.lokasi,
        l.kerusakan as keluhan_awal,
        l.unit as gedung, 
        l.nama_pelapor,      
        l.nomor_inventaris,
        l.path_foto_bukti,
        l.link_pendukung,
        jp.status_perbaikan,
        jp.tanggal_perbaikan as jadwal_perbaikan,
        u.nama as nama_teknisi,
        p.status_kerusakan,
        p.diagnosa_rusak,
        p.hasil_perbaikan,
        p.catatan_teknisi,
        p.waktu_mulai,
        p.waktu_selesai,
        v.rating,            
        v.ulasan
    ');

        // 3. Join Tabel
        $builder->join('tb_laporan l', 'l.id_laporan = jp.id_laporan');
        $builder->join('tb_user u', 'u.id_user = jp.id_teknisi', 'left');
        $builder->join('tb_perbaikan p', 'p.id_jadwal = jp.id_jadwal', 'left');
        $builder->join('tb_validasi v', "v.id_laporan = l.id_laporan AND v.jenis_validasi = 'PELAPOR'", 'left');

        // 4. Filter berdasarkan Teknisi Login
        $builder->where('jp.id_teknisi', $idTeknisi);

        // 5. Filter berdasarkan Kategori Tab (Baru/Proses/Pending/Riwayat)
        if ($kategori === 'baru') {

            $builder->where('jp.status_perbaikan', 'MENUNGGU');

        } elseif ($kategori === 'proses') {

            $builder->where('jp.status_perbaikan', 'PROSES');

        } elseif ($kategori === 'pending') {

            $builder->where('jp.status_perbaikan', 'PENDING');

        } elseif ($kategori === 'riwayat') {

            $builder->whereIn(
                'jp.status_perbaikan',
                ['SELESAI', 'RUSAK', 'BATAL']
            );

        } else {

            throw new \Exception('Kategori tidak valid');

        }

        $result = $builder->orderBy('jp.created_at', 'DESC')->get()->getResultArray();

        $mappedData = [];
        foreach ($result as $row) {
            // --- PERBAIKAN LOGIKA FOTO (MULTIPLE) ---
            $fotoName = $row['path_foto_bukti'];
            $listFotoUrl = []; // 1. Siapkan wadah array kosong

            if (!empty($fotoName)) {
                // 2. Pecah string berdasarkan koma
                $files = explode(',', $fotoName);

                // 3. Loop (Ulangi) untuk setiap file yang ditemukan
                foreach ($files as $f) {
                    $cleanName = trim($f);
                    if ($cleanName !== '') {
                        // Masukkan URL ke dalam array
                        $listFotoUrl[] = base_url('uploads/laporan/' . $cleanName);
                    }
                }
            }

            // (Opsional) Jika array kosong, mau diisi placeholder atau dibiarkan kosong
            // if (empty($listFotoUrl)) { ... } 

            // ---------------------------------------------

            // 6. Mapping Data
            $mappedData[] = [
                'id' => $row['nomor_laporan'],
                'id_db' => $row['id_laporan'],
                'status' => $kategori,
                'alat' => $row['nama_alat'],
                'tgl_laporan' => date('d M Y', strtotime($row['tanggal_laporan'])),
                'tgl_perbaikan' => $row['jadwal_perbaikan'] ? $row['jadwal_perbaikan'] : '-',
                'pelapor' => $row['nama_pelapor'] ?? '-',
                'inv_no' => $row['nomor_inventaris'] ?? '-',
                'teknisi_pelaksana' => $row['nama_teknisi'] ?? 'Saya',
                'kerusakan' => $row['status_kerusakan'] ?? 'Belum Dicek',
                'hasil_perbaikan' => $row['hasil_perbaikan'] ?? null,

                // --- BAGIAN INI SANGAT PENTING ---
                // Kirim sebagai 'foto_urls' (jamak/array) agar bisa dibaca looping JS
                'foto_urls' => $listFotoUrl,

                // (Opsional) 'foto_url' tunggal tetap ada untuk kompatibilitas kode lama jika perlu
                'foto_url' => !empty($listFotoUrl) ? $listFotoUrl[0] : '',

                'link_pendukung' => $row['link_pendukung'],
                'gedung' => $row['unit'],
                'lokasi' => $row['lokasi'],
                'keluhan_lengkap' => $row['keluhan_awal'],
                'alasan_pending' => $row['catatan_teknisi'] ?? '-',
                'diagnosa_rusak' => $row['diagnosa_rusak'] ?? '-',
                'catatan_teknisi' => $row['catatan_teknisi'] ?? '-',
                'revisi' => false,
                'proses_status' => $row['status_kerusakan'] ? 'Sedang Perbaikan' : 'Menunggu Pengecekan',
                'rating' => $row['rating'] ?? null,
                'ulasan' => $row['ulasan'] ?? null
            ];
        }

        return $this->response->setJSON($mappedData);
    }

    // File: app/Controllers/Teknisi.php

    public function pending()
    {
        $db = \Config\Database::connect();

        try {
            $nomor = $this->request->getPost('nomor_laporan');
            $alasan = $this->request->getPost('alasan');

            if (!$nomor || !$alasan) {
                throw new \Exception('Data tidak lengkap');
            }

            $jadwal = $db->table('tb_jadwal_perbaikan jp')
                ->join('tb_laporan l', 'l.id_laporan = jp.id_laporan')
                ->where('l.nomor_laporan', $nomor)
                ->select('jp.id_jadwal, jp.id_laporan, jp.status_perbaikan')
                ->get()->getRowArray();

            if (!$jadwal) {
                throw new \Exception('Jadwal tidak ditemukan');
            }

            if ($jadwal['status_perbaikan'] !== 'PROSES') {
                throw new \Exception('Hanya tugas PROSES yang bisa dipending');
            }

            // STATUS TEKNISI
            $db->table('tb_jadwal_perbaikan')
                ->where('id_jadwal', $jadwal['id_jadwal'])
                ->update(['status_perbaikan' => 'PENDING']);

            // STATUS LAPORAN mengikuti status pending
            $db->table('tb_laporan')
                ->where('id_laporan', $jadwal['id_laporan'])
                ->update(['status_laporan' => 'PENDING']);

            // SIMPAN ALASAN
            $db->table('tb_perbaikan')
                ->where('id_jadwal', $jadwal['id_jadwal'])
                ->update([
                    'alasan_pending' => $alasan,
                    'waktu_pending' => date('Y-m-d H:i:s')
                ]);
            // ===========================================
            // EMAIL KE PELAPOR
            // ===========================================

            $laporan = $db->table('tb_laporan')
                ->where('id_laporan', $jadwal['id_laporan'])
                ->get()
                ->getRowArray();

            $pelapor = $db->table('tb_user')
                ->where('id_user', $laporan['id_pelapor'])
                ->get()
                ->getRowArray();

            if (!empty($pelapor['email'])) {

                $emailService = new \App\Libraries\EmailService();

                $emailService->kirimPending(
                    $pelapor['email'],
                    [
                        'judul' => 'Perbaikan Ditunda',
                        'pesan' => 'Proses perbaikan sementara ditunda oleh teknisi.',
                        'warna' => '#fd7e14',
                        'status' => 'PENDING',
                        'alasan_pending' => $alasan,

                        'nama_pelapor' => $laporan['nama_pelapor'],
                        'nomor_laporan' => $laporan['nomor_laporan'],
                        'tanggal' => date('d F Y H:i:s'),
                        'nama_alat' => $laporan['nama_alat'],
                        'lokasi' => $laporan['lokasi']
                    ]
                );

            }

            return $this->response->setJSON(['status' => 'success']);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function mulai_kerja($idLaporan)
    {
        $db = \Config\Database::connect();

        $jadwal = $db->table('tb_jadwal_perbaikan jp')
            ->select('jp.id_jadwal, jp.id_laporan, jp.status_perbaikan')
            ->join('tb_laporan l', 'l.id_laporan = jp.id_laporan')
            ->where('l.nomor_laporan', $idLaporan)
            ->get()
            ->getRowArray();

        if (!$jadwal || $jadwal['status_perbaikan'] !== 'MENUNGGU') {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Tugas tidak bisa dimulai'
            ]);
        }

        // 1. Update status jadwal ke PROSES
        $db->table('tb_jadwal_perbaikan')
            ->where('id_jadwal', $jadwal['id_jadwal'])
            ->update(['status_perbaikan' => 'PROSES']);

        // 2. Pastikan status laporan mengikuti status pekerjaan
        $db->table('tb_laporan')
            ->where('id_laporan', $jadwal['id_laporan'])
            ->update(['status_laporan' => 'DIPROSES']);

        // 2. PERBAIKAN DI SINI: Gunakan Update atau Insert (Upsert logic)
        // Cek apakah data sudah ada di tb_perbaikan (hasil dari update_status sebelumnya)
        $exists = $db->table('tb_perbaikan')
            ->where('id_jadwal', $jadwal['id_jadwal'])
            ->countAllResults();

        if ($exists) {
            // Jika sudah ada (baris id_perbaikan 1 di gambar Anda), cukup update waktu_mulai
            $db->table('tb_perbaikan')
                ->where('id_jadwal', $jadwal['id_jadwal'])
                ->update(['waktu_mulai' => date('Y-m-d H:i:s')]);
        } else {
            // Jika benar-benar belum ada, baru insert
            $db->table('tb_perbaikan')->insert([
                'id_jadwal' => $jadwal['id_jadwal'],
                'waktu_mulai' => date('Y-m-d H:i:s')
            ]);
        }

        // ===========================================
        // EMAIL KE PELAPOR
        // ===========================================

        $laporan = $db->table('tb_laporan')
            ->where('id_laporan', $jadwal['id_laporan'])
            ->get()
            ->getRowArray();

        $pelapor = $db->table('tb_user')
            ->where('id_user', $laporan['id_pelapor'])
            ->get()
            ->getRowArray();

        if (!empty($pelapor['email'])) {

            $emailService = new \App\Libraries\EmailService();

            $emailService->kirimMulaiPerbaikan(
                $pelapor['email'],
                [
                    'judul' => 'Perbaikan Sedang Dikerjakan',
                    'pesan' => 'Teknisi telah memulai proses perbaikan terhadap laporan yang Anda kirim.',
                    'warna' => '#0d6efd',
                    'status' => 'DIPROSES',

                    'nama_pelapor' => $laporan['nama_pelapor'],
                    'nomor_laporan' => $laporan['nomor_laporan'],
                    'tanggal' => date('d F Y H:i'),
                    'nama_alat' => $laporan['nama_alat'],
                    'lokasi' => $laporan['lokasi']
                ]
            );

        }

        return $this->response->setJSON(['status' => 'success']);
    }

    public function lanjutkan_kerja($idLaporan)
    {
        $db = \Config\Database::connect();

        $jadwal = $db->table('tb_jadwal_perbaikan jp')
            ->select('jp.id_jadwal, jp.id_laporan, jp.status_perbaikan')
            ->join('tb_laporan l', 'l.id_laporan = jp.id_laporan')
            ->where('l.nomor_laporan', $idLaporan)
            ->get()->getRowArray();

        if (!$jadwal) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ]);
        }

        if ($jadwal['status_perbaikan'] !== 'PENDING') {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Hanya tugas PENDING yang bisa dilanjutkan'
            ]);
        }

        // UPDATE STATUS
        $db->table('tb_jadwal_perbaikan')
            ->where('id_jadwal', $jadwal['id_jadwal'])
            ->update([
                'status_perbaikan' => 'PROSES'
            ]);

        // Pastikan status laporan kembali ke DIPROSES ketika pekerjaan dilanjutkan
        $db->table('tb_laporan')
            ->where('id_laporan', $jadwal['id_laporan'])
            ->update(['status_laporan' => 'DIPROSES']);

        // Catat waktu dilanjutkan ketika pekerjaan kembali dikerjakan dari pending
        $db->table('tb_perbaikan')
            ->where('id_jadwal', $jadwal['id_jadwal'])
            ->update([
                'waktu_dilanjutkan' => date('Y-m-d H:i:s')
            ]);

        // ===========================================
        // EMAIL KE PELAPOR
        // ===========================================

        $laporan = $db->table('tb_laporan')
            ->where('id_laporan', $jadwal['id_laporan'])
            ->get()
            ->getRowArray();

        $pelapor = $db->table('tb_user')
            ->where('id_user', $laporan['id_pelapor'])
            ->get()
            ->getRowArray();

        if (!empty($pelapor['email'])) {

            $emailService = new \App\Libraries\EmailService();

            $emailService->kirimLanjutkanPerbaikan(
                $pelapor['email'],
                [
                    'judul' => 'Perbaikan Dilanjutkan',
                    'pesan' => 'Teknisi telah melanjutkan proses perbaikan terhadap laporan yang sebelumnya tertunda.',
                    'warna' => '#0d6efd',
                    'status' => 'DIPROSES',

                    'nama_pelapor' => $laporan['nama_pelapor'],
                    'nomor_laporan' => $laporan['nomor_laporan'],
                    'tanggal' => date('d F Y H:i:s'),
                    'nama_alat' => $laporan['nama_alat'],
                    'lokasi' => $laporan['lokasi']
                ]
            );

        }

        return $this->response->setJSON(['status' => 'success']);
    }

    public function selesai()
    {
        $db = \Config\Database::connect();

        try {
            $nomor = $this->request->getPost('nomor_laporan');
            $uraian = $this->request->getPost('uraian');
            $files = $this->request->getFileMultiple('foto');

            if (!$nomor || !$files) {
                throw new \Exception('Data tidak lengkap');
            }

            if (!is_array($files)) {
                $files = [$files];
            }

            $files = array_filter($files, function ($file) {
                return $file && $file->isValid() && !$file->hasMoved();
            });

            if (empty($files)) {
                throw new \Exception('Bukti foto wajib diupload minimal satu.');
            }

            if (count($files) > 4) {
                throw new \Exception('Maksimal 4 foto dapat diunggah.');
            }

            $uploadedFiles = [];
            foreach ($files as $file) {
                if ($file->getSize() > 2 * 1024 * 1024) {
                    throw new \Exception('Ukuran foto maksimal 2MB per foto.');
                }

                $randomName = $file->getRandomName();
                $file->move(FCPATH . 'uploads/perbaikan', $randomName);
                $uploadedFiles[] = $randomName;
            }

            // Ambil jadwal
            $jadwal = $db->table('tb_jadwal_perbaikan jp')
                ->join('tb_laporan l', 'l.id_laporan = jp.id_laporan')
                ->where('l.nomor_laporan', $nomor)
                ->select('jp.id_jadwal, jp.id_laporan')
                ->get()->getRowArray();

            if (!$jadwal) {
                throw new \Exception('Jadwal tidak ditemukan');
            }

            // Simpan nama file uploaded sebagai daftar koma
            $namaFoto = implode(',', $uploadedFiles);

            // UPDATE PERBAIKAN
            $db->table('tb_perbaikan')
                ->where('id_jadwal', $jadwal['id_jadwal'])
                ->update([
                    'foto_bukti' => $namaFoto,
                    'catatan_teknisi' => $uraian,
                    'waktu_selesai' => date('Y-m-d H:i:s'),
                    'hasil_perbaikan' => 'SELESAI'
                ]);

            // KUNCI RIWAYAT
            $db->table('tb_jadwal_perbaikan')
                ->where('id_jadwal', $jadwal['id_jadwal'])
                ->update([
                    'status_perbaikan' => 'SELESAI'
                ]);

            $db->table('tb_laporan')
                ->where('id_laporan', $jadwal['id_laporan'])
                ->update([
                    'status_laporan' => 'MENUNGGU KONFIRMASI'
                ]);

            // ===========================================
            // EMAIL KE PELAPOR
            // ===========================================

            $laporan = $db->table('tb_laporan')
                ->where('id_laporan', $jadwal['id_laporan'])
                ->get()
                ->getRowArray();

            $pelapor = $db->table('tb_user')
                ->where('id_user', $laporan['id_pelapor'])
                ->get()
                ->getRowArray();

            if (!empty($pelapor['email'])) {

                $emailService = new \App\Libraries\EmailService();

                $emailService->kirimSelesai(
                    $pelapor['email'],
                    [
                        'judul' => 'Perbaikan Telah Diselesaikan',
                        'pesan' => 'Teknisi telah menyelesaikan proses perbaikan. Silakan login ke sistem untuk melakukan konfirmasi.',
                        'warna' => '#198754',
                        'status' => 'MENUNGGU KONFIRMASI',

                        'nama_pelapor' => $laporan['nama_pelapor'],
                        'nomor_laporan' => $laporan['nomor_laporan'],
                        'tanggal' => date('d F Y H:i'),
                        'nama_alat' => $laporan['nama_alat'],
                        'lokasi' => $laporan['lokasi']
                    ]
                );

            }
            return $this->response->setJSON(['status' => 'success']);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function rusak_total($nomorLaporan)
    {
        $db = \Config\Database::connect();

        // 1. Ambil Alasan
        $alasanRusak = $this->request->getPost('alasan_rusak');
        if (empty($alasanRusak)) {
            $alasanRusak = 'Kerusakan parah (Tidak ada keterangan detail)';
        }

        try {
            // 2. Cari Data Jadwal
            $jadwal = $db->table('tb_jadwal_perbaikan jp')
                ->join('tb_laporan l', 'l.id_laporan = jp.id_laporan')
                ->where('l.nomor_laporan', $nomorLaporan)
                ->select('jp.id_jadwal, jp.id_laporan, jp.status_perbaikan')
                ->get()->getRowArray();

            if (!$jadwal)
                throw new \Exception('Jadwal tidak ditemukan');

            // 3. Update Status JADWAL (Tetap 'RUSAK' untuk status pekerjaan)
            $db->table('tb_jadwal_perbaikan')
                ->where('id_jadwal', $jadwal['id_jadwal'])
                ->update(['status_perbaikan' => 'SELESAI']);

            // Ubah status laporan agar masuk ke tab Perlu Validasi Pelapor
            $db->table('tb_laporan')
                ->where('id_laporan', $jadwal['id_laporan'])
                ->update(['status_laporan' => 'MENUNGGU KONFIRMASI']);

            // 4. Update Tabel Perbaikan (LOGIKA UPDATE STATUS ADA DI SINI)
            $perbaikanExist = $db->table('tb_perbaikan')
                ->where('id_jadwal', $jadwal['id_jadwal'])
                ->countAllResults();

            // KITA PAKSA STATUS JADI 'Berat'
            $dataUpdate = [
                'status_kerusakan' => 'Berat',
                'hasil_perbaikan' => 'RUSAK TOTAL',
                'diagnosa_rusak' => $alasanRusak,
                'waktu_selesai' => date('Y-m-d H:i:s'),
                'waktu_cek_kerusakan' => date('Y-m-d H:i:s'),
                'catatan_teknisi' => 'Peralatan dinyatakan tidak dapat diperbaiki.',
            ];

            if ($perbaikanExist > 0) {
                // Jika sebelumnya sudah ada (misal "Sedang"), kita TIMPA jadi "Berat"
                $db->table('tb_perbaikan')
                    ->where('id_jadwal', $jadwal['id_jadwal'])
                    ->update($dataUpdate);
            } else {
                // Jika belum ada, kita INSERT baru sebagai "Berat"
                $dataUpdate['id_jadwal'] = $jadwal['id_jadwal'];
                $db->table('tb_perbaikan')->insert($dataUpdate);
            }

            // ===========================================
            // EMAIL KE PELAPOR
            // ===========================================

            $laporan = $db->table('tb_laporan')
                ->where('id_laporan', $jadwal['id_laporan'])
                ->get()
                ->getRowArray();

            $pelapor = $db->table('tb_user')
                ->where('id_user', $laporan['id_pelapor'])
                ->get()
                ->getRowArray();

            if (!empty($pelapor['email'])) {

                $emailService = new \App\Libraries\EmailService();

                $emailService->kirimTidakDapatDiperbaiki(
                    $pelapor['email'],
                    [
                        'judul' => 'Peralatan Tidak Dapat Diperbaiki',
                        'pesan' => 'Teknisi telah melakukan pemeriksaan dan menyatakan bahwa peralatan tidak dapat diperbaiki.',

                        'warna' => '#dc3545',
                        'status' => 'TIDAK DAPAT DIPERBAIKI',

                        'nama_pelapor' => $laporan['nama_pelapor'],
                        'nomor_laporan' => $laporan['nomor_laporan'],
                        'tanggal' => date('d F Y H:i:s'),
                        'nama_alat' => $laporan['nama_alat'],
                        'lokasi' => $laporan['lokasi'],

                        'keluhan' => $laporan['kerusakan'],
                        'diagnosa' => $alasanRusak,
                        'timeline' => [
                            [
                                'waktu' => date('d F Y H:i:s'),
                                'status' => '❌ Peralatan dinyatakan Rusak Total'
                            ]
                        ],
                    ]
                );

            }

            return $this->response->setJSON(['status' => 'success']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function riwayat()
    {
        $request = \Config\Services::request();
        $db = \Config\Database::connect();

        $idTeknisi = session()->get('id_user');

        // ====================================================
        // 1. DATA MASTER (UNTUK DROPDOWN & JS CASCADING)
        // ====================================================

        // A. Ambil Daftar Unit
        $queryUnit = $db->table('tb_master_unit')
            ->select('nama_unit')
            ->distinct()->where('nama_unit !=', '')->orderBy('nama_unit', 'ASC')->get();
        $list_unit = $queryUnit->getResultArray();

        // B. [PERBAIKAN UTAMA] Ambil Alat + JOIN ke Tabel User untuk Nama Teknisi
        $queryAlat = $db->table('tb_master_alat')
            ->select('tb_master_alat.nama_alat, tb_master_alat.nomor_inventaris, tb_user.nama as nama_teknisi')
            ->join('tb_user', 'tb_user.id_user = tb_master_alat.id_teknisi', 'left') // Join ID ke Nama
            ->orderBy('tb_master_alat.nama_alat', 'ASC')
            ->get();
        $daftarAlat = $queryAlat->getResultArray();

        // C. Buat Peta Pelaksana (Format: Nama Alat => Nama Teknisi)
        $mapPelaksana = [];
        foreach ($daftarAlat as $alat) {
            // Jika kolom nama_teknisi ada isinya, masukkan ke Map
            if (!empty($alat['nama_teknisi'])) {
                $mapPelaksana[$alat['nama_alat']] = $alat['nama_teknisi'];
            }
        }

        // D. Logic Lokasi (CASCADING LENGKAP)
        // Kita ambil Gedung, Lantai, dan Ruangan sekaligus
        $rawLokasi = $db->table('tb_master_lokasi')
            ->select('tb_master_lokasi.gedung, tb_master_lokasi.lantai, tb_master_lokasi.ruangan, tb_master_unit.nama_unit')
            ->join('tb_master_unit', 'tb_master_unit.id_unit = tb_master_lokasi.id_unit')
            ->orderBy('tb_master_lokasi.ruangan', 'ASC')
            ->get()->getResultArray();

        // Susun Array untuk JavaScript
        $daftar_lokasi = [];
        foreach ($rawLokasi as $loc) {
            $u = $loc['nama_unit']; // Key: Nama Unit

            // [PERBAIKAN DISINI BUNG!]
            // Kita gabungkan ketiganya jadi satu kalimat panjang
            // Contoh Hasil: "Gedung Teknik Sipil - Lantai 1 - Lab Uji Tanah"
            $lokasiLengkap = $loc['gedung'] . ' - ' . $loc['lantai'] . ' - ' . $loc['ruangan'];

            if (!empty($u)) {
                $daftar_lokasi[$u][] = $lokasiLengkap;
            }
        }

        // ====================================================
        // 2. QUERY DATA UTAMA (TABEL LAPORAN)
        // ====================================================
        $builder = $db->table('tb_laporan l');

        $builder->select([
            'l.*',
            'jp.status_perbaikan',
            'jp.tanggal_perbaikan',
            'u.nama as pelaksana',
            'p.status_kerusakan',
            'p.catatan_teknisi',
            'p.waktu_selesai'
        ]);

        $builder->join('tb_jadwal_perbaikan jp', 'jp.id_laporan = l.id_laporan', 'left');
        $builder->join('tb_user u', 'u.id_user = jp.id_teknisi', 'left');
        $builder->join('tb_perbaikan p', 'p.id_jadwal = jp.id_jadwal', 'left');

        // Filter: Hanya tampilkan Riwayat (Selesai/Rusak)
        $builder->where('jp.id_teknisi', $idTeknisi);

        // Filter: Hanya tampilkan Riwayat (Selesai/Rusak)
        $builder->groupStart()
            ->where('jp.status_perbaikan', 'SELESAI')
            ->orWhere('jp.status_perbaikan', 'RUSAK')
            ->groupEnd();

        // Tangkap Input Filter
        $dateRange = $request->getGet('daterange');
        $status = $request->getGet('status');
        $unit = $request->getGet('unit');

        // Logic Filter Date
        if ($dateRange) {
            $dates = explode(' s/d ', $dateRange);
            $tglAwal = $dates[0];
            $tglAkhir = isset($dates[1]) ? $dates[1] : $tglAwal;
            $builder->where("DATE(p.waktu_selesai) >=", $tglAwal);
            $builder->where("DATE(p.waktu_selesai) <=", $tglAkhir);
        }

        if ($status) {
            $builder->where('jp.status_perbaikan', $status);
        }

        if ($unit) {
            $builder->like('l.unit', $unit);
        }

        $builder->orderBy('p.waktu_selesai', 'DESC');

        // ====================================================
        // 3. KIRIM DATA KE VIEW
        // ====================================================
        $data = [
            'laporan_list' => $builder->get()->getResultArray(),
            'filter_range' => $dateRange,
            'filter_status' => $status,
            'filter_unit' => $unit,

            // Variabel Data Master untuk View & JS
            'list_unit' => $list_unit,
            'daftar_alat' => $daftarAlat,
            'daftar_lokasi' => $daftar_lokasi,
            'map_pelaksana' => $mapPelaksana
        ];

        return view('teknisi/riwayat', $data);
    }

    // Fungsi untuk AJAX Realtime Statistik
    // File: app/Controllers/Teknisi.php

    public function get_statistik_json()
    {
        $request = \Config\Services::request();
        $db = \Config\Database::connect();

        $idTeknisi = session()->get('id_user');

        // 1. Setup Query Builder
        $builder = $db->table('tb_perbaikan p');
        $builder->join('tb_jadwal_perbaikan jp', 'jp.id_jadwal = p.id_jadwal');
        // [TAMBAHAN] Join ke Laporan karena kita butuh data 'Unit' dan 'Tanggal Laporan' untuk filter
        $builder->join('tb_laporan l', 'l.id_laporan = jp.id_laporan');

        // 2. Filter Dasar (Hanya milik Teknisi ini & Status Selesai)
        $builder->where('jp.id_teknisi', $idTeknisi);
        $builder->groupStart()
            ->where('jp.status_perbaikan', 'SELESAI')
            ->orWhere('jp.status_perbaikan', 'RUSAK')
            ->groupEnd();

        // ============================================================
        // 3. TERAPKAN FILTER DARI USER (Copy Logic dari Datatable)
        // ============================================================

        $filterDate = $request->getGet('daterange');
        $filterUnit = $request->getGet('unit');
        $filterBulan = $request->getGet('bulan');
        $filterTahun = $request->getGet('tahun');

        // A. Filter Tanggal Range
        if (!empty($filterDate)) {
            $dates = explode(' to ', $filterDate); // Sesuaikan dengan JS Anda (" to " atau " s/d ")
            // Cek kalau formatnya pakai " s/d " (sesuai flatpickr config Anda)
            if (strpos($filterDate, ' s/d ') !== false) {
                $dates = explode(' s/d ', $filterDate);
            }

            if (count($dates) >= 1) {
                $tglAwal = trim($dates[0]);
                $tglAkhir = isset($dates[1]) ? trim($dates[1]) : $tglAwal;
                $builder->groupStart()
                    ->where('l.tanggal_laporan >=', $tglAwal)
                    ->where('l.tanggal_laporan <=', $tglAkhir . ' 23:59:59')
                    ->groupEnd();
            }
        }

        // B. Filter Unit
        if (!empty($filterUnit)) {
            $builder->where('l.unit', $filterUnit);
        }

        // C. Filter Bulan
        if (!empty($filterBulan)) {
            $builder->where('MONTH(l.tanggal_laporan)', $filterBulan);
        }

        // D. Filter Tahun
        if (!empty($filterTahun)) {
            $builder->where('YEAR(l.tanggal_laporan)', $filterTahun);
        }

        // ============================================================

        // 4. Ambil Data
        $data = $builder->select('p.status_kerusakan')->get()->getResultArray();

        // 5. Hitung Manual
        $total = count($data);
        $ringan = 0;
        $sedang = 0;
        $berat = 0;

        foreach ($data as $row) {
            $status = strtolower($row['status_kerusakan'] ?? '');
            if ($status == 'ringan')
                $ringan++;
            elseif ($status == 'sedang')
                $sedang++;
            elseif ($status == 'berat' || $status == 'rusak')
                $berat++;
        }

        return $this->response->setJSON([
            'total' => $total,
            'ringan' => $ringan,
            'sedang' => $sedang,
            'berat' => $berat
        ]);
    }

    // ... (Pastikan ini ada di dalam class Teknisi) ...

    public function get_dashboard_charts()
    {
        $db = \Config\Database::connect();
        $idTeknisi = session()->get('id_user');

        // Ambil data milik teknisi
        $data = $db->table('tb_jadwal_perbaikan jp')
            ->select('l.unit, l.nama_alat, p.status_kerusakan, jp.status_perbaikan')
            ->join('tb_laporan l', 'l.id_laporan = jp.id_laporan')
            ->join('tb_perbaikan p', 'p.id_jadwal = jp.id_jadwal', 'left')
            ->where('jp.id_teknisi', $idTeknisi)
            ->get()->getResultArray();

        // Siapkan Wadah Grafik
        $statsJurusan = [];
        $statsAlat = [];
        $statsSeverity = ['Ringan' => 0, 'Sedang' => 0, 'Berat' => 0];
        $statsStatus = ['Selesai' => 0, 'Proses' => 0, 'Menunggu' => 0, 'Batal' => 0];

        // ==========================================
        // PERBAIKAN: Siapkan Variabel Untuk 5 Kotak Card
        // ==========================================
        $totalLaporanKerusakan = count($data);
        $totalSelesaiDiperbaiki = 0;
        $totalBelumDiperbaiki = 0;
        $totalBarangRusak = 0;
        $totalPeminjaman = 0; // Set 0 dulu karena tabel peminjaman belum ada

        foreach ($data as $row) {
            // Chart Jurusan
            $unit = $row['unit'] ? $row['unit'] : 'Lainnya';
            $statsJurusan[$unit] = ($statsJurusan[$unit] ?? 0) + 1;

            // Chart Alat
            $alat = $row['nama_alat'] ? $row['nama_alat'] : 'Tanpa Nama';
            $statsAlat[$alat] = ($statsAlat[$alat] ?? 0) + 1;

            // Logika Fisik Kerusakan
            $sev = strtolower($row['status_kerusakan'] ?? '');
            if (strpos($sev, 'ringan') !== false) {
                $statsSeverity['Ringan']++;
            } elseif (strpos($sev, 'sedang') !== false) {
                $statsSeverity['Sedang']++;
            } elseif (strpos($sev, 'berat') !== false || strpos($sev, 'rusak') !== false) {
                $statsSeverity['Berat']++;
                $totalBarangRusak++; // <--- Tambah ke kotak Barang Rusak
            }

            // Logika Status Pekerjaan
            $stat = strtoupper($row['status_perbaikan'] ?? '');
            if ($stat == 'SELESAI') {
                $statsStatus['Selesai']++;
                $totalSelesaiDiperbaiki++; // <--- Tambah ke kotak Selesai
            } elseif ($stat == 'PROSES' || $stat == 'PENDING') {
                $statsStatus['Proses']++;
                $totalBelumDiperbaiki++; // <--- Tambah ke kotak Belum Diperbaiki
            } elseif ($stat == 'MENUNGGU') {
                $statsStatus['Menunggu']++;
                $totalBelumDiperbaiki++; // <--- Tambah ke kotak Belum Diperbaiki
            } else {
                $statsStatus['Batal']++;
            }
        }

        arsort($statsJurusan);
        arsort($statsAlat);

        return $this->response->setJSON([
            // Data untuk 5 Kotak Angka
            'cards' => [
                'laporan_kerusakan' => $totalLaporanKerusakan,
                'belum_diperbaiki' => $totalBelumDiperbaiki,
                'selesai_diperbaiki' => $totalSelesaiDiperbaiki,
                'peminjaman' => $totalPeminjaman,
                'barang_rusak' => $totalBarangRusak
            ],
            // Data untuk Grafik
            'charts' => [
                'jurusan' => ['labels' => array_keys($statsJurusan), 'data' => array_values($statsJurusan)],
                'alat' => ['labels' => array_slice(array_keys($statsAlat), 0, 10), 'data' => array_slice(array_values($statsAlat), 0, 10)],
                'severity' => ['labels' => array_keys($statsSeverity), 'data' => array_values($statsSeverity)],
                'status' => ['labels' => array_keys($statsStatus), 'data' => array_values($statsStatus)]
            ]
        ]);
    }

    public function get_riwayat_datatable()
    {
        // 1. Matikan Debugbar agar respon JSON bersih (Opsional)
        if (ENVIRONMENT !== 'production') {
            // \Config\Services::toolbar()->respond(); 
        }

        try {
            $request = \Config\Services::request();
            $db = \Config\Database::connect();

            // [PERBAIKAN 1] AMBIL ID TEKNISI YANG LOGIN
            $idTeknisi = session()->get('id_user');

            // --------------------------------------------------------------------
            // 2. DEFINISI KOLOM SORTING (Sesuai Urutan TH di View HTML)
            // --------------------------------------------------------------------
            // Index 0-13 (14 Kolom Data). Kolom ke-15 (Aksi) dihandle JS.
            $columns = [
                0 => 'l.nomor_laporan',     // Nomor Laporan
                1 => 'l.tanggal_laporan',   // Tanggal
                2 => 'l.tanggal_perbaikan',   // Tanggal
                3 => 'l.nama_alat',         // Nama Alat
                4 => 'l.nomor_inventaris',  // No Inventaris
                5 => 'l.lokasi_alat',       // Lokasi
                6 => 'l.unit',              // Unit
                7 => 'l.kerusakan',         // Keluhan (Pastikan nama kolom DB benar)
                8 => 'p.status_kerusakan',  // Status Fisik (Tabel Perbaikan)
                9 => 'u.nama',              // Teknisi
                10 => 'jp.status_perbaikan', // Status Perbaikan
                11 => 'p.catatan_teknisi',  // Alasan
                12 => 'l.validasi_kepala',  // Validasi (Cek null nanti)
                13 => 'l.path_foto_bukti'   // Foto
            ];

            // 3. TANGKAP INPUT DARI DATATABLES & FILTER
            $draw = $request->getVar('draw');
            $start = $request->getVar('start');
            $length = $request->getVar('length');
            $search = $request->getVar('search');
            $order = $request->getVar('order');

            // Filter Custom
            $filterDate = $request->getVar('daterange');
            $filterStatus = $request->getVar('status');
            $filterUnit = $request->getVar('unit');

            // [BARU] Tangkap Bulan & Tahun
            $filterBulan = $request->getVar('bulan');
            $filterTahun = $request->getVar('tahun');

            // 4. LOGIKA FILTER (Update Closure)
            // [PERBAIKAN 2] Tambahkan $idTeknisi ke dalam 'use'
            $applyFilterLogic = function ($builder) use ($filterDate, $filterStatus, $filterUnit, $search, $filterBulan, $filterTahun, $idTeknisi) {

                // [PERBAIKAN 3] TAMBAHKAN FILTER ID TEKNISI DISINI
                // Agar Cipto hanya melihat data miliknya sendiri
                $builder->where('jp.id_teknisi', $idTeknisi);

                // A. Filter Wajib (Tetap)
                $builder->groupStart()
                    ->where('jp.status_perbaikan', 'SELESAI')
                    ->orWhere('jp.status_perbaikan', 'RUSAK')
                    ->groupEnd();

                // B. Filter Tanggal Range (Tetap)
                if (!empty($filterDate)) {
                    $dates = explode(' to ', $filterDate);
                    if (count($dates) >= 1) {
                        $tglAwal = $dates[0];
                        $tglAkhir = isset($dates[1]) ? $dates[1] : $tglAwal;
                        $builder->groupStart()
                            ->where('l.tanggal_laporan >=', $tglAwal)
                            ->where('l.tanggal_laporan <=', $tglAkhir . ' 23:59:59')
                            ->groupEnd();
                    }
                }

                // [BARU] C. Filter Bulan
                if (!empty($filterBulan)) {
                    // Ambil bulan dari kolom tanggal_laporan
                    $builder->where('MONTH(l.tanggal_laporan)', $filterBulan);
                }

                // [BARU] D. Filter Tahun
                if (!empty($filterTahun)) {
                    // Ambil tahun dari kolom tanggal_laporan
                    $builder->where('YEAR(l.tanggal_laporan)', $filterTahun);
                }

                // E. Filter Status (Yang sudah diperbaiki tadi)
                if (!empty($filterStatus)) {
                    $statusFisik = ['Ringan', 'Sedang', 'Berat'];
                    if (in_array($filterStatus, $statusFisik)) {
                        $builder->like('p.status_kerusakan', $filterStatus);
                    } else {
                        $builder->where('jp.status_perbaikan', $filterStatus);
                    }
                }

                // F. Filter Unit (Tetap)
                if (!empty($filterUnit)) {
                    $builder->where('l.unit', $filterUnit);
                }

                // G. Global Search (Tetap)
                if (!empty($search) && !empty($search['value'])) {
                    $val = $search['value'];
                    $builder->groupStart()
                        ->like('l.nomor_laporan', $val)
                        ->orLike('l.nama_alat', $val)
                        ->orLike('l.unit', $val)
                        ->orLike('u.nama', $val)
                        ->groupEnd();
                }

                // C. Filter Status (LOGIKA CERDAS)
                if (!empty($filterStatus)) {
                    // Daftar status fisik (sesuai value di option dropdown HTML)
                    $statusFisik = ['Ringan', 'Sedang', 'Berat'];

                    if (in_array($filterStatus, $statusFisik)) {
                        // Jika user pilih Ringan/Sedang/Berat, cari di tabel PERBAIKAN (kolom fisik)
                        // Gunakan 'like' agar tidak sensitif huruf besar/kecil (Berat vs BERAT)
                        $builder->like('p.status_kerusakan', $filterStatus);
                    } else {
                        // Jika user pilih SELESAI, cari di tabel JADWAL (kolom perbaikan)
                        $builder->where('jp.status_perbaikan', $filterStatus);
                    }
                }

                // D. Filter Unit
                if (!empty($filterUnit)) {
                    $builder->where('l.unit', $filterUnit);
                }

                // E. Global Search (Pencarian Teks)
                if (!empty($search) && !empty($search['value'])) {
                    $val = $search['value'];
                    $builder->groupStart()
                        ->like('l.nomor_laporan', $val)
                        ->orLike('l.nama_alat', $val)
                        ->orLike('l.unit', $val)
                        ->orLike('u.nama', $val) // Cari nama teknisi juga
                        ->orLike('l.nomor_inventaris', $val) // Cari No. Inventaris
                        ->orLike('l.lokasi', $val)      // Cari Lokasi
                        ->orLike('l.kerusakan', $val)
                        ->groupEnd();
                }
            };

            // --------------------------------------------------------------------
            // 5. QUERY 1: HITUNG JUMLAH DATA SETELAH FILTER (RecordsFiltered)
            // --------------------------------------------------------------------
            $countBuilder = $db->table('tb_laporan l');
            $countBuilder->join('tb_jadwal_perbaikan jp', 'jp.id_laporan = l.id_laporan', 'left');
            $countBuilder->join('tb_user u', 'u.id_user = jp.id_teknisi', 'left');
            $countBuilder->join('tb_perbaikan p', 'p.id_jadwal = jp.id_jadwal', 'left');

            $applyFilterLogic($countBuilder);
            $recordsFiltered = $countBuilder->countAllResults();

            // --------------------------------------------------------------------
            // 6. QUERY 2: AMBIL DATA SEBENARNYA (Fetching)
            // --------------------------------------------------------------------
            $builder = $db->table('tb_laporan l');
            // Pilih kolom spesifik agar tidak ambigu
            $builder->select('l.*, jp.status_perbaikan, jp.tanggal_perbaikan, u.nama as pelaksana, p.catatan_teknisi, p.status_kerusakan as kondisi_fisik, p.waktu_selesai');
            $builder->join('tb_jadwal_perbaikan jp', 'jp.id_laporan = l.id_laporan', 'left');
            $builder->join('tb_user u', 'u.id_user = jp.id_teknisi', 'left');
            $builder->join('tb_perbaikan p', 'p.id_jadwal = jp.id_jadwal', 'left');

            // Terapkan filter yang sama
            $applyFilterLogic($builder);

            // Terapkan Sorting
            if ($order && isset($order[0]['column']) && isset($columns[$order[0]['column']])) {
                $colIndex = $order[0]['column'];
                $colDir = $order[0]['dir'];
                $builder->orderBy($columns[$colIndex], $colDir);
            } else {
                $builder->orderBy('l.tanggal_laporan', 'DESC'); // Default sort terbaru
            }

            // Terapkan Pagination (Limit & Offset)
            if ($length != -1) {
                $builder->limit($length, $start);
            }

            $data = $builder->get()->getResultArray();

            // --------------------------------------------------------------------
            // 7. FORMAT DATA KE JSON ARRAY
            // --------------------------------------------------------------------
            $result = [];
            $no = $start + 1; // Nomor urut halaman

            foreach ($data as $row) {
                // 1. Logic Badge Status Perbaikan (SELESAI/RUSAK)
                $badgeStatus = ($row['status_perbaikan'] == 'SELESAI') ? 'bg-success' : 'bg-danger';

                // ... (Di dalam foreach) ...

                // 2. LOGIC FOTO (MULTI FOTO)
                $foto = '-'; // Default jika kosong
                $pathFoto = $row['path_foto_bukti'] ?? '';

                if (!empty($pathFoto)) {
                    // Pecah string berdasarkan koma
                    $files = explode(',', $pathFoto);
                    $tempFoto = []; // Penampung HTML sementara

                    foreach ($files as $f) {
                        $f = trim($f); // Bersihkan spasi
                        if (empty($f))
                            continue; // Skip jika kosong

                        $url = base_url('uploads/laporan/' . $f);

                        // Buat elemen gambar kecil
                        // Kita beri margin kanan sedikit (me-1) agar ada jarak antar foto
                        $tempFoto[] = '<a href="' . $url . '" target="_blank" title="Lihat Foto">
                                        <img src="' . $url . '" width="40" height="40" class="img-thumbnail" style="object-fit: cover;">
                                       </a>';
                    }

                    // Gabungkan semua foto dalam satu container flex agar rapi
                    if (!empty($tempFoto)) {
                        // d-flex flex-wrap: Agar foto berjejer dan turun ke bawah jika sempit
                        // gap-1: Jarak antar foto
                        $foto = '<div class="d-flex flex-wrap gap-1 justify-content-center">'
                            . implode('', $tempFoto)
                            . '</div>';
                    }
                }

                // 4. Data Teks (Safe Check)
                $keluhan = $row['kerusakan'] ?? $row['kerusakan_keluhan'] ?? $row['keluhan_awal'] ?? '-';
                $lokasi = $row['lokasi_alat'] ?? $row['lokasi'] ?? '-';

                // 5. [BARU] LOGIC BADGE STATUS KERUSAKAN (FISIK)
                $rawFisik = $row['kondisi_fisik'] ?? $row['status_kerusakan'] ?? '-';
                $fisikText = ucfirst($rawFisik); // Huruf depan besar
                $fisikLower = strtolower($rawFisik); // Untuk pengecekan kondisi

                // Tentukan Warna Badge
                $badgeFisik = 'bg-secondary'; // Default Abu-abu
                if ($fisikLower === 'ringan') {
                    $badgeFisik = 'bg-success'; // Hijau
                } elseif ($fisikLower === 'sedang') {
                    $badgeFisik = 'bg-warning text-dark'; // Kuning (teks hitam biar terbaca)
                } elseif ($fisikLower === 'berat' || $fisikLower === 'rusak') {
                    $badgeFisik = 'bg-danger'; // Merah
                }

                // Masukkan ke array
                $result[] = [
                    $row['nomor_laporan'] ?? '-',                          // 0
                    $row['tanggal_laporan'] ?? '-',                        // 1
                    $row['tanggal_perbaikan'] ?? '-',
                    $row['nama_alat'] ?? '-',                              // 2
                    $row['nomor_inventaris'] ?? '-',                       // 3
                    $row['lokasi'] ?? '-',                                        // 4
                    $row['unit'] ?? '-',                                   // 5
                    $keluhan,                                       // 6
                    '<span class="badge ' . $badgeFisik . '">' . $fisikText . '</span>', // 7 
                    $row['pelaksana'] ?? '-',                              // 8
                    '<span class="badge ' . $badgeStatus . '">' . ($row['status_perbaikan'] ?? 'BELUM') . '</span>', // 9
                    '<span class="">' . ($row['catatan_teknisi'] ?? '-') . '</span>', // 10
                    $row['validasi_kepala'] ?? 'Menunggu',          // 11
                    $foto                                           // 12
                ];
            }

            // --------------------------------------------------------------------
            // [PERBAIKAN DISINI] 
            // 8. QUERY 3: HITUNG TOTAL MILIK TEKNISI INI SAJA (Base Count)
            // --------------------------------------------------------------------
            // Kita ganti countAll() global dengan count yang difilter ID Teknisi

            $totalBuilder = $db->table('tb_laporan l');
            $totalBuilder->join('tb_jadwal_perbaikan jp', 'jp.id_laporan = l.id_laporan', 'left');

            // Filter Dasar (Wajib Sama dengan Logic Utama)
            $totalBuilder->where('jp.id_teknisi', $idTeknisi);
            $totalBuilder->groupStart()
                ->where('jp.status_perbaikan', 'SELESAI')
                ->orWhere('jp.status_perbaikan', 'RUSAK')
                ->groupEnd();

            // Hitung hasilnya
            $recordsTotal = $totalBuilder->countAllResults();

            // Return JSON response
            return $this->response->setJSON([
                "draw" => intval($draw),
                "recordsTotal" => intval($recordsTotal), // <-- Sekarang nilainya akan 8
                "recordsFiltered" => intval($recordsFiltered),
                "data" => $result
            ]);
        } catch (\Throwable $e) {
            // Error Handling: Kirim pesan error sebagai JSON valid
            // Agar DataTables tidak freeze, tapi menampilkan pesan error di console
            return $this->response->setStatusCode(200)->setJSON([
                "draw" => 1,
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => [],
                "error" => "SERVER ERROR: " . $e->getMessage() . " (Line: " . $e->getLine() . ")"
            ]);
        }
    }

    public function tambah_data()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Akses ditolak!']);
        }

        $db = \Config\Database::connect();
        $laporanModel = new \App\Models\LaporanModel();

        // Ambil Input
        $namaAlat = trim((string) $this->request->getPost('nama_alat'));
        $namaUnit = trim((string) $this->request->getPost('unit')); // Contoh: "Manajemen Informatika"
        $statusKerusakan = trim((string) $this->request->getPost('status_kerusakan'));

        // ====================================================================
        // [PERBAIKAN LOGIKA PENCARIAN ID PELAPOR: DUAL LAYER]
        // ====================================================================

        // 1. Percobaan Pertama: Cari dengan Nama Unit Lengkap
        $userUnit = $db->table('tb_user')
            ->select('id_user')
            ->like('nama', $namaUnit)
            ->get()
            ->getRowArray();

        // 2. Percobaan Kedua (Fallback): Jika Gagal, cari pakai KATA TERAKHIR
        // Contoh: Input "Rekayasa ... Bisnis Pertanian" -> Ambil kata "Pertanian"
        if (!$userUnit) {
            // Pecah kalimat jadi kata-kata
            $pecahanKata = explode(' ', trim($namaUnit));
            // Ambil kata paling belakang
            $kataKunci = end($pecahanKata);

            // Cek ke database lagi pakai kata kunci pendek (Hanya jika kata > 3 huruf)
            if (strlen($kataKunci) > 3) {
                $userUnit = $db->table('tb_user')
                    ->select('id_user')
                    ->like('nama', $kataKunci)
                    ->get()
                    ->getRowArray();
            }
        }

        // Ambil ID-nya (Jika ketemu di percobaan 1 atau 2)
        $idPelaporOtomatis = $userUnit ? $userUnit['id_user'] : null;

        // Cari Penanggung Jawab DULU
        $nomorInventaris = trim((string) $this->request->getPost('nomor_inventaris'));
        $teknisiQuery = $db->table('tb_master_alat')->select('id_teknisi');

        if (!empty($namaAlat)) {
            $teknisiQuery->where('nama_alat', $namaAlat);
        }

        $teknisiPJ = $teknisiQuery->get()->getRowArray();
        $idTargetTeknisi = $teknisiPJ['id_teknisi'] ?? null;

        if (!$idTargetTeknisi && !empty($nomorInventaris)) {
            $fallbackTeknisi = $db->table('tb_master_alat')
                ->select('id_teknisi')
                ->where('nomor_inventaris', $nomorInventaris)
                ->get()->getRowArray();

            $idTargetTeknisi = $fallbackTeknisi['id_teknisi'] ?? null;
        }

        if (!$idTargetTeknisi && !empty($namaAlat)) {
            $fallbackTeknisi = $db->table('tb_master_alat')
                ->select('id_teknisi')
                ->like('nama_alat', $namaAlat)
                ->get()->getRowArray();

            $idTargetTeknisi = $fallbackTeknisi['id_teknisi'] ?? null;
        }

        // Tentukan Status (sama seperti alur admin manual)
        $statusJadwal = 'MENUNGGU';
        $statusLaporan = 'BARU';
        $tglPerbaikan = NULL;
        $waktuSelesai = NULL;
        $catatanTeknisi = NULL;
        $pesanResponse = 'Laporan disimpan. Silakan lihat di tab Laporan Baru.';

        // Normalize status_kerusakan agar cocok enum
        $statusKerusakanDb = null;
        if ($statusKerusakan && $statusKerusakan !== 'Belum Dicek') {
            $statusKerusakanDb = strtoupper($statusKerusakan);
        }

        // Loop Insert
        $maxRetries = 3;
        $attempt = 0;
        $success = false;
        $lastError = '';

        while ($attempt < $maxRetries && !$success) {
            $attempt++;
            $nomorBaru = $laporanModel->generateNomorLaporan();

            // 2. SIAPKAN DATA LAPORAN
            $dataLaporan = [
                'nomor_laporan' => $nomorBaru,
                'id_pelapor' => $idPelaporOtomatis, // <--- Ini sekarang akan terisi ID (misal: 25)
                'tanggal_laporan' => $this->request->getPost('tanggal') . ' ' . date('H:i:s'),
                'nama_alat' => $namaAlat,
                'nomor_inventaris' => $this->request->getPost('nomor_inventaris'),
                'unit' => $this->request->getPost('unit'),
                'lokasi' => $this->request->getPost('lokasi_alat'),

                // [PERBAIKAN 2: MAPPING NAMA PELAPOR]
                // Ambil dari input 'pelapor', bukan 'nama_pelapor'
                'nama_pelapor' => $this->request->getPost('pelapor'),

                'pelaksana' => $this->request->getPost('pelaksana'),
                'media_laporan' => $this->request->getPost('media_laporan'),
                'kerusakan' => $this->request->getPost('kerusakan_keluhan'),
                'cetak_identitas_alat' => $this->request->getPost('cetak_identitas_alat'),
                'status_laporan' => $statusLaporan,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $db->transStart();
            try {
                $laporanModel->insert($dataLaporan);
                $idLaporanBaru = $laporanModel->getInsertID();

                if ($idTargetTeknisi) {
                    $dataJadwal = [
                        'id_laporan' => $idLaporanBaru,
                        'id_teknisi' => $idTargetTeknisi,
                        'tanggal_perbaikan' => $tglPerbaikan,
                        'status_perbaikan' => $statusJadwal,
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    $db->table('tb_jadwal_perbaikan')->insert($dataJadwal);
                    $idJadwalBaru = $db->insertID();

                    // Simpan status kerusakan pada tabel perbaikan saat laporan manual dibuat
                    $dataPerbaikan = [
                        'id_jadwal' => $idJadwalBaru,
                        'status_kerusakan' => $statusKerusakanDb,
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    $insertPerbaikan = $db->table('tb_perbaikan')->insert($dataPerbaikan);
                    if ($insertPerbaikan === false) {
                        $errorDB = $db->error();
                        $message = $errorDB['message'] ?? 'Unknown database error saat insert tb_perbaikan';
                        throw new \Exception('Gagal insert data perbaikan ke database: ' . $message);
                    }
                }

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
            return $this->response->setJSON(['status' => 'success', 'message' => $pesanResponse]);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal: ' . $lastError]);
        }
    }

    // Fungsi untuk Request AJAX dari Modal Tambah
    public function get_nomor_otomatis()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'error']);
        }

        $laporanModel = new \App\Models\LaporanModel();

        // 1. Ambil Tanggal Hari Ini (Format YYYYMMDD) -> 20260207
        $nomorBaru = $laporanModel->generateNomorLaporan();

        return $this->response->setJSON(['nomor' => $nomorBaru]);
    }

    public function hapus_laporan()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Akses ditolak']);
        }

        $nomorLaporan = $this->request->getPost('nomor_laporan');
        $db = \Config\Database::connect();

        $db->transStart();
        try {
            // 1. Hapus di Tabel Laporan (Parent)
            // Pastikan relasi database Anda (Foreign Key) sudah ON UPDATE CASCADE ON DELETE CASCADE
            // Jika belum, hapus manual tabel anaknya (jadwal & perbaikan) terlebih dahulu.

            // Contoh Hapus Manual (Jika tidak ada Cascade):
            // $db->table('tb_jadwal_perbaikan')->whereIn('id_laporan', function($builder) use ($nomorLaporan) {
            //      return $builder->select('id_laporan')->from('tb_laporan')->where('nomor_laporan', $nomorLaporan);
            // })->delete();

            // Hapus Laporan Utama
            $db->table('tb_laporan')->where('nomor_laporan', $nomorLaporan)->delete();

            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                throw new \Exception('Gagal menghapus data dari database.');
            }

            return $this->response->setJSON(['status' => 'success', 'message' => 'Data berhasil dihapus permanently.']);
        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function get_data_by_nomor($nomorLaporan)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Akses ditolak']);
        }

        $db = \Config\Database::connect();

        // Query Lengkap (Join semua tabel terkait)
        $data = $db->table('tb_laporan l')
            ->select([
                'l.*',
                'u.nama as pelaksana_nama', // Nama Teknisi
                'jp.id_teknisi as pelaksana_id', // ID Teknisi
                'p.status_kerusakan',
                'p.catatan_teknisi' // Keterangan/Alasan
            ])
            ->join('tb_jadwal_perbaikan jp', 'jp.id_laporan = l.id_laporan', 'left')
            ->join('tb_user u', 'u.id_user = jp.id_teknisi', 'left')
            ->join('tb_perbaikan p', 'p.id_jadwal = jp.id_jadwal', 'left')
            ->where('l.nomor_laporan', $nomorLaporan)
            ->get()->getRowArray();

        if ($data) {
            return $this->response->setJSON(['status' => 'success', 'data' => $data]);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak ditemukan']);
        }
    }

    public function update_data()
    {
        // 1. Cek Request AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Akses ditolak']);
        }

        $db = \Config\Database::connect();
        $nomorLaporan = $this->request->getPost('nomor_laporan');

        if (!$nomorLaporan) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Nomor Laporan tidak ditemukan.']);
        }

        // 2. Siapkan Data Update (MAPPING INPUT FORM -> KOLOM DATABASE)
        // Kiri: Nama Kolom di Database
        // Kanan: name="..." di HTML Form
        $dataUpdate = [
            // Kolom Baru yang Anda tambahkan
            'media_laporan' => $this->request->getPost('media_laporan'),
            'uraian_pekerjaan' => $this->request->getPost('uraian_pekerjaan'),
            'nama_barang' => $this->request->getPost('nama_barang'),
            'jumlah_barang' => $this->request->getPost('jumlah_barang'),
            'cetak_identitas_alat' => $this->request->getPost('cetak_identitas_alat'),

            // Kolom Lain (Jika ingin bisa diedit juga)
            // 'nama_pelapor'      => $this->request->getPost('pelapor'),
            // 'kerusakan'         => $this->request->getPost('kerusakan_keluhan'),

            'updated_at' => date('Y-m-d H:i:s')
        ];

        // 3. Cek Tanggal (Jika diubah di form)
        $tglBaru = $this->request->getPost('tanggal');
        if (!empty($tglBaru)) {
            // Gabungkan tanggal baru dengan jam saat ini
            $dataUpdate['tanggal_laporan'] = $tglBaru . ' ' . date('H:i:s');
        }

        // 4. Eksekusi Update ke Database MySQL
        $db->transStart();
        try {
            // Update Tabel tb_laporan
            $update = $db->table('tb_laporan')
                ->where('nomor_laporan', $nomorLaporan)
                ->update($dataUpdate);

            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                throw new \Exception('Gagal memperbarui database.');
            }

            return $this->response->setJSON(['status' => 'success', 'message' => 'Data berhasil disimpan ke Database.']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    // Tambahkan ini di dalam Class Teknisi
    public function get_count_dashboard()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'error']);
        }

        $db = \Config\Database::connect();
        $idTeknisi = session()->get('id_user');

        // Helper function biar kodenya rapi
        // Menghitung jumlah berdasarkan status dan ID Teknisi
        $hitung = function ($statusArray) use ($db, $idTeknisi) {
            return $db->table('tb_jadwal_perbaikan')
                ->where('id_teknisi', $idTeknisi)
                ->whereIn('status_perbaikan', $statusArray)
                ->countAllResults();
        };

        // 1. Tugas Baru (Status: MENUNGGU)
        $baru = $hitung(['MENUNGGU']);

        // 2. Proses (Status: PROSES)
        $proses = $hitung(['PROSES']);

        // 3. Pending (Status: PENDING)
        $pending = $hitung(['PENDING']);

        // 4. Riwayat (Status: SELESAI, RUSAK, BATAL)
        $riwayat = $hitung(['SELESAI', 'RUSAK', 'BATAL']);

        return $this->response->setJSON([
            'baru' => $baru,
            'proses' => $proses,
            'pending' => $pending,
            'riwayat' => $riwayat
        ]);
    }

    // ===================================================================
// FUNGSI BARU: AMBIL NOTIFIKASI REALTIME
// ===================================================================
    public function get_notifikasi()
    {
        $db = \Config\Database::connect();

        try {

            $idTeknisi = session()->get('id_user');

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
                ->join('tb_jadwal_perbaikan jp', 'jp.id_laporan = l.id_laporan', 'left')
                ->join('tb_user u', 'u.id_user = jp.id_teknisi', 'left')

                // Hanya tugas milik teknisi yang login
                ->where('jp.id_teknisi', $idTeknisi)

                ->orderBy('l.updated_at', 'DESC')
                ->limit(10)
                ->get()
                ->getResultArray();

            foreach ($notifikasi as &$item) {

                if ($item['status_laporan'] == 'BARU') {

                    $item['pesan'] = 'Laporan baru menunggu penugasan';

                } else if ($item['status_laporan'] == 'DIJADWALKAN') {

                    $item['pesan'] = 'Anda dijadwalkan melakukan perbaikan';

                } else if ($item['status_laporan'] == 'DIPROSES') {

                    $item['pesan'] = 'Perbaikan sedang berlangsung';

                } else if ($item['status_laporan'] == 'PENDING') {

                    $item['pesan'] = 'Perbaikan sedang ditunda';

                } else if ($item['status_laporan'] == 'MENUNGGU KONFIRMASI') {

                    $item['pesan'] = 'Menunggu konfirmasi pelapor';

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
}