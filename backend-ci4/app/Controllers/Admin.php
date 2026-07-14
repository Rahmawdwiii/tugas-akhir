<?php

namespace App\Controllers;

use App\Models\UnitModel;
use App\Models\UserModel;
use CodeIgniter\Database\Config;
class Admin extends BaseController
{
    protected $userModel;
    protected $lokasiModel;
    protected $unitModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->unitModel = new UnitModel();
    }
    /**
     * Helper function untuk membaca JSON.
     * DIUBAH AGAR LEBIH ROBUST TERHADAP FILE KOSONG ATAU JSON TIDAK VALID.
     */
    protected function readJson($file)
    {
        if (!file_exists($file)) {
            return []; // Kembalikan array kosong jika file tidak ada
        }
        $content = file_get_contents($file);
        $data = json_decode($content, true);

        // Pastikan hasil decode adalah array. Jika null (karena JSON rusak), kembalikan array kosong.
        return is_array($data) ? $data : [];
    }

    /**
     * Helper function untuk mencari & update data di array.
     */
    protected function updateListItem(&$list, $id, $data, &$updatedFlag)
    {
        if (is_array($list)) {
            foreach ($list as $index => $item) {
                if (isset($item['nomor_laporan']) && $item['nomor_laporan'] === $id) {
                    // Gabungkan data lama dengan data baru yang tidak kosong
                    $dataToMerge = array_filter($data, fn($value) => $value !== null && $value !== '');

                    // Tambahkan timestamp update jika diperlukan
                    $dataToMerge['tanggal_update'] = date('Y-m-d H:i:s');

                    // Gunakan array_merge untuk menimpa data lama dengan data baru
                    $list[$index] = array_merge($item, $dataToMerge);
                    $updatedFlag = true;
                }
            }
        }
    }

    // --- GANTI FUNGSI INI DI Admin.php ---
    public function get_antrian($kategori)
    {
        $db = \Config\Database::connect();

        try {
            $builder = $db->table('tb_laporan l');

            // 1. SELECT BUNGKUS DENGAN MAX UNTUK TABEL JOIN (Mencegah error Full Group By)
            $builder->select('
                l.id_laporan,
                l.nomor_laporan AS id,
                l.nama_alat AS alat,
                l.nomor_inventaris AS inv_no,
                l.tanggal_laporan AS tgl_laporan,
                l.kerusakan,
                l.lokasi,
                l.unit AS gedung, 
                l.nama_pelapor AS pelapor,
                l.kerusakan AS keluhan_lengkap,
                l.path_foto_bukti AS foto_url,
                l.link_pendukung,
                l.status_laporan,
                l.validasi_kepala,

                MAX(p.hasil_perbaikan) AS hasil_perbaikan,
                MAX(jp.status_perbaikan) AS status_perbaikan,
                MAX(jp.tanggal_perbaikan) AS tgl_dijadwalkan,
                MAX(jp.tanggal_perbaikan) AS tgl_perbaikan,
                MAX(p.status_kerusakan) AS status_kerusakan,
                MAX(p.waktu_cek_kerusakan) AS waktu_cek_kerusakan,
                MAX(p.waktu_mulai) AS waktu_mulai,
                MAX(p.waktu_dilanjutkan) AS waktu_dilanjutkan,
                MAX(p.waktu_selesai) AS waktu_selesai,
                MAX(p.catatan_teknisi) AS catatan_teknisi,
                MAX(p.diagnosa_rusak) AS diagnosa_rusak,
                MAX(p.foto_bukti) AS foto_bukti_teknisi,
                MAX(u.nama) AS teknisi_nama,
                MAX(u.nama) AS nama_teknisi,
                MAX(vp.rating) AS rating,
                MAX(vp.ulasan) AS ulasan_pelapor
            ');

            // 2. JOIN TABEL
            $builder->join('tb_jadwal_perbaikan jp', 'jp.id_laporan = l.id_laporan', 'left');
            $builder->join('tb_user u', 'u.id_user = jp.id_teknisi', 'left');
            $builder->join('tb_perbaikan p', 'p.id_jadwal = jp.id_jadwal', 'left');
            $builder->join(
                'tb_validasi vp',
                'vp.id_laporan = l.id_laporan AND vp.jenis_validasi = "PELAPOR"',
                'left'
            );

            // 3. FILTER TAB
            if ($kategori === 'new') {
                $builder->where('l.status_laporan', 'BARU');
            } elseif ($kategori === 'proses') {
                $builder->groupStart()
                    ->where('l.status_laporan', 'DIPROSES')
                    ->orWhere('l.status_laporan', 'PENDING')
                    ->groupEnd();
            } elseif ($kategori === 'dijadwalkan') {
                $builder->where('l.status_laporan', 'DIJADWALKAN');
            } elseif ($kategori === 'validasi_akhir') {

                $builder->groupStart()

                    ->where('l.status_laporan', 'MENUNGGU KONFIRMASI')

                    ->orGroupStart()
                    ->where('l.status_laporan', 'SELESAI')
                    ->where('l.validasi_kepala', 'Menunggu')
                    ->groupEnd()

                    ->groupEnd();
            } elseif ($kategori === 'riwayat') {
                $builder
                    ->where('l.status_laporan', 'SELESAI')
                    ->where('l.validasi_kepala', 'Disetujui');
            }

            // 4. KUNCI ANTI ERROR (Semua kolom tb_laporan harus di-group by)
            $builder->groupBy([
                'l.id_laporan',
                'l.nomor_laporan',
                'l.nama_alat',
                'l.nomor_inventaris',
                'l.tanggal_laporan',
                'l.kerusakan',
                'l.lokasi',
                'l.unit',
                'l.nama_pelapor',
                'l.path_foto_bukti',
                'l.link_pendukung',
                'l.status_laporan',
                'l.validasi_kepala',
                'l.created_at'
            ]);

            // 5. AMBIL DATA
            $data = $builder->orderBy('l.created_at', 'DESC')->get()->getResultArray();

            // 6. PROSES LOOPING UNTUK MEMBUAT ARRAY FOTO (Agar JS tidak error)
            foreach ($data as &$row) {
                // FOTO PELAPOR
                $rawFoto = $row['foto_url'];
                $listFotoUrl = [];

                if (!empty($rawFoto)) {
                    $files = explode(',', $rawFoto);
                    foreach ($files as $f) {
                        $cleanName = trim($f);
                        if ($cleanName !== '') {
                            if (strpos($cleanName, 'http') === 0) {
                                $listFotoUrl[] = $cleanName;
                            } else {
                                $listFotoUrl[] = base_url('uploads/laporan/' . $cleanName);
                            }
                        }
                    }
                }
                $row['foto_urls'] = $listFotoUrl;

                // FOTO BUKTI TEKNISI
                $rawFotoTeknisi = $row['foto_bukti_teknisi'];
                $listFotoTeknisiUrl = [];

                if (!empty($rawFotoTeknisi)) {
                    $filesTeknisi = explode(',', $rawFotoTeknisi);
                    foreach ($filesTeknisi as $f) {
                        $cleanName = trim($f);
                        if ($cleanName !== '') {
                            if (strpos($cleanName, 'http') === 0) {
                                $listFotoTeknisiUrl[] = $cleanName;
                            } else {
                                $listFotoTeknisiUrl[] = base_url('uploads/perbaikan/' . $cleanName);
                            }
                        }
                    }
                }
                $row['foto_bukti_teknisi_urls'] = $listFotoTeknisiUrl;
            }

            log_message('error', 'Kategori : ' . $kategori);
            log_message('error', json_encode($data));

            // 7. RETURN DATA JSON
            return $this->response->setJSON($data);

        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'error' => true,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function dashboard()
    {
        /**
         * FUNGSI UNTUK MENAMPILKAN TABEL LAPORAN (GET)
         */

        $filePath = WRITEPATH . 'data/laporan.json';
        $laporan = [];

        if (file_exists($filePath)) {
            $laporan = json_decode(file_get_contents($filePath), true);

            // 1. Tentukan lokasi file sumber (file utama yang berisi semua laporan)
            $filePath = WRITEPATH . 'data/dashboard_laporan.json';
            $dashboardList = [];

            if (file_exists($filePath)) {
                $dashboardList = json_decode(file_get_contents($filePath), true) ?? [];

                if (is_array($dashboardList)) {
                    // Optional: Balik urutan agar laporan terbaru ada di atas
                    $dashboardList = array_reverse($dashboardList);
                }
            }

            // 2. Kirim data laporan LENGKAP ke view dashboard
            $data['dashboardList'] = $dashboardList;

            // 3. (Anda perlu menghitung counts di sini jika cards ringkasan masih ada)
            // ... logic penghitungan cards ...

            // Pastikan view di sini menggunakan nama file dashboard Anda
            return view('admin/dashboard', $data);
        }
    }

    public function data_barang_rusak()
    {
        $db = \Config\Database::connect();

        $daftar_rusak = $db->table('tb_barang_rusak br')
            ->select([
                'br.id_barang_rusak',
                'br.id_laporan',
                'br.nomor_laporan',
                'br.nomor_inventaris',
                'br.nama_alat',
                'br.lokasi',
                'br.unit',
                'br.alasan_rusak',
                'br.tanggal_rusak',

                'l.tanggal_laporan',
                'l.nama_pelapor',
                'l.status_laporan',

                'p.status_kerusakan',
                'p.hasil_perbaikan',
                'p.catatan_teknisi',
                'p.waktu_selesai',

                'u.nama AS nama_teknisi'
            ])
            ->join('tb_laporan l', 'l.id_laporan = br.id_laporan', 'left')
            ->join('tb_jadwal_perbaikan jp', 'jp.id_laporan = l.id_laporan', 'left')
            ->join('tb_perbaikan p', 'p.id_jadwal = jp.id_jadwal', 'left')
            ->join('tb_user u', 'u.id_user = jp.id_teknisi', 'left')
            ->orderBy('br.tanggal_rusak', 'DESC')
            ->get()
            ->getResultArray();

        $listUnit = $this->unitModel
            ->orderBy('nama_unit', 'ASC')
            ->findAll();

        $data = [
            'daftar_rusak' => $daftar_rusak,
            'total_rusak' => count($daftar_rusak),
            'listUnit' => $listUnit,
        ];

        return view('admin/data_barang_rusak', $data);
    }

    public function getMasterAlat()
    {
        $alatModel = new \App\Models\AlatModel();

        // Ambil data real-time dari database
        // Kita sesuaikan format return-nya agar sama dengan struktur array lama Anda
        // yaitu: [['nama_alat' => '...', 'no_inventaris' => '...'], ...]

        $dataDB = $alatModel->select('nama_alat, nomor_inventaris as no_inventaris')
            ->orderBy('nama_alat', 'ASC')
            ->findAll();

        return $dataDB;
    }

    // FUNGSI AMBIL DATA ALAT (UNTUK EDIT)
    public function get_alat($id)
    {
        // 1. Cek Request AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $alatModel = new \App\Models\AlatModel();

        // 2. Ambil data berdasarkan ID
        $data = $alatModel->find($id);

        // 3. Kirim respon JSON
        if ($data) {
            return $this->response->setJSON($data);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data alat tidak ditemukan']);
        }
    }

    // SIMPAN PERUBAHAN DATA ALAT
    public function update_alat()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $alatModel = new \App\Models\AlatModel();

        $id = $this->request->getPost('id_alat');

        $data = [
            'id_alat' => $id,
            // Sesuaikan key kiri dengan nama kolom di database
            'nomor_inventaris' => $this->request->getPost('nomor_inventaris'),
            'nama_alat' => $this->request->getPost('nama_alat'),
        ];

        if ($alatModel->save($data)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Data berhasil diperbarui!']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal update database.']);
        }
    }

    // HAPUS ALAT
    public function hapus_alat($id)
    {
        // 1. Cek apakah ini request AJAX?
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        // 2. Panggil Model (Gunakan Model Alat, bukan Unit!)
        // Asumsi nama modelnya AlatModel. Sesuaikan jika beda.
        $alatModel = new \App\Models\AlatModel();

        // 3. Cek data
        $data = $alatModel->find($id);

        if ($data) {
            // 4. Hapus Data
            $alatModel->delete($id);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Data alat berhasil dihapus.'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data tidak ditemukan.'
            ]);
        }
    }

    public function data_master_alat()
    {
        $model = new \App\Models\AlatModel();

        // Ambil semua data
        $data['daftar_alat'] = $model->orderBy('id_alat', 'DESC')->findAll();

        return view('admin/data_master_alat', $data);
    }

    public function simpan_alat()
    {
        if (!$this->request->isAJAX())
            return $this->response->setStatusCode(404);

        $alatModel = new \App\Models\AlatModel();

        // Ambil data array dari form
        $nomorInv = $this->request->getPost('nomor_inventaris');
        $namaAlat = $this->request->getPost('nama_alat');

        if (!$nomorInv || !$namaAlat) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak boleh kosong.']);
        }

        // Loop untuk simpan multiple data
        $count = 0;
        foreach ($nomorInv as $key => $val) {
            if (!empty($val) && !empty($namaAlat[$key])) {
                $alatModel->insert([
                    'nomor_inventaris' => $val,
                    'nama_alat' => $namaAlat[$key]
                ]);
                $count++;
            }
        }

        if ($count > 0) {
            return $this->response->setJSON(['status' => 'success', 'message' => "$count data alat berhasil disimpan!"]);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menyimpan data.']);
        }
    }

    public function data_master_lokasi()
    {
        $lokasiModel = new \App\Models\LokasiModel();
        $db = \Config\Database::connect();

        // 1. Ambil Keyword Pencarian
        $keyword = $this->request->getGet('keyword');

        // 2. Data Unit (Untuk Dropdown) - Ambil Real dari Database
        $data['daftar_unit'] = $db->table('tb_master_unit')->orderBy('id_unit', 'ASC')->get()->getResultArray();

        // 3. Data Gedung (Untuk Dropdown) - Ambil Unik dari Data Lokasi
        $data['daftar_gedung'] = $lokasiModel->select('gedung, id_unit')
            ->distinct()
            ->orderBy('gedung', 'ASC')
            ->findAll();

        // 4. Data Kampus (Untuk Datalist)
        $data['daftar_kampus'] = $lokasiModel
            ->select('kampus')
            ->distinct()
            ->where('kampus IS NOT NULL')
            ->where('kampus !=', '')
            ->orderBy('kampus', 'ASC')
            ->findAll();

        // 4. Ambil Data Utama
        $dataQuery = $lokasiModel->getLokasiLengkap($keyword);

        // --- KUNCI PENGEMBALIAN PAGINATION JS ---
        // Gunakan findAll() untuk mengambil SEMUA data (misal 50 baris).
        // Jangan gunakan paginate(). Biarkan JavaScript di View yang memotong-motong halamannya.
        $data['daftar_lokasi'] = $dataQuery->findAll();

        // Pager tidak dibutuhkan untuk Client-Side Pagination, tapi boleh dibiarkan
        $data['pager'] = $lokasiModel->pager;
        $data['keyword'] = $keyword;

        return view('admin/data_master_lokasi', $data);
    }

    // AMBIL DATA LOKASI (GET)
    public function get_lokasi($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $lokasiModel = new \App\Models\LokasiModel();
        $data = $lokasiModel->find($id);

        return $this->response->setJSON($data);
    }

    // UPDATE DATA LOKASI (POST)
    public function update_lokasi()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $lokasiModel = new \App\Models\LokasiModel();

        $id = $this->request->getPost('id_lokasi');

        $data = [
            'id_lokasi' => $id,
            // id_unit tidak kita update karena readonly di form, 
            // kecuali Anda mengubah inputnya menjadi select box.
            'gedung' => $this->request->getPost('gedung'),
            'lantai' => $this->request->getPost('lantai'),
            'ruangan' => $this->request->getPost('ruangan'),
            'kampus' => $this->request->getPost('kampus'),
        ];

        if ($lokasiModel->save($data)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Data lokasi berhasil diperbarui!']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal update database.']);
        }
    }

    public function hapus_lokasi($id)
    {
        $model = new \App\Models\LokasiModel(); // Sesuaikan nama model

        // Cek data
        if (!$model->find($id)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data lokasi tidak ditemukan.'
            ]);
        }

        // Proses Hapus
        if ($model->delete($id)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Data lokasi berhasil dihapus.'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal menghapus data.'
            ]);
        }
    }
    public function data_master_unit()
    {
        $unitModel = new UnitModel();

        // Ambil data unit, urutkan berdasarkan nama
        $data['daftar_unit'] = $unitModel->orderBy('nama_unit', 'ASC')->findAll();

        return view('admin/data_master_unit', $data);
    }

    // --- FUNGSI AMBIL DATA (GET) ---
    // --- GET DATA UNIT (Dipakai saat tombol Edit diklik) ---
    public function get_unit($id)
    {
        // 1. Validasi AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        // 2. Instansiasi Model
        $unitModel = new UnitModel();

        // 3. Ambil data & langsung kembalikan sebagai JSON
        return $this->response->setJSON($unitModel->find($id));
    }

    // --- UPDATE DATA UNIT (Dipakai saat tombol Simpan diklik) ---
    public function update_unit()
    {
        // 1. Validasi Request AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        // 2. Instansiasi Model (Singkat saja karena sudah ada 'use' di atas)
        $unitModel = new UnitModel();

        // 3. Siapkan Data
        // Save() otomatis mendeteksi Update jika 'id' terisi
        $data = [
            'id_unit' => $this->request->getPost('id_unit'),
            'nama_unit' => $this->request->getPost('nama_unit'),
            'kategori' => $this->request->getPost('kategori'),
        ];

        // 4. Proses Simpan & Return Response
        if ($unitModel->save($data)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Data unit berhasil diperbarui!'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Gagal menyimpan perubahan.'
        ]);
    }
    // FUNGSI MENGHAPUS UNIT (DAN ALAT TERKAIT)
    public function hapus_unit($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        // Langsung pakai $this->unitModel (milik class global)
        $dataUnit = $this->unitModel->find($id);

        if ($dataUnit) {
            if ($this->unitModel->delete($id, true)) {
                return $this->response->setJSON(['status' => 'success', 'message' => 'Data berhasil dihapus.']);
            } else {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menghapus database.']);
            }
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak ditemukan.']);
        }
    }

    // --- SIMPAN DATA UNIT BARU ---
    public function simpan_unit()
    {
        // 1. Cek Request AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        // 2. Validasi Input
        if (
            !$this->validate([
                'nama_unit' => 'required',
                'kategori' => 'required'
            ])
        ) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data tidak lengkap. Mohon isi semua kolom bertanda *.'
            ]);
        }

        // 3. Panggil Model
        $unitModel = new \App\Models\UnitModel();

        // 4. Siapkan Data
        $data = [
            'nama_unit' => $this->request->getPost('nama_unit'),
            'kategori' => $this->request->getPost('kategori'),
        ];

        // 5. Simpan ke Database
        if ($unitModel->insert($data)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Data unit berhasil ditambahkan!'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal menyimpan ke database.'
            ]);
        }
    }
    private function getMasterLokasi()
    {
        return [
            'Manajemen Informatika' => [
                "Gedung MI, Lantai 2, Ruang Teori 1",
                "Gedung MI, Lantai 2, Ruang Teori 2",
                "Gedung MI, Lantai 3, Lab 1 (Basis Data)",
                "Gedung MI, Lantai 3, Lab 2 (Statistik)",
            ],
            'Bahasa dan Pariwisata' => [
                "Gedung Bahasa, Lantai 3, Ruang Teori 4",
                "Gedung Bahasa, Lantai 3, Ruang Teori 5",
            ],
            'Teknik Mesin' => [
                "Gedung Mesin, Lantai 1, Bengkel Produksi",
                "Gedung Mesin, Lantai 2, Ruang Dosen Mesin",
            ],
            'Gedung Graha Pendidikan' => [
                "Ruang Mesin Lift Roof Top Gedung Graha Pendidikan",
                "Lantai 1, Hall Utama"
            ]
        ];
    }

    private function getMapPelaksana()
    {
        return [
            // Grup M. Karison
            "Komputer/PC" => "M. Karison",
            "Laptop" => "M. Karison",
            "Printer" => "M. Karison",
            "Absensi" => "M. Karison",

            // Grup Riadi Putra
            "AIR CONDITIONER" => "Riadi Putra",
            "CCTV" => "Riadi Putra",
            "MCB" => "Riadi Putra",
            "LAMPU PENERANGAN" => "Riadi Putra",
            "ACCESS POINT" => "Riadi Putra",
            "STOP KONTAK" => "Riadi Putra",
            "LPJU" => "Riadi Putra",
            "KABEL FIBER OPTIC" => "Riadi Putra",
            "GENSET" => "Riadi Putra",

            // Grup Edial Salmes
            "TV/SMART TV" => "Edial Salmes",
            "POMPA AIR" => "Edial Salmes",
            "KULKAS" => "Edial Salmes",
            "DISPENSER" => "Edial Salmes",
            "VIDEOTRONE" => "Edial Salmes",
            "SOUNSYSTEM/PORTABLE" => "Edial Salmes",
            "TELPON" => "Edial Salmes",
            "KIPAS ANGIN" => "Edial Salmes",
            "IP POGGING" => "Edial Salmes",
            "POWERSUPPLY" => "Edial Salmes"
        ];
    }

    // 1. UPDATE FUNGSI MENAMPILKAN HALAMAN
    public function data_master_stok()
    {
        $stokModel = new \App\Models\StokModel();
        $alatModel = new \App\Models\AlatModel();

        // 1. Ambil data stok lengkap (tetap sama)
        $data['daftar_stok'] = $stokModel->getStokLengkap();

        // 2. [FILTER KHUSUS] Ambil daftar alat HANYA yang Tool & Sparepart
        // Pastikan nama kolom 'kategori' sesuai dengan yang ada di tb_alat Anda
        $data['pilihan_alat'] = $alatModel->groupStart()
            ->where('kategori', 'TOOL')       // Syarat 1
            ->orWhere('kategori', 'BHP') // Syarat 2
            ->groupEnd()
            ->orderBy('nama_alat', 'ASC') // Biar urut abjad enak carinya
            ->findAll();

        return view('admin/data_master_stok', $data);
    }

    // 2. UPDATE FUNGSI SIMPAN (SESUAI STRUKTUR DB ANDA)
    public function simpan_stok()
    {
        if (!$this->request->isAJAX())
            return $this->response->setStatusCode(404);

        $kode_array = $this->request->getPost('kode_alat');
        $nama_array = $this->request->getPost('nama_alat');
        $jumlah_array = $this->request->getPost('jumlah');

        if (empty($nama_array)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Tidak ada data.']);
        }

        $stokModel = new \App\Models\StokModel();
        $berhasil = 0;

        foreach ($nama_array as $index => $nama_barang) {
            if (!empty($nama_barang) && isset($jumlah_array[$index])) {
                $stokModel->insert([
                    'nomor_inventaris' => $kode_array[$index] ?? '-',
                    'nama_barang' => $nama_barang,
                    'jumlah' => $jumlah_array[$index]
                ]);
                $berhasil++;
            }
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => $berhasil . ' Data stok berhasil disimpan!'
        ]);
    }

    // FUNGSI MENGHAPUS STOK (DAN ALAT TERKAIT)
    public function hapus_stok($id)
    {
        // 1. Cek Request AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        // 2. Panggil Model STOK (Bukan Lokasi!)
        // Asumsi nama model Anda StokModel. Sesuaikan jika beda.
        $stokModel = new \App\Models\StokModel();

        // 3. Cek data sebelum hapus
        $dataStok = $stokModel->find($id);

        if ($dataStok) {
            // 4. Hapus Data
            // Parameter true = Hard Delete (Hapus Permanen)
            if ($stokModel->delete($id, true)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Data stok berhasil dihapus.'
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal menghapus data dari database.'
                ]);
            }
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data stok tidak ditemukan.'
            ]);
        }
    }

    public function tambah_lokasi()
    {
        // 1. Cek Request AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        // 2. Validasi Input
        $validation = \Config\Services::validation();
        $rules = [
            'id_unit' => 'required', // <--- PENTING: Wajib ada validasi ini
            'gedung' => 'required',
            'lantai' => 'required', // Hapus '|numeric' jika lantai pakai teks (misal "Lantai 1")
            'kampus' => 'required',
            'ruangan' => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data tidak lengkap. Harap pilih Unit/Jurusan.'
            ]);
        }

        // 3. Siapkan Data
        $lokasiModel = new \App\Models\LokasiModel();

        // Normalisasi input
        $kampus = preg_replace('/\s+/', ' ', trim($this->request->getPost('kampus')));
        $kampus = ucwords(strtolower($kampus));

        $gedung = preg_replace('/\s+/', ' ', trim($this->request->getPost('gedung')));

        $ruangan = preg_replace('/\s+/', ' ', trim($this->request->getPost('ruangan')));
        $ruangan = ucwords(strtolower($ruangan));

        $data = [
            'id_unit' => $this->request->getPost('id_unit'),
            'gedung' => $gedung,
            'lantai' => trim($this->request->getPost('lantai')),
            'kampus' => $kampus,
            'ruangan' => $ruangan,
        ];

        // 4. Simpan ke Database
        if ($lokasiModel->insert($data)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Data lokasi berhasil ditambahkan!'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal menyimpan data ke database.'
            ]);
        }
    }

    // FUNGSI AMBIL DATA STOK SPESIFIK (UNTUK EDIT)
    public function get_stok($id)
    {
        if (!$this->request->isAJAX())
            return $this->response->setStatusCode(404);

        $stokModel = new \App\Models\StokModel();
        // Tanpa JOIN! Langsung ambil dari tabel tb_master_stok
        $data = $stokModel->find($id);

        if ($data)
            return $this->response->setJSON($data);
        return $this->response->setStatusCode(404, 'Data tidak ditemukan');
    }

    // FUNGSI UPDATE STOK (AKSI TOMBOL SIMPAN DI MODAL EDIT)
    public function update_stok()
    {
        if (!$this->request->isAJAX())
            return $this->response->setStatusCode(404);

        $stokModel = new \App\Models\StokModel();

        // Tangkap semua data dari form yang sekarang sudah bisa diedit
        $data = [
            'id_stok' => $this->request->getPost('id_stok'),
            'nomor_inventaris' => $this->request->getPost('nomor_inventaris'), // Ambil Kode
            'nama_barang' => $this->request->getPost('nama_barang'), // Ambil Nama
            'jumlah' => $this->request->getPost('jumlah')       // Ambil Jumlah
        ];

        if ($stokModel->save($data)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Stok berhasil diperbarui!']);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal memperbarui database.']);
    }
    public function laporan_kerusakan()
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
            'laporan_list' => [],
            'filter_range' => $dateRange,
            'filter_status' => $status,
            'filter_unit' => $unit,

            // Variabel Data Master untuk View & JS
            'list_unit' => $list_unit,
            'daftar_alat' => $daftarAlat,
            'daftar_lokasi' => $daftar_lokasi,
            'map_pelaksana' => $mapPelaksana
        ];

        return view('admin/laporan_kerusakan', $data);
    }

    /**
     * FUNGSI INI MENERIMA PERMINTAAN VALIDASI (AJAX)
     */
    public function validasi()
    {
        $this->response->setHeader('Content-Type', 'application/json');

        $nomorLaporan = $this->request->getPost('nomor_laporan');
        if (!$nomorLaporan) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ID Laporan tidak ada.']);
        }

        $db = \Config\Database::connect();
        $db->transException(true)->transStart();

        try {
            // --- 1. Update field validasi_kepala di database ---
            $laporan = $db->table('tb_laporan')->where('nomor_laporan', $nomorLaporan)->get()->getRowArray();
            if (!$laporan) {
                throw new \Exception('Laporan tidak ditemukan.');
            }
            // Pastikan pelapor sudah memberikan rating dan ulasan
            $validasiPelapor = $db->table('tb_validasi')
                ->where('id_laporan', $laporan['id_laporan'])
                ->where('jenis_validasi', 'PELAPOR')
                ->get()
                ->getRowArray();

            if (
                !$validasiPelapor ||
                empty($validasiPelapor['rating']) ||
                empty(trim($validasiPelapor['ulasan'] ?? ''))
            ) {
                throw new \Exception(
                    'Pelapor belum memberikan rating dan ulasan.'
                );
            }
            $updated = $db->table('tb_laporan')
                ->where('nomor_laporan', $nomorLaporan)
                ->update([
                    'validasi_kepala' => 'Disetujui',
                    'status_laporan' => 'SELESAI',
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

            if ($updated === false) {
                $err = $db->error();
                throw new \Exception('Gagal update database: ' . ($err['message'] ?? ''));
            }

            // =====================================================
            // CEK APAKAH LAPORAN RUSAK TOTAL
            // =====================================================

            $dataRusak = $db->table('tb_jadwal_perbaikan jp')
                ->select('
                   l.id_laporan,
                    l.nomor_laporan,
                    l.nomor_inventaris,
                    l.nama_alat,
                    l.lokasi,
                    l.unit,
                    l.tanggal_laporan,
                    p.catatan_teknisi,
                    p.hasil_perbaikan,
                    p.status_kerusakan
                ')
                ->join('tb_laporan l', 'l.id_laporan = jp.id_laporan')
                ->join('tb_perbaikan p', 'p.id_jadwal = jp.id_jadwal')
                ->where('l.nomor_laporan', $nomorLaporan)
                ->groupStart()
                ->where('p.hasil_perbaikan', 'RUSAK TOTAL')
                ->orWhere('jp.status_perbaikan', 'RUSAK')
                ->groupEnd()
                ->get()
                ->getRowArray();

            if ($dataRusak) {

                $cek = $db->table('tb_barang_rusak')
                    ->where('id_laporan', $dataRusak['id_laporan'])
                    ->countAllResults();

                if ($cek == 0) {

                    $db->table('tb_barang_rusak')->insert([

                        'id_laporan' => $dataRusak['id_laporan'],
                        'nomor_laporan' => $dataRusak['nomor_laporan'],
                        'nomor_inventaris' =>
                            !empty(trim($dataRusak['nomor_inventaris'] ?? ''))
                            ? $dataRusak['nomor_inventaris']
                            : '-',
                        'nama_alat' => $dataRusak['nama_alat'],
                        'lokasi' => $dataRusak['lokasi'],
                        'unit' => $dataRusak['unit'],
                        'alasan_rusak' =>
                            !empty(trim($dataRusak['catatan_teknisi'] ?? ''))
                            ? $dataRusak['catatan_teknisi']
                            : 'Tidak Ekonomis Lagi Untuk Diperbaiki',
                        'tanggal_rusak' => $dataRusak['tanggal_laporan']

                    ]);

                }

            }
            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaksi database gagal.');
            }

            return $this->response->setJSON(['status' => 'success', 'message' => 'Laporan berhasil divalidasi.']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function updateJadwalPerbaikan()
    {
        $this->response->setHeader('Content-Type', 'application/json');

        // Mengambil data dari form
        $nomorLaporan = $this->request->getPost('nomor_laporan');
        // KEY YANG DITERIMA DARI FORM HARUSNYA 'tanggal_perbaikan'
        $newDate = $this->request->getPost('tanggal_perbaikan');

        // Cek jika ada input yang kosong
        if (!$nomorLaporan || !$newDate) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data Nomor Laporan atau Tanggal Perbaikan tidak lengkap.']);
        }

        try {
            // --- Path ke file-file Anda ---
            $fileLaporan = WRITEPATH . 'data/laporan.json';
            $fileRiwayat = WRITEPATH . 'data/riwayat_pelapor.json';
            $fileJadwal = WRITEPATH . 'data/jadwal_teknisi.json';

            // Menggunakan pemanggilan protected method:
            $laporanList = $this->readJson($fileLaporan);
            $riwayatList = $this->readJson($fileRiwayat);
            $jadwalList = $this->readJson($fileJadwal);

            $updatedFlag = false;

            // Fungsi helper untuk mencari & update array
            $updateList = function (&$list, $id, $date) use (&$updatedFlag) {
                if (is_array($list)) {
                    foreach ($list as $index => $item) {
                        if (isset($item['nomor_laporan']) && $item['nomor_laporan'] === $id) {
                            // KEY YANG DISIMPAN ADALAH 'tanggal_perbaikan'
                            $list[$index]['tanggal_perbaikan'] = $date;
                            $updatedFlag = true;
                        }
                    }
                }
            };

            // Update ketiga file di memori dengan key 'tanggal_perbaikan'
            $updateList($laporanList, $nomorLaporan, $newDate);
            $updateList($riwayatList, $nomorLaporan, $newDate);
            $updateList($jadwalList, $nomorLaporan, $newDate);

            if (!$updatedFlag) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Laporan tidak ditemukan di file mana pun.']);
            }

            // Simpan ketiga file ke disk
            file_put_contents($fileLaporan, json_encode($laporanList, JSON_PRETTY_PRINT));
            file_put_contents($fileRiwayat, json_encode($riwayatList, JSON_PRETTY_PRINT));
            file_put_contents($fileJadwal, json_encode($jadwalList, JSON_PRETTY_PRINT));


            return $this->response->setJSON(['status' => 'success', 'message' => 'Jadwal Perbaikan berhasil diperbarui.']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }


    // =======================================================
    // === FUNGSI AJAX BARU UNTUK FITUR EDIT LAPORAN ===
    // =======================================================

    /**
     * [BARU] Menangani permintaan AJAX GET: admin/get_data_by_nomor/{nomorLaporan}
     * Mengambil data laporan spesifik dan mengembalikan dalam format JSON.
     *
     * @param string $nomorLaporan Nomor unik dari laporan yang akan diedit.
     */

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
        $statusKerusakanRaw = $this->request->getPost('status_kerusakan') ?? 'Belum Dicek'; // Default jika kosong

        // Normalize status_kerusakan ke format database (uppercase untuk ENUM)
        $statusKerusakan = null; // Default null agar tidak error ENUM
        if ($statusKerusakanRaw && $statusKerusakanRaw !== 'Belum Dicek') {
            $statusKerusakan = strtoupper($statusKerusakanRaw); // 'Ringan' -> 'RINGAN'
        }

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

        // Cari Penanggung Jawab DULU menggunakan master alat
        $nomorInventaris = trim((string) $this->request->getPost('nomor_inventaris'));
        $teknisiQuery = $db->table('tb_master_alat')->select('id_teknisi');

        if (!empty($namaAlat)) {
            $teknisiQuery->where('nama_alat', $namaAlat);
        }

        $teknisiPJ = $teknisiQuery->get()->getRowArray();
        $idTargetTeknisi = $teknisiPJ['id_teknisi'] ?? null;

        // Fallback: cari berdasarkan nomor inventaris jika nama alat tidak cocok
        if (!$idTargetTeknisi && !empty($nomorInventaris)) {
            $fallbackTeknisi = $db->table('tb_master_alat')
                ->select('id_teknisi')
                ->where('nomor_inventaris', $nomorInventaris)
                ->get()->getRowArray();

            $idTargetTeknisi = $fallbackTeknisi['id_teknisi'] ?? null;
        }

        // Jika masih belum ketemu, coba pencarian lebih longgar berdasarkan nama alat
        if (!$idTargetTeknisi && !empty($namaAlat)) {
            $fallbackTeknisi = $db->table('tb_master_alat')
                ->select('id_teknisi')
                ->like('nama_alat', $namaAlat)
                ->get()->getRowArray();

            $idTargetTeknisi = $fallbackTeknisi['id_teknisi'] ?? null;
        }

        // Tentukan Status - Laporan baru masuk tab BARU, belum DIPROSES
        $statusJadwal = 'MENUNGGU';
        $statusLaporan = 'BARU';
        $tglPerbaikan = NULL;
        $waktuSelesai = NULL;
        $catatanTeknisi = NULL;
        $pesanResponse = 'Laporan disimpan. Silakan lihat di tab Laporan Baru.';

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

                    // Validasi id_jadwal berhasil di-generate
                    if (!$idJadwalBaru || $idJadwalBaru <= 0) {
                        throw new \Exception('Gagal generate ID Jadwal Perbaikan');
                    }

                    // Simpan status_kerusakan ke tb_perbaikan saat laporan manual dibuat
                    $dataPerbaikan = [
                        'id_jadwal' => $idJadwalBaru,
                        'status_kerusakan' => $statusKerusakan, // Bisa null, itu okay
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
            // ===========================================
            // KIRIM EMAIL NOTIFIKASI
            // ===========================================

            $emailService = new \App\Libraries\EmailService();

            // ------------------------
            // Ambil data laporan
            // ------------------------
            $laporan = $db->table('tb_laporan')
                ->where('id_laporan', $idLaporanBaru)
                ->get()
                ->getRowArray();

            // ------------------------
            // Ambil data pelapor
            // ------------------------
            $pelapor = null;

            if (!empty($laporan['id_pelapor'])) {

                $pelapor = $db->table('tb_user')
                    ->where('id_user', $laporan['id_pelapor'])
                    ->get()
                    ->getRowArray();

            }

            // ------------------------
            // Ambil admin UPA
            // ------------------------
            $admins = $db->table('tb_user')
                ->where('akses', 'admin')
                ->get()
                ->getResultArray();

            // ------------------------
            // Ambil teknisi
            // ------------------------
            $teknisi = null;

            if (!empty($idTargetTeknisi)) {

                $teknisi = $db->table('tb_user')
                    ->where('id_user', $idTargetTeknisi)
                    ->get()
                    ->getRowArray();

            }

            dd([
                'pelapor' => $pelapor,
                'admin' => $admins,
                'teknisi' => $teknisi,
                'idTargetTeknisi' => $idTargetTeknisi
            ]);

            // ------------------------
            // Data email
            // ------------------------
            $emailData = [

                'judul' => 'Laporan Baru',

                'pesan' => 'Laporan kerusakan baru telah dibuat.',

                'warna' => '#0d6efd',

                'status' => 'BARU',

                'nama_pelapor' => $laporan['nama_pelapor'],

                'nomor_laporan' => $laporan['nomor_laporan'],

                'tanggal' => date('d F Y H:i:s'),

                'nama_alat' => $laporan['nama_alat'],

                'lokasi' => $laporan['lokasi'],

                'keluhan' => $laporan['kerusakan']

            ];

            // ------------------------
            // Kirim ke pelapor
            // ------------------------
            if (!empty($pelapor['email'])) {

                $emailService->kirimLaporanBaru(
                    $pelapor['email'],
                    $emailData
                );

            }

            // ------------------------
            // Kirim ke admin
            // ------------------------
            foreach ($admins as $admin) {

                if (!empty($admin['email'])) {

                    $emailService->kirimLaporanBaru(
                        $admin['email'],
                        $emailData
                    );

                }

            }

            // ------------------------
            // Kirim ke teknisi
            // ------------------------
            if (!empty($teknisi['email'])) {

                $hasil = $emailService->kirimLaporanBaru(
                    $teknisi['email'],
                    $emailData
                );

                if (!$hasil) {
                    dd($emailService->getError());
                }
            }
            return $this->response->setJSON([
                'status' => 'success',
                'message' => $pesanResponse,
                'nomor_laporan' => $nomorBaru
            ]);
        } else {
            // Jika error, berikan pesan yang lebih detail untuk debugging
            $errorMsg = 'Terjadi kesalahan saat menyimpan laporan';
            if (!empty($lastError)) {
                // Batasi panjang error message untuk keamanan
                $errorMsg = substr($lastError, 0, 200);
            }
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $errorMsg,
                'debug' => $lastError // Untuk development saja
            ]);
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

    public function hapus_peminjaman()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $id = $this->request->getPost('id_peminjaman');

        if (!$id) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'ID tidak ditemukan.'
            ]);
        }

        $peminjamanModel = new \App\Models\PeminjamanModel();
        $data = $peminjamanModel->find($id);

        if (!$data) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data peminjaman tidak ditemukan.'
            ]);
        }

        // Hapus file lampiran jika ada
        if (!empty($data['lampiran'])) {
            $filePath = FCPATH . 'uploads/peminjaman/' . $data['lampiran'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        if ($peminjamanModel->delete($id)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Data peminjaman berhasil dihapus.'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal menghapus data dari database.'
            ]);
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

    public function user()
    {
        // Anda bisa pakai $this->userModel karena sudah didefinisikan di __construct
        // atau pakai new UserModel() juga tidak masalah.

        // UBAH 'ASC' MENJADI 'DESC' DISINI:
        //$data['daftar_user'] = $this->userModel->orderBy('id', 'DESC')->findAll();

        // Masih ASC (Ascending/Kecil ke Besar) -> User baru ada di bawah
        $data['daftar_user'] = $this->userModel->orderBy('id_user', 'ASC')->findAll();

        // 1. Ambil Data Utama (Daftar User untuk Tabel)
        $data['daftar_user'] = $this->userModel->orderBy('id_user', 'ASC')->findAll();

        // 2. [TAMBAHAN WAJIB] Ambil Data untuk Widget Kontak Administrasi
        // Agar variabel $teknisi dan $admin_upt dikenali di View user.php
        $data['teknisi'] = $this->userModel->where('akses', 'teknisi')->findAll();

        // Sesuaikan 'role' dengan database Anda (misal: 'admin' atau 'kepala')
        $data['admin_upt'] = $this->userModel->where('akses', 'kepala')->first();

        // 3. Kirim semua data ke View
        return view('admin/user', $data);
    }

    public function simpan_user()
    {
        // 1. Cek Request AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        // 2. Ambil Data
        $id = $this->request->getPost('id_user');
        $password = $this->request->getPost('password');

        // 3. Aturan Validasi Dasar
        $rules = [
            'nama' => [
                'rules' => 'required',
                'errors' => ['required' => 'Nama User wajib diisi.']
            ],
            'jabatan' => [
                'rules' => 'required',
                'errors' => ['required' => 'Jabatan wajib diisi.']
            ],
            'akses' => [
                'rules' => 'required',
                'errors' => ['required' => 'Hak Akses wajib dipilih.']
            ],
            'username' => [
                'rules' => 'required|is_unique[tb_user.username,id_user,' . $id . ']',
                'errors' => [
                    'required' => 'Username wajib diisi.',
                    'is_unique' => 'Username ini sudah terpakai.'
                ]
            ],
            'email' => [
                'rules' => 'required|valid_email|is_unique[tb_user.email,id_user,' . $id . ']',
                'errors' => [
                    'required' => 'Email wajib diisi.',
                    'valid_email' => 'Format email tidak valid.',
                    'is_unique' => 'Email sudah digunakan.'
                ]
            ],
        ];

        // 4. Logika Validasi Password (Min 4, Max 20)

        // KONDISI A: Tambah User Baru (ID Kosong) -> Password WAJIB
        if (empty($id)) {
            $rules['password'] = [
                'rules' => 'required|min_length[4]|max_length[20]',
                'errors' => [
                    'required' => 'Password wajib diisi untuk user baru.',
                    'min_length' => 'Password terlalu pendek (Minimal 4 karakter).',
                    'max_length' => 'Password terlalu panjang (Maksimal 12 karakter).'
                ]
            ];
        }
        // KONDISI B: Edit User (ID Ada) -> Password OPSIONAL
        else {
            // Hanya validasi jika kolom password diisi
            if (!empty($password)) {
                $rules['password'] = [
                    'rules' => 'min_length[4]|max_length[12]',
                    'errors' => [
                        'min_length' => 'Password terlalu pendek (Minimal 4 karakter).',
                        'max_length' => 'Password terlalu panjang (Maksimal 12 karakter).'
                    ]
                ];
            }
        }

        // 5. Jalankan Validasi
        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors(),
                'message' => 'Periksa kembali inputan Anda.'
            ]);
        }

        // 6. Siapkan Data Simpan
        $userModel = new \App\Models\UserModel();

        $data = [
            'id_user' => $id,
            'nama' => $this->request->getPost('nama'),
            'jabatan' => $this->request->getPost('jabatan'),
            'akses' => $this->request->getPost('akses'),
            'username' => $this->request->getPost('username'),
            'email' => trim($this->request->getPost('email'))
        ];

        // Hash Password jika diisi
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        // 7. Simpan ke Database
        if ($userModel->save($data)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Data user berhasil disimpan!'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal menyimpan data ke database.'
            ]);
        }
    }

    // FUNGSI MENGHAPUS USER (AJAX)
    // --- HAPUS USER ---
    public function hapus_user($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($id);

        if ($user) {
            $userModel->delete($id);
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Data user berhasil dihapus.'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'User tidak ditemukan.'
            ]);
        }
    }

    // FUNGSI AMBIL DATA USER (UNTUK EDIT)
    public function get_user($id)
    {
        // Pastikan hanya bisa diakses via AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        // Cari data di database
        $data = $this->userModel->find($id);

        if ($data) {
            return $this->response->setJSON($data);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak ditemukan']);
        }
    }

    // Di file Admin.php

    // 1. API untuk Ambil Lokasi berdasarkan Unit
    public function get_lokasi_by_unit($unit_id)
    {
        $lokasiModel = new \App\Models\LokasiModel();
        // Asumsi tabel lokasi punya kolom 'id_unit'
        $data = $lokasiModel->where('id_unit', $unit_id)->findAll();
        return $this->response->setJSON($data);
    }

    // 2. API untuk Cek Nomor Terakhir
    public function get_last_nomor_peminjaman()
    {
        $db = \Config\Database::connect();
        // Ambil nomor laporan terakhir dari tb_laporan hari ini
        $lastData = $db->table('tb_laporan')
            ->orderBy('id_laporan', 'DESC')
            ->get()->getRowArray();

        return $this->response->setJSON([
            'last_nomor' => $lastData ? $lastData['nomor_laporan'] : null
        ]);
    }

    public function laporan_peminjaman()
    {
        $unitModel = new \App\Models\UnitModel();
        $lokasiModel = new \App\Models\LokasiModel();
        $peminjamanModel = new \App\Models\PeminjamanModel();
        $db = \Config\Database::connect();

        // Ambil data peminjaman + JOIN ke tabel unit dan lokasi
        $daftar_peminjaman = $db->table('tb_peminjaman p')
            ->select('p.*, u.nama_unit, l.gedung, l.ruangan')
            ->join('tb_master_unit u', 'u.id_unit = p.id_unit', 'left')
            ->join('tb_master_lokasi l', 'l.id_lokasi = p.lokasi', 'left')
            ->orderBy('p.id_peminjaman', 'DESC')
            ->get()
            ->getResultArray();

        $data = [
            'title' => 'Laporan Peminjaman',
            // provide both keys to keep compatibility with other parts
            'daftar_unit' => $unitModel->findAll(),
            'list_unit' => $unitModel->findAll(),
            'filter_unit' => '',
            'daftar_lokasi' => $lokasiModel->findAll(),
            'daftar_peminjaman' => $daftar_peminjaman,
        ];

        return view('admin/laporan_peminjaman', $data);
    }

    public function simpan_peminjaman()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        // 1. Aturan Validasi
        $rules = [
            'nomor' => 'required',
            'unit' => 'required',
            'kegiatan' => 'required',
            'lokasi' => 'required',
            'mulai' => 'required',
            'selesai' => 'required',
            'peminjam' => 'required',
            'handphone' => 'required',
            // Validasi File (Opsional, max 500KB, gambar)
            'lampiran' => [
                'rules' => 'is_image[lampiran]|mime_in[lampiran,image/jpg,image/jpeg,image/png]|max_size[lampiran,512]',
                'errors' => [
                    'is_image' => 'File harus berupa gambar.',
                    'mime_in' => 'Format gambar tidak valid.',
                    'max_size' => 'Ukuran gambar terlalu besar (Maks 500KB).'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors(),
                'message' => 'Periksa inputan Anda.'
            ]);
        }

        // 2. Handle Upload File (tunggu diproses setelah cek apakah ini update)
        $fileLampiran = $this->request->getFile('lampiran');
        $namaFile = null;

        // 3. Siapkan Data umum
        $peminjamanModel = new \App\Models\PeminjamanModel();

        $postId = $this->request->getPost('id_peminjaman');

        // Ambil data dasar dari form
        $data = [
            'nomor' => $this->request->getPost('nomor'),
            'id_unit' => $this->request->getPost('unit'),
            'kegiatan' => $this->request->getPost('kegiatan'),
            'lokasi' => $this->request->getPost('lokasi'), // Ini string ID lokasi
            'tanggal_mulai' => $this->request->getPost('mulai'),
            'tanggal_selesai' => $this->request->getPost('selesai'),
            'keterangan' => $this->request->getPost('keterangan'),
            'identitas' => $this->request->getPost('identitas'),
            'peminjam' => $this->request->getPost('peminjam'),
            'handphone' => $this->request->getPost('handphone'),
        ];

        // Jika ada ID, ini adalah update: ambil record lama untuk menjaga lampiran
        if (!empty($postId)) {
            $existing = $peminjamanModel->find($postId);
        } else {
            $existing = null;
        }

        // Proses upload file jika ada file baru
        if ($fileLampiran && $fileLampiran->isValid() && !$fileLampiran->hasMoved()) {
            $namaFile = $fileLampiran->getRandomName();
            $fileLampiran->move('uploads/peminjaman', $namaFile);

            // Jika ini update dan ada file lama, hapus file lama
            if (!empty($existing) && !empty($existing['lampiran'])) {
                $oldPath = FCPATH . 'uploads/peminjaman/' . $existing['lampiran'];
                if (is_file($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $data['lampiran'] = $namaFile;
        } else {
            // Tidak ada file baru: jika update, pertahankan nama file lama
            if (!empty($existing) && isset($existing['lampiran'])) {
                $data['lampiran'] = $existing['lampiran'];
            } else {
                $data['lampiran'] = null;
            }
        }

        // 4. Simpan atau Update ke Database
        if (!empty($postId)) {
            // Update
            if ($peminjamanModel->update($postId, $data)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Data peminjaman berhasil diperbarui!'
                ]);
            }
        } else {
            // Insert baru
            if ($peminjamanModel->insert($data)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Data peminjaman berhasil disimpan!'
                ]);
            }
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Gagal menyimpan ke database.'
        ]);
    }

    // API: Ambil data peminjaman per ID (untuk keperluan edit)
    public function get_peminjaman($id = null)
    {
        if ($id === null)
            return $this->response->setStatusCode(400);

        $peminjamanModel = new \App\Models\PeminjamanModel();
        $data = $peminjamanModel->find($id);

        if (!$data) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak ditemukan']);
        }

        return $this->response->setJSON($data);
    }

    public function antrian_perbaikan()
    {
        return view('admin/antrian_perbaikan');
    }

    // File: app/Controllers/Admin.php

    // 1. Fungsi Update Status (Agar Toggle Admin berfungsi)
    public function update_status_online()
    {
        // Ambil ID user secara konsisten
        $idUser = session()->get('id_user') ?? session()->get('id_users');

        if (!$idUser) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Sesi habis'
            ]);
        }

        $status = $this->request->getPost('is_online');
        if ($status === null) {
            $status = $this->request->getPost('status');
        }
        $status = (int) $status;

        $userModel = new \App\Models\UserModel();


        // Update is_online dan last_active ketika tersedia. Safe-update dengan try/catch.
        $now = date('Y-m-d H:i:s');
        try {
            $userModel->update($idUser, ['is_online' => $status, 'last_active' => $now]);
        } catch (\Throwable $e) {
            // Fallback: update hanya is_online
            if (!$userModel->update($idUser, ['is_online' => $status])) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal update database'
                ]);
            }
        }

        // Sinkron session
        session()->set('is_online', $status);
        session()->set('last_active', $now);

        return $this->response->setJSON(['status' => 'success']);
    }

    // 2. Update Fungsi data_teknisi (Agar data Admin UPT juga terkirim ke view)
    public function data_teknisi()
    {
        $userModel = new \App\Models\UserModel();

        // Ambil data teknisi (sudah benar)
        $data['teknisi'] = $userModel->where('role', 'teknisi')->findAll();

        // [TAMBAHAN PENTING] Ambil juga data Admin UPT/Kepala agar bisa ditampilkan statusnya
        // Sesuaikan 'role' dengan database Anda (misal: 'admin' atau 'kepala')
        $data['admin_upt'] = $userModel->where('role', 'kepala')->first();

        return view('admin/data_teknisi', $data); // Pastikan ini memuat view user.php Anda
    }

    // API: Ambil status teknisi (JSON) untuk keperluan refresh realtime di halaman Admin
    public function get_teknisi_json()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $userModel = new \App\Models\UserModel();

        // Ambil list teknisi
        $teknisi = $userModel->where('akses', 'teknisi')->findAll();

        // Pilih admin yang sedang login jika dia admin/kepala, agar toggle dari dashboard admin langsung tercermin
        $currentUserId = session()->get('id_user');
        $admin_upt = null;

        if ($currentUserId) {
            $currentUser = $userModel->find($currentUserId);
            if ($currentUser && in_array($currentUser['akses'], ['admin', 'kepala'])) {
                $admin_upt = $currentUser;
            }
        }

        if (!$admin_upt) {
            $admin_upt = $userModel->whereIn('akses', ['kepala', 'admin'])->orderBy('id_user', 'ASC')->first();
        }

        return $this->response->setJSON([
            'status' => 'success',
            'teknisi' => $teknisi,
            'admin_upt' => $admin_upt
        ]);
    }

    // --- TAMBAHKAN FUNGSI INI DI Admin.php ---
    public function tugaskan_teknisi()
    {
        $this->response->setHeader('Content-Type', 'application/json');

        // 1. Ambil Data dari AJAX
        $nomorLaporan = $this->request->getPost('nomor_laporan');
        $namaTeknisi = $this->request->getPost('nama_teknisi'); // Kita terima Nama (karena select option value-nya Nama)
        $tanggal = $this->request->getPost('tanggal_perbaikan');

        if (!$nomorLaporan || !$namaTeknisi || !$tanggal) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak lengkap.']);
        }

        $db = \Config\Database::connect();

        try {
            // 2. Cari ID User Teknisi berdasarkan Nama
            // (Karena value di <select> HTML Anda menggunakan Nama, bukan ID)
            $user = $db->table('tb_user')->where('nama', $namaTeknisi)->get()->getRowArray();

            if (!$user) {
                // Opsional: Jika tidak ketemu by nama, coba anggap inputnya adalah ID (fallback)
                $user = $db->table('tb_user')->where('id_user', $namaTeknisi)->get()->getRowArray();
            }

            if (!$user) {
                throw new \Exception("Teknisi dengan nama '$namaTeknisi' tidak ditemukan di database.");
            }
            $idTeknisi = $user['id_user'];

            // 3. Cari ID Laporan berdasarkan Nomor Laporan
            $laporan = $db->table('tb_laporan')->where('nomor_laporan', $nomorLaporan)->get()->getRowArray();
            if (!$laporan) {
                throw new \Exception("Laporan tidak ditemukan.");
            }
            $idLaporan = $laporan['id_laporan'];

            // 4. Simpan/Update ke tb_jadwal_perbaikan
            $jadwalExist = $db->table('tb_jadwal_perbaikan')->where('id_laporan', $idLaporan)->countAllResults();

            $dataJadwal = [
                'id_laporan' => $idLaporan,
                'id_teknisi' => $idTeknisi,
                'tanggal_perbaikan' => $tanggal,
                'status_perbaikan' => 'MENUNGGU', // Status awal penugasan
                'waktu_dijadwalkan' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s')
            ];

            if ($jadwalExist > 0) {
                // Update jika sudah ada
                $db->table('tb_jadwal_perbaikan')->where('id_laporan', $idLaporan)->update($dataJadwal);
            } else {
                // Insert baru
                $db->table('tb_jadwal_perbaikan')->insert($dataJadwal);
            }

            // 5. Update Status Laporan Utama menjadi 'DIJADWALKAN'
            $db->table('tb_laporan')->where('id_laporan', $idLaporan)->update([
                'status_laporan' => 'DIJADWALKAN'
            ]);

            // ===========================================
            // EMAIL KE TEKNISI
            // ===========================================

            if (!empty($user['email'])) {

                $emailService = new \App\Libraries\EmailService();

                $emailService->kirimPenugasanTeknisi(
                    $user['email'],
                    [
                        'judul' => 'Penugasan Perbaikan Baru',
                        'pesan' => 'Anda mendapatkan penugasan baru untuk melakukan perbaikan fasilitas.',
                        'warna' => '#0d6efd',
                        'status' => 'DIJADWALKAN',

                        'nama_pelapor' => $user['nama'],
                        'nomor_laporan' => $laporan['nomor_laporan'],
                        'tanggal' => date('d F Y H:i:s'),
                        'nama_alat' => $laporan['nama_alat'],
                        'lokasi' => $laporan['lokasi'],

                        'pelapor' => $laporan['nama_pelapor'],
                        'keluhan' => $laporan['kerusakan'],
                        'tanggal_perbaikan' => $tanggal
                    ]
                );

            }

            return $this->response->setJSON(['status' => 'success', 'message' => 'Teknisi berhasil ditugaskan!']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function get_dashboard_admin()
    {
        $db = \Config\Database::connect();

        // KUNCI PERBAIKAN: 
        // 1. Kita buang filter 'id_teknisi' agar semua laporan kampus terbaca.
        // 2. Kita gunakan 'tb_laporan' sebagai tabel utama agar laporan baru juga terhitung.
        $data = $db->table('tb_laporan l')
            ->select('l.unit, l.nama_alat, p.status_kerusakan, l.status_laporan, jp.status_perbaikan')
            ->join('tb_jadwal_perbaikan jp', 'jp.id_laporan = l.id_laporan', 'left')
            ->join('tb_perbaikan p', 'p.id_jadwal = jp.id_jadwal', 'left')
            ->get()->getResultArray();

        // Siapkan Wadah Grafik
        $statsJurusan = [];
        $statsAlat = [];
        $statsSeverity = ['Ringan' => 0, 'Sedang' => 0, 'Berat' => 0];
        $statsStatus = ['Selesai' => 0, 'Proses' => 0, 'Menunggu' => 0, 'Dijadwalkan' => 0, 'Batal' => 0];

        $totalLaporanKerusakan = count($data);
        $totalSelesaiDiperbaiki = 0;
        $totalBelumDiperbaiki = 0;
        $totalBarangRusak = 0;
        $totalPeminjaman = 0;

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
                $totalBarangRusak++;
            }

            // Logika Status Pekerjaan
            // Kita prioritaskan status dari tb_laporan, jika belum ditugaskan statusnya 'BARU'
            $statLaporan = strtoupper($row['status_laporan'] ?? '');
            $statJadwal = strtoupper($row['status_perbaikan'] ?? '');

            if ($statLaporan == 'SELESAI' || $statJadwal == 'SELESAI') {
                $statsStatus['Selesai']++;
                $totalSelesaiDiperbaiki++;
            } elseif ($statLaporan == 'DIPROSES' || $statJadwal == 'PROSES' || $statJadwal == 'PENDING') {
                $statsStatus['Proses']++;
                $totalBelumDiperbaiki++;
            } elseif ($statLaporan == 'BARU' || $statLaporan == 'DIJADWALKAN' || $statJadwal == 'MENUNGGU') {
                $statsStatus['Menunggu']++;
                $totalBelumDiperbaiki++;
            } else {
                $statsStatus['Batal']++;
            }
        }

        arsort($statsJurusan);
        arsort($statsAlat);

        return $this->response->setJSON([
            'cards' => [
                'laporan_kerusakan' => $totalLaporanKerusakan,
                'belum_diperbaiki' => $totalBelumDiperbaiki,
                'selesai_diperbaiki' => $totalSelesaiDiperbaiki,
                'peminjaman' => $totalPeminjaman,
                'barang_rusak' => $totalBarangRusak
            ],
            'charts' => [
                'jurusan' => ['labels' => array_keys($statsJurusan), 'data' => array_values($statsJurusan)],
                'alat' => ['labels' => array_slice(array_keys($statsAlat), 0, 10), 'data' => array_slice(array_values($statsAlat), 0, 10)],
                'severity' => ['labels' => array_keys($statsSeverity), 'data' => array_values($statsSeverity)],
                'status' => ['labels' => array_keys($statsStatus), 'data' => array_values($statsStatus)]
            ]
        ]);
    }

    // ===================================================================
    // FUNGSI BARU: AMBIL NOTIFIKASI REALTIME
    // ===================================================================
    public function get_notifikasi()
    {
        $db = \Config\Database::connect();

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
                ->orderBy('l.updated_at', 'DESC')
                ->limit(10)
                ->get()
                ->getResultArray();

            foreach ($notifikasi as &$item) {

                if ($item['status_laporan'] == 'BARU') {

                    $item['pesan'] = 'Laporan kerusakan baru telah dibuat';

                } else if ($item['status_laporan'] == 'DIJADWALKAN') {

                    $item['pesan'] = 'Perbaikan telah dijadwalkan';

                } else if ($item['status_laporan'] == 'DIPROSES') {

                    $item['pesan'] = 'Teknisi sedang melakukan perbaikan';

                } else if ($item['status_laporan'] == 'PENDING') {

                    $item['pesan'] = 'Perbaikan sementara ditunda';

                } else if ($item['status_laporan'] == 'MENUNGGU KONFIRMASI') {

                    $item['pesan'] = 'Menunggu konfirmasi dari pelapor';

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
    public function get_statistik_json()
    {
        $request = \Config\Services::request();
        $db = \Config\Database::connect();

        // 1. Setup Query Builder
        $builder = $db->table('tb_perbaikan p');
        $builder->join('tb_jadwal_perbaikan jp', 'jp.id_jadwal = p.id_jadwal');
        $builder->join('tb_laporan l', 'l.id_laporan = jp.id_laporan');

        // KUNCI PERBAIKAN: 
        // Hapus $builder->where('jp.id_teknisi', $idTeknisi); <-- INI YANG BIKIN 0 TERUS DI ADMIN!

        // Filter Dasar (Hanya Status Selesai atau Rusak)
        $builder->groupStart()
            ->where('jp.status_perbaikan', 'SELESAI')
            ->orWhere('jp.status_perbaikan', 'RUSAK')
            ->groupEnd();

        // ============================================================
        // 3. TERAPKAN FILTER DARI USER
        // ============================================================
        $filterDate = $request->getGet('daterange');
        $filterUnit = $request->getGet('unit');
        $filterBulan = $request->getGet('bulan');
        $filterTahun = $request->getGet('tahun');

        // A. Filter Tanggal Range
        if (!empty($filterDate)) {
            $dates = explode(' to ', $filterDate);
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

    public function get_statistik_riwayat_json()
    {
        $request = \Config\Services::request();
        $db = \Config\Database::connect();

        // 1. Setup Query Builder
        $builder = $db->table('tb_perbaikan p');
        $builder->join('tb_jadwal_perbaikan jp', 'jp.id_jadwal = p.id_jadwal');
        $builder->join('tb_laporan l', 'l.id_laporan = jp.id_laporan');

        // KUNCI PERBAIKAN: 
        // Hapus $builder->where('jp.id_teknisi', $idTeknisi); <-- INI YANG BIKIN 0 TERUS DI ADMIN!

        // Filter Dasar (Hanya Status Selesai atau Rusak)
        $builder->groupStart()
            ->where('jp.status_perbaikan', 'SELESAI')
            ->orWhere('jp.status_perbaikan', 'RUSAK')
            ->groupEnd();

        // ============================================================
        // 3. TERAPKAN FILTER DARI USER
        // ============================================================
        $filterDate = $request->getGet('daterange');
        $filterUnit = $request->getGet('unit');
        $filterBulan = $request->getGet('bulan');
        $filterTahun = $request->getGet('tahun');

        // A. Filter Tanggal Range
        if (!empty($filterDate)) {
            $dates = explode(' to ', $filterDate);
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

    public function get_statistik_barang()
    {
        $db = \Config\Database::connect();

        // Asumsi: Kita hitung berdasarkan kategori di tb_master_alat
        // Jika tidak ada kolom 'status', buat perumpamaan data
        $total = $db->table('tb_master_alat')->countAll();

        // Sesuaikan kondisi WHERE dengan kolom yang ada di tabel Anda
        $aktif = $db->table('tb_master_alat')->where('kategori', 'Aktif')->countAllResults();
        $dipinjam = $db->table('tb_master_alat')->where('kategori', 'Dipinjam')->countAllResults();
        $rusak = $db->table('tb_master_alat')->where('kategori', 'Rusak')->countAllResults();

        return $this->response->setJSON([
            'aktif' => ($total > 0) ? round(($aktif / $total) * 100) : 0,
            'dipinjam' => ($total > 0) ? round(($dipinjam / $total) * 100) : 0,
            'rusak' => ($total > 0) ? round(($rusak / $total) * 100) : 0
        ]);
    }

    public function get_statistik_antrian()
    {
        $db = \Config\Database::connect();

        return $this->response->setJSON([

            'new' => $db->table('tb_laporan')
                ->where('status_laporan', 'BARU')
                ->countAllResults(),

            'proses' => $db->table('tb_laporan')
                ->groupStart()
                ->where('status_laporan', 'DIPROSES')
                ->orWhere('status_laporan', 'PENDING')
                ->groupEnd()
                ->countAllResults(),

            'komplain' => 0,

            'validasi_akhir' => $db->table('tb_laporan')
                ->groupStart()
                ->where('status_laporan', 'MENUNGGU KONFIRMASI')
                ->orGroupStart()
                ->where('status_laporan', 'SELESAI')
                ->where('validasi_kepala', 'Menunggu')
                ->groupEnd()
                ->groupEnd()
                ->countAllResults(),

            'riwayat' => $db->table('tb_laporan')
                ->where('status_laporan', 'SELESAI')
                ->where('validasi_kepala', 'Disetujui')
                ->countAllResults(),

        ]);
    }

    /* =========================
       DETAIL LAPORAN
    ========================== */
    public function get_detail_laporan($id)
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

        jp.id_jadwal,
        jp.tanggal_perbaikan AS tgl_dijadwalkan,
        jp.status_perbaikan,

        p.waktu_mulai,
        p.waktu_dilanjutkan,
        p.waktu_selesai,
        p.status_kerusakan,
        p.catatan_teknisi,
        p.foto_bukti,

        u.nama AS nama_teknisi
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
            ->where('l.id_laporan', $id)
            ->get()
            ->getRowArray();

        return $this->response->setJSON($data);
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
            'laporan_list' => [],
            'filter_range' => $dateRange,
            'filter_status' => $status,
            'filter_unit' => $unit,

            // Variabel Data Master untuk View & JS
            'list_unit' => $list_unit,
            'daftar_alat' => $daftarAlat,
            'daftar_lokasi' => $daftar_lokasi,
            'map_pelaksana' => $mapPelaksana
        ];

        return view('admin/riwayat', $data);
    }

    public function get_laporan($mode = 'aktif')
    {
        // 1. Matikan Debugbar agar respon JSON bersih
        if (ENVIRONMENT !== 'production') {
            // \Config\Services::toolbar()->respond(); 
        }

        try {
            $request = \Config\Services::request();
            $db = \Config\Database::connect();

            // --------------------------------------------------------------------
            // 2. DEFINISI KOLOM SORTING (Sesuai Urutan TH di View HTML)
            // --------------------------------------------------------------------
            $columns = [
                0 => 'l.nomor_laporan',     // Nomor Laporan
                1 => 'l.tanggal_laporan',   // Tanggal
                2 => 'l.tanggal_perbaikan',   // Tanggal
                3 => 'l.nama_alat',         // Nama Alat
                4 => 'l.nomor_inventaris',  // No Inventaris
                5 => 'l.lokasi',       // Lokasi
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

            $filterDate = $request->getVar('daterange');
            $filterStatus = $request->getVar('status');
            $filterUnit = $request->getVar('unit');
            $filterBulan = $request->getVar('bulan');
            $filterTahun = $request->getVar('tahun');

            // Jika endpoint historis memanggil function ini,
            // gunakan parameter function.
            // Jika tidak, gunakan parameter GET seperti biasa.

            // 4. LOGIKA FILTER KHUSUS ADMIN (HAPUS FILTER TEKNISI)
            $applyFilterLogic = function ($builder) use ($filterDate, $filterStatus, $filterUnit, $search, $filterBulan, $filterTahun, $mode) {

                // ---> KUNCI UTAMA: FILTER 'id_teknisi' SUDAH DIHAPUS <---

                // A. Filter Wajib (Tampilkan Laporan yang Selesai / Rusak di tabel ini)
                if ($mode === 'aktif') {

                    $builder->groupStart()
                        ->where('jp.status_perbaikan', 'SELESAI')
                        ->orWhere('jp.status_perbaikan', 'RUSAK')
                        ->groupEnd();
                }

                // B. Filter Tanggal Range 
                if (!empty($filterDate)) {
                    $dates = explode(' to ', $filterDate);
                    // Cek flatpickr
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

                // C. Filter Bulan
                if (!empty($filterBulan)) {
                    $builder->where('MONTH(l.tanggal_laporan)', $filterBulan);
                }

                // D. Filter Tahun
                if (!empty($filterTahun)) {
                    $builder->where('YEAR(l.tanggal_laporan)', $filterTahun);
                }

                // E. Filter Status 
                if (!empty($filterStatus)) {
                    $statusFisik = ['Ringan', 'Sedang', 'Berat'];
                    if (in_array($filterStatus, $statusFisik)) {
                        $builder->like('p.status_kerusakan', $filterStatus);
                    } else {
                        $builder->where('jp.status_perbaikan', $filterStatus);
                    }
                }

                // F. Filter Unit 
                if (!empty($filterUnit)) {
                    $builder->where('l.unit', $filterUnit);
                }

                // G. Global Search 
                if (!empty($search) && !empty($search['value'])) {
                    $val = $search['value'];
                    $builder->groupStart()
                        ->like('l.nomor_laporan', $val)
                        ->orLike('l.nama_alat', $val)
                        ->orLike('l.unit', $val)
                        ->orLike('u.nama', $val)
                        ->orLike('l.nomor_inventaris', $val)
                        ->orLike('l.lokasi', $val) // Pastikan kolom ini benar di DB
                        ->orLike('l.kerusakan', $val)
                        ->groupEnd();
                }
            };

            // --------------------------------------------------------------------
            // 5. QUERY 1: HITUNG JUMLAH DATA SETELAH FILTER
            // --------------------------------------------------------------------
            $countBuilder = $db->table('tb_laporan l');
            $countBuilder->join('tb_jadwal_perbaikan jp', 'jp.id_laporan = l.id_laporan', 'left');
            $countBuilder->join('tb_user u', 'u.id_user = l.id_teknisi', 'left');
            $countBuilder->join('tb_perbaikan p', 'p.id_jadwal = jp.id_jadwal', 'left');

            $applyFilterLogic($countBuilder);
            $recordsFiltered = $countBuilder->countAllResults();

            // --------------------------------------------------------------------
            // 6. QUERY 2: AMBIL DATA SEBENARNYA 
            // --------------------------------------------------------------------
            $builder = $db->table('tb_laporan l');
            $builder->select('l.*, jp.status_perbaikan, jp.tanggal_perbaikan, u.nama as pelaksana, p.catatan_teknisi, p.status_kerusakan as kondisi_fisik, p.waktu_selesai');
            $builder->join('tb_jadwal_perbaikan jp', 'jp.id_laporan = l.id_laporan', 'left');
            $builder->join('tb_user u', 'u.id_user = l.id_teknisi', 'left');
            $builder->join('tb_perbaikan p', 'p.id_jadwal = jp.id_jadwal', 'left');

            $applyFilterLogic($builder);

            if ($order && isset($order[0]['column']) && isset($columns[$order[0]['column']])) {
                $colIndex = $order[0]['column'];
                $colDir = $order[0]['dir'];
                $builder->orderBy($columns[$colIndex], $colDir);
            } else {
                $builder->orderBy('l.tanggal_laporan', 'DESC');
            }

            if ($length != -1) {
                $builder->limit($length, $start);
            }

            $data = $builder->get()->getResultArray();

            // --------------------------------------------------------------------
            // 7. FORMAT DATA KE JSON ARRAY
            // --------------------------------------------------------------------
            $result = [];

            foreach ($data as $row) {
                $statusPerbaikan = $row['status_perbaikan'] ?? 'BELUM DIPROSES';

                if ($statusPerbaikan == 'SELESAI') {

                    $badgeStatus = 'bg-success';

                } elseif ($statusPerbaikan == 'RUSAK') {

                    $badgeStatus = 'bg-danger';

                } elseif ($statusPerbaikan == 'PROSES') {

                    $badgeStatus = 'bg-warning text-dark';

                } else {

                    $badgeStatus = 'bg-secondary';

                }

                // FOTO
                $foto = '-';
                $pathFoto = $row['path_foto_bukti'] ?? '';
                if (!empty($pathFoto)) {
                    $files = explode(',', $pathFoto);
                    $tempFoto = [];
                    foreach ($files as $f) {
                        $f = trim($f);
                        if (empty($f))
                            continue;
                        $url = base_url('uploads/laporan/' . $f);
                        $tempFoto[] = '<a href="' . $url . '" target="_blank"><img src="' . $url . '" width="40" height="40" class="img-thumbnail" style="object-fit: cover;"></a>';
                    }
                    if (!empty($tempFoto)) {
                        $foto = '<div class="d-flex flex-wrap gap-1 justify-content-center">' . implode('', $tempFoto) . '</div>';
                    }
                }

                $keluhan = $row['kerusakan'] ?? '-';
                $lokasi = $row['lokasi_alat'] ?? '-';

                // STATUS FISIK
                $rawFisik = $row['kondisi_fisik'] ?? $row['status_kerusakan'] ?? '-';
                $fisikText = ucfirst($rawFisik);
                $fisikLower = strtolower($rawFisik);

                $badgeFisik = 'bg-secondary';
                if ($fisikLower === 'ringan')
                    $badgeFisik = 'bg-success';
                elseif ($fisikLower === 'sedang')
                    $badgeFisik = 'bg-warning text-dark';
                elseif ($fisikLower === 'berat' || $fisikLower === 'rusak')
                    $badgeFisik = 'bg-danger';

                // PENTING: Harus berjumlah tepat 14 Index (0 - 13)
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
                    '<span class="badge ' . $badgeStatus . '">' . $statusPerbaikan . '</span>', // 9
                    '<span class="">' . ($row['catatan_teknisi'] ?? '-') . '</span>', // 10
                    $row['validasi_kepala'] ?? 'Menunggu',          // 11
                    $foto                                           // 12
                ];
            }

            // --------------------------------------------------------------------
            // 8. QUERY 3: HITUNG TOTAL GLOBAL ADMIN
            // --------------------------------------------------------------------
            $totalBuilder = $db->table('tb_laporan l');
            $totalBuilder->join('tb_jadwal_perbaikan jp', 'jp.id_laporan = l.id_laporan', 'left');
            if ($mode === 'aktif') {

                $totalBuilder->groupStart()
                    ->where('jp.status_perbaikan', 'SELESAI')
                    ->orWhere('jp.status_perbaikan', 'RUSAK')
                    ->groupEnd();

            }
            $recordsTotal = $totalBuilder->countAllResults();

            return $this->response->setJSON([
                "draw" => intval($draw),
                "recordsTotal" => intval($recordsTotal),
                "recordsFiltered" => intval($recordsFiltered),
                "data" => $result
            ]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(200)->setJSON([
                "draw" => 1,
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => [],
                "error" => "SERVER ERROR: " . $e->getMessage() . " (Line: " . $e->getLine() . ")"
            ]);
        }
    }

    public function get_laporan_riwayat()
    {
        // 1. Matikan Debugbar agar respon JSON bersih
        if (ENVIRONMENT !== 'production') {
            // \Config\Services::toolbar()->respond(); 
        }

        try {
            $request = \Config\Services::request();
            $db = \Config\Database::connect();

            // --------------------------------------------------------------------
            // 2. DEFINISI KOLOM SORTING (Sesuai Urutan TH di View HTML)
            // --------------------------------------------------------------------
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

            $filterDate = $request->getVar('daterange');
            $filterStatus = $request->getVar('status');
            $filterUnit = $request->getVar('unit');
            $filterBulan = $request->getVar('bulan');
            $filterTahun = $request->getVar('tahun');

            // 4. LOGIKA FILTER KHUSUS ADMIN (HAPUS FILTER TEKNISI)
            $applyFilterLogic = function ($builder) use ($filterDate, $filterStatus, $filterUnit, $search, $filterBulan, $filterTahun) {

                // ---> KUNCI UTAMA: FILTER 'id_teknisi' SUDAH DIHAPUS <---

                // A. Filter Wajib (Tampilkan Laporan yang Selesai / Rusak di tabel ini)
                $builder->where('jp.status_perbaikan', 'SELESAI');

                // B. Filter Tanggal Range 
                if (!empty($filterDate)) {
                    $dates = explode(' to ', $filterDate);
                    // Cek flatpickr
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

                // C. Filter Bulan
                if (!empty($filterBulan)) {
                    $builder->where('MONTH(l.tanggal_laporan)', $filterBulan);
                }

                // D. Filter Tahun
                if (!empty($filterTahun)) {
                    $builder->where('YEAR(l.tanggal_laporan)', $filterTahun);
                }

                // E. Filter Status 
                if (!empty($filterStatus)) {
                    $statusFisik = ['Ringan', 'Sedang', 'Berat'];
                    if (in_array($filterStatus, $statusFisik)) {
                        $builder->like('p.status_kerusakan', $filterStatus);
                    } else {
                        $builder->where('jp.status_perbaikan', $filterStatus);
                    }
                }

                // F. Filter Unit 
                if (!empty($filterUnit)) {
                    $builder->where('l.unit', $filterUnit);
                }

                // G. Global Search 
                if (!empty($search) && !empty($search['value'])) {
                    $val = $search['value'];
                    $builder->groupStart()
                        ->like('l.nomor_laporan', $val)
                        ->orLike('l.nama_alat', $val)
                        ->orLike('l.unit', $val)
                        ->orLike('u.nama', $val)
                        ->orLike('l.nomor_inventaris', $val)
                        ->orLike('l.lokasi', $val) // Pastikan kolom ini benar di DB
                        ->orLike('l.kerusakan', $val)
                        ->groupEnd();
                }
            };

            // --------------------------------------------------------------------
            // 5. QUERY 1: HITUNG JUMLAH DATA SETELAH FILTER
            // --------------------------------------------------------------------
            $countBuilder = $db->table('tb_laporan l');
            $countBuilder->join('tb_jadwal_perbaikan jp', 'jp.id_laporan = l.id_laporan', 'left');
            $countBuilder->join('tb_user u', 'u.id_user = jp.id_teknisi', 'left');
            $countBuilder->join('tb_perbaikan p', 'p.id_jadwal = jp.id_jadwal', 'left');

            $applyFilterLogic($countBuilder);
            $recordsFiltered = $countBuilder->countAllResults();

            // --------------------------------------------------------------------
            // 6. QUERY 2: AMBIL DATA SEBENARNYA 
            // --------------------------------------------------------------------
            $builder = $db->table('tb_laporan l');
            // Tambahkan jp.tanggal_perbaikan di dalam select
            $builder->select('l.*, jp.status_perbaikan, jp.tanggal_perbaikan, u.nama as pelaksana, p.catatan_teknisi, p.status_kerusakan as kondisi_fisik, p.waktu_selesai');
            $builder->join('tb_jadwal_perbaikan jp', 'jp.id_laporan = l.id_laporan', 'left');
            $builder->join('tb_user u', 'u.id_user = jp.id_teknisi', 'left');
            $builder->join('tb_perbaikan p', 'p.id_jadwal = jp.id_jadwal', 'left');

            $applyFilterLogic($builder);

            if ($order && isset($order[0]['column']) && isset($columns[$order[0]['column']])) {
                $colIndex = $order[0]['column'];
                $colDir = $order[0]['dir'];
                $builder->orderBy($columns[$colIndex], $colDir);
            } else {
                $builder->orderBy('l.tanggal_laporan', 'DESC');
            }

            if ($length != -1) {
                $builder->limit($length, $start);
            }

            $data = $builder->get()->getResultArray();

            // --------------------------------------------------------------------
            // 7. FORMAT DATA KE JSON ARRAY
            // --------------------------------------------------------------------
            $result = [];

            foreach ($data as $row) {
                $badgeStatus = ($row['status_perbaikan'] == 'SELESAI') ? 'bg-success' : 'bg-danger';

                // FOTO
                $foto = '-';
                $pathFoto = $row['path_foto_bukti'] ?? '';
                if (!empty($pathFoto)) {
                    $files = explode(',', $pathFoto);
                    $tempFoto = [];
                    foreach ($files as $f) {
                        $f = trim($f);
                        if (empty($f))
                            continue;
                        $url = base_url('uploads/laporan/' . $f);
                        $tempFoto[] = '<a href="' . $url . '" target="_blank"><img src="' . $url . '" width="40" height="40" class="img-thumbnail" style="object-fit: cover;"></a>';
                    }
                    if (!empty($tempFoto)) {
                        $foto = '<div class="d-flex flex-wrap gap-1 justify-content-center">' . implode('', $tempFoto) . '</div>';
                    }
                }

                $keluhan = $row['kerusakan'] ?? '-';
                $lokasi = $row['lokasi_alat'] ?? '-';

                // STATUS FISIK
                $rawFisik = $row['kondisi_fisik'] ?? $row['status_kerusakan'] ?? '-';
                $fisikText = ucfirst($rawFisik);
                $fisikLower = strtolower($rawFisik);

                $badgeFisik = 'bg-secondary';
                if ($fisikLower === 'ringan')
                    $badgeFisik = 'bg-success';
                elseif ($fisikLower === 'sedang')
                    $badgeFisik = 'bg-warning text-dark';
                elseif ($fisikLower === 'berat' || $fisikLower === 'rusak')
                    $badgeFisik = 'bg-danger';

                // PENTING: Harus berjumlah tepat 14 Index (0 - 13)
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
            // 8. QUERY 3: HITUNG TOTAL GLOBAL ADMIN
            // --------------------------------------------------------------------
            $totalBuilder = $db->table('tb_laporan l');
            $totalBuilder->join('tb_jadwal_perbaikan jp', 'jp.id_laporan = l.id_laporan', 'left');

            // ========================================================
            // PERBAIKAN DI SINI BUNG!
            // Sesuaikan dengan yang di atas.
            // ========================================================
            $totalBuilder->where('jp.status_perbaikan', 'SELESAI');

            // Kode lama (Hapus/Komentari):
            // $totalBuilder->groupStart()
            //     ->where('jp.status_perbaikan', 'SELESAI')
            //     ->orWhere('jp.status_perbaikan', 'RUSAK')
            //     ->groupEnd();
            // ========================================================

            $recordsTotal = $totalBuilder->countAllResults();
            return $this->response->setJSON([
                "draw" => intval($draw),
                "recordsTotal" => intval($recordsTotal),
                "recordsFiltered" => intval($recordsFiltered),
                "data" => $result
            ]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(200)->setJSON([
                "draw" => 1,
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => [],
                "error" => "SERVER ERROR: " . $e->getMessage() . " (Line: " . $e->getLine() . ")"
            ]);
        }
    }

    public function fp_growth()
    {
        return view('admin/fp_growth');
    }

    public function generate_fp_growth()
    {
        $request = $this->request->getJSON(true);

        $payload = [
            'jenis_analisis' => $request['jenis_analisis'],
            'metode_grouping' => $request['metode_grouping'],
            'jenis_item' => $request['jenis_item'],
            'jenis_filter' => $request['jenis_filter'],

            'min_support' => (float) $request['min_support'],
            'min_confidence' => (float) $request['min_confidence'],

            'tahun_awal' => !empty($request['tahun_awal'])
                ? $request['tahun_awal']
                : null,

            'tahun_akhir' => !empty($request['tahun_akhir'])
                ? $request['tahun_akhir']
                : null,

            'tanggal_awal' => !empty($request['tanggal_awal'])
                ? $request['tanggal_awal']
                : null,

            'tanggal_akhir' => !empty($request['tanggal_akhir'])
                ? $request['tanggal_akhir']
                : null
        ];

        try {

            $client = \Config\Services::curlrequest();

            $response = $client->post(
                'http://127.0.0.1:8000/generate-fp-growth',
                [
                    'json' => $payload,
                    'http_errors' => false
                ]
            );

            $result = json_decode(
                $response->getBody(),
                true
            );

            return $this->response->setJSON($result);

        } catch (\Exception $e) {

            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function get_riwayat_eksperimen()
    {
        $apiUrl =
            "http://127.0.0.1:8000/riwayat-eksperimen";

        $response =
            file_get_contents($apiUrl);

        return $this->response
            ->setJSON(
                json_decode($response, true)
            );
    }

    public function get_rule_manual()
    {
        $request = $this->request->getJSON(true);

        $jenisAnalisis = $request['jenis_analisis'];
        $jenisItem = $request['jenis_item'];
        $jenisFilter = $request['jenis_filter'];

        $db = \Config\Database::connect();

        $data = $db->table('tb_rule_manual')
            ->where('jenis_analisis', $jenisAnalisis)
            ->where('jenis_item', $jenisItem)
            ->where('jenis_filter', $jenisFilter)
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'success' => true,
            'data' => $data
        ]);
    }

    public function get_laporan_historis()
    {
        return $this->get_laporan('historis');
    }

}
