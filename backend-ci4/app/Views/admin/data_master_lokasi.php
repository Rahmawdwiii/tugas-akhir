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
      padding-left: 30px;
      display: none;
    }

    .submenu.show {
      display: block;
    }

    /* CONTENT */
    .content {
      margin-left: 250px;
      /*Kalau mau pakai footer yg satunya hapus ini */
      margin-top: 60px;
      /*Kalau mau pakai footer yg satunya hapus ini */
      padding: 20px;
      transition: all 0.3s;
      min-height: calc(100vh - 120px);
      /*Kalau mau pakai footer yg satunya hapus ini */
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

    .modal {
      z-index: 2000 !important;
    }

    .modal-backdrop {
      z-index: 1900 !important;
    }

    /* --- TAMBAHAN CSS UNTUK TABEL RAPI --- */
    .table-fixed {
      table-layout: fixed;
      /* Kunci agar lebar kolom tidak berubah-ubah */
      width: 100%;
    }

    .table-fixed td {
      white-space: normal !important;
      /* Paksa teks turun ke bawah (wrap) */
      word-wrap: break-word;
      /* Potong kata jika terlalu panjang tanpa spasi */
      vertical-align: middle;
      /* Posisi teks di tengah vertikal */
      font-size: 14px;
      /* Opsional: perkecil huruf sedikit agar muat banyak */
    }
  </style>
</head>

<body>
  <!-- HEADER -->
  <?= $this->include('layout/header') ?>

  <!-- SIDEBAR -->
  <?= $this->include('layout/sidebar_admin') ?>

  <div class="content">
    <div class="container-fluid">
      <div class="card shadow-sm border-0">

        <!-- HEADER -->
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
          <h5 class="mb-0">
            <i class="fas fa-map-marker-alt me-2"></i> DATA MASTER LOKASI
          </h5>

          <div>
            <button class="btn btn-light btn-sm" title="Tambah" onclick="tambahModalLokasi()">
              <i class="fa fa-plus"></i>
            </button>
            <button class="btn btn-light btn-sm" title="Reload" id="btnReload">
              <i class="fa fa-undo"></i>
            </button>
            <button class="btn btn-light btn-sm" title="Copy" id="btnCopy">
              <i class="fas fa-copy"></i>
            </button>
            <button class="btn btn-light btn-sm" title="Excel" onclick="exportToExcel()">
              <i class="fas fa-file-excel"></i>
            </button>
          </div>
        </div>

        <!-- BODY -->
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="dt-buttons">
              <!--<button class="btn btn-outline-info btn-sm"><i class="fas fa-file-csv"></i> CSV</button>-->
              <!--<button class="btn btn-outline-danger btn-sm"><i class="fas fa-file-pdf"></i> PDF</button>-->
            </div>
            <div class="form-group d-flex align-items-center">
              <label class="me-2 mb-0 fw-semibold text-secondary">Cari:</label>
              <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Ketik lokasi..."
                style="width:200px;">
            </div>
          </div>

          <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover text-center align-middle">
              <thead class="table-primary">
                <tr>
                  <!-- Atur Lebar Kolom Disini (Total harus 100%) -->
                  <th style="width: 5%;">ID</th>
                  <th style="width: 20%;">UNIT / JURUSAN</th>
                  <th style="width: 15%;">GEDUNG</th>
                  <th style="width: 10%;">LANTAI</th>
                  <th style="width: 25%;">RUANGAN</th> <!-- Beri porsi besar agar muat banyak teks -->
                  <th style="width: 15%;">KAMPUS</th>
                  <th style="width: 10%;">AKSI</th>
                </tr>
              </thead>
              <tbody id="tableBody">
                <?php
                $page = $currentPage ?? 1;
                $nomor = 1 + (10 * ($page - 1));
                ?>

                <?php if (!empty($daftar_lokasi)): ?>
                  <?php foreach ($daftar_lokasi as $lokasi): ?>
                    <tr>
                      <td><?= $nomor++ ?></td>

                      <td><?= esc($lokasi['nama_unit']) ?></td>
                      <td><?= esc($lokasi['gedung']) ?></td>
                      <td><?= esc($lokasi['lantai']) ?></td>
                      <td><?= esc($lokasi['ruangan']) ?></td>
                      <td><?= esc($lokasi['kampus']) ?></td>

                      <td>
                        <button class="btn btn-warning btn-sm text-white" title="Edit"
                          onclick="editLokasi(<?= $lokasi['id_lokasi'] ?>)">
                          <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" title="Hapus"
                          onclick="hapusLokasi(<?= $lokasi['id_lokasi'] ?>)">
                          <i class="fas fa-trash"></i>
                        </button>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="7" class="text-center">Data Kosong</td>
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

  <!-- ========================================== -->
  <!-- MODAL TAMBAH LOKASI -->
  <!-- ========================================== -->
  <div class="modal fade" id="modal_lokasi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i> Tambah Data Lokasi</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <form id="form_tambah_lokasi">

            <!-- 1. PILIH UNIT (Sumber Filter) -->
            <div class="mb-3">
              <label class="form-label fw-bold">Pilih Unit / Jurusan</label>
              <select class="form-select" name="id_unit" id="selectUnit" required>
                <option value="">-- Pilih Unit Terlebih Dahulu --</option>
                <?php foreach ($daftar_unit as $u): ?>
                  <option value="<?= $u['id_unit'] ?>"><?= $u['nama_unit'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <!-- Input Lainnya -->
            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label class="form-label fw-bold">Kampus</label>

                <input type="text" class="form-control" name="kampus" list="daftarKampus"
                  placeholder="Contoh: Kampus Bukit" required>

                <datalist id="daftarKampus">
                  <?php foreach ($daftar_kampus as $kampus): ?>
                    <option value="<?= esc($kampus['kampus']) ?>">
                    <?php endforeach; ?>
                </datalist>
              </div>

              <!-- 2. INPUT GEDUNG -->
              <div class="mb-3">
                <label class="form-label fw-bold">Gedung</label>
                <input type="text" class="form-control" name="gedung" id="selectGedung"
                  placeholder="Masukkan nama gedung" required>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-bold">Lantai</label>
                <input type="number" class="form-control" name="lantai" placeholder="Contoh: 2" required>
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold">Nama Ruangan</label>
              <input type="text" class="form-control" name="ruangan" placeholder="Contoh: Lab Jaringan" required>
            </div>
          </form>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="button" class="btn btn-primary" onclick="simpanLokasi()">Simpan</button>
        </div>
      </div>
    </div>
  </div>

  <!-- ========================================== -->
  <!-- MODAL EDIT LOKASI -->
  <!-- ========================================== -->
  <div class="modal fade" id="modalLokasi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">

        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="modalLokasiLabel">Edit Data Lokasi</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body p-4">
          <form id="formLokasi">
            <input type="hidden" id="id_lokasi_edit" name="id_lokasi">

            <div class="row mb-3 align-items-center">
              <label class="col-sm-4 col-form-label fw-bold">ID UNIT / JURUSAN</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="id_unit_edit" name="id_unit" readonly
                  style="background-color: #e9ecef;">
                <small class="text-muted">*Unit tidak dapat diubah di menu ini</small>
              </div>
            </div>

            <div class="row mb-3 align-items-center">
              <label class="col-sm-4 col-form-label fw-bold">GEDUNG</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="gedung_edit" name="gedung">
              </div>
            </div>

            <div class="row mb-3 align-items-center">
              <label class="col-sm-4 col-form-label fw-bold">LANTAI</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="lantai_edit" name="lantai">
              </div>
            </div>

            <div class="row mb-3 align-items-center">
              <label class="col-sm-4 col-form-label fw-bold">RUANGAN</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="ruangan_edit" name="ruangan">
              </div>
            </div>

            <div class="row mb-3 align-items-center">
              <label class="col-sm-4 col-form-label fw-bold">KAMPUS</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="kampus_edit" name="kampus">
              </div>
            </div>
          </form>
        </div>

        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
          <button type="button" class="btn btn-primary px-4 text-white" onclick="updateLokasi()">Simpan</button>
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
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <canvas id="tempChartCanvas" style="display: none;" width="800" height="400"></canvas>

  <script>
    const BASE_URL = "<?= base_url() ?>";
    const CSRF_TOKEN = "<?= csrf_token() ?>";
    const CSRF_HASH = "<?= csrf_hash() ?>";
  </script>

  <script src="<?= base_url('admin/data_master_lokasi.js') ?>"></script>

</body>

</html>