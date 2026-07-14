<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>UPAPP | Dashboard</title>

  <!-- Bootstrap & FontAwesome -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">

  <style>
    /* Wrapper Body */
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

    /* CARD (Gabungan dari semua duplikat) */
    .card {
      border: none;
      border-radius: 12px;
      font-family: 'Segoe UI', Tahoma, sans-serif;
      font-size: 0.9rem;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
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

    /* Warna card */
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

    /* TEXT */
    h5.fw-bold {
      color: #003366;
      font-weight: 700;
    }

    /* FORM ELEMENT */
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

    .btn-light {
      background-color: #f8f9fa;
      border: 1px solid #dee2e6;
    }

    /* TABLE */
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
    }

    .dt-buttons .btn {
      font-size: 0.8rem;
      margin-right: 5px;
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

    /* ANIMATION */
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

    .select2-container .select2-selection--single {
      height: 31px !important;
      /* Tinggi form-select-sm */
      padding: 2px 0;
      border: 1px solid #ced4da;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
      height: 30px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
      line-height: 24px !important;
      font-size: 0.875rem;
      /* Ukuran font small */
    }

    /* Paksa Header Tabel Rata Tengah */
    #tableLaporan thead th {
      text-align: center !important;
      vertical-align: middle !important;
    }

    /* --- CSS PERBAIKAN DROPDOWN --- */
    div.dt-button-collection {
      /* Hapus position fixed/absolute yang pakai !important */
      /* Biarkan DataTables mengatur posisi top/left secara otomatis via JS */
      position: absolute;
      z-index: 9999 !important;

      /* Styling Tampilan Saja */
      background-color: #fff !important;
      box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
      border: 1px solid rgba(0, 0, 0, 0.15);
      border-radius: 6px;
      padding: 5px 0;
      /* Padding atas bawah */
      min-width: 150px;
    }

    /* Item di dalam dropdown */
    div.dt-button-collection button.dt-button {
      display: block;
      width: 100%;
      text-align: left;
      background: transparent;
      border: none;
      padding: 8px 16px;
      /* Padding lebih lega */
      font-size: 0.85rem;
      color: #212529;
    }

    /* Hover effect */
    div.dt-button-collection button.dt-button:hover {
      background-color: #e9ecef !important;
      /* Abu-abu yang lebih terlihat */
      color: #0d6efd !important;
      /* Teks jadi biru */
      padding-left: 20px !important;
      /* Efek geser kanan sedikit */
      transition: all 0.2s ease-in-out !important;
      /* Animasi halus */
      cursor: pointer;
    }

    /* --- CSS WARNA DROPDOWN COLVIS --- */

    /* 1. KONDISI DEFAULT ITEM (Belum aktif/disembunyikan) */
    div.dt-button-collection button.dt-button {
      background-color: white !important;
      color: #333 !important;
      padding: 8px 16px !important;
      border-bottom: 1px solid #f0f0f0 !important;
      /* Garis tipis pemisah */
    }

    /* 2. KONDISI SETELAH DIKLIK / AKTIF (Kolom Ditampilkan) -> JADI ABU-ABU */
    div.dt-button-collection button.dt-button.active {
      background-color: #6c757d !important;
      /* Abu-abu Secondary */
      color: white !important;
      box-shadow: none !important;
    }

    /* 3. KONDISI HOVER (Saat Mouse Menempel) -> JADI HIJAU */
    /* Berlaku untuk item aktif maupun tidak aktif */
    div.dt-button-collection button.dt-button:hover,
    div.dt-button-collection button.dt-button.active:hover {
      background-color: #198754 !important;
      /* Hijau Success */
      color: white !important;
      cursor: pointer;
      transition: background-color 0.2s ease;
      /* Transisi halus */
    }

    /* Matikan background abu-abu bawaan DataTables yang kadang muncul menutupi layar */
    .dt-button-background {
      display: none !important;
    }

    /* --- TAMBAHKAN CSS INI DI BAGIAN BAWAH STYLE --- */

    /* 1. Reset Bentuk Tombol DataTables agar KOTAK seperti Bootstrap */
    button.dt-button {
      border-radius: 4px !important;
      /* Sudut sedikit melengkung (Standar Bootstrap) */
      background-image: none !important;
      /* Hapus gradasi aneh */
      box-shadow: none !important;
      /* Hapus bayangan */
      border: 1px solid transparent !important;
      height: 31px !important;
      /* Tinggi sama dengan tombol lain */
      padding: 0.25rem 0.5rem !important;
      font-size: 0.875rem !important;
      display: inline-flex !important;
      align-items: center;
      gap: 5px;
    }

    /* 2. KHUSUS Tombol 'Kolom Ditampilkan' -> WARNA HIJAU (Success) */
    button.buttons-colvis {
      background-color: #198754 !important;
      /* Hijau Success */
      border-color: #198754 !important;
      color: white !important;
    }

    button.buttons-colvis:hover {
      background-color: #157347 !important;
      border-color: #146c43 !important;
    }

    /* 3. KHUSUS Tombol 'Show Rows' -> WARNA ABU (Secondary) */
    button.buttons-page-length {
      background-color: #6c757d !important;
      /* Abu Secondary */
      border-color: #6c757d !important;
      color: white !important;
    }

    button.buttons-page-length:hover {
      background-color: #5c636a !important;
      border-color: #565e64 !important;
    }

    .modal {
      z-index: 100050 !important;
      /* Angka sangat tinggi agar di atas header */
    }

    .modal-backdrop {
      z-index: 100040 !important;
      /* Tepat di bawah modal */
    }

    /* --- CSS PERBAIKAN Z-INDEX SWEETALERT --- */
    .swal2-container {
      z-index: 2147483647 !important;
    }
  </style>
</head>

<body>
  <?= $this->include('layout/header') ?>

  <?= $this->include('layout/sidebar_teknisi') ?>

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
          <div class="row g-2 align-items-end mb-4">

            <div class="col-md-3">
              <label class="form-label fw-bold">Tanggal</label>
              <input type="text"
                id="filter_daterange"
                class="form-control form-control-sm"
                placeholder="Cari Tanggal">
            </div>

            <div class="col-md-2">
              <label class="form-label fw-bold">Bulan</label>
              <select name="cetak_bulan" id="cetak_bulan" class="form-select form-select-sm">
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
              <select name="cetak_tahun" id="cetak_tahun" class="form-select form-select-sm text-center">
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
              <label class="form-label small fw-bold text-muted mb-1">&nbsp;</label>
              <button type="button" class="btn btn-primary btn-sm" id="btnCetakGlobal">
                <i class="fas fa-print"></i>
              </button>
            </div>

          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header bg-primary text-white">
          <h5 class="mb-0"><i class="fas fa-tools me-2"></i> LAPORAN KERUSAKAN</h5>
        </div>

        <div class="card-body justify-content-between align-items-center mb-3">
          <div id="customToolbar" class="dt-buttons btn-group mb-3">
            <button type="button" class="btn btn-success btn-sm" title="Tambah Laporan" onclick="openModalTambah()"><i class="fas fa-plus"></i> Tambah</button>
            <button class="btn btn-secondary btn-sm" title="Reload" id="btnReload">
              <i class="fa fa-undo me-1"></i> Reload
            </button>
            <button class="btn btn-outline-dark btn-sm" id="btnCopy" title="Copy">
              <i class="fas fa-copy"></i> Copy
            </button>
            <button class="btn btn-outline-success btn-sm" onclick="exportToExcel()"><i class="fas fa-file-excel"></i> Excel</button>
            <button type="button" class="btn btn-outline-danger btn-sm" id="btnExportPDF" onclick="exportToPDFKerusakan(this)">
              <i class="fas fa-file-pdf me-1"></i> PDF
            </button>

            <div class="dt-buttons btn-group">

              <!--<button class="btn btn-secondary btn-sm buttons-collection buttons-page-length"
                tabindex="0"
                aria-controls="table"
                type="button"
                aria-haspopup="true"
                aria-expanded="false">
                <span><i class="fas fa-list me-1"></i> Show 10 rows</span>
              </button>-->

            </div>
            <div id="search-container" class="d-flex align-items-center gap-2"></div>
          </div>

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
  </main>

  <!-- MODAL KERUSAKAN -->
  <div class="modal fade" id="ModalStatusKerusakan" tabindex="-1" aria-labelledby="ModalStatusKerusakanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="ModalStatusKerusakanLabel">Pilih Status Kerusakan</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form id="formStatusKerusakan">
            <div class="mb-3">
              <label for="statusSelectKerusakan" class="form-label fw-bold">Status:</label>
              <select id="statusSelectKerusakan" class="form-select">
                <option value="">-- Pilih Status --</option>
                <option value="Ringan">Ringan</option>
                <option value="Sedang">Sedang</option>
                <option value="Berat">Berat</option>
              </select>
            </div>
            <div class="text-end">
              <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL PERBAIKAN -->
  <div class="modal fade" id="ModalStatusPerbaikan" tabindex="-1" aria-labelledby="ModalStatusPerbaikanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="ModalStatusPerbaikanLabel">Pilih Status Perbaikan</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form id="formStatusPerbaikan">

            <!-- NOMOR LAPORAN -->
            <input type="hidden" id="laporanIdPerbaikan" name="nomor_laporan" value="">

            <!-- STATUS -->
            <div class="mb-3">
              <label for="statusSelectPerbaikan" class="form-label fw-bold">Status:</label>
              <select id="statusSelectPerbaikan" name="status_perbaikan" class="form-select" required>
                <option value="">-- Pilih Status --</option>
                <option value="Menunggu">Menunggu</option>
                <option value="Diperbaiki">Diperbaiki</option>
                <option value="Selesai">Selesai</option>
              </select>
            </div>

            <!-- KETERANGAN -->
            <div class="mb-3">
              <label for="inputkerusakan_keluhan" class="form-label fw-bold">Deskripsi / Keterangan</label>
              <textarea class="form-control" name="kerusakan_keluhan" rows="2" id="inputkerusakan_keluhan"
                placeholder="Jelaskan perbaikan/kerusakan..." required></textarea>
            </div>

            <!-- BUTTON -->
            <div class="text-end">
              <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
          </form>
        </div>
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
                <input type="text" name="nomor_laporan_view" id="tambah_nomor_laporan_view"
                  class="form-control bg-gray-100 text-primary fw-bold"
                  readonly />
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

                  <?php if (!empty($list_unit)): ?>
                    <?php foreach ($list_unit as $u): ?>
                      <option value="<?= esc($u['nama_unit']) ?>">
                        <?= esc($u['nama_unit']) ?>
                      </option>
                    <?php endforeach; ?>
                  <?php endif; ?>
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
                  <option value="">-- Pilih Status --</option>
                  <option value="Ringan">Ringan</option>
                  <option value="Sedang">Sedang</option>
                  <option value="Berat">Berat</option>
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
                  <option value="">--Pilih Media--</option>
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
                  <option value="">--Pilih--</option>
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

  <!-- FOOTER -->
  <!--<footer>
          <p class="m-0">© 2025 UPAPP POLSRI</p>
        </footer>-->

  <!-- DI AKTIFKAN -->
  <?php echo $this->include('layout/footer'); ?>

  <!-- SCRIPT -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

  <script>
    // Kita pindahkan tag PHP ke sini, agar file eksternal JS bisa membacanya
    const BASE_URL = "<?= base_url() ?>";
    const dataMasterLokasi = <?= json_encode($daftar_lokasi) ?>;
    const pelaksanaMap = <?= json_encode($map_pelaksana ?? []) ?>;
  </script>

  <script src="<?= base_url('teknisi/riwayat.js') ?>"></script>

</body>

</html>