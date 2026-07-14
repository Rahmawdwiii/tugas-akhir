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

    .modal {
      z-index: 9999 !important;
    }

    .modal-backdrop {
      z-index: 9998 !important;
      /* Backdrop di belakang modal tapi di depan header */
    }
  </style>
</head>

<body>
  <!-- HEADER -->
  <?= $this->include('layout/header') ?>

  <!-- SIDEBAR -->
  <?= $this->include('layout/sidebar_admin') ?>

  <section class="content">
    <div class="container-fluid py-3">
      <div class="card shadow border-0">
        <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
          <h5 class="mb-0"><i class="fas fa-toolbox me-2"></i>DATA MASTER ALAT</h5>
          <div>
            <button class="btn btn-light btn-sm" title="Tambah" onclick="tambahModalAlat()"><i class="fa fa-plus"></i></button>
            <button class="btn btn-light btn-sm" title="Reload" id="btnReload">
              <i class="fa fa-undo"></i>
            </button>
            <button class="btn btn-light btn-sm" id="btnCopy" title="Copy">
              <i class="fas fa-copy"></i>
            </button>
            <button class="btn btn-light btn-sm" onclick="exportToExcel()"><i class="fas fa-file-excel"></i></button>
            <!-- <button class="btn btn-light btn-sm me-1" title="CSV"><i class="fas fa-file-csv"></i></button>
            <button class="btn btn-light btn-sm" title="Kolom Ditampilkan"><i class="fas fa-columns"></i></button>-->
          </div>
        </div>

        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="dt-buttons">
              <!--<button class="btn btn-outline-info btn-sm"><i class="fas fa-file-csv"></i> CSV</button>-->
              <!--<button class="btn btn-outline-danger btn-sm"><i class="fas fa-file-pdf"></i> PDF</button>-->
            </div>
            <div class="form-group d-flex align-items-center">
              <label class="me-2 mb-0 fw-semibold text-secondary">Cari:</label>
              <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Ketik nama alat..." style="width:200px;">
            </div>
          </div>

          <div class="table-responsive">
            <table id="table" class="table table-hover table-bordered align-middle text-center">
              <thead class="table-warning">
                <tr>
                  <th style="width: 5%;">ID</th>
                  <th style="width: 10%;">NOMOR INVENTARIS</th>
                  <th style="width: 20%;">NAMA ALAT</th>
                  <th style="width: 5%;">AKSI</th>
                </tr>
              </thead>
              <tbody id="tableBody">
                <!-- LOGIKA PHP UNTUK MENAMPILKAN DATA -->
                <?php if (!empty($daftar_alat)) : ?>
                  <?php $no = 1; ?>
                  <?php foreach ($daftar_alat as $alat) : ?>
                    <tr>
                      <td><?= $no++ ?></td>

                      <!-- Menampilkan Nomor Inventaris -->
                      <td>
                        <span class="nomor_inventaris">
                          <?= esc($alat['nomor_inventaris']) ?>
                        </span>
                      </td>

                      <!-- Menampilkan Nama Alat -->
                      <td class="nama_alat">
                        <?= esc($alat['nama_alat']) ?>
                      </td>

                      <!-- Tombol Aksi -->
                      <td>
                        <button class="btn btn-warning btn-sm text-white" title="Edit" onclick="editAlat(<?= $alat['id_alat'] ?>)">
                          <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" href="javascript:void(0)" onclick="hapusAlat(<?= $alat['id_alat'] ?>)" title="Hapus">
                          <i class="fa fa-trash"></i>
                        </button>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else : ?>
                  <tr class="empty-row">
                    <td colspan="4" class="text-center text-muted py-4">
                      <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                      Data alat belum ditambahkan.
                    </td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

          <!-- Info & pagination -->
          <div class="d-flex justify-content-between align-items-center mt-3">
            <div id="table_info" class="text-muted small">Showing 0 to 0 of 0 entries</div>

            <!-- CONTAINER PAGINATION -->
            <div class="dataTables_paginate paging_simple_numbers" id="table_paginate">
              <ul class="pagination mb-0">
                <!-- Tombol akan digenerate JS di sini -->
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
    </div>
    </div>
  </section>

  <!-- ========================================== -->
  <!-- MODAL TAMBAH DATA ALAT -->
  <!-- ========================================== -->
  <div class="modal fade" id="modal_alat" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">

        <div class="modal-header bg-warning fw-bold text-dark">
          <h5 class="modal-title">Tambah Data Alat</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <form id="form_alat">
            <?= csrf_field() ?>

            <div class="row mb-2 fw-bold">
              <div class="col-md-5">Nomor Inventaris <span class="text-danger">*</span></div>
              <div class="col-md-6">Nama Alat <span class="text-danger">*</span></div>
              <div class="col-md-1"></div>
            </div>

            <div id="alat-form-container">
            </div>
          </form>

          <button type="button" class="btn btn-outline-warning btn-sm mt-3" onclick="tambahAlatRow()">
            <i class="fa fa-plus-circle"></i> Tambah Baris Lain
          </button>
        </div>

        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="button" class="btn btn-warning px-4 text-white" onclick="saveAlat()">Simpan</button>
        </div>

      </div>
    </div>
  </div>

  <!-- ========================================== -->
  <!-- MODAL EDIT ALAT -->
  <!-- ========================================== -->
  <div class="modal fade" id="modal_edit_alat" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">

        <div class="modal-header bg-warning text-dark">
          <h5 class="modal-title fw-bold text-dark">Edit Data Alat</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body p-4">
          <form id="form_edit_alat">
            <input type="hidden" name="id_alat" id="edit_id">

            <div class="row mb-3 align-items-center">
              <label for="edit_no_inv" class="col-sm-4 col-form-label fw-bold">NOMOR INVENTARIS</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" name="nomor_inventaris" id="edit_no_inv" required>
              </div>
            </div>

            <div class="row mb-3 align-items-center">
              <label for="edit_nama" class="col-sm-4 col-form-label fw-bold">NAMA ALAT</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" name="nama_alat" id="edit_nama" required>
              </div>
            </div>

          </form>
        </div>

        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
          <button type="button" class="btn btn-warning px-4" onclick="updateAlat()">Update</button>
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
  <script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
  <canvas id="tempChartCanvas" style="display: none;" width="800" height="400"></canvas>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    // Simpan base_url dan token CSRF agar bisa dibaca dari JS eksternal
    const BASE_URL = "<?= base_url() ?>";
    const CSRF_TOKEN = "<?= csrf_token() ?>";
    const CSRF_HASH = "<?= csrf_hash() ?>";
  </script>

  <script src="<?= base_url('admin/data_master_alat.js') ?>"></script>
</body>

</html>