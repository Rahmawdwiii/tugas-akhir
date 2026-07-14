<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>UPAPP | Dashboard</title>

  <!-- Bootstrap & FontAwesome -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

  <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
  <link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css" rel="stylesheet">

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

  <style>
    /*Kalau mau pakai footer yg satunya aktifkan ini*/
    .main-body-wrapper {
      display: flex;
    }

    body {
      background-color: #f5f9ff;
      overflow-x: hidden;
      margin: 0;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    /* HEADER */
    header {
      background-color: #b3d9ff;
      padding: 10px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      height: 60px;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 1100;
    }

    header .left {
      display: flex;
      align-items: center;
    }

    .brand-logo {
      height: 40px;
      margin-right: 10px;
    }

    header,
    header .left span,
    header .right i,
    header .right button {
      color: #003366 !important;
    }

    header .right button {
      border-color: #003366 !important;
    }

    header .right button:hover {
      background-color: #003366 !important;
      color: #ffffff !important;
    }

    /* SIDEBAR */
    #sidebar {
      position: fixed;
      top: 60px;
      left: 0;
      width: 250px;
      height: calc(100vh - 60px);
      background-color: #b3d9ff;
      transition: all 0.3s;
      overflow-y: auto;
      z-index: 1000;
    }

    #sidebar.collapsed {
      width: 0;
      padding: 0;
      overflow: hidden;
    }

    #sidebar ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    #sidebar .nav-item a {
      display: flex;
      align-items: center;
      padding: 10px 18px;
      color: #003366;
      text-decoration: none;
      font-weight: 500;
      border-radius: 8px;
      margin: 2px 8px;
      transition: background 0.2s ease;
    }

    #sidebar .nav-item a:hover,
    #sidebar .nav-item a.active {
      background: #ffffff;
      color: #003366;
      font-weight: 600;
    }

    #sidebar .nav-item i {
      margin-right: 10px;
    }

    /* SUBMENU */
    .submenu {
      padding-left: 30px;
      display: none;
    }

    .submenu.show {
      display: block;
    }

    /* CONTENT */
    .content {
      margin-left: 250px;
      margin-top: 60px;
      padding: 20px;
      transition: all 0.3s;
      min-height: calc(100vh - 120px);
      background: #f7faff;
      flex: 1;
    }

    .content.full {
      margin-left: 0;
    }

    /* TOGGLE BUTTON */
    #toggleSidebar {
      border: none;
      background: transparent;
      font-size: 22px;
      cursor: pointer;
      margin-right: 10px;
    }

    /* === CARD STYLE (Digabungkan) === */
    .card {
      border: none;
      /* Menggunakan border-radius 10px (nilai terakhir dari kode Anda) */
      border-radius: 10px;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
      font-family: 'Segoe UI', Tahoma, sans-serif;
      font-size: 0.9rem;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }

    .card:hover {
      transform: translateY(-3px);
      box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
    }

    .card-header {
      font-weight: 600;
      letter-spacing: 0.5px;
    }

    /* Variasi Warna Card */
    .card.bg-primary {
      background-color: #71b8ffff !important;
      color: #003366;
    }

    .card.bg-success {
      background-color: #b8e6b3 !important;
      color: #004d00;
    }

    .card.bg-warning {
      background-color: #fff0b3 !important;
      color: #805b00;
    }

    .card.bg-danger {
      background-color: #ffb3b3 !important;
      color: #660000;
    }

    /* FORM & BUTTON STYLES */
    .form-control,
    .select2 {
      font-size: 0.85rem;
      border-radius: 6px;
    }

    .btn {
      font-size: 0.8rem;
      border-radius: 6px;
    }

    .btn i {
      margin-right: 4px;
    }

    .btn-primary {
      background-color: #0d6efd;
      border-color: #0d6efd;
    }

    .dt-buttons .btn {
      font-size: 0.8rem;
      margin-right: 5px;
    }

    /* PROGRESS */
    .progress {
      height: 20px;
      border-radius: 10px;
      overflow: hidden;
      background-color: #e9ecef;
    }

    .progress-bar {
      line-height: 20px;
      font-weight: 600;
      font-size: 13px;
    }

    h5.fw-bold {
      color: #003366;
      font-weight: 700;
    }

    /* TABLE STYLES */
    table.table {
      font-size: 0.85rem;
      vertical-align: middle;
    }

    table.table thead th {
      background-color: #f8f9fa;
      font-weight: 600;
      color: #333;
      border-bottom: 2px solid #dee2e6;
    }

    table.table td {
      padding: 8px 10px;
    }

    .table-responsive {
      margin-top: 10px;
      padding: 15px 15px;
      /* Menjaga padding agar shadow terlihat rapi */
    }

    /* MODAL STYLES */
    /* Atur agar modal muncul agak ke bawah dari atas layar */
    #ModalEditLaporan .modal-dialog,
    .modal-dialog {
      margin-top: 100px;
    }

    /* ANIMASI */
    .fade-in {
      opacity: 0;
      transform: translateY(10px);
      animation: fadeInUp 0.6s ease forwards;
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(10px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* RESPONSIVE */
    @media (max-width: 768px) {
      .content {
        margin-left: 0;
      }

      #sidebar {
        width: 200px;
      }
    }

    /* Paksa Modal agar berada di lapisan paling atas (di atas Header) */
    .modal {
      z-index: 99999 !important;
      /* Angka sangat tinggi */
    }

    /* Paksa Layar Gelap (Backdrop) agar juga naik */
    .modal-backdrop {
      z-index: 99990 !important;
      /* Sedikit di bawah modal */
    }
  </style>
</head>

<body>
  <!-- HEADER -->
  <?= $this->include('layout/header') ?>

  <!-- SIDEBAR -->
  <?= $this->include('layout/sidebar_admin') ?>

  <!-- CONTENT -->
  <main class="content" id="mainContent">
    <div class="container-fluid">

      <div class="row g-3 mb-4">
        <div class="col-md-3 col-6 fade-in">
          <div class="card shadow-sm p-3 bg-primary">
            <h6>Selesai Diperbaiki</h6>
            <h3 id="stat_total">Loading...</h3>
          </div>
        </div>
        <div class="col-md-3 col-6 fade-in" style="animation-delay: .1s;">
          <div class="card shadow-sm p-3 bg-success">
            <h6>Rusak Ringan</h6>
            <h3 id="stat_ringan">0</h3>
          </div>
        </div>
        <div class="col-md-3 col-6 fade-in" style="animation-delay: .2s;">
          <div class="card shadow-sm p-3 bg-warning">
            <h6>Rusak Sedang</h6>
            <h3 id="stat_sedang">0</h3>
          </div>
        </div>
        <div class="col-md-3 col-6 fade-in" style="animation-delay: .3s;">
          <div class="card shadow-sm p-3 bg-danger">
            <h6>Rusak Berat</h6>
            <h3 id="stat_berat">0</h3>
          </div>
        </div>
      </div>

      <!-- Filter -->
      <div class="card shadow-sm mb-4">
        <div class="card-body">
          <div class="row g-2 align-items-end">

            <div class="col-md-3">
              <label class="form-label fw-bold">Tanggal</label>
              <input type="text" id="filter_daterange" class="form-control" placeholder="Cari Tanggal">
            </div>

            <div class="col-md-2">
              <label class="form-label fw-bold">Bulan</label>
              <select name="cetak_bulan" id="cetak_bulan" class="form-select select2 text-center">
                <option value="" class="text-muted">-- Pilih Bulan --</option>
                <?php
                $daftar_bulan = [
                  '01' => 'JANUARI',
                  '02' => 'FEBRUARI',
                  '03' => 'MARET',
                  '04' => 'APRIL',
                  '05' => 'MEI',
                  '06' => 'JUNI',
                  '07' => 'JULI',
                  '08' => 'AGUSTUS',
                  '09' => 'SEPTEMBER',
                  '10' => 'OKTOBER',
                  '11' => 'NOVEMBER',
                  '12' => 'DESEMBER'
                ];
                foreach ($daftar_bulan as $angka => $nama) {
                  echo "<option value=\"$angka\">$nama</option>";
                }
                ?>
              </select>
            </div>

            <div class="col-md-2">
              <label class="form-label fw-bold">Tahun</label>
              <select name="cetak_tahun" id="cetak_tahun" class="form-select select2 text-center">
                <option value="" class="text-muted">-- Pilih Tahun --</option>
                <?php
                $tahun_mulai = 2025; // Tahun paling atas (pertama)
                $tahun_sekarang = date('Y'); // Tahun saat ini (otomatis)

                // Looping MAJU: Dari tahun mulai, naik terus sampai tahun sekarang
                for ($t = $tahun_mulai; $t <= $tahun_sekarang; $t++) {
                  echo "<option value=\"$t\">$t</option>";
                }
                ?>
              </select>
            </div>

            <div class="col-md-2">
              <label class="form-label fw-bold">Status</label>
              <select id="filterStatus" class="form-select form-select-sm text-center">
                <option value="" class="text-muted">-- Semua Status --</option>
                <option value="Ringan">Ringan</option>
                <option value="Sedang">Sedang</option>
                <option value="Berat">Berat</option>
                <option value="SELESAI">Selesai</option>
              </select>
            </div>

            <!-- <div class="col-md-2">
              <label for="pelaksana_cari1" class="form-label fw-bold">Pelaksana</label>
              <select name="pelaksana_cari1" id="pelaksana_cari1" class="form-select select2 text-left">
                <option value="">-- Semua Pelaksana --</option>
                <option value="Edial Salmes">Edial Salmes</option>
                <option value="Muhammad Karison">Muhammad Karison</option>
                <option value="Sukri">Sukri</option>
                <option value="Riadi Putra">Riadi Putra</option>
                <option value="Cipto">Cipto</option>
              </select>
            </div> -->

            <!-- Hapus Filter Pelaksana
            <div class="col-md-1 d-grid">
              <label class="form-label small text-muted">&nbsp;</label>
              <button
                type="button"
                id="hapus_pelaksana_cari1"
                class="btn btn-outline-danger"
                onclick="hapus_pelaksana_cari1()"
                title="Reset Pelaksana">
                <i class="fas fa-trash"></i>
              </button>
            </div> -->

            <div class="col-md-2">
              <label class="form-label fw-bold">Unit</label>
              <select name="unit" id="filter_unit" class="form-select form-select-sm shadow-sm text-center">
                <option value="" class="text-muted">-- Semua Unit --</option>

                <?php if (!empty($list_unit)): ?>
                  <?php foreach ($list_unit as $u): ?>

                    <option value="<?= esc($u['nama_unit']) ?>"
                      <?= ($filter_unit == $u['nama_unit']) ? 'selected' : '' ?>>
                      <?= esc($u['nama_unit']) ?>
                    </option>

                  <?php endforeach; ?>
                <?php endif; ?>

              </select>
            </div>

            <div class="col-md-1 d-grid">
              <label class="form-label fw-bold d-block">&nbsp;</label>
              <button type="button" class="btn btn-primary w-100" id="btnCetakGlobal" title="Cetak Data">
                <i class="fas fa-print"></i>
              </button>
            </div>

          </div>
        </div>
      </div>

      <div class="card-body">
        <!-- DATATABLE -->
        <div class="card">
          <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-tools me-2"></i> LAPORAN KERUSAKAN SELESAI</h5>
          </div>

          <div class="card-body">
            <!-- Tombol aksi atas -->
            <!-- Perhatikan onclick="openModalTambah()" menggunakan tanda kurung -->
            <button type="button" class="btn btn-success btn-sm" title="Tambah Laporan" onclick="openModalTambah()"><i class="fas fa-plus"></i> Tambah</button>
            <button class="btn btn-secondary btn-sm" title="Reload" id="btnReload">
              <i class="fa fa-undo me-1"></i> Reload
            </button>
            <button class="btn btn-outline-dark btn-sm" id="btnCopy" title="Copy">
              <i class="fas fa-copy"></i> Copy
            </button>
            <button class="btn btn-outline-success btn-sm" onclick="exportToExcel()"><i class="fas fa-file-excel"></i> Excel</button>
            <!--<button class="btn btn-outline-info btn-sm"><i class="fas fa-file-csv"></i> CSV</button>-->
            <button type="button" class="btn btn-outline-danger btn-sm" id="btnExportPDF" onclick="exportToPDFKerusakan(this)">
              <i class="fas fa-file-pdf me-1"></i> PDF
            </button>
            <!--<button class="btn btn-outline-info btn-sm" onclick="openStatistikModal()">
              <i class="fas fa-chart-pie me-1"></i> Statistik
            </button>-->
            <div class="dt-buttons btn-group">
              <div id="search-container" class="d-flex align-items-center gap-2"></div>
            </div>

            <!-- Tabel laporan kerusakan -->
            <div class="table-responsive">
              <table class="table table-bordered table-striped text-center align-middle" id="tableLaporan">
                <thead class="table-light">
                  <tr class="text-center align-middle">
                    <th>NOMOR LAPORAN</th>
                    <th>TANGGAL LAPORAN</th>
                    <th>TANGGAL PERBAIKAN</th>
                    <th>NAMA ALAT</th>
                    <th>NOMOR INVENTARIS</th>
                    <th>LOKASI ALAT</th>
                    <th>JURUSAN / UNIT</th>
                    <th>KERUSAKAN/KELUHAN</th>
                    <th>STATUS KERUSAKAN</th>
                    <th>PELAKSANA</th>
                    <th>STATUS PERBAIKAN</th>
                    <th>ALASAN</th>
                    <th>VALIDASI KEPALA</th>
                    <th>FOTO</th>
                    <th>AKSI</th>
                  </tr>
                </thead>

                <tbody id="tableBody">

                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal Pilih Pelaksana dan Tentukan Tanggal-->
      <div class="modal fade" id="modalPelaksana" tabindex="-1" aria-labelledby="modalPelaksanaLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header bg-primary text-white">
              <!--<h5 class="modal-title" id="modalPelaksanaLabel">Pilih Pelaksana & Tanggal</h5>-->
              <h5 class="modal-title" id="modalPelaksanaLabel">Tentukan Tanggal</h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <form id="formPelaksana">
                <!--<div class="mb-3">
                    <label for="pelaksanaSelect" class="form-label fw-bold">Pilih Pelaksana:</label>
                    <select id="pelaksanaSelect" class="form-select" required>
                      <option value="">-- Pilih Pelaksana --</option>
                      <option value="Edial Salmes">Edial Salmes</option>
                      <option value="Muhammad Karison">Muhammad Karison</option>
                      <option value="Sukri">Sukri</option>
                      <option value="Riadi Putra">Riadi Putra</option>
                      <option value="Cipto">Cipto</option>
                    </select>
                  </div>-->
                <div class="mb-3">
                  <label for="tanggalPelaksana" class="form-label fw-bold">Pilih Tanggal:</label>
                  <input type="date" id="tanggalPelaksana" class="form-control">
                </div>
                <div class="text-end">
                  <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
  </main>

  <!-- Modal Validasi Pelaksana -->
  <div class="modal fade" id="validasiModal" tabindex="-1" aria-labelledby="validasiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-warning text-dark">
          <h5 class="modal-title" id="validasiModalLabel"><i class="fas fa-exclamation-triangle"></i> Konfirmasi Validasi</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>Apakah Anda yakin ingin memvalidasi laporan ini?
            Tindakan ini tidak dapat dibatalkan.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="button" class="btn btn-warning text-dark" id="btnConfirmValidasi" onclick="confirmValidasi()">
            Ya, Validasi
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Tombol Edit-->
  <div class="modal fade" id="ModalEditLaporan" tabindex="-1" aria-labelledby="ModalEditLaporanLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content rounded-xl shadow">
        <div class="modal-header bg-warning text-white rounded-top">
          <h5 class="modal-title fw-bold" id="ModalEditLaporanLabel">
            <i class="fas fa-edit me-2"></i> Edit Laporan Kerusakan
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <form id="formEditLaporan">
          <div class="modal-body p-4">
            <input type="hidden" name="nomor_laporan" id="edit_nomor_laporan">

            <!-- Nomor Laporan -->
            <div class="row mb-3 align-items-center">
              <label for="edit_nomor_laporan_view" class="col-md-4 col-form-label fw-bold text-uppercase text-gray-700">Nomor Laporan Kerusakan</label>
              <div class="col-md-8">
                <input type="text" id="edit_nomor_laporan_view" class="form-control bg-light text-secondary fw-bold" readonly />
              </div>
            </div>

            <!-- Nama Alat -->
            <div class="row mb-3 align-items-center">
              <label for="edit_nama_alat" class="col-md-4 col-form-label fw-bold text-uppercase text-gray-700">Nama Alat</label>
              <div class="col-md-8">
                <input type="text" id="edit_nama_alat" class="form-control bg-light text-secondary fw-bold" value="" readonly disabled />
              </div>
            </div>

            <!-- Nomor Inventaris -->
            <div class="row mb-3 align-items-center">
              <label for="edit_nomor_inventaris" class="col-md-4 col-form-label fw-bold text-uppercase text-gray-700">Nomor Inventaris</label>
              <div class="col-md-8">
                <input type="text" id="edit_nomor_inventaris" class="form-control bg-light text-secondary fw-bold" value="" readonly disabled />
              </div>
            </div>

            <!-- Unit -->
            <div class="row mb-3 align-items-center">
              <label for="edit_unit" class="col-md-4 col-form-label fw-bold text-uppercase text-gray-700">Jurusan/Unit</label>
              <div class="col-md-8">
                <input type="text" id="edit_unit" class="form-control bg-light text-secondary fw-bold" readonly disabled />
              </div>
            </div>

            <!-- Lokasi Alat -->
            <div class="row mb-3 align-items-center">
              <label for="edit_lokasi_alat" class="col-md-4 col-form-label fw-bold text-uppercase text-gray-700">Lokasi Alat</label>
              <div class="col-md-8">
                <input type="text" id="edit_lokasi_alat" class="form-control bg-light text-secondary fw-bold" readonly disabled />
              </div>
            </div>

            <!-- Status Kerusakan -->
            <div class="row mb-3 align-items-center">
              <label for="edit_status_kerusakan" class="col-md-4 col-form-label fw-bold text-uppercase text-gray-700">Status Kerusakan</label>
              <div class="col-md-8">
                <input type="text" id="edit_status_kerusakan" class="form-control bg-light text-secondary fw-bold" readonly disabled />
              </div>
            </div>

            <!-- Pelapor -->
            <div class="row mb-3 align-items-center">
              <label for="edit_pelapor" class="col-md-4 col-form-label fw-bold text-uppercase text-gray-700">Pelapor <span class="text-danger">*</span></label>
              <div class="col-md-8">
                <textarea id="edit_pelapor" rows="3" class="form-control bg-light text-secondary fw-bold" readonly disabled></textarea>
                <!-- opsi lainnya
                  <select name="pelapor" id="edit_pelapor" class="form-select">
                    <option value="">--Pilih Pelapor--</option>
                  </select>
                   -->
              </div>
            </div>

            <!-- Pelaksana -->
            <div class="row mb-3 align-items-center">
              <label for="edit_pelaksana" class="col-md-4 col-form-label fw-bold text-uppercase text-gray-700">Pelaksana</label>
              <div class="col-md-8">
                <input type="text" id="edit_pelaksana" class="form-control bg-light text-secondary fw-bold" readonly disabled />
              </div>
            </div>

            <!-- Media Pelaporan -->
            <div class="row mb-3 align-items-center">
              <label for="edit_media_laporan" class="col-md-4 col-form-label fw-bold text-uppercase text-gray-700">Media Pelaporan</label>
              <div class="col-md-8">
                <select name="media_laporan" id="edit_media_laporan" class="form-select">
                  <option value="">--Pilih Media Pelaporan--</option>
                  <option value="Telepon">Telepon</option>
                  <option value="Email">Email</option>
                  <option value="Langsung">Langsung</option>
                </select>
              </div>
            </div>

            <!-- Kerusakan / Keluhan -->
            <div class="row mb-3 align-items-center">
              <label for="edit_kerusakan_keluhan" class="col-md-4 col-form-label fw-bold text-uppercase text-gray-700">Kerusakan/Keluhan</label>
              <div class="col-md-8">
                <input type="text" id="edit_kerusakan_keluhan" class="form-control bg-light text-secondary fw-bold" readonly disabled />
              </div>
            </div>

            <!-- Uraian Pekerjaan -->
            <div class="row mb-3 align-items-start">
              <label for="edit_uraian_pekerjaan" class="col-md-4 col-form-label fw-bold text-uppercase text-gray-700 pt-2">Uraian Pekerjaan</label>
              <div class="col-md-8">
                <textarea name="uraian_pekerjaan" id="edit_uraian_pekerjaan" rows="3" class="form-control"></textarea>
              </div>
            </div>

            <!-- Nama Barang -->
            <div class="row mb-3 align-items-start">
              <label for="edit_nama_barang" class="col-md-4 col-form-label fw-bold text-uppercase text-gray-700 pt-2">Nama Barang</label>
              <div class="col-md-8">
                <textarea name="nama_barang" id="edit_nama_barang" rows="3" class="form-control"></textarea>
              </div>
            </div>

            <!-- Jumlah -->
            <div class="row mb-3 align-items-start">
              <label for="edit_jumlah_barang" class="col-md-4 col-form-label fw-bold text-uppercase text-gray-700 pt-2">Jumlah</label>
              <div class="col-md-8">
                <textarea name="jumlah_barang" id="edit_jumlah_barang" rows="3" class="form-control"></textarea>
              </div>
            </div>

            <!-- Cetak Identitas Alat -->
            <div class="row mb-3 align-items-center">
              <label for="edit_cetak_identitas_alat" class="col-md-4 col-form-label fw-bold text-uppercase text-gray-700">Cetak Identitas Alat</label>
              <div class="col-md-8">
                <select name="cetak_identitas_alat" id="edit_cetak_identitas_alat" class="form-select">
                  <option value="">--Pilih Cek Identitas--</option>
                  <option value="Ya">Ya</option>
                  <option value="Tidak">Tidak</option>
                </select>
              </div>
            </div>

            <!-- Tanggal -->
            <div class="row mb-3 align-items-center">
              <label for="edit_tanggal" class="col-md-4 col-form-label fw-bold text-uppercase text-gray-700">Tanggal</label>
              <div class="col-md-8">
                <input type="date" name="tanggal" id="edit_tanggal" class="form-control" />
              </div>
            </div>
          </div>

          <div class="modal-footer justify-content-between p-3 bg-light rounded-bottom">
            <button type="button" class="btn btn-secondary rounded" data-bs-dismiss="modal">BATAL</button>
            <button type="submit" id="btnSimpanEdit" class="btn btn-warning text-white fw-bold rounded">SIMPAN</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Tombol Tambah-->
  <div class="modal fade" id="ModalTambahLaporan" tabindex="-1" aria-labelledby="ModalTambahLaporanLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content rounded-xl shadow">
        <div class="modal-header bg-success text-white rounded-top">

          <h5 class="modal-title fw-bold" id="ModalTambahLaporanLabel">
            <i class="fas fa-plus me-2 text-white"></i>
            <span class="text-white">Tambah Laporan Kerusakan</span>
          </h5>

          <button type="button" class="btn-close btn-close-white"
            data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <form id="formTambahLaporan">
          <div class="modal-body p-4">
            <input type="hidden" name="nomor_laporan" id="tambah_nomor_laporan">

            <!-- 1. NOMOR LAPORAN (OTOMATIS) -->
            <div class="row mb-3 align-items-center">
              <label for="tambah_nomor_laporan_view" class="col-md-4 col-form-label fw-bold text-uppercase text-gray-700 text-sm">
                Nomor Laporan
              </label>
              <div class="col-md-8">
                <input type="text" name="nomor_laporan" id="tambah_nomor_laporan_view"
                  class="form-control bg-gray-100 text-primary fw-bold"
                  readonly />
                <small class="text-muted text-xs"></small>
              </div>
            </div>

            <!-- === BAGIAN NAMA ALAT & INVENTARIS (SESUAI REQUEST) === -->

            <!-- Nama Alat: Dropdown -->
            <div class="row mb-3 align-items-center">
              <label for="tambah_nama_alat" class="col-md-4 col-form-label fw-bold text-uppercase text-gray-700">Nama Alat</label>
              <div class="col-md-8">
                <select id="tambah_nama_alat" name="nama_alat" class="form-select" required>
                  <option value="" data-inventaris="">-- Pilih Alat --</option>
                  <?php if (!empty($daftar_alat)): ?>
                    <?php foreach ($daftar_alat as $alat): ?>
                      <!-- Menyimpan nomor inventaris di atribut data-inventaris -->
                      <option value="<?= esc($alat['nama_alat']) ?>"
                        data-inventaris="<?= esc($alat['nomor_inventaris'] ?? $alat['no_inventaris'] ?? '') ?>">
                        <?= esc($alat['nama_alat']) ?>
                      </option>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </select>
              </div>
            </div>

            <!-- Nomor Inventaris: Readonly & Autofill -->
            <div class="row mb-3 align-items-center">
              <label for="tambah_nomor_inventaris" class="col-md-4 col-form-label fw-bold text-uppercase text-gray-700">Nomor Inventaris</label>
              <div class="col-md-8">
                <input type="text" id="tambah_nomor_inventaris" name="nomor_inventaris"
                  class="form-control bg-light"
                  placeholder="Otomatis terisi saat alat dipilih..." readonly />
              </div>
            </div>
            <!-- === AKHIR BAGIAN YANG DIUBAH === -->

            <!-- === BAGIAN CASCADING DROPDOWN (JURUSAN & LOKASI) === -->

            <!-- 1. PILIH JURUSAN / UNIT (Parent) -->
            <div class="row mb-3 align-items-center">
              <label for="tambah_unit" class="col-md-4 col-form-label fw-bold text-uppercase text-gray-700 text-sm">
                Jurusan / Unit
              </label>
              <div class="col-md-8">
                <select name="unit" id="tambah_unit" class="form-select" required>
                  <option value="">-- Pilih Unit Terlebih Dahulu --</option>
                  <!-- Loop Data Jurusan dari Array Keys daftar_lokasi -->
                  <?php if (!empty($daftar_lokasi)): ?>
                    <?php foreach (array_keys($daftar_lokasi) as $jurusan): ?>
                      <option value="<?= esc($jurusan) ?>"><?= esc($jurusan) ?></option>
                    <?php endforeach; ?>
                  <?php endif; ?>
                  <!-- Opsi Tambahan Manual -->
                  <option value="KPA">KPA</option>
                  <option value="Pos Satpam">Pos Satpam</option>
                  <option value="Lainnya">Lainnya</option>
                </select>
              </div>
            </div>

            <!-- 2. PILIH LOKASI ALAT (Child - Tergantung Jurusan) -->
            <div class="row mb-3 align-items-center">
              <label for="tambah_lokasi_alat" class="col-md-4 col-form-label fw-bold text-uppercase text-gray-700 text-sm">
                Lokasi Alat
              </label>
              <div class="col-md-8">
                <select name="lokasi_alat" id="tambah_lokasi_alat" class="form-select" disabled required>
                  <option value="">-- Pilih Jurusan/Unit di atas --</option>
                  <!-- Opsi akan diisi otomatis oleh JavaScript -->
                </select>
              </div>
            </div>
            <!-- === AKHIR BAGIAN CASCADING === -->

            <div class="row mb-3 align-items-center">
              <label for="tambah_status_kerusakan" class="col-md-4 col-form-label fw-bold text-uppercase text-gray-700">Status Kerusakan <span class="text-danger">*</span></label>
              <div class="col-md-8">
                <select name="status_kerusakan" id="tambah_status_kerusakan" class="form-select">
                  <option value="">--Pilih Status--</option>
                  <option selected>Ringan</option>
                  <option selected>Sedang</option>
                  <option selected>Berat</option>
                </select>
              </div>
            </div>

            <hr>

            <!--<div class="row mb-3 align-items-center">
              <label for="tambah_pelapor" class="col-md-4 col-form-label fw-bold text-uppercase text-gray-700">Pelapor <span class="text-danger">*</span></label>
              <div class="col-md-8">
                <select name="pelapor" id="tambah_pelapor" class="form-select">
                  <option value="">--Pilih Pelapor--</option>
                  
                </select>
              </div>
            </div> -->

            <div class="row mb-3 align-items-center">
              <label for="tambah_pelapor" class="col-md-4 col-form-label fw-bold text-uppercase text-gray-700">Pelapor <span class="text-danger">*</span></label>
              <div class="col-md-8">
                <input type="text" name="pelapor" id="tambah_pelapor" class="form-control" placeholder="Masukkan nama pelapor" required>
              </div>
            </div>

            <div class="row mb-3 align-items-center">
              <label for="tambah_pelaksana" class="col-md-4 col-form-label fw-bold text-uppercase text-gray-700">Pelaksana</label>
              <div class="col-md-8">
                <input type="text" name="pelaksana" id="tambah_pelaksana" class="form-control" readonly />
              </div>
            </div>

            <div class="row mb-3 align-items-center">
              <label for="tambah_media_laporan" class="col-md-4 col-form-label fw-bold text-uppercase text-gray-700">Media Pelaporan</label>
              <div class="col-md-8">
                <select name="media_laporan" id="tambah_media_laporan" class="form-select">
                  <option value="">--Pilih Status--</option>
                  <option value="Telepon">Telepon</option>
                  <option value="Langsung">Datang Langsung</option>
                </select>
              </div>
            </div>

            <div class="row mb-3 align-items-start">
              <label for="tambah_kerusakan_keluhan" class="col-md-4 col-form-label fw-bold text-uppercase text-gray-700 pt-2">Kerusakan / Keluhan</label>
              <div class="col-md-8">
                <textarea name="kerusakan_keluhan" id="tambah_kerusakan_keluhan" rows="3" class="form-control"></textarea>
              </div>
            </div>

            <div class="row mb-3 align-items-center">
              <label for="tambah_cetak_identitas_alat" class="col-md-4 col-form-label fw-bold text-uppercase text-gray-700">Cetak Identitas Alat</label>
              <div class="col-md-8">
                <select name="cetak_identitas_alat" id="tambah_cetak_identitas_alat" class="form-select">
                  <option value="">--Pilih Status--</option>
                  <option value="Ya">Ya</option>
                  <option value="Tidak">Tidak</option>
                </select>
              </div>
            </div>

            <div class="row mb-3 align-items-center">
              <label for="tambah_tanggal" class="col-md-4 col-form-label fw-bold text-uppercase text-gray-700">Tanggal</label>
              <div class="col-md-8">
                <input type="date" name="tanggal" id="tambah_tanggal" class="form-control" />
              </div>
            </div>
          </div>

          <div class="modal-footer justify-content-between p-3 bg-light rounded-bottom">
            <button type="button" class="btn btn-secondary rounded" data-bs-dismiss="modal">BATAL</button>
            <button type="submit" id="btnSimpanTambah" class="btn btn-success fw-bold rounded">
              <i class="fas fa-plus me-1"></i> Tambah
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Statistik
  <div class="modal fade" id="modalStatistik" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title text-white fw-bold"><i class="fas fa-chart-line me-2"></i> Statistik Laporan Kerusakan</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body bg-light">

          <div class="row g-3 mb-4">
            <div class="col-md-3">
              <div class="card shadow-sm border-start border-primary border-4 h-100">
                <div class="card-body">
                  <small class="text-muted text-uppercase fw-bold">Total Laporan</small>
                  <h2 class="display-6 fw-bold text-primary mb-0" id="statTotal">0</h2>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card shadow-sm border-start border-warning border-4 h-100">
                <div class="card-body">
                  <small class="text-muted text-uppercase fw-bold">Menunggu Perbaikan</small>
                  <h2 class="display-6 fw-bold text-warning mb-0" id="statMenunggu">0</h2>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card shadow-sm border-start border-success border-4 h-100">
                <div class="card-body">
                  <small class="text-muted text-uppercase fw-bold">Selesai Diperbaiki</small>
                  <h2 class="display-6 fw-bold text-success mb-0" id="statSelesai">0</h2>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card shadow-sm border-start border-danger border-4 h-100">
                <div class="card-body">
                  <small class="text-muted text-uppercase fw-bold">Total Jenis Alat</small>
                  <h2 class="display-6 fw-bold text-danger mb-0" id="statJenisAlat">0</h2>
                </div>
              </div>
            </div>
          </div>

          <div class="row g-3">
            <div class="col-lg-8">
              <div class="card shadow-sm h-100">
                <div class="card-header bg-white fw-bold">Laporan per Jurusan/Unit</div>
                <div class="card-body">
                  <canvas id="webChartJurusan" height="500"></canvas>
                </div>
              </div>
            </div>

            <div class="col-lg-4">
              <div class="card shadow-sm h-100">
                <div class="card-header bg-white fw-bold">Status Tingkat Kerusakan</div>
                <div class="card-body">
                  <canvas id="webChartSeverity" height="200"></canvas>
                </div>
              </div>
            </div>

            <div class="col-lg-8">
              <div class="card shadow-sm h-100">
                <div class="card-header bg-white fw-bold">Kerusakan per Alat</div>
                <div class="card-body">
                  <canvas id="webChartAlat" height="250"></canvas>
                </div>
              </div>
            </div>

            <div class="col-lg-4">
              <div class="card shadow-sm h-100">
                <div class="card-header bg-white fw-bold">Status Hasil Perbaikan</div>
                <div class="card-body">
                  <canvas id="webChartStatus" height="200"></canvas>
                </div>
              </div>
            </div>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        </div>
      </div>
    </div>
  </div>-->

  <!-- FOOTER -->
  <!--<footer>
    <p class="m-0">© 2025 UPAPP POLSRI</p>
  </footer>-->

  <!-- DI AKTIFKAN -->
  <?php echo $this->include('layout/footer'); ?>

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

  <canvas id="tempChartCanvas" style="display: none;" width="800" height="400"></canvas>

  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

  <script>
    // Ambil Data dari Controller (PHP -> JS)
    const dataMasterLokasi = <?= json_encode($daftar_lokasi ?? []) ?>;
    const pelaksanaMap = <?= json_encode($map_pelaksana ?? []) ?>;
    const BASE_URL = "<?= base_url() ?>";
  </script>

  <script src="<?= base_url('admin/riwayat.js') ?>"></script>

</body>

</html>