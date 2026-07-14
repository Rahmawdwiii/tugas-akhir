<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Admin Penjadwalan & Validasi</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />

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

        /* Setting Dasar */
        body {
            font-family: "Segoe UI", sans-serif;
            background-color: #f8f9fa;
        }

        /* --- CSS KHUSUS DASHBOARD ADMIN (Card Overview) --- */
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
        .card-penugasan {
            background-color: #f0f7ff !important;
            border-left: 5px solid #0d6efd !important;
        }

        .card-penugasan .text-color {
            color: #0d6efd !important;
        }

        .card-penugasan .admin-icon-shape {
            background: #0d6efd;
            color: #fff;
        }

        .card-komplain-admin {
            background-color: #fff0f0 !important;
            border-left: 5px solid #dc3545 !important;
        }

        .card-komplain-admin .text-color {
            color: #dc3545 !important;
        }

        .card-komplain-admin .admin-icon-shape {
            background: #dc3545;
            color: #fff;
        }

        .card-proses-admin {
            background-color: #fff8e1 !important;
            border-left: 5px solid #ffc107 !important;
        }

        .card-proses-admin .text-color {
            color: #ffc107 !important;
        }

        .card-proses-admin .admin-icon-shape {
            background: #ffc107;
            color: #fff;
        }

        .card-validasi-admin {
            background-color: #f0fff0 !important;
            border-left: 5px solid #198754 !important;
        }

        .card-validasi-admin .text-color {
            color: #198754 !important;
        }

        .card-validasi-admin .admin-icon-shape {
            background: #198754;
            color: #fff;
        }

        .card-rusak-admin {
            background-color: #f8f8f8 !important;
            border-left: 5px solid #343a40 !important;
        }

        .card-rusak-admin .text-color {
            color: #343a40 !important;
        }

        .card-rusak-admin .admin-icon-shape {
            background: #343a40;
            color: #fff;
        }

        .card-fixed-height {
            height: 100%;
        }

        .card-fixed-height .card-body {
            min-height: 100px;
            /* Nilai ini bisa disesuaikan. 110px cukup untuk menampung 2 baris teks + h4 */
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            /* Untuk distribusi konten yang rapi */
        }

        /* CSS Khusus Antrian Card */
        .antrian-card-detail {
            border: 1px solid #e9ecef;
            border-left: 5px solid;
            border-radius: 8px;
            padding: 10px;
            background-color: #fff;
        }

        /* Style Card Detail Antrian */
        .antrian-penugasan-card {
            border-left-color: #0d6efd !important;
        }

        .antrian-komplain-card {
            border-left-color: #dc3545 !important;
            background-color: #fffafc;
        }

        .antrian-validasi-card {
            border-left-color: #198754 !important;
            
        }

        .antrian-rusak-card {
            border-left-color: #343a40 !important;
            background-color: #f8f8f8;
        }

        /* CSS untuk Laporan Card (Mirip dengan Pelapor Dashboard) */
        .laporan-card {
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 15px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
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
            color: #6c757d;
            font-size: 0.88rem;
        }

        .laporan-card .progress-summary {
            min-width: 160px;
            text-align: right;
        }

        .laporan-card .detail-text {
            font-size: 0.85rem;
            color: #6c757d;
        }

        .laporan-card .icon-progress {
            font-size: 1.5rem;
            color: #0d6efd;
        }

        /* --- CSS TIMELINE (Sama seperti Pelapor) --- */
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

        .komplain-pesan-box {
            background: #fff;
            border: 1px solid #ffdddd;
            border-radius: 6px;
            padding: 8px;
        }

        .small-rating .fas {
            font-size: 0.8rem;
        }

        /* Pastikan Modal selalu paling depan */
        .modal {
            z-index: 100050 !important;
            /* Angka sangat tinggi agar di atas header */
        }

        /* Backdrop (layar gelap di belakang modal) juga harus tinggi */
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
    <!-- HEADER -->
    <?= $this->include('layout/header') ?>

    <!-- SIDEBAR -->
    <?= $this->include('layout/sidebar_admin') ?>

    <!-- CONTENT -->
    <main class="content" id="mainContent">
        <div class="container-fluid py-4">

            <div class="row g-4 mb-5">
                <div class="col-sm-6 col-lg-3">
                    <div class="card admin-card card-penugasan card-fixed-height" onclick="loadAntrian('new')">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <small class="text-uppercase text-color fw-bold">Penugasan Baru</small>
                                    <h4 id="count_penugasan" class="fw-bolder text-dark mb-0">0 Laporan</h4>
                                </div>
                                <div class="admin-icon-shape">
                                    <i class="fas fa-user-plus"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0 pt-0">
                            <small class="text-color fw-bold cursor-pointer">Lihat & Tugaskan Teknisi <i
                                    class="fas fa-arrow-right ms-1"></i></small>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="card admin-card card-proses-admin card-fixed-height" onclick="loadAntrian('proses')">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <small class="text-uppercase text-color fw-bold">Sedang Diproses</small>
                                    <h4 id="count_proses" class="fw-bolder text-dark mb-0">0 Laporan</h4>
                                </div>
                                <div class="admin-icon-shape">
                                    <i class="fas fa-tools"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0 pt-0">
                            <small class="text-color fw-bold cursor-pointer">Lacak Status Aktif <i
                                    class="fas fa-arrow-right ms-1"></i></small>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="card admin-card card-validasi-admin card-fixed-height"
                        onclick="loadAntrian('validasi_akhir')">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <small class="text-uppercase text-color fw-bold">Validasi Akhir</small>
                                    <h4 id="count_validasi" class="fw-bolder text-dark mb-0">0 Laporan</h4>
                                </div>
                                <div class="admin-icon-shape">
                                    <i class="fas fa-check-double"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0 pt-0">
                            <small class="text-color fw-bold cursor-pointer">Konfirmasi & Arsipkan <i
                                    class="fas fa-arrow-right ms-1"></i></small>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="card admin-card card-riwayat-admin card-fixed-height" onclick="loadAntrian('riwayat')">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <small class="text-uppercase text-color fw-bold">Riwayat</small>
                                    <h4 id="count_riwayat" class="fw-bolder text-dark mb-0">0 Laporan</h4>
                                </div>
                                <div class="admin-icon-shape"><i class="fas fa-archive"></i></div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0 pt-0">
                            <small class="text-color fw-bold cursor-pointer">Lihat Arsip Tugas <i
                                    class="fas fa-arrow-right ms-1"></i></small>
                        </div>
                    </div>
                </div>
            </div>

            <h4 class="fw-bold mb-3 mt-4" id="antrian_header">
                Daftar Laporan (Antrian)
            </h4>
            <div id="antrian_dynamic_content" class="d-grid gap-3">
                <div class="alert alert-info">
                    Klik salah satu Card di atas untuk memfilter dan menampilkan daftar
                    laporan antrian.
                </div>
            </div>
        </div>

        <!-- MODAL UNTUK SEGALA CARD-->
        <div class="modal fade" id="modalReviewKomplain" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-danger text-white border-0">
                        <h5 class="modal-title fw-bold">
                            <i class="fas fa-exclamation-triangle me-2"></i> REVIEW KOMPLAIN
                            PELAPOR
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <h5 class="fw-bold text-dark mb-3" id="modal_review_laporan_title">
                            Laporan: AC Sentral Bocor (LPR-REV-001)
                        </h5>

                        <!-- RINGKASAN LAPORAN -->
                        <div class="alert alert-light border small mb-4">
                            <table class="table table-sm table-borderless mb-0">
                                <tbody>
                                    <tr>
                                        <th width="35%">Nomor Laporan</th>
                                        <td id="modal_review_lpr_no" class="fw-bold text-dark">-</td>
                                    </tr>
                                    <tr>
                                        <th>Nama Alat</th>
                                        <td id="modal_review_lpr_alat" class="fw-bold text-dark">-</td>
                                    </tr>
                                    <tr>
                                        <th>Jurusan / Unit</th>
                                        <td id="modal_review_lpr_unit">-</td>
                                    </tr>
                                    <tr>
                                        <th>Lokasi Spesifik</th>
                                        <td id="modal_review_lpr_lokasi">-</td>
                                    </tr>
                                    <tr>
                                        <th>Teknisi Pelaksana</th>
                                        <td id="modal_review_lpr_teknisi">-</td>
                                    </tr>
                                    <tr>
                                        <th>Status Kerusakan</th>
                                        <td id="modal_review_lpr_kerusakan">-</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!--<div class="alert bg-danger-subtle text-danger p-3 mb-4">
                            <strong class="d-block mb-1"><i class="fas fa-comment-dots me-1"></i> PESAN DARI
                                PELAPOR:</strong>
                            <p class="mb-0 fst-italic" id="modal_review_pesan">
                                Pesan komplain tertera di sini.
                            </p> -->
                    </div>

                    <h6 class="fw-bold text-dark mb-2">Riwayat Tugas Teknisi:</h6>
                    <div class="border p-3 bg-light rounded mb-4">
                        <p class="small mb-0" id="modal_review_riwayat">-</p>
                    </div>

                    <!-- <div class="d-grid gap-2">
                            <button class="btn btn-danger fw-bold py-2"
                                onclick="processReview('LPR-REV-001', 'revert')">
                                <i class="fas fa-undo me-2"></i> KEMBALIKAN KE TEKNISI (REVISI)
                            </button>
                            <button class="btn btn-outline-secondary btn-sm"
                                onclick="processReview('LPR-REV-001', 'valid')">
                                Tutup Laporan (Validasi Admin)
                            </button> -->
                </div>
            </div>
        </div>

        <!-- MODAL VALIDASI -->
        <div class="modal fade" id="modalValidate" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-success text-white border-0">
                        <h5 class="modal-title fw-bold">
                            <i class="fas fa-check-double me-2"></i> REVIEW & VALIDASI
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="modal-body p-4">
                            <div class="row g-3 mb-4">
                                <div class="col-lg-6">
                                    <div class="card border shadow-sm h-100">
                                        <div class="card-header bg-primary text-white py-2">
                                            <strong>Data Laporan Pelapor</strong>
                                        </div>
                                        <div class="card-body p-3">
                                            <table class="table table-sm table-borderless mb-0">
                                                <tbody>
                                                    <tr>
                                                        <th width="35%">Nomor Laporan</th>
                                                        <td id="validasi_modal_lpr_no"></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Nama Alat</th>
                                                        <td id="validasi_modal_nama_alat"></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Nomor Inventaris</th>
                                                        <td id="validasi_modal_inv_no"></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Jurusan / Unit</th>
                                                        <td id="validasi_modal_unit"></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Lokasi</th>
                                                        <td id="validasi_modal_lokasi"></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Tanggal Laporan</th>
                                                        <td id="validasi_modal_tanggal_laporan"></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Keluhan/Kerusakan</th>
                                                        <td id="validasi_modal_keluhan">-</td>
                                                    </tr>
                                                    <!--<tr>
                                                        <th>Pesan Komplain</th>
                                                        <td id="validasi_modal_pesan_komplain">-</td>
                                                    </tr> -->
                                                    <tr>
                                                        <th>Link Pendukung</th>
                                                        <td id="validasi_modal_link_pendukung">-</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="card border shadow-sm h-100">
                                        <div class="card-header bg-success text-white py-2">
                                            <strong>Hasil Perbaikan Teknisi</strong>
                                        </div>
                                        <div class="card-body p-3">
                                            <table class="table table-sm table-borderless mb-0">
                                                <tbody>
                                                    <tr>
                                                        <th width="35%">Teknisi</th>
                                                        <td id="validasi_modal_teknisi"></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Tanggal Perbaikan</th>
                                                        <td id="validasi_modal_tanggal"></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Status Perbaikan</th>
                                                        <td id="validasi_modal_status">
                                                            <span class="badge bg-success"></span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Status Laporan</th>
                                                        <td id="validasi_modal_status_laporan"></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Status Kerusakan</th>
                                                        <td id="validasi_modal_status_kerusakan">-</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Uraian Pekerjaan Teknisi</th>
                                                        <td id="validasi_modal_catatan_teknisi">-</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ULASAN PELAPOR -->
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-body">
                                    <h6 class="fw-bold mb-2">
                                        <i class="fas fa-comment-dots me-1"></i>
                                        Ulasan Pelapor
                                    </h6>

                                    <p class="fst-italic text-muted mb-2" id="validasi_modal_ulasan">
                                        “Komputer sudah kembali normal, tidak mengalami blue screen
                                        lagi. Terima kasih atas respon cepat teknisinya.”
                                    </p>

                                    <div class="fw-bold">
                                        Rating: <span class="text-warning" id="validasi_modal_rating">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="far fa-star"></i>
                                            <span class="ms-1">4 / 5</span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- FOTO PELAPOR -->
                            <div class="mb-4">
                                <h6 class="fw-bold mb-3">
                                    <i class="fas fa-camera me-1"></i> Foto Pelapor
                                </h6>
                                <div class="bg-light p-3 rounded border text-center mb-3">
                                    <div id="validasi_modal_foto_container" class="row g-2"></div>

                                    <div id="validasi_modal_no_foto" class="py-4" style="display: none;">
                                        <div class="text-muted opacity-50 mb-2">
                                            <i class="fas fa-image fa-2x"></i>
                                        </div>
                                        <h6 class="fw-bold text-muted small mb-0">Tidak Ada Foto Pelapor</h6>
                                    </div>
                                </div>
                            </div>

                            <!-- FOTO TEKNISI -->
                            <div class="mb-4">
                                <h6 class="fw-bold mb-3">
                                    <i class="fas fa-tools me-1"></i> Bukti Foto Teknisi
                                </h6>
                                <div class="bg-light p-3 rounded border text-center mb-3">
                                    <div id="validasi_modal_teknisi_foto_container" class="row g-2"></div>

                                    <div id="validasi_modal_no_teknisi_foto" class="py-4" style="display: none;">
                                        <div class="text-muted opacity-50 mb-2">
                                            <i class="fas fa-image fa-2x"></i>
                                        </div>
                                        <h6 class="fw-bold text-muted small mb-0">Tidak Ada Foto Teknisi</h6>
                                    </div>
                                </div>
                            </div>

                            <!-- TOMBOL AKSI -->
                            <div class="d-grid mt-4">
                                <button id="validasi_modal_button" class="btn btn-success fw-bold py-2" type="button">
                                    <i class="fas fa-check-double me-2"></i>
                                    VALIDASI
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Rusak 
        <div class="modal fade" id="modalRusak" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-dark text-white border-0">
                        <h5 class="modal-title fw-bold">
                            <i class="fas fa-ban me-2"></i> REVIEW RUSAK TOTAL
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <h5 class="fw-bolder text-dark mb-1" id="rusak_alat">
                            PC Laboratorium 04
                        </h5>
                        <p class="small text-muted mb-3">
                            Laporan: LPR-AFK-002 | Teknisi: M. Karisom
                        </p> -->

        <!-- RINGKASAN LAPORAN 
                        <div class="alert alert-light border small mb-4">
                            <table class="table table-sm table-borderless mb-0">
                                <tbody>
                                    <tr>
                                        <th width="35%">Nomor Laporan</th>
                                        <td>LPR-005</td>
                                    </tr>
                                    <tr>
                                        <th>Nama Alat</th>
                                        <td>Komputer Blue Screen</td>
                                    </tr>
                                    <tr>
                                        <th>Jurusan / Unit</th>
                                        <td>Multimedia</td>
                                    </tr>
                                    <tr>
                                        <th>Lokasi</th>
                                        <td>Lab Multimedia</td>
                                    </tr>
                                    <tr>
                                        <th>Teknisi</th>
                                        <td>Eko</td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Perbaikan</th>
                                        <td>20 Nov 2025</td>
                                    </tr>
                                    <tr>
                                        <th>Status Perbaikan</th>
                                        <td>
                                            <span class="badge bg-success">Selesai</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <h6 class="fw-bold mb-3 text-dark">
                            <i class="fas fa-stethoscope me-2"></i> Diagnosa Teknisi:
                        </h6>
                        <div class="alert alert-secondary p-3 small">
                            <p class="mb-0 fst-italic" id="rusak_diagnosa">
                                Diagnosa teknis di sini.
                            </p>
                        </div>

                        <h6 class="fw-bold mb-3 text-danger">
                            <i class="fas fa-gavel me-2"></i> Tindak Lanjut Aset:
                        </h6>
                        <div class="alert alert-danger p-3">
                            <p class="small mb-0">
                                **Keputusan:** Alat dinyatakan **tidak ekonomis** dan disetujui
                                untuk proses rusak.
                            </p>
                        </div>

                        <div class="d-grid mt-4">
                            <button class="btn btn-dark fw-bold py-2" onclick="arsipRusak('LPR-AFK-002')">
                                <i class="fas fa-check-double me-2"></i> VALIDASI
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->

        <!-- MODAL PILIH TEKNISI & JADWAL -->
        <!--<div class="modal fade" id="modalPilihLain" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-secondary text-white border-0">
                        <h5 class="modal-title text-white fw-bold">Pilih Teknisi & Jadwal</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-4">
                        <p class="small text-muted mb-3" id="pilih_lain_laporan_info">
                            Laporan: <strong>AC Split Panas</strong> (LPR-009)
                        </p>

                        <div class="mb-3">
                            <label for="selectTeknisi" class="form-label fw-bold">
                                Pilih Teknisi yang Tersedia:
                            </label>
                            <select class="form-select" id="selectTeknisi">
                                <option value="">-- Pilih --</option>
                                <option value="M. Karison">
                                    M. Karison (Teknisi Komputer)
                                </option>
                                <option value="Riadi Putra">
                                    Riadi Putra (Teknisi Kelistrikan)
                                </option>
                                <option value="Edial Salmes">
                                    Edial Salmes (Teknisi Elektronika - Rekomendasi)
                                </option>
                                <option value="Cipto">Cipto (Teknisi AC)</option>
                                <option value="Sairespen">Sairespen (Teknisi AC)</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="inputTanggalPilihLain" class="form-label fw-bold">
                                Tanggal Perbaikan:
                            </label>
                            <input type="date" class="form-control" id="inputTanggalPilihLain" required />
                        </div>

                        <button type="button" class="btn btn-primary w-100 fw-bold" onclick="assignManualHandler()">
                            Konfirmasi Penugasan
                        </button>
                    </div>
                </div>
            </div>
        </div>-->

        <!-- MODAL DETAIL LAPORAN -->
        <div class="modal fade" id="modalDetailLaporan" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-primary text-white border-0">
                        <h5 class="modal-title text-white fw-bold">
                            <i class="fa-solid fa-file me-2"></i> Detail Lengkap Laporan
                            <span id="detail_modal_id"></span>
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body p-4">
                        <div class="row">
                            <div class="col-md-6 border-end text-center">

                                <div class="mb-4">
                                    <h5 class="fw-bolder text-dark mb-1" id="detail_modal_alat_display">
                                        Nama Alat
                                    </h5>
                                    <small class="text-muted d-block">INV: <span
                                            id="detail_modal_inv_display">-</span></small>
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

                                <!-- HASIL PEKERJAAN TEKNISI -->
                                <div id="detail_modal_hasil_teknisi" style="display:none;" class="text-start">

                                    <h6 class="fw-bold text-dark mb-2 small text-uppercase">
                                        <i class="fas fa-tools me-1"></i>
                                        Uraian Pekerjaan Teknisi
                                    </h6>

                                    <div class="alert alert-light border shadow-sm">
                                        <p id="detail_modal_catatan_teknisi"
                                            class="mb-0 fst-italic text-secondary text-start">
                                            -
                                        </p>
                                    </div>

                                    <h6 class="fw-bold text-dark mb-2 small text-uppercase">
                                        <i class="fas fa-camera me-1"></i>
                                        Bukti Foto Teknisi
                                    </h6>

                                    <div class="bg-light p-3 rounded border mb-3">

                                        <div id="detail_modal_teknisi_foto_container" class="row g-2">
                                        </div>

                                        <div id="detail_modal_no_teknisi_foto" class="py-4">

                                            <div class="text-muted opacity-50 mb-2">
                                                <i class="fas fa-image fa-3x"></i>
                                            </div>

                                            <h6 class="fw-bold text-muted small">
                                                Tidak Ada Foto Teknisi
                                            </h6>
                                        </div>
                                    </div>
                                </div>

                                <div id="detail_modal_diagnosa_section" style="display:none;" class="text-start">

                                    <h6 class="fw-bold text-dark mb-2 small text-uppercase mt-4">
                                        <i class="fas fa-triangle-exclamation me-1 text-danger"></i>
                                        Alasan Tidak Bisa Diperbaiki / Diagnosa
                                    </h6>

                                    <div class="alert alert-danger border shadow-sm">
                                        <p id="detail_modal_diagnosa" class="mb-0 fst-italic">

                                            -

                                        </p>
                                    </div>
                                </div>

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
                                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                    <h6 class="fw-bold text-dark mb-0 text-nowrap">
                                        <i class="fas fa-file-alt me-1"></i> Detail Laporan
                                    </h6>
                                    <div>
                                        <span class="small text-muted me-1">STATUS KERUSAKAN:</span>
                                        <span class="badge mb-1 me-1" id="detail_modal_kerusakan_display"></span>
                                    </div>
                                </div>

                                <table class="table table-sm table-striped small">
                                    <tbody>
                                        <tr>
                                            <td class="text-muted fw-bold" style="width: 40%">NOMOR LAPORAN</td>
                                            <td class="fw-bold text-dark" id="detail_modal_lpr_id">-</td>
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

                                <h6 class="fw-bold text-dark mb-2 small text-uppercase">
                                    <i class="fas fa-comment-dots me-1"></i> Keluhan / Kerusakan
                                </h6>
                                <div class="alert alert-light border small shadow-sm">
                                    <p class="mb-0 fst-italic text-secondary" id="detail_modal_keluhan">
                                        -
                                    </p>
                                </div>

                                <!--<div id="detail_modal_komplain_section" style="display: none;" class="mt-3">
                                    <h6 class="fw-bold text-dark mb-2 small text-uppercase">
                                        <i class="fas fa-history me-1"></i> Riwayat Komplain
                                    </h6>
                                    <div class="alert alert-danger border small shadow-sm">
                                        <p class="mb-0 fst-italic text-white" id="detail_modal_komplain">-</p>
                                    </div>
                                </div>-->

                                <hr>

                                <h6 class="fw-bold text-dark mb-3 small text-uppercase">
                                    <i class="fas fa-stream me-1"></i>
                                    Progress Perbaikan
                                </h6>

                                <div id="detail_modal_timeline"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">

                        <button type="button" id="detail_modal_validate_button" class="btn btn-dark"
                            style="display:none;">
                            <i class="fas fa-check-double me-2"></i>
                            VALIDASI
                        </button>

                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Tutup
                        </button>

                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL FOTO PREVIEW (STANDALONE - ROOT LEVEL) -->
        <div class="modal fade" id="modalFotoPreview" tabindex="-1" style="z-index: 1100;">
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

        <!-- MODAL JADWAL DI BUTTON TUGASKAN-->
        <!-- MODAL SET JADWAL PENUGASAN -->
        <div class="modal fade" id="modalSetJadwal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-primary text-white border-0 modal-header-assign">
                        <h5 class="modal-title text-white fw-bold">
                            <i class="fas fa-calendar-check me-2"></i>
                            Tetapkan Jadwal Penugasan
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-4">
                        <p class="small text-muted mb-3">
                            Tugaskan
                            <strong id="jadwal_teknisi_name">NAMA TEKNISI</strong>
                            untuk laporan
                            <strong id="jadwal_laporan_id">LPR-ID</strong>.
                        </p>

                        <div class="mb-3">
                            <label for="inputTanggal" class="form-label fw-bold">
                                Pilih Tanggal Perbaikan:
                            </label>
                            <input type="date" class="form-control" id="inputTanggal" required />
                        </div>
                    </div>

                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="button" class="btn btn-primary fw-bold" onclick="finalAssign()">
                            Konfirmasi Penugasan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- FOOTER -->
    <!--<footer>
    <p class="m-0">© 2025 UPAPP POLSRI</p>
  </footer>-->

    <!-- DI AKTIFKAN -->
    <?php echo $this->include('layout/footer'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Menyimpan base_url ke variabel JS agar bisa dibaca di file eksternal
        const BASE_URL = "<?= base_url() ?>";
    </script>

    <script src="<?= base_url('admin/antrian_perbaikan.js') ?>"></script>
</body>

</html>