<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>UPAPP | Dashboard</title>

  <!-- Bootstrap & FontAwesome -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

  <style>
    /*Kalau mau pakai footer yg satunya aktifkan ini*/
    .main-body-wrapper {
      display: flex;
      /* Ini kuncinya: menata anak-anaknya (sidebar & content) berdampingan */
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
      padding-left: 3%0px;
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
      /* Ini membuat content mengambil ruang kosong */
    }

    .content.full {
      margin-left: 0;
    }

    /* FOOTER */
    /* Kalau mau pakai footer satunya hapus ini 
    footer {
      background-color: #b3d9ff;
      text-align: center;
      padding: 10px;
      position: fixed;
      bottom: 0;
      left: 0;
      width: 100%;
      z-index: 900;
    }*/

    /* TOGGLE BUTTON */
    #toggleSidebar {
      border: none;
      background: transparent;
      font-size: 22px;
      cursor: pointer;
      margin-right: 10px;
    }

    /* CARD STYLE */
    .card {
      border: none;
      border-radius: 15px;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .card:hover {
      transform: translateY(-3px);
      box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
    }

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

    h5.fw-bold {
      color: #003366;
      font-weight: 700;
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

    .card {
      border-radius: 12px;
    }

    .btn-light {
      background-color: #f8f9fa;
      border: 1px solid #dee2e6;
    }

    .table th {
      font-weight: 600;
    }

    /* Styling khusus Card Tugas */
    .task-card {
      border: none;
      border-radius: 12px;
      transition: transform 0.2s;
      border-left: 5px solid #ccc;
      /* Default border */
    }

    .task-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    /* Warna Border Kiri berdasarkan Prioritas/Status */
    .border-start-danger {
      border-left-color: #dc3545 !important;
    }

    /* Rusak Berat */
    .border-start-warning {
      border-left-color: #ffc107 !important;
    }

    /* Rusak Sedang */
    .border-start-success {
      border-left-color: #198754 !important;
    }

    /* Selesai */
    .border-start-primary {
      border-left-color: #0d6efd !important;
    }

    /* Sedang Dikerjakan */

    .location-badge {
      background-color: #e9ecef;
      color: #495057;
      font-size: 0.85rem;
      padding: 5px 10px;
      border-radius: 8px;
      display: inline-block;
    }

    :root {
      --primary-color: #003366;
      --bg-color: #f3f6f9;
      --card-radius: 12px;
    }

    body {
      background-color: var(--bg-color);
      font-family: "Segoe UI", sans-serif;
      color: #333;
    }

    /* === 1. CARD OVERVIEW STYLE (ADMIN-LIKE) === */
    .admin-card {
      transition: all 0.3s ease;
      cursor: pointer;
      border-radius: 12px !important;
      border: none !important;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05) !important;
    }

    .admin-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15) !important;
    }

    .admin-icon-shape {
      width: 48px;
      height: 48px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      font-size: 1.2rem;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .fw-bolder {
      font-weight: 700 !important;
    }

    /* Warna Status Card Overview */
    .card-tugas-baru {
      background-color: #fef0f0 !important;
      border-left: 5px solid #dc3545 !important;
    }

    .card-tugas-baru .text-color {
      color: #dc3545 !important;
    }

    .card-tugas-baru .admin-icon-shape {
      background: #dc3545;
      color: #fff;
    }

    .card-proses {
      background-color: #e0f2fe !important;
      border-left: 5px solid #0d6efd !important;
    }

    .card-proses .text-color {
      color: #0d6efd !important;
    }

    .card-proses .admin-icon-shape {
      background: #0d6efd;
      color: #fff;
    }

    .card-pending-ov {
      background-color: #fff8e1 !important;
      border-left: 5px solid #ffc107 !important;
    }

    .card-pending-ov .text-color {
      color: #ffc107 !important;
    }

    .card-pending-ov .admin-icon-shape {
      background: #ffc107;
      color: #fff;
    }

    .card-riwayat-ov {
      background-color: #f8f9fa !important;
      border-left: 5px solid #6c757d !important;
    }

    .card-riwayat-ov .text-color {
      color: #6c757d !important;
    }

    .card-riwayat-ov .admin-icon-shape {
      background: #6c757d;
      color: #fff;
    }


    /* === 2. CARD DETAIL ANTRIAN KECIL (ANTRIAN-TASK-CARD) === */
    .antrian-task-card {
      border: none;
      border-radius: var(--card-radius);
      background: #fff;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
      transition: all 0.3s ease;
      position: relative;
      cursor: pointer;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      min-height: 250px;
      width: 100%;
      height: 100%;
    }

    .antrian-task-card::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 6px;
    }

    /* Warna Strip (Mengikuti Status Kerusakan) */
    .antrian-card-berat::before {
      background: linear-gradient(90deg, #dc3545, #ff6b6b);
    }

    /* Merah */
    .antrian-card-sedang::before {
      background: linear-gradient(90deg, #ffc107, #ffdb72);
    }

    /* Kuning */
    .antrian-card-ringan::before {
      background: linear-gradient(90deg, #198754, #20c997);
    }

    /* Hijau */
    .antrian-card-komplain::before {
      background: linear-gradient(90deg, #dc3545, #fd7e14);
    }

    .antrian-card-pending-detail::before {
      background: linear-gradient(90deg, #fd7e14, #ffc107);
    }

    .antrian-card-riwayat-validasi::before {
      background: linear-gradient(90deg, #0d6efd, #5bc0de);
    }

    .antrian-card-riwayat-konfirm::before {
      background: linear-gradient(90deg, #198754, #20c997);
    }

    .antrian-card-riwayat-arsip::before {
      background: linear-gradient(90deg, #6c757d, #adb5bd);
    }

    .antrian-card-rusak::before {
      background: linear-gradient(90deg, #212529, #495057);
    }

    /* Isi Card Detail */
    .antrian-card-body {
      padding: 1.5rem 1.5rem 1rem 1.5rem;
      flex: 1;
    }

    .card-footer-action {
      padding: 0 1.5rem 1.5rem 1.5rem;
    }

    .schedule-strip {
      background-color: #f0f7ff;
      color: #004085;
      padding: 6px 12px;
      border-radius: 8px;
      font-size: 0.8rem;
      font-weight: 600;
      display: flex;
      align-items: center;
      margin-bottom: 12px;
      border: 1px dashed #b8daff;
    }

    .badge-lokasi {
      background: #f8f9fa;
      color: #666;
      padding: 6px 12px;
      border-radius: 8px;
      font-size: 0.85rem;
      font-weight: 600;
      border: 1px solid #e9ecef;
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }

    .bg-komplain-subtle {
      background-color: #fff5f5;
      border: 1px dashed #dc3545;
    }

    /* Footer Riwayat Styles */
    .card-footer-riwayat {
      font-size: 0.8rem;
      font-weight: 600;
      padding: 1rem 1.5rem;
      border: 0;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .bg-arsip-footer {
      background-color: #f3f4f6 !important;
      border-top: 1px solid #e5e7eb !important;
    }

    /* Warna Konfirmasi Pelapor (Kuning/Oranye) */
    .bg-konfirm-footer {
      background-color: #fff8e1 !important;
    }

    .text-konfirm {
      color: #b45309;
    }

    /* Warna Validasi Admin (Biru Primer) */
    .bg-primary-subtle {
      background-color: #e0f2fe !important;
    }

    .text-primary-validate {
      color: #0d6efd;
    }

    .text-arsip {
      color: #4b5363;
    }

    /* Modal Table Styling */
    .table-detail th {
      width: 40%;
      color: #6c757d;
      font-weight: 600;
      font-size: 0.85rem;
      padding: 10px 0 10px 15px;
    }

    .table-detail td {
      font-weight: 600;
      color: #333;
      font-size: 0.9rem;
      padding: 10px 0;
    }

    .modal,
    .modal {
      /* Set nilai lebih tinggi dari z-index header (1100) */
      z-index: 1101 !important;
    }

    /* --- CSS TAMBAHAN UNTUK SWEETALERT --- */
    .swal2-container {
      z-index: 9999 !important;
      /* Angka tinggi agar selalu di paling depan */
    }
  </style>
</head>

<body>
  <!-- HEADER -->
  <?= $this->include('layout/header') ?>

  <!-- SIDEBAR -->
  <?= $this->include('layout/sidebar_teknisi') ?>

  <div class="content" id="mainContent">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h3 class="fw-bold text-dark mb-1">Jadwal Perbaikan Teknisi</h3>
      </div>
    </div>

    <div class="container-fluid py-4">
      <div class="row g-4 mb-5">
        <div class="col-sm-6 col-lg-3">
          <div class="card admin-card card-tugas-baru" onclick="loadAntrian('baru')">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start">
                <div><small class="text-uppercase text-color fw-bold">Tugas Baru</small>
                  <h4 class="fw-bolder text-dark mb-0">
                    <span id="count_baru"><i class="fas fa-spinner fa-spin fa-sm"></i></span> Laporan
                  </h4>
                </div>
                <div class="admin-icon-shape"><i class="fas fa-hammer"></i></div>
              </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0"><small
                class="text-color fw-bold cursor-pointer">Kerjakan<i class="fas fa-arrow-right ms-1"></i></small></div>
          </div>
        </div>

        <div class="col-sm-6 col-lg-3">
          <div class="card admin-card card-proses" onclick="loadAntrian('proses')">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start">
                <div><small class="text-uppercase text-color fw-bold">Dikerjakan</small>
                  <h4 class="fw-bolder text-dark mb-0">
                    <span id="count_proses"><i class="fas fa-spinner fa-spin fa-sm"></i></span> Laporan
                  </h4>
                </div>
                <div class="admin-icon-shape"><i class="fas fa-tools"></i></div>
              </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0"><small
                class="text-color fw-bold cursor-pointer">Tindak Lanjut Tugas <i
                  class="fas fa-arrow-right ms-1"></i></small></div>
          </div>
        </div>

        <div class="col-sm-6 col-lg-3">
          <div class="card admin-card card-pending-ov" onclick="loadAntrian('pending')">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start">
                <div><small class="text-uppercase text-color fw-bold">Pending</small>
                  <h4 class="fw-bolder text-dark mb-0">
                    <span id="count_pending"><i class="fas fa-spinner fa-spin fa-sm"></i></span> Laporan
                  </h4>
                </div>
                <div class="admin-icon-shape"><i class="fas fa-pause-circle"></i></div>
              </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0"><small
                class="text-color fw-bold cursor-pointer">Lanjutkan Pekerjaan <i
                  class="fas fa-arrow-right ms-1"></i></small></div>
          </div>
        </div>

        <div class="col-sm-6 col-lg-3">
          <div class="card admin-card card-riwayat-ov" onclick="loadAntrian('riwayat')">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start">
                <div><small class="text-uppercase text-color fw-bold">Riwayat</small>
                  <h4 class="fw-bolder text-dark mb-0">
                    <span id="count_riwayat"><i class="fas fa-spinner fa-spin fa-sm"></i></span> Laporan
                  </h4>
                </div>
                <div class="admin-icon-shape"><i class="fas fa-archive"></i></div>
              </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0"><small class="text-color fw-bold cursor-pointer">Lihat
                Arsip Tugas <i class="fas fa-arrow-right ms-1"></i></small></div>
          </div>
        </div>
      </div>
      <h4 class="fw-bold mb-3 mt-4" id="antrian_header">Daftar Tugas Baru</h4>
      <div id="antrian_dynamic_content" class="row g-4">
        <div class="alert alert-info col-12">Klik Salah Satu Card di atas untuk memuat informasi mengenai laporan.</div>
      </div>
    </div>

    <!-- Filter Jadwal 
    <div class="card shadow-sm mb-4">
      <div class="card-body">
        <form id="filterForm" class="row g-3 align-items-end">
          <div class="col-md-3">
            <label class="form-label fw-bold">Nama Alat</label>
            <input type="text" id="filterNamaAlat" class="form-control" placeholder="Cari nama alat">
          </div>
          <div class="col-md-3">
            <label class="form-label fw-bold">Tanggal</label>
            <input type="date" id="filterTanggal" class="form-control">
          </div>
          <div class="col-md-3">
            <label class="form-label fw-bold">Status</label>
            <select id="filterStatus" class="form-select">
              <option value="">Sedang</option>
              <option value="Menunggu">Ringan</option>
              <option value="Dalam Proses">Berat</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label fw-bold">Pelaksana</label>
            <input type="text" id="filterPelaksana" class="form-control" placeholder="Cari Pelaksana">
          </div>
          <div class="col-12 text-end">
            <button type="button" class="btn btn-primary mt-2" onclick="applyFilter()">Cari</button>
            <button type="button" class="btn btn-outline-secondary mt-2" onclick="resetFilter()">Reset</button>
          </div>
        </form>
      </div>
    </div>-->

    <!-- List Jadwal 
    <div class="card shadow-sm mb-5">
      <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fa fa-list me-2"></i> List Jadwal Perbaikan</h5>
      </div>
      <div class="card-body table-responsive">
        <table id="jadwalTable" class="table table-bordered table-striped text-center">
          <thead>
            <tr>
              <th>NO LAPORAN</th>
              <th>TANGGAL LAPORAN</th>
              <th>TANGGAL PERBAIKAN</th>
              <th>NAMA ALAT</th>
              <th>STATUS KERUSAKAN</th>
              <th>LOKASI</th>
              <th>JURUSAN/UNIT</th>
              <th>PELAKSANA</th> 
            </tr>
          </thead>
          <tbody> -->

    <?php
    /*
<!-- Pastikan Anda berada di file teknisi/jadwal.php -->
          <tbody id="tableBody">
            <?php if (!empty($jadwalList) && is_array($jadwalList)): ?>
              <!-- WAJIB: GANTI $jadwalList di dalam loop menjadi $jadwal -->
              <?php foreach ($jadwalList as $jadwal): ?>
                <tr>
                  <!-- Akses data HARUS menggunakan $jadwal -->
                  <td><?= esc($jadwal['nomor_laporan'] ?? '-') ?></td>

                  <td><?= esc($jadwal['tanggal_laporan'] ?? '-') ?></td>

                  <td>
                    <?php
                    // Ambil nilai tanggal perbaikan, default ke string kosong jika null/undefined
                    $tanggal_perbaikan = $jadwal['tanggal_perbaikan'] ?? '';

                    // Tentukan kondisi status (sudah ditentukan atau belum)
                    if (empty($tanggal_perbaikan) || $tanggal_perbaikan === '-') {
                      // KONDISI: Belum Ditentukan (Primary/Biru)
                      $badge_class = 'bg-primary text-white';
                      $text_output = 'Belum ditentukan';
                    } else {
                      // KONDISI: Sudah Ditentukan (Success/Hijau)
                      $badge_class = 'bg-success text-white';
                      // Gunakan fungsi esc() pada tanggal yang valid untuk keamanan
                      $text_output = esc($tanggal_perbaikan);
                    }
                    ?>

                    <span class="badge <?= $badge_class ?>">
                      <?= $text_output ?>
                    </span>
                  </td>

                  <td><?= esc($jadwal['nama_alat'] ?? '-') ?></td>

                  <!-- STATUS KERUSAKAN -->
                  <td>
                    <?php
                    $statusKerusakan = $jadwal['status_kerusakan'] ?? '-';
                    $badgeClass = '';
                    $badgeText = esc($statusKerusakan); // Tetap gunakan esc() untuk teks di dalam badge

                    switch ($statusKerusakan) {
                      case 'Ringan':
                        $badgeClass = 'bg-success';
                        break;
                      case 'Sedang':
                        $badgeClass = 'bg-warning text-dark'; // Biasanya text-dark untuk warning
                        break;
                      case 'Berat':
                        $badgeClass = 'bg-danger'; // info untuk sedang dalam proses
                        break;
                      default:
                        $badgeClass = 'bg-secondary'; // Default jika status tidak dikenali atau '-'
                        $badgeText = 'Belum dicek'; // Jika status kosong, badge-nya juga '-'
                        break;
                    }
                    ?>
                    <span class="badge <?= $badgeClass ?>"><?= $badgeText ?></span>
                  </td>

                  <td><?= esc($jadwal['lokasi_alat'] ?? '-') ?></td>

                  <td><?= esc($jadwal['unit'] ?? '-') ?></td>

                  <td><?= esc($jadwal['pelaksana'] ?? '-') ?></td>

                  <td style="display:none;">
                    <button class="btn btn-sm btn-info"><i class="fas fa-edit"></i></button>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr class="empty-row">
                <td colspan="11" class="text-center text-muted py-3">Tidak ada data laporan.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
*/
    ?>

    <!-- Info & pagination 
    <div class="d-flex justify-content-between align-items-center mt-3">
      <div id="table_info" class="text-muted small">Showing 0 to 0 of 0 entries</div>-->

    <!-- CONTAINER PAGINATION -->
    <div class="dataTables_paginate paging_simple_numbers" id="table_paginate">
      <ul class="pagination mb-0">
        <!-- Tombol akan digenerate JS di sini -->
      </ul>
    </div>
  </div>


  <!--MODAL-->
  <div class="modal fade" id="modalDetailLaporan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
      <div class="modal-content border-0 shadow-lg">
        <div class="modal-header bg-primary text-white border-0">
          <h5 class="modal-title text-white fw-bold"><i class="fas fa-search me-2"></i> Detail Lengkap Laporan <span
              id="detail_modal_id"></span></h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body p-4">
          <div class="row">
            <div class="col-md-6 border-end text-center">

              <div class="mb-4">
                <h5 class="fw-bolder text-dark mb-1" id="detail_modal_alat_display">
                  Nama Alat
                </h5>
                <small class="text-muted d-block">INV: <span id="detail_modal_inv_display">-</span></small>
              </div>

              <div class="text-start">
                <h6 class="fw-bold text-dark mb-2 small text-uppercase">
                  <i class="fas fa-camera me-1"></i> Bukti Foto
                </h6>

                <div class="bg-light p-3 rounded border text-center mb-3">

                  <div id="detail_modal_foto_container" class="row g-2"></div>

                  <div id="detail_modal_no_foto" class="py-4" style="display: none;">
                    <div class="text-muted opacity-50 mb-2">
                      <i class="fas fa-image fa-3x"></i>
                    </div>
                    <h6 class="fw-bold text-muted small">Tidak Ada Bukti Foto</h6>
                    <p class="small text-muted mb-0" style="font-size: 0.75rem;">
                      Cek Link Pendukung di bawah.
                    </p>
                  </div>

                </div>

                <div id="container_link_pendukung" style="display: none;">
                  <h6 class="fw-bold text-dark mb-2 small text-uppercase">
                    <i class="fas fa-link me-1"></i> File / Link Pendukung
                  </h6>
                  <a href="#" target="_blank" id="btn_link_pendukung"
                    class="btn btn-outline-primary btn-sm w-100 fw-bold shadow-sm">
                    <i class="fas fa-external-link-alt me-2"></i> Buka Link Pendukung
                  </a>
                </div>
              </div>

              <div class="modal fade" id="modalFotoPreview" tabindex="-1">

                <div class="modal-dialog modal-dialog-centered" style="max-width: 95%;">

                  <div class="modal-content p-0 border-0 bg-transparent">
                    <div class="modal-body text-center position-relative p-0">

                      <button type="button"
                        class="btn-close btn-close-white position-absolute top-0 end-0 m-4 p-2 shadow-none"
                        data-bs-dismiss="modal" aria-label="Close"
                        style="z-index: 10; opacity: 1; background-color: rgba(0,0,0,0.5); border-radius: 50%;"></button>

                      <img id="fotoPreviewZoom" src="" class="img-fluid rounded shadow-lg" style="
                        max-height: 90vh;  /* Tinggi maksimal 90% layar */
                        width: auto;       /* Lebar mengikuti proporsi asli */
                        max-width: 100%;   /* Agar tidak melebihi lebar layar */
                        object-fit: contain;
                        margin: 0 auto;
                        display: block;    /* Memastikan margin auto bekerja */
                      ">
                    </div>
                  </div>
                </div>
              </div>
              </tr>

              <!-- ULASAN PELAPOR -->
              <div class="text-start">
                <h6 class="fw-bold text-dark mb-2 small text-uppercase mt-4">
                  <i class="fas fa-comment-dots me-1"></i>
                  Ulasan Pelapor
                </h6>

                <div class="alert alert-light border shadow-sm">

                  <div class="mb-3">
                    <small class="text-muted d-block mb-1">
                      Rating
                    </small>

                    <div id="detail_modal_rating" class="text-warning fs-5">
                      <i class="far fa-star"></i>
                      <i class="far fa-star"></i>
                      <i class="far fa-star"></i>
                      <i class="far fa-star"></i>
                      <i class="far fa-star"></i>
                    </div>
                  </div>

                  <hr>

                  <div>
                    <small class="text-muted d-block mb-1">
                      Ulasan
                    </small>

                    <p id="detail_modal_ulasan" class="mb-0 fst-italic text-secondary">
                      -
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <h6 class="fw-bold text-dark mb-0"><i class="fas fa-info-circle me-1"></i> Detail Laporan</h6>
                <div>
                  <span class="small text-muted me-2">STATUS KERUSAKAN:</span>
                  <span class="badge fs-6" id="detail_modal_kerusakan_display">Berat</span>
                </div>
              </div>

              <table class="table table-sm table-striped small">
                <tbody>
                  <tr>
                    <td class="text-muted fw-bold" style="width: 40%;">NOMOR LAPORAN</td>
                    <td class="fw-bold" id="detail_modal_lpr_id">LPR-009</td>
                  </tr>
                  <tr>
                    <td class="text-muted fw-bold">TANGGAL LAPORAN</td>
                    <td id="detail_modal_tgl">-</td>
                  </tr>
                  <tr>
                    <td class="text-muted fw-bold">TANGGAL PERBAIKAN</td>
                    <td class="fw-bold text-primary" id="detail_modal_tgl_perbaikan">-</td>
                  </tr>
                  <tr>
                    <td class="text-muted fw-bold">NOMOR INVENTARIS</td>
                    <td id="detail_modal_inv">-</td>
                  </tr>
                  <tr>
                    <td class="text-muted fw-bold">LOKASI ALAT</td>
                    <td id="detail_modal_lokasi">-</td>
                  </tr>
                  <tr>
                    <td class="text-muted fw-bold">JURUSAN / UNIT</td>
                    <td id="detail_modal_unit">-</td>
                  </tr>
                  <tr>
                    <td class="text-muted fw-bold">PELAPOR</td>
                    <td id="detail_modal_pelapor">-</td>
                  </tr>
                  <tr>
                    <td class="text-muted fw-bold">TEKNISI (PELAKSANA)</td>
                    <td class="fw-bold text-success" id="detail_modal_teknisi">-</td>
                  </tr>
                </tbody>
              </table>

              <td>
                <div class="d-flex gap-2">
                  <select class="form-select form-select-sm border-secondary fw-bold" id="input_kerusakan">
                    <option value="">-- Pilih --</option>
                    <option value="Ringan">Ringan</option>
                    <option value="Sedang">Sedang</option>
                    <option value="Berat">Berat</option>
                  </select>

                  <button class="btn btn-primary btn-sm fw-bold" onclick="updateStatusKerusakan()">
                    <i class="fas fa-paper-plane"></i> Kirim
                  </button>
                </div>
              </td>

              <h6 class="fw-bold text-dark mt-3 mb-2"><i class="fas fa-comment-dots me-1"></i> Kerusakan/Keluhan
                Lengkap:</h6>
              <div class="alert alert-light border small">
                <p class="mb-0 fst-italic" id="detail_modal_keluhan">Kerusakan/Keluhan lengkap tertera di sini.</p>
              </div>

              <!--<div id="container_pesan_komplain" style="display: none;" class="mt-3 fade-in">
                <h6 class="fw-bold text-danger mb-2">
                  <i class="fas fa-exclamation-circle me-1"></i> Pesan Komplain Pelapor:
                </h6>
                <div class="alert alert-danger bg-danger-subtle text-danger border-danger small shadow-sm">
                  <p class="mb-0 fw-bold" id="detail_modal_pesan_komplain">-</p>
                </div>
              </div>-->

              <div id="container_alasan_rusak" style="display: none;" class="mt-3 fade-in">
                <h6 class="fw-bold text-danger mb-2">
                  <i class="fas fa-ban me-1"></i> Alasan Tidak Bisa Diperbaiki / Diagnosa:
                </h6>
                <div class="alert alert-danger bg-danger-subtle text-danger border-danger small shadow-sm">
                  <p class="mb-0 fw-bold" id="detail_modal_alasan_rusak">-</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer border-0">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modalPending" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow-lg">
        <div class="modal-header bg-warning-subtle border-0">
          <h5 class="modal-title fw-bold text-dark">Tunda Pekerjaan</h5><button type="button" class="btn-close"
            data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-4"><label class="form-label fw-bold">Alasan Penundaan (Wajib)</label><textarea
            class="form-control" id="inputAlasanPending" rows="3"></textarea></div>
        <div class="modal-footer border-0"><button class="btn btn-warning fw-bold px-4"
            onclick="submitPending()">Simpan</button></div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modalSelesai" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow-lg">
        <div class="modal-header bg-success text-white border-0">
          <h5 class="modal-title text-white fw-bold">Laporan Selesai</h5><button type="button"
            class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-4">
          <p class="text-muted mb-3">Upload bukti foto hasil perbaikan.</p><input type="file" id="inputFileSelesai"
            class="form-control" multiple accept="image/*">
        </div>
        <div class="modal-body p-4"><label class="form-label text-muted">Uraian Pekerjaan</label><textarea
            class="form-control" id="inputUraianPekerjaa" rows="3"></textarea></div>
        <div class="modal-footer border-0"><button class="btn btn-success fw-bold px-4"
            onclick="submitSelesai()">Kirim</button></div>
      </div>
    </div>
  </div>

  <div id="container_ulasan_pelapor" style="display: none;" class="mt-4 fade-in border-top pt-3">
    <h6 class="fw-bold text-dark mb-2">
      <i class="fas fa-star text-warning me-1"></i> Penilaian & Ulasan Pelapor:
    </h6>
    <div class="d-flex align-items-center mb-2" id="detail_modal_bintang">
    </div>
    <div class="alert alert-success bg-success-subtle text-dark border-success small shadow-sm">
      <p class="mb-0 fst-italic" id="detail_modal_ulasan_teks">...</p>
    </div>
  </div>

  <!-- FOOTER -->
  <!--<footer>
    <p class="m-0">© 2025 UPAPP POLSRI</p>
  </footer>-->

  <!-- DI AKTIFKAN -->
  <?php echo $this->include('layout/footer'); ?>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

  <script>
    // Kita simpan base_url ke dalam variabel konstan agar bisa dibaca oleh file eksternal .js
    const BASE_URL = "<?= base_url() ?>";
  </script>

  <script src="<?= base_url('teknisi/jadwal.js') ?>"></script>

</body>

</html>