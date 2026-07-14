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

    body {
      background-color: #f5f9ff;
      font-family: 'Segoe UI', Tahoma, sans-serif;
    }

    .card {
      border: none;
      border-radius: 15px;
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
      background: #fff;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .card:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
    }

    .card-header {
      background-color: #b3d9ff;
      color: #003366;
      font-weight: 700;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-radius: 15px 15px 0 0;
      padding: 15px 20px;
    }

    .card-header h5 {
      margin: 0;
      font-size: 1.2rem;
    }

    .table {
      font-size: 0.9rem;
      margin-top: 10px;
      border-radius: 10px;
      overflow: hidden;
    }

    .table thead th {
      background-color: #b3d9ff;
      color: #00284d;
      font-weight: 600;
    }

    .table td {
      vertical-align: middle;
    }

    .btn-sm {
      font-size: 0.8rem;
      border-radius: 6px;
      padding: 5px 10px;
    }

    .btn-primary {
      background-color: #007bff;
      border-color: #007bff;
    }

    .btn-outline-primary:hover {
      background-color: #003366;
      color: white;
    }

    .dataTables_filter input {
      border-radius: 8px;
      border: 1px solid #ccc;
      padding: 5px 10px;
      outline: none;
    }

    .dt-buttons .btn {
      margin-right: 6px;
    }

    .table-responsive {
      margin-top: 15px;
    }

    .modal {
      z-index: 9999 !important;
    }

    .modal-backdrop {
      z-index: 9998 !important;
      /* Backdrop di belakang modal tapi di depan header */
    }

    .btn-simpan {
      background-color: #b3d9ff;
      color: #003366;
    }

    .btn-simpan:hover {
      background-color: #a3c7e8;
      color: #00284d;
    }

    .modal-header-upa {
      background: #a8cbef;
      color: #0b2f5b;
      border-bottom: 1px solid #8fbbe6;
    }

    .modal-header-upa .modal-title {
      font-weight: 700;
      color: #0b2f5b;
    }

    .modal-header-upa .btn-close {
      filter: none;
    }
  </style>
</head>

<body>
  <!-- HEADER -->
  <?= $this->include('layout/header') ?>

  <div class="content">
    <!-- SIDEBAR -->
    <?= $this->include('layout/sidebar_admin') ?>

    <div class="container-fluid mt-4 fade-in">
      <div class="card">
        <div class="card-header">
          <h5><i class="fas fa-building me-2"></i>Data Unit</h5>
          <div>
            <button class="btn btn-light btn-sm" title="Tambah" onclick="tambahModalUnit()"><i
                class="fa fa-plus"></i></button>
            <button class="btn btn-light btn-sm" title="Reload" id="btnReload">
              <i class="fa fa-undo"></i>
            </button>
            <button class="btn btn-light btn-sm" id="btnCopy" title="Copy">
              <i class="fas fa-copy"></i>
            </button>
            <button class="btn btn-light btn-sm" onclick="exportToExcel()"><i class="fas fa-file-excel"></i></button>
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
              <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Ketik nama unit..."
                style="width:200px;">
            </div>
          </div>

          <div class="table-responsive">
            <table class="table table-striped table-bordered text-center align-middle">
              <thead style="background-color: #b3d9ff;">
                <tr>
                  <th width="5%">ID</th>
                  <th width="20%">JURUSAN / UNIT</th>
                  <th width="10%">KATEGORI</th>
                  <th width="5%">AKSI</th>
                </tr>
              </thead>

              <tbody id="tableBody">
                <?php if (!empty($daftar_unit)): ?>
                  <?php $no = 1; ?>
                  <?php foreach ($daftar_unit as $unit): ?>
                    <tr>
                      <!-- ID (Nomor Urut) -->
                      <td><?= $no++ ?></td>

                      <!-- Nama Unit -->
                      <td class="text-center">
                        <?= esc($unit['nama_unit']) ?>
                      </td>

                      <!-- Kategori -->
                      <td class="text-center">
                        <?= esc($unit['kategori']) ?>
                      </td>

                      <!-- Aksi -->
                      <td>
                        <div class="btn-group btn-group-sm">
                          <button class="btn btn-warning btn-sm text-white" title="Edit"
                            onclick="editUnit(<?= $unit['id_unit'] ?>)">
                            <i class="fas fa-edit"></i>
                          </button>
                          <button class="btn btn-danger" onclick="hapusUnit('<?= esc($unit['id_unit']) ?>')">
                            <i class="fas fa-trash-alt"></i>
                          </button>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr class="empty-row">
                    <td colspan="3" class="text-muted text-center py-5 bg-light">Belum ada data unit.</td>
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

    <!-- ========================================== -->
    <!-- MODAL TAMBAH UNIT -->
    <!-- ========================================== -->
  </div>
  <div class="modal fade" id="modal_unit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">

        <div class="modal-header" style="background-color: #b3d9ff !important;">
          <h5 class="modal-title fw-bold" style="color:#003366">
            <i class="fas fa-plus-circle me-2"></i>Tambah Data Unit
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <form id="form_unit">
            <?= csrf_field() ?>

            <div class="mb-3">
              <label class="form-label fw-bold">Nama Unit / Jurusan <span class="text-danger">*</span></label>
              <input type="text" class="form-control" name="nama_unit" placeholder="Contoh: Teknik Sipil" required>
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold">Kategori <span class="text-danger">*</span></label>
              <select class="form-select" name="kategori" required>
                <option value="" selected disabled>-- Pilih Kategori --</option>
                <option value="Jurusan">Jurusan</option>
                <option value="Unit/Lembaga">Unit / Lembaga</option>
              </select>
            </div>
          </form>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
          <button type="button" class="btn btn-simpan px-4" onclick="simpanUnit()">Simpan</button>
        </div>

      </div>
    </div>
  </div>

  <!-- ========================================== -->
  <!-- MODAL EDIT UNIT -->
  <!-- ========================================== -->
  <div class="modal fade" id="modalUnit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">

        <div class="modal-header modal-header-upa">
          <h5 class="modal-title">
            <i class="fas fa-edit me-2"></i>Edit Unit
          </h5>

          <button type="button" class="btn-close" data-bs-dismiss="modal">
          </button>
        </div>

        <div class="modal-body p-4">
          <form id="formUnit">
            <!-- ID Hidden (Kunci untuk update) -->
            <input type="hidden" id="id_unit_edit" name="id_unit">

            <div class="row mb-3 align-items-center">
              <label class="col-sm-4 col-form-label fw-bold">NAMA UNIT / JURUSAN</label>
              <div class="col-sm-8">
                <!-- Input Nama Unit -->
                <input type="text" class="form-control" id="nama_unit_edit" name="nama_unit" required>
              </div>
            </div>

            <div class="row mb-3 align-items-center">
              <label class="col-sm-4 col-form-label fw-bold">KATEGORI</label>
              <div class="col-sm-8">
                <!-- Dropdown Kategori -->
                <select class="form-select" id="kategori_edit" name="kategori" required>
                  <option value="Jurusan">Jurusan</option>
                  <option value="Unit/Lembaga">Unit/Lembaga</option>
                </select>
              </div>
            </div>
          </form>
        </div>

        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
          <!-- PENTING: Tombol ini memanggil updateUnit() -->
          <button type="button" class="btn btn-warning px-4 text-white" onclick="updateUnit()">Simpan</button>
        </div>

      </div>
    </div>
  </div>

  <!-- FOOTER -->
  <!--<footer>
    <p class="m-0">© 2025 UPAPP POLSRI</p>
  </footer>-->

  <!-- DI AKTIFKAN -->
  <?= $this->include('layout/footer'); ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <canvas id="tempChartCanvas" style="display: none;" width="800" height="400"></canvas>

  <script>
    // Simpan data array alat jika ada dari PHP (Jika kosong, akan bernilai string kosong)
    const alatOptions = ``;
    const BASE_URL = "<?= base_url() ?>";
    const CSRF_TOKEN = "<?= csrf_token() ?>";
    const CSRF_HASH = "<?= csrf_hash() ?>";
  </script>

  <script src="<?= base_url('admin/data_master_unit.js') ?>"></script>

</body>

</html>