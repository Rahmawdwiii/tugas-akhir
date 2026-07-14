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
      font-family: 'Segoe UI', Tahoma, sans-serif;
      font-size: 0.9rem;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
      border-radius: 10px;
    }

    .card:hover {
      transform: translateY(-3px);
      box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
    }

    .card-header {
      font-weight: 600;
      letter-spacing: 0.5px;
    }

    .card.bg-primary {
      background-color: #71b8ff !important;
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

    /* BUTTON & INPUT */
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

    /* TABLE STYLE */
    table.table {
      font-size: 0.85rem;
      vertical-align: middle;
      border-collapse: collapse !important;
      width: 100%;
    }

    table.table th,
    table.table td {
      border: 1px solid #dee2e6;
      padding: 8px 10px;
    }

    table.table thead th {
      background-color: #f8f9fa;
      font-weight: 600;
      color: #333;
      border-bottom: 2px solid #dee2e6;
    }

    .table-responsive {
      margin-top: 10px;
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

    /* GROUP BUTTON AREA */
    .button-group {
      display: flex;
      align-items: center;
      gap: 6px;
      margin-top: 10px;
      flex-wrap: wrap;
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
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h3 class="fw-bold text-dark mb-1">Dashboard Admin</h3>
          <p class="text-muted mb-0">
            Selamat bertugas,
            <span class="text-primary fw-bold">
              Pak <?= session()->get('nama') ? esc(session()->get('nama')) : 'Pelapor' ?>
            </span>
          </p>
        </div>
        <?php $isOnline = session()->get('is_online') ?? 0; ?>

        <div class="bg-white px-3 py-2 rounded-pill shadow-sm d-flex align-items-center gap-2 border">
          <span class="small fw-bold text-muted">STATUS</span>

          <div class="form-check form-switch mb-0">
            <input class="form-check-input" type="checkbox" id="toggleOnline" style="cursor: pointer;"
              onchange="setOnlineStatus(this)" <?= ($isOnline == 1) ? 'checked' : '' ?> />
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

      <!-- PROGRESS BAR SECTION -->
      <div class="mt-4 fade-in" style="animation-delay: .4s;">
        <div class="card border-0 shadow-sm p-4">
          <h5 class="fw-bold mb-3 text-primary">Statistik Peminjaman & Kerusakan</h5>
          <div class="progress mb-3">
            <div id="pb_aktif" class="progress-bar bg-success" role="progressbar" style="width: 0%">0% Barang Aktif
            </div>
          </div>
          <div class="progress mb-3">
            <div id="pb_dipinjam" class="progress-bar bg-warning" role="progressbar" style="width: 0%">0% Dipinjam</div>
          </div>
          <div class="progress">
            <div id="pb_rusak" class="progress-bar bg-danger" role="progressbar" style="width: 0%">10% Rusak</div>
          </div>
        </div>
      </div>
      <br>

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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <canvas id="tempChartCanvas" style="display: none;" width="800" height="400"></canvas>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>

  <script>
    // Menyimpan URL base & CSRF ke dalam variabel JS
    const BASE_URL = "<?= base_url() ?>";
    const CSRF_TOKEN_NAME = "<?= csrf_token() ?>";
    const CSRF_HASH = "<?= csrf_hash() ?>";
  </script>

  <script src="<?= base_url('admin/dashboard.js') ?>"></script>

</body>

</html>