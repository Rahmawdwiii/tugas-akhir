<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>UPAPP | Dashboard</title>

  <!-- Bootstrap & FontAwesome -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">

  <style>
    /*Kalau mau pakai footer yg satunya aktifkan ini*/
    .main-body-wrapper {
      display: flex;
      /* Ini kuncinya: menata anak-anaknya (sidebar & content) berdampingan */
    }

    body {
      background-color: #f5f9ff;
      overflow-x: auto;
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

      width: calc(100% - 250px);

      padding: 20px;

      transition: .3s;

      min-height: calc(100vh - 120px);

      background: #f7faff;

      flex: 1;
    }

    .content.full {

      margin-left: 0;

      width: 100%;
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

    /* Memastikan Modal selalu di atas Header (1100) */
    .modal {
      z-index: 9999 !important;
      /* Pastikan ini angka tertinggi */
    }

    .modal-backdrop {
      z-index: 9998 !important;
      /* Sedikit di bawah modal, tapi di atas header */
    }

    /* Memastikan SweetAlert (swal2-container) selalu di atas segalanya */
    .swal2-container {
      z-index: 20000 !important;
      /* Angka ini harus LEBIH BESAR dari 9999 */
    }

    /* Pastikan wrapper DataTables mengisi ruang 100% */
    .dataTables_wrapper {
      width: 100% !important;
    }

    /* Memperbaiki posisi pagination agar selalu di kanan */
    .dataTables_wrapper .dataTables_paginate {
      display: flex;
      justify-content: flex-end;
    }

    /* Jika menggunakan table-responsive, jangan beri margin negatif pada row */
    .table-responsive {
      overflow-x: auto;
      width: 100%;
    }
  </style>
</head>

<body>
  <!-- HEADER -->
  <?= $this->include('layout/header') ?>

  <!-- SIDEBAR -->
  <?= $this->include('layout/sidebar_admin') ?>

  <!-- CONTENT -->
  <section class="content">
    <div class="container-fluid">
      <!-- Filter -->
      <div class="card shadow-sm mb-4">

        <div class="card-body">
          <div class="row g-2 align-items-end mb-4">

            <div class="col-md-3">
              <label class="form-label fw-bold">Tanggal</label>
              <input type="text" id="filter_daterange" class="form-control form-control-sm" placeholder="Cari Tanggal">
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

            <!-- Status filter removed per request -->

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

            <div class="col-md-2">
              <label class="form-label fw-bold">Unit</label>
              <select name="unit" id="filter_unit" class="form-select form-select-sm shadow-sm text-center">
                <option value="" class="text-muted">-- Semua Unit --</option>

                <?php if (!empty($list_unit)): ?>
                  <?php foreach ($list_unit as $u): ?>

                    <option value="<?= esc($u['nama_unit']) ?>" <?= ($filter_unit == $u['nama_unit']) ? 'selected' : '' ?>>
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
        <div class="card-header d-flex justify-content-between align-items-center">
          <h3 class="card-title">DATA PEMINJAMAN</h3>
        </div>
        <div class="card-body">

          <div class="row mb-3">

            <div class="col-12 mb-3">
              <div class="d-flex flex-wrap gap-1 align-items-center">

                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahPeminjaman">
                  <i class="fa fa-plus me-1"></i> Tambah
                </button>

                <button class="btn btn-secondary btn-sm" title="Reload" id="btnReload">
                  <i class="fa fa-undo me-1"></i> Reload
                </button>

                <button type="button" class="btn btn-outline-success btn-sm" id="btnExportExcel"
                  onclick="exportToExcelPeminjaman(this)">
                  <i class="fas fa-file-excel me-1"></i> Excel
                </button>

                <button type="button" class="btn btn-outline-danger btn-sm" id="btnExportPDF"
                  onclick="exportToPDFPeminjaman(this)">
                  <i class="fas fa-file-pdf me-1"></i> PDF
                </button>

                <!-- Show Rows & Search akan dipindah ke sini oleh JS -->
                <div id="search-container" class="d-flex align-items-center gap-1"></div>

              </div>
            </div>

          </div>

          <!-- Tabel -->
          <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover text-center align-middle w-100" id="peminjamanTable">
              <thead class="table-dark">
                <tr>
                  <th>ID</th>
                  <th>NOMOR</th>
                  <th>UNIT/JURUSAN/UKM</th>
                  <th>KEGIATAN/ACARA</th>
                  <th>LOKASI</th>
                  <th>TANGGAL</th>
                  <th>NOMOR IDENTITAS</th>
                  <th>NAMA PEMINJAM</th>
                  <th>HANDPHONE</th>
                  <th>LAMPIRAN</th>
                  <th>KETERANGAN</th>
                  <th>AKSI</th>
                </tr>
              </thead>
              <tbody id="tableBody">
                <?php if (empty($daftar_peminjaman)): ?>
                  <tr class="empty-row">
                    <td colspan="12" class="text-muted">Belum ada data peminjaman</td>
                  </tr>

                <?php else: ?>
                  <?php $no = 1;
                  foreach ($daftar_peminjaman as $row): ?>

                    <tr>
                      <td><?= $no++ ?></td>

                      <td><?= $row['nomor'] ?></td>

                      <td><?= isset($row['nama_unit']) ? $row['nama_unit'] : $row['id_unit'] ?></td>

                      <td><?= $row['kegiatan'] ?></td>

                      <td>
                        <?php
                        if (!empty($row['gedung'])) {
                          echo $row['gedung'] . ' <br><small class="text-muted">' . $row['ruangan'] . '</small>';
                        } else {
                          echo '<span class="badge bg-danger">Lokasi ID: ' . $row['lokasi'] . ' (Hilang)</span>';
                        }
                        ?>
                      </td>

                      <td>
                        <?= date('d/m/Y', strtotime($row['tanggal_mulai'])) ?> <br> s/d <br>
                        <?= date('d/m/Y', strtotime($row['tanggal_selesai'])) ?>
                      </td>

                      <td><?= $row['identitas'] ?></td>

                      <td><?= $row['peminjam'] ?></td>

                      <td><?= $row['handphone'] ?></td>

                      <td>
                        <?php if (!empty($row['lampiran'])): ?>
                          <a href="<?= base_url('uploads/peminjaman/' . $row['lampiran']) ?>" target="_blank"
                            class="btn btn-primary btn-sm">
                            <i class="fas fa-download"></i> Unduh
                          </a>
                        <?php else: ?>
                          <span class="text-muted">-</span>
                        <?php endif; ?>
                      </td>

                      <td><?= $row['keterangan'] ?></td>

                      <td>
                        <div class="d-flex gap-1 justify-content-center">
                          <button class="btn btn-warning btn-sm" title="Edit"
                            onclick="editPeminjaman('<?= esc($row['id_peminjaman']) ?>')"><i
                              class="fas fa-edit"></i></button>
                          <button class="btn btn-danger" onclick="hapusPeminjaman('<?= esc($row['id_peminjaman']) ?>')">
                            <i class="fas fa-trash-alt"></i>
                          </button>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- MODAL TAMBAH PEMINJAMAN -->
  <div class="modal fade" id="modalTambahPeminjaman" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">

        <div class="modal-header">
          <h3 class="modal-title">TAMBAH DATA PEMINJAMAN</h3>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <form id="formTambahPeminjaman">
            <input type="hidden" name="id_peminjaman" id="id_peminjaman" value="">
            <div class="row mb-2">
              <label class="col-md-4 col-form-label text-end">NOMOR</label>
              <div class="col-md-8">
                <input type="text" name="nomor" id="nomor" class="form-control text-center bg-light" readonly>
              </div>
            </div>

            <div class="row mb-2">
              <label class="col-md-4 col-form-label text-end">UNIT/JURUSAN/UKM</label>
              <div class="col-md-8">
                <select name="unit" id="selectUnit" class="form-select text-center">
                  <option value="">-- Pilih Unit --</option>
                  <?php foreach ($daftar_unit as $u): ?>
                    <option value="<?= $u['id_unit'] ?>"><?= $u['nama_unit'] ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div class="row mb-2">
              <label class="col-md-4 col-form-label text-end">LOKASI</label>
              <div class="col-md-8">
                <select name="lokasi" id="selectLokasi" class="form-select text-center" disabled>
                  <option value="">-- Pilih Unit Dulu --</option>

                  <?php foreach ($daftar_lokasi as $loc): ?>
                    <?php
                    // 1. Ambil ID Unit pemilik lokasi ini
                    $owners = $loc['id_unit'];

                    // 2. Pastikan bentuknya array agar konsisten saat difilter JS
                    if (!is_array($owners)) {
                      // Jika string dipisah koma (misal: "1,5"), pecah jadi array
                      $owners = explode(',', str_replace(' ', '', $owners));
                    }

                    // 3. Encode jadi JSON string untuk atribut data-units
                    $ownersJson = json_encode($owners);

                    // 4. Format Teks Tampilan (Opsional: Gabung Gedung + Ruangan)
                    // Sesuaikan dengan nama kolom di tabel Anda
                    $displayText = $loc['gedung'] . ' - ' . $loc['ruangan'];
                    ?>

                    <option value="<?= $loc['id_lokasi'] ?>" data-units='<?= $ownersJson ?>'>
                      <?= $displayText ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <div class="row mb-2">
              <label class="col-md-4 col-form-label text-end">KEGIATAN/ACARA</label>
              <div class="col-md-8"><input type="text" name="kegiatan" class="form-control text-center"></div>
            </div>
            <div class="row mb-2">
              <label class="col-md-4 col-form-label text-end">TANGGAL PEMINJAMAN</label>
              <div class="col-md-3">
                <input type="text" name="mulai" class="form-control text-center" placeholder="Mulai...."
                  onfocus="(this.type='date')" onblur="if(!this.value)this.type='text'">
              </div>
              <div class="col-md-2 text-center">-</div>
              <div class="col-md-3">
                <input type="text" name="selesai" class="form-control text-center" placeholder="Selesai...."
                  onfocus="(this.type='date')" onblur="if(!this.value)this.type='text'">
              </div>
            </div>

            <div class="row mb-2">
              <label class="col-md-4 col-form-label text-end">NAMA BARANG & JUMLAH</label>
              <div class="col-md-8"><textarea name="keterangan" class="form-control" rows="3"></textarea></div>
            </div>
            <div class="row mb-2">
              <label class="col-md-4 col-form-label text-end">NOMOR IDENTITAS</label>
              <div class="col-md-8"><input type="text" name="identitas" class="form-control text-center"></div>
            </div>
            <div class="row mb-2">
              <label class="col-md-4 col-form-label text-end">NAMA PEMINJAM</label>
              <div class="col-md-8"><input type="text" name="peminjam" class="form-control text-center"></div>
            </div>
            <div class="row mb-2">
              <label class="col-md-4 col-form-label text-end">NOMOR HANDPHONE</label>
              <div class="col-md-8"><input type="text" name="handphone" class="form-control text-center"></div>
            </div>
            <div class="row mb-2">
              <label class="col-md-4 col-form-label text-end">LAMPIRAN</label>
              <div class="col-md-8">
                <input type="file" name="lampiran" class="form-control">
                <small class="text-muted">Format .jpg/.jpeg, max 500KB</small>
              </div>
            </div>
          </form>
        </div>

        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">BATAL</button>
          <button type="button" class="btn btn-primary" onclick="savePeminjaman()">SIMPAN</button>
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

  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <canvas id="tempChartCanvas" style="display: none;" width="800" height="400"></canvas>

  <script>
    // Simpan base_url ke dalam variabel JS agar bisa diakses di file eksternal
    const BASE_URL = "<?= base_url() ?>";
  </script>

  <script src="<?= base_url('admin/laporan_peminjaman.js') ?>"></script>

</body>

</html>