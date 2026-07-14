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

    /* PAKSA POP-UP SWEETALERT TAMPIL DI ATAS HEADER KAPANPUN & DIMANAPUN */
    .swal2-container {
      z-index: 9999 !important;
    }

    /* Beri sedikit jarak dari atas agar tidak terlalu menempel dengan tepi layar */
    .swal2-toast {
      margin-top: 60px !important;
      /* Disesuaikan dengan tinggi header Bung */
    }
  </style>
</head>

<body>
  <!-- HEADER -->
  <?= $this->include('layout/header') ?>

  <!-- SIDEBAR -->
  <?= $this->include('layout/sidebar_teknisi') ?>

  <!-- CONTENT DASHBOARD TEKNISI -->

  <main class="content" id="mainContent">

    <div class="container-fluid">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h3 class="fw-bold text-dark mb-1">Dashboard Teknisi</h3>
          <p class="text-muted mb-0">
            Selamat bertugas,
            <span class="text-primary fw-bold">
              Pak <?= session()->get('nama') ? esc(session()->get('nama')) : 'Pelapor' ?>
            </span>
          </p>
        </div>
        <?php $isOnline = $is_online ?? 0; ?>

        <div class="bg-white px-3 py-2 rounded-pill shadow-sm d-flex align-items-center gap-2 border">
          <span class="small fw-bold text-muted">STATUS</span>

          <div class="form-check form-switch mb-0">
            <input
              class="form-check-input"
              type="checkbox"
              id="toggleOnline"
              style="cursor: pointer;"
              onchange="setOnlineStatus(this)"
              <?= ($isOnline == 1) ? 'checked' : '' ?> />
          </div>

          <span id="labelStatus" class="small fw-bold <?= ($isOnline == 1) ? 'text-success' : 'text-muted' ?>">
            <?= ($isOnline == 1) ? 'ONLINE' : 'OFFLINE' ?>
          </span>
        </div>
      </div>

      <div class="row g-3">
        <div class="col-md-3 col-6 fade-in">
          <div class="card shadow-sm p-3 bg-primary">
            <h6>Laporan Kerusakan</h6>
            <h3 id="txtLaporanKerusakan">0</h3>
          </div>
        </div>

        <div class="col-md-3 col-6 fade-in" style="animation-delay: .1s;">
          <div class="card shadow-sm p-3 bg-warning">
            <h6>Belum Diperbaiki</h6>
            <h3 id="txtBelumDiperbaiki">0</h3>
          </div>
        </div>

        <div class="col-md-3 col-6 fade-in" style="animation-delay: .1s;">
          <div class="card shadow-sm p-3 bg-success">
            <h6>Selesai Diperbaiki</h6>
            <h3 id="txtSelesaiDiperbaiki">0</h3>
          </div>
        </div>

        <div class="col-md-3 col-6 fade-in" style="animation-delay: .2s;">
          <div class="card shadow-sm p-3 bg-whites">
            <h6>Laporan Peminjaman</h6>
            <h3 id="txtPeminjaman">0</h3>
          </div>
        </div>

        <div class="col-md-3 col-6 fade-in" style="animation-delay: .3s;">
          <div class="card shadow-sm p-3 bg-danger">
            <h6>Total Barang Rusak</h6>
            <h3 id="txtBarangRusak">0</h3>
          </div>
        </div>
      </div>

      <!--<div class="mt-4 fade-in" style="animation-delay: .4s;">
        <div class="card border-0 shadow-sm p-4">
          <h5 class="fw-bold mb-3 text-primary">Statistik Peminjaman & Kerusakan</h5>
          <p class="text-muted">Contoh tampilan progres seperti di Ace Master</p>
          <div class="progress mb-3">
            <div class="progress-bar bg-success" role="progressbar" style="width: 70%">70% Barang Aktif</div>
          </div>
          <div class="progress mb-3">
            <div class="progress-bar bg-warning" role="progressbar" style="width: 20%">20% Dipinjam</div>
          </div>
          <div class="progress">
            <div class="progress-bar bg-danger" role="progressbar" style="width: 10%">10% Rusak</div>
          </div>
        </div>
      </div>-->
    </div>
    <br>

    <style>
      .card {
        font-family: 'Segoe UI', Tahoma, sans-serif;
        font-size: 0.9rem;
        /* sedikit kecil dan rapi */
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
      }

      .card-header {
        font-weight: 600;
        letter-spacing: 0.5px;
      }

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

      .dt-buttons .btn {
        font-size: 0.8rem;
        margin-right: 5px;
      }

      /* Buat tampilan tabel lebih “bersih” */
      .table-responsive {
        margin-top: 10px;
      }

      /* Warna tombol utama */
      .btn-primary {
        background-color: #0d6efd;
        border-color: #0d6efd;
      }

      /* === Gaya Profesional === */
      .card {
        font-family: 'Segoe UI', Tahoma, sans-serif;
        font-size: 0.9rem;
        /* sedikit kecil dan rapi */
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
      }

      .card-header {
        font-weight: 600;
        letter-spacing: 0.5px;
      }

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

      .dt-buttons .btn {
        font-size: 0.8rem;
        margin-right: 5px;
      }

      /* Buat tampilan tabel lebih “bersih” */
      .table-responsive {
        margin-top: 10px;
      }

      /* Warna tombol utama */
      .btn-primary {
        background-color: #0d6efd;
        border-color: #0d6efd;
      }
    </style>

    <!--<div class="card">
      <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-tools me-2"></i> LAPORAN KERUSAKAN SELESAI</h5>
      </div>
      <div class="card-body">
         FILTER FORM
        <div class="form-group row align-items-center mb-3">
          <div class="col-md-2">
            <input type="text" name="tanggal_cari1" id="tanggal_cari1" class="form-control text-center" data-toggle="datepicker" placeholder="Cari Tanggal">
          </div>

          <div class="col-md-3 text-center">
            <select name="pelaksana_cari1" id="pelaksana_cari1" class="form-control select2 text-center">
              <option value="">--Cari Pelaksana--</option>
              <option value="icon">Icon - M. Karison</option>
              <option value="riput">Riput - Riadi Putra</option>
              <option value="yogi">Yogi - Yogi Permana</option>
              <option value="hari">Hari - Hari Susanto</option>
              <option value="zacky">Zacky - M. Zacky</option>
            </select>
          </div>

          <div class="col-md-1 text-center">
            <button id="hapus_pelaksana_cari1" onclick="hapus_pelaksana_cari1()" class="btn btn-outline-danger">
              <i class="fas fa-trash"></i>
            </button>
          </div>

          <div class="col-md-2 text-center">
            <select name="cetak_bulan" id="cetak_bulan" class="form-control text-center">
              <option value="">-- Pilih Bulan --</option>
              <option value="01">JANUARI</option>
              <option value="02">FEBRUARI</option>
              <option value="03">MARET</option>
              <option value="04">APRIL</option>
              <option value="05">MEI</option>
              <option value="06">JUNI</option>
              <option value="07">JULI</option>
              <option value="08">AGUSTUS</option>
              <option value="09">SEPTEMBER</option>
              <option value="10">OKTOBER</option>
              <option value="11">NOVEMBER</option>
              <option value="12">DESEMBER</option>
            </select>
          </div>

          <div class="col-md-2 text-center">
            <select name="cetak_tahun" id="cetak_tahun" class="form-control text-center">
              <option value="">-- Pilih Tahun --</option>
              <option value="2025">2025</option>
            </select>
          </div>

          <div class="col-md-1 text-center">
            <button class="btn btn-primary btn-sm form-control" onclick="cetak()">
              <i class="fas fa-print"></i> CETAK
            </button>
          </div>
        </div>
        -->

    <!-- DATATABLE 
        <div class="table-responsive">
          <table id="table_selesai" class="table table-bordered table-striped text-xs text-center">
            <thead class="table-light">
              <tr>
                <th>NOMOR LAPORAN</th>
                <th>TANGGAL</th>
                <th>NAMA ALAT</th>
                <th>NOMOR INVENTARIS</th>
                <th>LOKASI ALAT</th>
                <th>JURUSAN / UNIT</th>
                <th style="display:none;">STATUS</th>
                <th style="display:none;">PELAKSANA</th>
                <th style="display:none;">VALIDASI KEPALA</th>
                <th style="display:none;">VALIDASI PELAKSANA</th>
                <th style="display:none;">AKSI</th>
              </tr>
            </thead>

            <tbody id="tableBody">
              <?php if (!empty($dashboardList) && is_array($dashboardList)): ?>
                <?php foreach ($dashboardList as $dashboard): ?>
                  <tr>
                    <td><?= esc($dashboard['nomor_laporan'] ?? '-') ?></td>

                    <td><?= esc($dashboard['tanggal_laporan'] ?? '-') ?></td>

                    <td><?= esc($dashboard['nama_alat'] ?? '-') ?></td>

                    <td><?= esc($dashboard['nomor_inventaris'] ?? '-') ?></td>

                    <td><?= esc($dashboard['lokasi_alat'] ?? '-') ?></td>

                    <td><?= esc($dashboard['unit'] ?? '-') ?></td>

                    <td style="display:none;"><?= esc($dashboardList['jenis_kerusakan'] ?? '-') ?></td>

                    <td style="display:none;"><?= esc($dashboardList['pelaksana'] ?? '-') ?></td>

                    <td style="display:none;"><?= esc($dashboardList['validasi_kepala'] ?? '-') ?></td>

                    <td style="display:none;"><?= esc($dashboardList['validasi_pelaksana'] ?? '-') ?></td>

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
            </tbody>
          </table>
        </div>-->

    <!-- Info & pagination 
        <div class="d-flex justify-content-between align-items-center mt-3">
          <div id="table_info" class="text-muted small">Showing 0 to 0 of 0 entries</div>
        -->

    <!-- CONTAINER PAGINATION
          <div class="dataTables_paginate paging_simple_numbers" id="table_paginate">
            <ul class="pagination mb-0">
            </ul>
          </div> -->
    </div>
    </div>
    </div>

    <div class="container-fluid px-0 mb-5">
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
  </main>

  <!-- FOOTER -->
  <!--<footer>
    <p class="m-0">© 2025 UPAPP POLSRI</p>
  </footer>-->

  <!-- DI AKTIFKAN -->
  <?php echo $this->include('layout/footer'); ?>

  <!-- SCRIPT -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

  <canvas id="tempChartCanvas" style="display: none;" width="800" height="400"></canvas>
  <script>
    const BASE_URL = '<?= base_url() ?>';
  </script>
  <script src="<?= base_url('teknisi/dashboard.js?v=' . time()) ?>"></script>
</body>

</html>