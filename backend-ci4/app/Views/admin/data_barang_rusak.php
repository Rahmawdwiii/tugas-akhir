<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>UPAPP | Dashboard</title>

  <!-- Bootstrap & FontAwesome -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
  <link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

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
      /*Kalau mau pakai footer yg satunya hapus ini */
      top: 0;
      /*Kalau mau pakai footer yg satunya hapus ini */
      left: 0;
      /*Kalau mau pakai footer yg satunya hapus ini */
      right: 0;
      /*Kalau mau pakai footer yg satunya hapus ini */
      z-index: 1100;
      /*Kalau mau pakai footer yg satunya hapus ini */
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
      /*Kalau mau pakai footer yg satunya hapus ini */
      top: 60px;
      /*Kalau mau pakai footer yg satunya hapus ini */
      left: 0;
      /*Kalau mau pakai footer yg satunya hapus ini */
      width: 250px;
      height: calc(100vh - 60px);
      /*Kalau mau pakai footer yg satunya hapus ini */
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
      width: calc(100% - 250px);

      transition: .3s;
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

    /* BUTTONS & TOOLBAR STYLES */
    .dt-buttons .btn {
      margin-right: 5px;
      font-size: 0.9rem;
    }

    #customToolbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 10px;
    }

    #search-container {
      margin-left: auto;
    }

    #table_barang_rusak_filter {
      margin: 0;
    }

    #table_barang_rusak_filter label {
      display: flex;
      align-items: center;
      gap: 8px;
      margin: 0;
      font-weight: 500;
    }

    #table_barang_rusak_filter input {
      width: 240px;
    }

    #customToolbar .btn-group {
      display: flex;
      gap: 5px;
    }

    #search-container {
      margin-left: auto !important;
    }

    #search-container .form-control {
      min-width: 200px;
    }

    .buttons-columnVisibility {
      max-height: 300px;
      overflow-y: auto;
    }

    .cursor-pointer {
      cursor: pointer;
    }

    .list-group-item:hover {
      background-color: #f8f9fa;
    }

    #columnVisibilityMenu .dropdown-item {
      padding: 0.5rem 0.75rem;
      border-radius: 4px;
      transition: background-color 0.2s;
    }

    #columnVisibilityMenu .dropdown-item:hover {
      background-color: #f0f0f0;
    }

    #columnVisibilityMenu .form-check-input {
      margin-top: 0.125rem;
    }
  </style>
</head>

<body>
  <!-- HEADER -->
  <?= $this->include('layout/header') ?>

  <!-- SIDEBAR -->
  <?= $this->include('layout/sidebar_admin') ?>

  <div class="content p-4">
    <!-- Info Box -->
    <!-- Info Box -->
    <div class="row mb-4">
      <div class="col-lg-3 col-md-6 col-12">
        <div class="small-box bg-danger text-white p-3 rounded shadow">
          <div class="inner">
            <h3><?= $total_rusak ?></h3>
            <p>Laporan Kerusakan Tidak Dapat Diperbaiki / Rusak</p>
          </div>
          <div class="icon fs-1">
            <i class="fas fa-clipboard-list"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Filter -->
    <div class="card shadow-sm mb-4">
      <div class="card-body">
        <div class="row g-2 align-items-end mb-4">

          <div class="col-md-3">
            <label class="form-label fw-bold">Tanggal</label>
            <input type="text" id="filter_tanggal" class="form-control form-control-sm" placeholder="Cari Tanggal">
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
              $tahun_mulai = 2025;
              $tahun_sekarang = date('Y');
              for ($t = $tahun_mulai; $t <= $tahun_sekarang; $t++) {
                echo "<option value=\"$t\">$t</option>";
              }
              ?>
            </select>
          </div>

          <div class="col-md-2">
            <label class="form-label fw-bold">Unit</label>

            <select name="unit" id="filter_unit" class="form-select form-select-sm shadow-sm text-center">

              <option value="">
                -- Semua Unit --
              </option>

              <?php foreach ($listUnit as $unit): ?>
                <option value="<?= esc($unit['nama_unit']) ?>">
                  <?= esc($unit['nama_unit']) ?>
                </option>
              <?php endforeach; ?>

            </select>
          </div>

          <!--<div class="col-md-1 d-grid">
            <label class="form-label small fw-bold text-muted mb-1">&nbsp;</label>
            <button type="button" class="btn btn-primary btn-sm" onclick="window.print()">
              <i class="fas fa-print"></i>
            </button>
          </div> -->

        </div>
      </div>
    </div>

    <!-- Table Card -->
    <div class="card">
      <div class="card-header bg-black text-white">
        <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i> DATA BARANG RUSAK</h5>
      </div>

      <div class="card-body">
        <!-- Toolbar -->
        <!-- Toolbar -->
        <div id="customToolbar" class="mb-3 d-flex justify-content-between align-items-center flex-wrap">

          <!-- Bagian Kiri -->
          <div class="d-flex align-items-center gap-2">

            <!-- Reload -->
            <button class="btn btn-secondary btn-sm" title="Reload" id="btnReload">
              <i class="fa fa-undo me-1"></i>
              Reload
            </button>

            <!-- Export Excel -->
            <button class="btn btn-outline-success btn-sm" onclick="exportToExcel()">
              <i class="fas fa-file-excel me-1"></i>
              Excel
            </button>

            <!-- Dropdown Kolom -->
            <div class="dropdown">

              <button class="btn btn-outline-info btn-sm dropdown-toggle" type="button" id="btnColumnVisibility"
                data-bs-toggle="dropdown" aria-expanded="false">

                <i class="fas fa-columns me-1"></i>
                Kolom Ditampilkan

              </button>

              <ul class="dropdown-menu dropdown-menu-end p-2" id="columnVisibilityMenu"
                aria-labelledby="btnColumnVisibility" style="min-width:250px;max-height:300px;overflow-y:auto;">

              </ul>

            </div>

          </div>

          <!-- Bagian Kanan -->
          <div id="search-container"></div>

        </div>

        <!-- Table -->
        <div class="table-responsive p-0">
          <table id="table_barang_rusak" class="table table-striped table-bordered text-center w-100">
            <thead>
              <tr class="table-dark">
                <th>No Laporan</th>
                <th>Tanggal</th>
                <th>Nama Pelapor</th>
                <th>Nama Alat</th>
                <th>No Inventaris</th>
                <th>Lokasi Alat</th>
                <th>Jurusan / Unit</th>
                <th>Teknisi</th>
                <th>Keterangan</th>
                <th>Status Kerusakan</th>
                <th>Hasil Perbaikan</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody id="tableBody">
              <?php if (!empty($daftar_rusak)): ?>
                <?php foreach ($daftar_rusak as $row): ?>
                  <tr>
                    <td><?= esc($row['nomor_laporan']) ?></td>
                    <td><?= date('d/m/Y', strtotime($row['tanggal_laporan'])) ?></td>
                    <td>
                      <?= esc($row['nama_pelapor']) ?>
                    </td>
                    <td><?= esc($row['nama_alat']) ?></td>
                    <td><?= esc($row['nomor_inventaris']) ?></td>
                    <td><?= esc($row['lokasi']) ?></td>
                    <td><?= esc($row['unit']) ?></td>
                    <td><?= esc($row['nama_teknisi'] ?? '-') ?></td>
                    <td><?= esc($row['catatan_teknisi'] ?? '-') ?></td>

                    <td>
                      <?= esc($row['status_kerusakan']) ?>
                    </td>

                    <td>
                      <span class="badge bg-danger">
                        <?= esc($row['hasil_perbaikan']) ?>
                      </span>
                    </td>
                    <td>
                      <button class="btn btn-sm btn-danger" onclick="hapusBarangRusak('<?= esc($row['nomor_laporan']) ?>')"
                        title="Hapus">
                        <i class="fas fa-trash"></i>
                      </button>
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
</body>

<!-- FOOTER -->
<!--<footer>
    <p class="m-0">© 2025 UPAPP POLSRI</p>
  </footer>-->

<!-- DI AKTIFKAN -->
<?php echo $this->include('layout/footer'); ?>

<script>
  const BASE_URL = "<?= base_url() ?>";
</script>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  let table; // DataTable instance

  $(document).ready(function () {
    // ========================================
    // 1. INISIALISASI DATATABLE
    // ========================================
    table = $('#table_barang_rusak').DataTable({
      pageLength: 10,
      lengthMenu: [5, 10, 25, 50, 100],

      searching: true,
      lengthChange: false,

      scrollX: true,
      autoWidth: false,


      language: {
        info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ entri',
        infoEmpty: 'Menampilkan 0 sampai 0 dari 0 entri',
        paginate: {
          previous: 'Sebelumnya',
          next: 'Berikutnya'
        }
      }
    });

    // pindahkan search bawaan DataTables
    $('#table_barang_rusak_filter').appendTo('#search-container');


    // ========================================
    // 3. INISIALISASI FLATPICKR (DATE RANGE)
    // ========================================
    flatpickr("#filter_tanggal", {
      mode: "range",
      dateFormat: "d/m/Y",

      altInput: true,
      altFormat: "j F Y",

      locale: {
        rangeSeparator: " s/d "
      },

      onReady: function (selectedDates, dateStr, instance) {

        const footer = document.createElement("div");

        footer.classList.add(
          "d-flex",
          "justify-content-between",
          "p-2",
          "border-top",
          "bg-white"
        );

        const clearBtn = document.createElement("button");
        clearBtn.type = "button";
        clearBtn.className =
          "btn btn-sm btn-link text-danger fw-bold text-decoration-none";
        clearBtn.innerText = "Clear";

        clearBtn.onclick = () => {
          instance.clear();
          table.draw();
          instance.close();
        };

        const todayBtn = document.createElement("button");
        todayBtn.type = "button";
        todayBtn.className =
          "btn btn-sm btn-link text-primary fw-bold text-decoration-none";
        todayBtn.innerText = "Hari Ini";

        todayBtn.onclick = () => {
          instance.setDate([new Date(), new Date()], true);
          table.draw();
          instance.close();
        };

        footer.appendChild(clearBtn);
        footer.appendChild(todayBtn);

        instance.calendarContainer.appendChild(footer);
      },

      onClose: function (selectedDates, dateStr, instance) {

        if (selectedDates.length === 1) {
          instance.setDate(
            [selectedDates[0], selectedDates[0]],
            true
          );
        }

      },

      onChange: function (selectedDates) {

        if (selectedDates.length === 2 || selectedDates.length === 0) {
          table.draw();
        }

      }
    });

    // ========================================
    // 4. EVENT LISTENER UNTUK FILTER BULAN, TAHUN, STATUS, UNIT
    // ========================================
    $('#cetak_bulan, #cetak_tahun, #filter_unit').on('change', function () {
      table.draw();
    });

    // ========================================
    // 6. RELOAD BUTTON
    // ========================================
    const btnReload = document.getElementById('btnReload');
    if (btnReload) {
      btnReload.addEventListener('click', function () {
        const icon = this.querySelector('i');
        if (icon) {
          icon.classList.remove('fa-undo');
          icon.classList.add('fa-spinner', 'fa-spin');
        }
        location.reload();
      });
    }
  });

  // ========================================
  // CUSTOM SEARCH FUNCTION
  // ========================================
  $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {

    // Pastikan hanya untuk tabel ini
    if (settings.nTable.id !== 'table_barang_rusak') {
      return true;
    }

    const dateRange = $('#filter_tanggal').val();
    const bulan = $('#cetak_bulan').val();
    const tahun = $('#cetak_tahun').val();
    const unit = $('#filter_unit').val();

    // ==========================
    // KOLOM DATA
    // ==========================
    const tanggal = data[1].trim();
    const unitData = data[6].trim();

    // ==========================
    // FILTER TANGGAL
    // ==========================
    if (dateRange) {

      const parts = dateRange.split(" to ");

      const parseDate = (str) => {
        const p = str.split('/');
        return new Date(p[2], p[1] - 1, p[0]);
      };

      const rowDate = parseDate(tanggal);

      if (parts.length === 2) {

        const start = parseDate(parts[0]);
        const end = parseDate(parts[1]);

        if (rowDate < start || rowDate > end) {
          return false;
        }

      } else {

        const selected = parseDate(parts[0]);

        if (rowDate.getTime() !== selected.getTime()) {
          return false;
        }
      }
    }

    // ==========================
    // FILTER BULAN
    // ==========================
    if (bulan) {

      const p = tanggal.split('/');

      if (p[1] !== bulan) {
        return false;
      }

    }

    // ==========================
    // FILTER TAHUN
    // ==========================
    if (tahun) {

      const p = tanggal.split('/');

      if (p[2] !== tahun) {
        return false;
      }

    }

    // ==========================
    // FILTER UNIT
    // ==========================
    if (unit) {

      if (unitData !== unit) {
        return false;
      }

    }

    return true;

  });

  // ========================================
  // SIDEBAR TOGGLE
  // ========================================
  const footerContent = document.querySelector('.footer-content');
  const toggleSidebar = document.getElementById('toggleSidebar');
  const sidebar = document.getElementById('sidebar');
  const content = document.querySelector('.content');
  const links = document.querySelectorAll('#sidebar .nav-link');

  if (toggleSidebar) {
    toggleSidebar.addEventListener('click', () => {
      sidebar.classList.toggle('collapsed');
      content.classList.toggle('full');
      if (footerContent) footerContent.classList.toggle('full');
    });
  }

  links.forEach(link => {
    link.addEventListener('click', function () {
      links.forEach(l => l.classList.remove('active'));
      this.classList.add('active');
    });
  });

  // ========================================
  // HAPUS BARANG RUSAK
  // ========================================
  window.hapusBarangRusak = function (nomorLaporan) {
    Swal.fire({
      title: 'Hapus Data?',
      text: 'Data laporan ini akan dihapus permanen.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Ya, Hapus!',
      cancelButtonText: 'Batal'
    }).then(result => {
      if (result.isConfirmed) {
        fetch(`${BASE_URL}admin/hapus_laporan`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: `nomor_laporan=${nomorLaporan}`
        })
          .then(res => res.json())
          .then(data => {
            if (data.status === 'success') {
              Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                timer: 1500,
                showConfirmButton: false
              }).then(() => location.reload());
            } else {
              Swal.fire('Gagal', data.message, 'error');
            }
          })
          .catch(err => {
            console.error(err);
            Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
          });
      }
    });
  };

  // ========================================
  // FUNGSI EXPORT EXCEL
  // ========================================
  window.exportToExcel = async function () {

    const btn = document.querySelector('.btn-outline-success');
    const originalText = btn ? btn.innerHTML : 'Excel';

    if (btn) {
      btn.disabled = true;
      btn.innerHTML =
        '<i class="fas fa-spinner fa-spin"></i> Generate...';
    }

    try {

      const table =
        $('#table_barang_rusak').DataTable();

      const allData =
        table.rows({ search: 'applied' })
          .data()
          .toArray();

      if (allData.length === 0) {

        Swal.fire({
          icon: 'warning',
          title: 'Perhatian',
          text: 'Tidak ada data untuk diexport.'
        });

        if (btn) {
          btn.disabled = false;
          btn.innerHTML = originalText;
        }

        return;
      }

      const workbook =
        new ExcelJS.Workbook();

      const worksheet =
        workbook.addWorksheet(
          'Data Barang Rusak'
        );

      // =====================================
      // HEADER UTAMA
      // =====================================

      worksheet.mergeCells('A1:L1');
      worksheet.getCell('A1').value =
        'UPA PERAWATAN DAN PERBAIKAN POLITEKNIK NEGERI SRIWIJAYA';

      worksheet.getCell('A1').font = {
        bold: true,
        size: 16,
        color: { argb: 'FFFFFFFF' }
      };

      worksheet.getCell('A1').fill = {
        type: 'pattern',
        pattern: 'solid',
        fgColor: { argb: 'FF003366' }
      };

      worksheet.getCell('A1').alignment = {
        horizontal: 'center',
        vertical: 'middle'
      };

      worksheet.getRow(1).height = 25;

      // =====================================
      // JUDUL LAPORAN
      // =====================================

      worksheet.mergeCells('A3:L3');
      worksheet.getCell('A3').value =
        'LAPORAN DATA BARANG RUSAK';

      worksheet.getCell('A3').font = {
        bold: true,
        size: 14
      };

      worksheet.getCell('A3').alignment = {
        horizontal: 'center'
      };

      // =====================================
      // TANGGAL CETAK
      // =====================================

      worksheet.mergeCells('A4:L4');
      worksheet.getCell('A4').value =
        `Tanggal Cetak : ${new Date().toLocaleDateString('id-ID')}`;

      worksheet.getCell('A4').alignment = {
        horizontal: 'center'
      };

      // =====================================
      // HEADER TABEL
      // =====================================

      const headers = [
        'No',
        'No. Laporan',
        'Tanggal',
        'Nama Pelapor',
        'Nama Alat',
        'No. Inventaris',
        'Lokasi',
        'Unit',
        'Teknisi',
        'Catatan Teknisi',
        'Status Kerusakan',
        'Hasil Perbaikan'
      ];

      const headerRow = worksheet.getRow(6);

      headers.forEach((header, index) => {

        const cell = headerRow.getCell(index + 1);

        cell.value = header;

        cell.font = {
          bold: true,
          color: { argb: 'FFFFFFFF' }
        };

        cell.fill = {
          type: 'pattern',
          pattern: 'solid',
          fgColor: { argb: 'FF1F4E78' }
        };

        cell.alignment = {
          horizontal: 'center',
          vertical: 'middle',
          wrapText: true
        };

      });

      headerRow.height = 22;

      // =====================================
      // LEBAR KOLOM
      // =====================================

      worksheet.getColumn(1).width = 6;
      worksheet.getColumn(2).width = 18;
      worksheet.getColumn(3).width = 15;
      worksheet.getColumn(4).width = 25; // Nama Pelapor
      worksheet.getColumn(5).width = 25; // Nama Alat
      worksheet.getColumn(6).width = 18;
      worksheet.getColumn(7).width = 35;
      worksheet.getColumn(8).width = 20;
      worksheet.getColumn(9).width = 18;
      worksheet.getColumn(10).width = 40;
      worksheet.getColumn(11).width = 18;
      worksheet.getColumn(12).width = 20;

      // =====================================
      // CLEAN HTML
      // =====================================

      const cleanHtml = (html) => {

        if (!html) return '';

        const div = document.createElement('div');

        div.innerHTML = html;

        return div.textContent.trim();
      };

      // =====================================
      // DATA
      // =====================================

      allData.forEach((row, idx) => {

        const excelRow = worksheet.addRow([
          idx + 1,
          row[0],
          row[1],
          row[2],
          row[3],
          row[4],
          row[5],
          row[6],
          row[7],
          cleanHtml(row[8]),
          cleanHtml(row[9]),
          cleanHtml(row[10])
        ]);

        excelRow.alignment = {
          horizontal: 'center',
          vertical: 'middle',
          wrapText: true
        };

      });

      // =====================================
      // BORDER
      // =====================================

      worksheet.eachRow((row, rowNumber) => {

        if (rowNumber >= 6) {

          row.eachCell((cell) => {

            cell.border = {
              top: { style: 'thin' },
              left: { style: 'thin' },
              bottom: { style: 'thin' },
              right: { style: 'thin' }
            };

          });

        }

      });

      // =====================================
      // DOWNLOAD FILE
      // =====================================

      const buffer =
        await workbook.xlsx.writeBuffer();

      const blob =
        new Blob(
          [buffer],
          {
            type:
              'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
          }
        );

      const url =
        window.URL.createObjectURL(blob);

      const link =
        document.createElement('a');

      link.href = url;

      link.download =
        `Data_Barang_Rusak_${new Date()
          .toISOString()
          .split('T')[0]}.xlsx`;

      link.click();

      window.URL.revokeObjectURL(url);

      if (btn) {
        btn.disabled = false;
        btn.innerHTML = originalText;
      }

    } catch (error) {

      console.error(error);

      Swal.fire(
        'Error',
        'Gagal mengexport data: ' + error.message,
        'error'
      );

      if (btn) {
        btn.disabled = false;
        btn.innerHTML = originalText;
      }
    }
  };

  // ========================================
  // COLUMN VISIBILITY DROPDOWN
  // ========================================
  function initColumnVisibilityDropdown() {
    const columnMenu = document.getElementById('columnVisibilityMenu');
    if (!columnMenu || !table) return;

    columnMenu.innerHTML = ''; // Clear existing items
    const columns = table.settings()[0].aoColumns;

    columns.forEach((col, index) => {
      const header = col.sTitle || 'Kolom ' + (index + 1);
      const visible = col.bVisible;

      if (header !== 'Aksi') {
        const li = document.createElement('li');
        li.className = 'px-0';
        li.innerHTML = `
          <label class="dropdown-item d-flex align-items-center gap-2 cursor-pointer mb-0">
            <input class="form-check-input" type="checkbox" value="${index}" ${visible ? 'checked' : ''} 
                   onchange="toggleColumn(${index}, this.checked)">
            <span>${header}</span>
          </label>
        `;
        columnMenu.appendChild(li);
      }
    });
  }

  // Initialize dropdown on table ready
  setTimeout(() => {
    initColumnVisibilityDropdown();
  }, 500);

  window.toggleColumn = function (index, visible) {
    if (table) {
      table.column(index).visible(visible);
    }
  };
</script>

</html>