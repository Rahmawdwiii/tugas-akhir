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

    .modal-sm {
      max-width: 355px;
    }

    #imagePreviewModal .modal-dialog {
      margin-top: 80px;
      /* 60px (tinggi header) + 20px (spasi) */
    }

    /* --- CARD OVERVIEW STYLES (Gaya Seragam) --- */
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

    .card-total {
      background-color: #f0f7ff !important;
      border-left: 5px solid #0d6efd !important;
    }

    .card-total .text-color {
      color: #0d6efd !important;
    }

    .card-total .admin-icon-shape {
      background: #0d6efd;
      color: #fff;
    }

    .card-proses-pl {
      background-color: #fff8e1 !important;
      border-left: 5px solid #ffc107 !important;
    }

    .card-proses-pl .text-color {
      color: #ffc107 !important;
    }

    .card-proses-pl .admin-icon-shape {
      background: #ffc107;
      color: #fff;
    }

    .card-validasi-pl {
      background-color: #fef0f0 !important;
      border-left: 5px solid #dc3545 !important;
    }

    .card-validasi-pl .text-color {
      color: #dc3545 !important;
    }

    .card-validasi-pl .admin-icon-shape {
      background: #dc3545;
      color: #fff;
    }

    .card-arsip-pl {
      background-color: #f0fff0 !important;
      border-left: 5px solid #198754 !important;
    }

    .card-arsip-pl .text-color {
      color: #198754 !important;
    }

    .card-arsip-pl .admin-icon-shape {
      background: #198754;
      color: #fff;
    }

    /* --- CARD DETAIL LAPORAN (E-COMMERCE STYLE) --- */
    .laporan-card {
      border: 1px solid #e9ecef;
      border-radius: 10px;
      padding: 15px;
      cursor: pointer;
      transition: all 0.2s;
      display: flex;
      align-items: center;
      justify-content: space-between;
      background: #fff;
    }

    .laporan-card:hover {
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      border-color: #0d6efd;
    }

    .laporan-card .status-badge {
      font-size: 0.8rem;
      font-weight: 600;
    }

    .laporan-card .detail-text {
      font-size: 0.85rem;
      color: #6c757d;
    }

    .laporan-card .icon-progress {
      font-size: 1.5rem;
      color: #0d6efd;
    }

    /* --- CSS TIMELINE (Dipindahkan ke Modal) --- */
    .tracking-timeline {
      position: relative;
      padding: 10px 0 30px 0;
      list-style: none;
    }

    .tracking-timeline::before {
      content: '';
      position: absolute;
      top: 0;
      bottom: 0;
      left: 24px;
      width: 3px;
      background: #e9ecef;
      z-index: 0;
    }

    .timeline-item {
      position: relative;
      display: flex;
      gap: 25px;
      margin-bottom: 35px;
      z-index: 1;
    }

    .timeline-item:last-child {
      margin-bottom: 0;
    }

    .timeline-icon-box {
      width: 48px;
      height: 48px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.2rem;
      background: #fff;
      border: 4px solid #fff;
      box-shadow: 0 0 0 3px #e9ecef;
      color: #adb5bd;
      flex-shrink: 0;
      transition: all 0.3s ease;
      position: relative;
      z-index: 2;
    }

    .timeline-content {
      flex-grow: 1;
      background: #fff;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
      border-left: 5px solid transparent;
      position: relative;
    }

    .timeline-content::before {
      content: '';
      position: absolute;
      top: 20px;
      left: -8px;
      width: 16px;
      height: 16px;
      background: #fff;
      transform: rotate(45deg);
      box-shadow: -2px 2px 5px rgba(0, 0, 0, 0.02);
    }

    .timeline-item.completed .timeline-icon-box {
      background: #0d6efd;
      box-shadow: 0 0 0 3px #0d6efd;
      color: #fff;
    }

    .timeline-item.completed .timeline-content {
      border-left-color: #0d6efd;
    }

    .timeline-item.active .timeline-icon-box {
      background: #fff;
      border-color: #0d6efd;
      color: #0d6efd;
      box-shadow: 0 0 0 3px #0d6efd;
      animation: pulseBlue 2s infinite;
    }

    .timeline-item.active .timeline-content {
      border-left-color: #0d6efd;
      background: #f8faff;
    }

    .timeline-item.attention .timeline-icon-box {
      background: #ffc107;
      box-shadow: 0 0 0 3px #ffc107;
      color: #212529;
      animation: pulseWarning 2s infinite;
    }

    .timeline-item.attention .timeline-content {
      border-left-color: #ffc107;
      background: #fff9e6;
    }

    .timeline-item.completed::after {
      content: '';
      position: absolute;
      top: 48px;
      left: 24px;
      bottom: -35px;
      width: 3px;
      background: #0d6efd;
      z-index: 0;
    }

    .timeline-item:last-child::after {
      display: none;
    }

    @keyframes pulseBlue {
      0% {
        box-shadow: 0 0 0 0 rgba(13, 110, 253, 0.7);
      }

      70% {
        box-shadow: 0 0 0 10px rgba(13, 110, 253, 0);
      }

      100% {
        box-shadow: 0 0 0 0 rgba(13, 110, 253, 0);
      }
    }

    @keyframes pulseWarning {
      0% {
        box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7);
      }

      70% {
        box-shadow: 0 0 0 10px rgba(255, 193, 7, 0);
      }

      100% {
        box-shadow: 0 0 0 0 rgba(255, 193, 7, 0);
      }
    }

    .modal,
    .modal {
      /* Set nilai lebih tinggi dari z-index header (1100) */
      z-index: 1101 !important;
    }

    /* --- CSS Khusus Rating Bintang yang Bisa Diklik (Perbarui jika sudah ada) --- */

    .rating-css .star-icon i {
      color: #ccc;
      /* Warna Default: Abu-abu */
      transition: color 0.2s;
    }

    /* 1. Prioritas Tertinggi: Bintang yang sudah diklik (selected) */
    .rating-css .star-icon i.selected {
      color: #ffc107 !important;
      /* WAJIB menggunakan !important atau urutan yang lebih spesifik */
    }

    /* 2. Efek Hover: Bintang yang sedang diarahkan kursor */
    /* Kita memastikan .hovered juga berwarna kuning, tetapi tidak perlu !important jika .selected lebih spesifik */
    .rating-css .star-icon i.hovered {
      color: #ffc107;
    }

    button,
    .btn,
    a,
    [onclick],
    /* Menargetkan elemen apa pun yang memiliki atribut onclick */
    .admin-card,
    /* Menargetkan Card Overview Admin/Teknisi Anda */
    .laporan-card {
      /* Menargetkan Card Laporan di Dashboard Pelapor */
      cursor: pointer;
    }
  </style>
</head>

<body>
  <!-- HEADER -->
  <?= $this->include('layout/header') ?>

  <!-- SIDEBAR -->
  <?= $this->include('layout/sidebar_pelapor') ?>

  <!-- CONTENT DASHBOARD TEKNISI -->
  <main class="content" id="mainContent">
    <!-- <div class="container-fluid">
      <div class="row g-3">
        <div class="col-md-3 col-6 fade-in">
          <div class="card shadow-sm p-3 bg-primary">
            <h6>Laporan Kerusakan</h6>
            <h3>3,578</h3>
          </div>
        </div>
        <div class="col-md-3 col-6 fade-in" style="animation-delay: .1s;">
          <div class="card shadow-sm p-3 bg-success">
            <h6>Selesai Diperbaiki</h6>
            <h3>3,138</h3>
          </div>
        </div>
        <div class="col-md-3 col-6 fade-in" style="animation-delay: .2s;">
          <div class="card shadow-sm p-3 bg-warning">
            <h6>Laporan Peminjaman</h6>
            <h3>984</h3>
          </div>
        </div>
        <div class="col-md-3 col-6 fade-in" style="animation-delay: .3s;">
          <div class="card shadow-sm p-3 bg-danger">
            <h6>Total Barang Rusak</h6>
            <h3>256</h3>
          </div>
        </div>
      </div>

      <div class="mt-4 fade-in" style="animation-delay: .4s;">
        <div class="card border-0 shadow-sm p-4">
          <h5 class="fw-bold mb-3 text-primary">Statistik Peminjaman & Kerusakan</h5>
          <p class="text-muted">Contoh tampilan progres seperti di Ace Master</p>-->
    <!-- <div class="progress mb-3">
            <div class="progress-bar bg-success" role="progressbar" style="width: 70%">70% Barang Aktif</div>
          </div>
          <div class="progress mb-3">
            <div class="progress-bar bg-warning" role="progressbar" style="width: 20%">20% Dipinjam</div>
          </div>
          <div class="progress">
            <div class="progress-bar bg-danger" role="progressbar" style="width: 10%">10% Rusak</div>
          </div>
        </div>
      </div> 
    </div>    -->

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h3 class="fw-bold text-dark mb-1">Dashboard Pelapor</h3>
        <p class="text-muted mb-0">
          Hari ini mau laporin apa ya,
          <span class="text-primary fw-bold">
            Pak <?= session()->get('nama') ? esc(session()->get('nama')) : 'Pelapor' ?>
            ?</span>
        </p>
      </div>
      <?php $isOnline = session()->get('is_online') ?? 0; ?>
    </div>

    <div class="row g-4 mb-5">
      <div class="col-sm-6 col-lg-3">
        <div class="card admin-card card-total" onclick="loadCardList('all')">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
              <div><small class="text-uppercase text-color fw-bold">Laporan Aktif</small>
                <h4 class="fw-bolder text-dark mb-0"><span id="counter_all">0</span> Laporan</h4>
              </div>
              <div class="admin-icon-shape"><i class="fas fa-file-alt"></i></div>
            </div>
          </div>
          <div class="card-footer bg-transparent border-0 pt-0"><small class="text-color fw-bold cursor-pointer">Lihat
              Semua Laporan <i class="fas fa-arrow-right ms-1"></i></small></div>
        </div>
      </div>

      <div class="col-sm-6 col-lg-3">
        <div class="card admin-card card-proses-pl" onclick="loadCardList('proses')">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
              <div><small class="text-uppercase text-color fw-bold">Sedang Proses</small>
                <h4 class="fw-bolder text-dark mb-0"><span id="counter_proses">0</span> Laporan</h4>
              </div>
              <div class="admin-icon-shape"><i class="fas fa-tools"></i></div>
            </div>
          </div>
          <div class="card-footer bg-transparent border-0 pt-0"><small class="text-color fw-bold cursor-pointer">Lacak
              Status Aktif <i class="fas fa-arrow-right ms-1"></i></small></div>
        </div>
      </div>

      <div class="col-sm-6 col-lg-3">
        <div class="card admin-card card-validasi-pl" onclick="loadCardList('validasi')">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
              <div><small class="text-uppercase text-color fw-bold">Perlu Validasi</small>
                <h4 class="fw-bolder text-dark mb-0"><span id="counter_validasi">0</span> Laporan</h4>
              </div>
              <div class="admin-icon-shape"><i class="fas fa-clipboard-check"></i></div>
            </div>
          </div>
          <div class="card-footer bg-transparent border-0 pt-0"><small class="text-color fw-bold cursor-pointer">Segera
              Beri Rating <i class="fas fa-arrow-right ms-1"></i></small></div>
        </div>
      </div>

      <div class="col-sm-6 col-lg-3">
        <div class="card admin-card card-arsip-pl" onclick="loadCardList('selesai')">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
              <div><small class="text-uppercase text-color fw-bold">Laporan Selesai</small>
                <h4 class="fw-bolder text-dark mb-0"><span id="counter_selesai">0</span> Laporan</h4>
              </div>
              <div class="admin-icon-shape"><i class="fas fa-check-circle"></i></div>
            </div>
          </div>
          <div class="card-footer bg-transparent border-0 pt-0"><small class="text-color fw-bold cursor-pointer">Lihat
              Riwayat <i class="fas fa-arrow-right ms-1"></i></small></div>
        </div>
      </div>
    </div>
    <div class="card shadow-sm border-0">
      <div class="card-body">
        <h5 class="fw-bold mb-4" id="content_header">
          <i class="fas fa-list-alt me-2"></i> Daftar Laporan
        </h5>

        <div id="dynamic_content" class="row g-3">
          <div id="antrian_dynamic_content" class="d-grid gap-3">
            <div class="alert alert-info">
              Klik salah satu Card di atas untuk memfilter dan menampilkan daftar
              laporan antrian.
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- MODAL -->
  <div class="modal fade" id="modalTimeline" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
      <div class="modal-content border-0 shadow-lg">

        <div class="modal-header bg-primary text-white border-0">
          <h5 class="modal-title text-white fw-bold" id="modalTimelineTitle"><i class="fas fa-search me-2"></i> Lacak
            Laporan: LPR-001</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body p-4">

          <h5 class="fw-bold text-dark mb-3"><i class="fas fa-file-alt me-2"></i> Detail Laporan</h5>

          <div class="row">
            <div class="col-md-6 border-end">
              <table class="table table-sm table-borderless table-detail small">
                <tbody>
                  <tr>
                    <th>NOMOR LAPORAN</th>
                    <td id="detail_modal_lpr_id">-</td>
                  </tr>
                  <tr>
                    <th>TANGGAL LAPORAN</th>
                    <td id="detail_modal_tgl">-</td>
                  </tr>
                  <tr>
                    <th>NAMA ALAT</th>
                    <td class="fw-bold" id="detail_modal_alat_display">-</td>
                  </tr>
                  <tr>
                    <th>NOMOR INVENTARIS</th>
                    <td id="detail_modal_inv">-</td>
                  </tr>
                  <tr>
                    <th>LOKASI ALAT</th>
                    <td id="detail_modal_lokasi">-</td>
                  </tr>
                  <tr>
                    <th>JURUSAN / UNIT</th>
                    <td id="detail_modal_unit">-</td>
                  </tr>
                  <tr>
                    <th>STATUS KERUSAKAN</th>
                    <td id="detail_modal_kerusakan_display">-</td>
                  </tr>
                  <tr>
                    <th>TEKNISI (PELAKSANA)</th>
                    <td class="fw-bold" id="detail_modal_teknisi">-</td>
                  </tr>
                  <tr>
                    <th>TANGGAL PERBAIKAN</th>
                    <td class="fw-bold text-primary" id="detail_modal_tgl_perbaikan">-</td>
                  </tr>
                  <tr>
                    <th>STATUS LAPORAN</th>
                    <td class="fw-bold" id="detail_modal_status_perbaikan">-</td>
                  </tr>
                  <tr>
                    <th>VALIDASI KEPALA UPA</th>
                    <td class="fw-bold" id="detail_modal_validasi_kepala">-</td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="col-md-6">
              <!-- BUKTI FOTO PELAPOR -->
              <h6 class="fw-bold text-dark mt-3 mb-2 small text-uppercase">
                <i class="fas fa-camera me-1"></i>
                Bukti Foto Pelapor
              </h6>

              <div id="detail_modal_pelapor_foto_container" class="row g-2">
              </div>

              <div id="detail_modal_no_pelapor_foto" class="py-4 text-center">

                <div class="text-muted opacity-50 mb-2">
                  <i class="fas fa-image fa-3x"></i>
                </div>

                <h6 class="fw-bold text-muted small">
                  Tidak Ada Foto Pelapor
                </h6>

              </div>
              <h6 class="fw-bold text-dark mt-3 mb-2"><i class="fas fa-comment-dots me-1"></i> KELUHAN/KERUSAKAN </h6>
              <div class="alert alert-light border small">
                <p class="mb-0 fst-italic" id="detail_modal_keluhan">Keluhan lengkap tertera di sini.</p>
              </div>

              <div id="detail_modal_komplain_section" style="display: none;" class="mt-3">
                <h6 class="fw-bold text-dark mb-2"><i class="fas fa-exclamation-triangle me-1"></i> Catatan Komplain
                </h6>
                <div class="alert alert-danger border small">
                  <p class="mb-0 fst-italic" id="detail_modal_komplain">-</p>
                </div>
              </div>

              <!-- HASIL PEKERJAAN TEKNISI -->
              <div id="detail_modal_hasil_teknisi" style="display:none;" class="text-start mt-3">

                <h6 class="fw-bold text-dark mb-2 small text-uppercase">
                  <i class="fas fa-tools me-1"></i>
                  Uraian Pekerjaan Teknisi
                </h6>

                <div class="alert alert-light border shadow-sm">
                  <p id="detail_modal_catatan_teknisi" class="mb-0 fst-italic text-secondary">
                    -
                  </p>
                </div>

                <h6 class="fw-bold text-dark mb-2 small text-uppercase">
                  <i class="fas fa-camera me-1"></i>
                  Bukti Foto Teknisi
                </h6>
                <div id="detail_modal_teknisi_foto_container" class="row g-2">
                </div>

                <div id="detail_modal_no_teknisi_foto" class="py-4 text-center">
                  <div class="text-muted opacity-50 mb-2">
                    <i class="fas fa-image fa-3x"></i>
                  </div>

                  <h6 class="fw-bold text-muted small">
                    Tidak Ada Foto Teknisi
                  </h6>
                </div>
              </div>

              <!-- DIAGNOSA RUSAK -->
              <div id="detail_modal_diagnosa_section" style="display:none;" class="text-start mt-3">
                <h6 class="fw-bold text-danger mb-2 small text-uppercase">
                  <i class="fas fa-times-circle me-1"></i>
                  Alasan Tidak Bisa Diperbaiki / Diagnosa
                </h6>

                <div class="alert alert-danger border shadow-sm">
                  <p id="detail_modal_diagnosa" class="mb-0 fst-italic">
                    -
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- ULASAN PELAPOR -->
          <div class="text-start mt-4">

            <h6 class="fw-bold text-dark mb-2 small text-uppercase">
              <i class="fas fa-comment-dots me-1"></i>
              Ulasan Pelapor
            </h6>

            <div class="alert alert-light border shadow-sm">

              <div class="mb-3">

                <small class="text-muted d-block mb-1">
                  Rating
                </small>

                <div id="detail_modal_rating" class="text-warning fs-5">
                  -
                </div>
              </div>
              <hr>

              <div>
                <small class="text-muted d-block mb-1">
                  Ulasan
                </small>

                <p id="detail_modal_ulasan" class="mb-0 fst-italic text-secondary">
                  Pelapor belum memberikan ulasan.
                </p>
              </div>
            </div>
          </div>

          <hr class="mt-4 mb-4">

          <h5 class="fw-bold text-dark mb-3"><i class="fas fa-route me-2"></i>Progres Perbaikan</h5>
          <div id="modalTimelineBody">
          </div>

        </div>

        <div class="modal-footer border-0 p-4 pt-0">
          <div id="modalActionCard" class="card w-100 border-0 shadow-sm bg-light">
            <div class="card-body py-3 px-4 d-flex justify-content-between align-items-center">
              <span class="fw-bold text-dark me-3">Mohon Konfirmasi Laporan</span>
              <div class="d-grid gap-2 d-md-flex">
                <!--<button class="btn btn-outline-danger btn-sm fw-bold py-2" onclick="bukaModalKomplain()">
                  <i class="fas fa-times me-1"></i> Masih Rusak
                </button>-->
                <button class="btn btn-success btn-sm fw-bold py-2" onclick="bukaModalRating()">
                  <i class="fas fa-check-circle me-1"></i> Selesai & Beri Rating
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL FOTO PREVIEW -->
  <div class="modal fade" id="modalFotoPreview" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 95%;">
      <div class="modal-content p-0 border-0 bg-transparent">
        <div class="modal-body text-center position-relative p-0">
          <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-4 p-2 shadow-none"
            data-bs-dismiss="modal" aria-label="Close"
            style="z-index: 10; opacity: 1; background-color: rgba(0,0,0,0.5); border-radius: 50%;"></button>
          <img id="fotoPreviewZoom" src="" class="img-fluid rounded shadow-lg" style="
            max-height: 90vh;
            width: auto;
            max-width: 100%;
            object-fit: contain;
            margin: 0 auto;
            display: block;
          ">
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL RATING -->
  <div class="modal fade" id="modalRating" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow-lg text-center">
        <div class="modal-header border-0 pb-0"><button type="button" class="btn-close"
            data-bs-dismiss="modal"></button></div>
        <div class="modal-body pt-0 pb-4 px-4">
          <div class="mb-3">
            <div
              class="bg-success bg-opacity-10 text-success rounded-circle d-inline-flex align-items-center justify-content-center"
              style="width: 70px; height: 70px;">
              <i class="fas fa-smile-beam fs-1"></i>
            </div>
          </div>
          <h5 class="fw-bold text-dark">Pekerjaan Selesai!</h5>
          <p class="text-muted small mb-4">Seberapa puas Anda dengan kinerja teknisi <b>Pak Cipto</b>?</p>
          <div class="rating-css mb-4 text-warning" id="ratingContainer">
            <div class="star-icon fs-2"> <i class="fas fa-star" data-rating="1"></i>
              <i class="fas fa-star" data-rating="2"></i>
              <i class="fas fa-star" data-rating="3"></i>
              <i class="fas fa-star" data-rating="4"></i>
              <i class="fas fa-star" data-rating="5"></i>
            </div>
            <input type="hidden" id="hiddenRatingInput" value="5">
          </div>
          <div class="form-floating mb-3 text-start">
            <textarea class="form-control" placeholder="Tulis ulasan..." id="inputUlasan"
              style="height: 100px"></textarea>
            <label for="inputUlasan">Tulis ulasan Anda (Opsional)</label>
          </div>
          <button class="btn btn-primary w-100 rounded-pill fw-bold py-2" onclick="kirimRating()">Kirim
            Penilaian</button>
        </div>
      </div>
    </div>
  </div>
  </div>

  <!-- FOOTER -->
  <!--<footer>
    <p class="m-0">© 2025 UPAPP POLSRI</p>
  </footer>-->

  <!-- DI AKTIFKAN -->
  <?php echo $this->include('layout/footer'); ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // Menyimpan base_url ke variabel JS agar bisa dipakai di file eksternal
    const BASE_URL = "<?= base_url() ?>";
  </script>

  <script src="<?= base_url('pelapor/dashboard.js') ?>"></script>

</body>

</html>