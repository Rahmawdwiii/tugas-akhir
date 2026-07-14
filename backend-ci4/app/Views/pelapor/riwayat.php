<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>UPAPP | Dashboard</title>

  <!-- Bootstrap & FontAwesome -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">

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

    h5.fw-bold {
      color: #003366;
      font-weight: 700;
    }

    .table th {
      font-weight: 600;
    }

    /* Tweaks agar tombol dan search DataTables sama dengan Teknisi */
    .dt-buttons .btn {
      margin-right: 5px;
      /* Jarak antar tombol */
    }

    .dataTables_filter input {
      display: inline-block;
      width: 250px !important;
      border-radius: 8px;
    }

    /* ===================================================
       TWEAKS DATATABLES AGAR KEMBAR DENGAN TEKNISI
       =================================================== */
    /* 1. Reset Bentuk Tombol DataTables agar KOTAK seperti Bootstrap */
    button.dt-button,
    .dt-buttons .btn {
      border-radius: 4px !important;
      background-image: none !important;
      box-shadow: none !important;
      height: 31px !important;
      /* Tinggi seragam */
      padding: 0.25rem 0.5rem !important;
      font-size: 0.875rem !important;
      display: inline-flex !important;
      align-items: center;
      justify-content: center;
      gap: 5px;
    }

    /* 2. KHUSUS Tombol 'Show Rows' -> WARNA ABU (Secondary) */
    button.buttons-page-length {
      background-color: #6c757d !important;
      border-color: #6c757d !important;
      color: white !important;
    }

    button.buttons-page-length:hover {
      background-color: #5c636a !important;
      border-color: #565e64 !important;
    }

    /* 3. Percantik Kotak Pencarian */
    .dataTables_filter input {
      display: inline-block;
      width: 250px !important;
      border-radius: 4px !important;
      height: 31px !important;
    }
  </style>
</head>

<body>
  <!-- HEADER -->
  <?= $this->include('layout/header') ?>

  <!-- SIDEBAR -->
  <?= $this->include('layout/sidebar_pelapor') ?>

  <!-- CONTENT -->
  <main class="content" id="mainContent">
    <div class="container-fluid">

      <!-- Judul -->
      <div class="row mb-3">
        <div class="col-12">
          <h4 class="fw-bold"><i class="fas fa-history me-2"></i> Riwayat Laporan Saya</h4>
        </div>
      </div>

      <!-- Filter -->
      <div class="row mb-3 g-3 align-items-center">
        <div class="col-md-3">
          <input type="date" id="filterTanggal" class="form-control" placeholder="Filter Tanggal">
        </div>
        <div class="col-md-3">
          <select id="filterStatus" class="form-control">
            <option value="">-- Filter Status --</option>
            <option value="Menunggu">Menunggu Validasi</option>
            <option value="Sedang">Sedang Diperbaiki</option>
            <option value="Selesai">Selesai</option>
          </select>
        </div>
        <div class="col-md-2">
          <button class="btn btn-primary w-100" onclick="filterRiwayat()"><i class="fas fa-filter me-2"></i>
            Filter</button>
        </div>
      </div>

      <!-- Tabel -->
      <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
          <h5 class="mb-0"><i class="fas fa-list me-2"></i> Daftar Laporan</h5>
        </div>
        <div class="card-body">
          <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
            <div class="dt-buttons btn-group shadow-sm">
              <button class="btn btn-secondary btn-sm" title="Reload" id="btnReload">
                <i class="fa fa-undo me-1"></i> Reload
              </button>
              <button class="btn btn-outline-dark btn-sm" id="btnCopy" title="Copy">
                <i class="fas fa-copy"></i> Copy
              </button>
              <button class="btn btn-outline-success btn-sm" onclick="exportToExcel()"><i class="fas fa-file-excel"></i>
                Excel</button>
              <button type="button" class="btn btn-outline-danger btn-sm" id="btnExportPDF"
                onclick="exportToPDFKerusakan(this)">
                <i class="fas fa-file-pdf me-1"></i> PDF
              </button>
            </div>

            <div id="search-container" class="d-flex align-items-center gap-2"></div>
          </div>
          <div class="table-responsive">
            <table id="riwayatTable" class="table table-bordered table-striped text-center align-middle">
              <thead class="table-light">
                <tr>
                  <th>NO LAPORAN</th>
                  <th>TANGGAL LAPORAN</th>
                  <th>TANGGAL PERBAIKAN</th>
                  <th>NAMA ALAT</th>
                  <th>LOKASI</th>
                  <th>JURUSAN/UNIT</th>
                  <th>STATUS PERBAIKAN</th>
                  <th>STATUS KERUSAKAN</th>
                  <th>KERUSAKAN/KELUHAN</th>
                  <th>HASIL PERBAIKAN</th>
                  <th>AKSI</th>
                </tr>
              </thead>
              <tbody id="riwayatTableBody">
                <?php if (!empty($riwayatList) && is_array($riwayatList)): ?>
                  <?php foreach ($riwayatList as $riwayat): ?>
                    <tr class="empty-row">
                      <td><?= esc($riwayat['nomor_laporan'] ?? '-') ?></td>
                      <td><?= esc($riwayat['tanggal_laporan'] ?? '-') ?></td>
                      <td>
                        <?php
                        // Ambil nilai tanggal perbaikan, default ke string kosong jika null/undefined.
                        $tanggal_perbaikan = $riwayat['tanggal_perbaikan'] ?? '';

                        // Tentukan teks dan kelas badge berdasarkan status
                        if (empty($tanggal_perbaikan) || $tanggal_perbaikan === '-') {
                          // KONDISI 1: Belum ditentukan (null, kosong, atau hanya tanda hubung '-')
                          $badge_class = 'bg-primary text-white';
                          $text_output = 'Belum ditentukan';
                        } else {
                          // KONDISI 2: Sudah ditentukan (Ada tanggal, misal: '2025-11-15')
                          $badge_class = 'bg-success text-white';
                          // Menggunakan esc() untuk keamanan
                          $text_output = esc($tanggal_perbaikan);
                        }
                        ?>

                        <span class="badge <?= $badge_class ?>">
                          <?= $text_output ?>
                        </span>
                      </td>
                      <td><?= esc($riwayat['nama_alat'] ?? '-') ?></td>
                      <td><?= esc($riwayat['lokasi'] ?? '-') ?></td>
                      <td><?= esc($riwayat['unit'] ?? '-') ?></td>

                      <!-- STATUS PERBAIKAN -->
                      <td>
                        <?php
                        $statusPerbaikan = trim((string) ($riwayat['status_perbaikan'] ?? ''));
                        $badgeClass = 'bg-secondary';
                        $badgeText = $statusPerbaikan !== '' ? esc($statusPerbaikan) : 'Belum diupdate';

                        switch (strtolower($statusPerbaikan)) {
                          case 'selesai':
                            $badgeClass = 'bg-success';
                            break;
                          case 'menunggu':
                          case 'pending':
                            $badgeClass = 'bg-warning text-dark';
                            break;
                          case 'diperbaiki':
                          case 'proses':
                          case 'sedang':
                            $badgeClass = 'bg-danger';
                            break;
                        }
                        ?>
                        <span class="badge <?= $badgeClass ?>"><?= $badgeText ?></span>
                      </td>

                      <!-- STATUS KERUSAKAN -->
                      <td>
                        <?php
                        $statusKerusakan = trim((string) ($riwayat['status_kerusakan'] ?? ''));
                        $badgeClass = 'bg-secondary';
                        $badgeText = $statusKerusakan !== '' ? esc($statusKerusakan) : 'Belum dicek';

                        switch (strtolower($statusKerusakan)) {
                          case 'ringan':
                            $badgeClass = 'bg-success';
                            break;
                          case 'sedang':
                            $badgeClass = 'bg-warning text-dark';
                            break;
                          case 'berat':
                          case 'rusak':
                            $badgeClass = 'bg-danger';
                            break;
                        }
                        ?>
                        <span class="badge <?= $badgeClass ?>"><?= $badgeText ?></span>
                      </td>
                      <td><?= esc($riwayat['kerusakan'] ?? '-') ?></td>
                      <td>
                        <?php
                        $fotoBukti = $riwayat['foto_bukti'] ?? '';
                        $catatanTeknisi = $riwayat['catatan_teknisi'] ?? '';
                        $hasResult = !empty($catatanTeknisi);
                        if ($hasResult) {
                          echo '<div class="text-start small"><strong>Uraian:</strong> ' . esc($catatanTeknisi) . '</div>';
                        } else {
                          echo '<small class="text-muted">Belum ada hasil</small>';
                        }
                        ?>
                      </td>
                      <td>
                        <?php
                        $fotoUrls = [];
                        if (!empty($fotoBukti)) {
                          $fotoUrls = array_filter(array_map(function ($f) {
                            $clean = trim($f);
                            return !empty($clean) ? base_url('uploads/perbaikan/' . $clean) : '';
                          }, explode(',', $fotoBukti)));
                        }
                        $fotoUrlsAttr = esc(implode(',', $fotoUrls));
                        $catatanAttr = esc($catatanTeknisi);
                        $nomorLaporanAttr = esc($riwayat['nomor_laporan'] ?? '');
                        ?>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="openPerbaikanDetail(this)"
                          data-foto-urls="<?= $fotoUrlsAttr ?>" data-catatan="<?= $catatanAttr ?>"
                          data-laporan="<?= $nomorLaporanAttr ?>">
                          <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-outline-success btn-sm"><i class="fas fa-download"></i></button>
                        <button class="btn btn-outline-primary btn-sm"><i class="fas fa-print"></i></button>
                        <button class="btn btn-outline-danger btn-sm"
                          onclick="hapusLaporan('<?= esc($riwayat['nomor_laporan']) ?>')">
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
  </main>

  <!-- FOOTER -->
  <!--<footer>
    <p class="m-0">© 2025 UPAPP POLSRI</p>
  </footer>-->

  <!-- DI AKTIFKAN -->
  <?php echo $this->include('layout/footer'); ?>

  <!-- SCRIPT -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>

  <script>
    let modalUnitInstance;
    let riwayatTable;
    // Tambahkan BASE_URL agar fungsi export tau ngambil gambar dari mana
    const BASE_URL = "<?= base_url() ?>";

    document.addEventListener('DOMContentLoaded', () => {
      const toggleSidebar = document.getElementById('toggleSidebar');
      const sidebar = document.getElementById('sidebar');
      const content = document.querySelector('.content');
      const footerContent = document.querySelector('.footer-content');

      if (toggleSidebar) {
        toggleSidebar.addEventListener('click', () => {
          sidebar.classList.toggle('collapsed');
          content.classList.toggle('full');
          if (footerContent) {
            footerContent.classList.toggle('full');
          }
        });
      }

      riwayatTable = $('#riwayatTable').DataTable({
        order: [[1, 'desc']],
        lengthMenu: [
          [10, 25, 50, 100, -1],
          [10, 25, 50, 100, "Semua"]
        ],
        dom: '<"d-none"Bf>t<"dt-footer d-flex justify-content-between align-items-center mt-2"ip>',
        buttons: [
          {
            extend: 'pageLength',
            className: 'btn btn-secondary btn-sm buttons-page-length',
            text: '<i class="fas fa-list me-1"></i> Show Rows',
          },
        ],
        columnDefs: [
          { orderable: false, targets: [9, 10] }
        ],
        language: {
          search: '',
          searchPlaceholder: 'Cari data...',
          lengthMenu: 'Tampilkan _MENU_ data',
          info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
          infoEmpty: 'Menampilkan 0 sampai 0 dari 0 data',
          emptyTable: 'Tidak ada data laporan.',
          paginate: {
            previous: 'Sebelumnya',
            next: 'Berikutnya'
          },
          zeroRecords: 'Tidak ada data yang sesuai',
        },
        initComplete: function () {
          const wrapper = $(this).closest('.dataTables_wrapper');
          const filterBox = wrapper.find('.dataTables_filter');
          const pageLengthBtn = wrapper.find('.buttons-page-length');
          const searchContainer = $('#search-container');

          if (pageLengthBtn.length) {
            pageLengthBtn.prependTo(searchContainer);
            pageLengthBtn.removeClass('dt-button mb-3').css({
              'margin': '0',
              'border-radius': '4px'
            });
          }
          if (filterBox.length) {
            filterBox.appendTo(searchContainer);
            filterBox.css('margin', '0');
            filterBox.find('input').addClass('form-control form-control-sm').css({
              'margin-left': '0'
            });
          }
        }
      });

      document.getElementById('filterTanggal').addEventListener('change', filterRiwayat);
      document.getElementById('filterStatus').addEventListener('change', filterRiwayat);

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

      // =======================================================
      // FUNGSI COPY (Dengan SweetAlert)
      // =======================================================
      const btnCopy = document.getElementById("btnCopy");
      if (btnCopy) {
        btnCopy.addEventListener("click", function () {
          const tableHead = document.querySelector("#tableLaporan thead tr") || document.querySelector("#riwayatTable thead tr");
          let clipboardText = "";
          let excludedIndices = [];

          if (tableHead) {
            const headers = Array.from(tableHead.querySelectorAll("th"));
            const validHeaders = headers
              .filter((th, index) => {
                const text = th.innerText.trim().toUpperCase();
                if (text === "AKSI" || text === "FOTO") {
                  excludedIndices.push(index);
                  return false;
                }
                return true;
              })
              .map((th) => th.innerText.trim());
            clipboardText += validHeaders.join("\t") + "\n";
          }

          const rows = document.querySelectorAll("#tableLaporan tbody tr, #riwayatTable tbody tr");

          if (rows.length > 0) {
            rows.forEach((row) => {
              if (row.classList.contains("dataTables_empty")) return;
              const cells = Array.from(row.querySelectorAll("td"));
              const rowData = cells
                .filter((td, index) => {
                  return !excludedIndices.includes(index);
                })
                .map((td) => {
                  return td.innerText.replace(/(\r\n|\n|\r)/gm, " ").trim();
                });
              clipboardText += rowData.join("\t") + "\n";
            });

            navigator.clipboard.writeText(clipboardText)
              .then(() => {
                // Notifikasi Sukses Copy
                Swal.fire({
                  icon: 'success',
                  title: 'Tersalin!',
                  text: 'Data berhasil disalin ke clipboard.',
                  timer: 1500,
                  showConfirmButton: false
                });

                const icon = btnCopy.querySelector("i");
                const originalClass = icon.className;
                icon.className = "fas fa-check text-success";
                setTimeout(() => {
                  icon.className = originalClass;
                }, 2000);
              })
              .catch((err) => {
                console.error("Gagal menyalin:", err);
                Swal.fire("Error!", "Browser tidak mengizinkan copy otomatis atau terjadi error.", "error");
              });
          } else {
            Swal.fire("Info", "Tidak ada data untuk disalin.", "info");
          }
        });
      }
    });

    function filterRiwayat() {
      const tanggal = document.getElementById('filterTanggal').value;
      const status = document.getElementById('filterStatus').value;
      riwayatTable.column(1).search(tanggal).column(6).search(status).draw();
    }

    window.openPerbaikanDetail = function (button) {
      const fotoUrls = (button.dataset.fotoUrls || '').split(',').map(u => u.trim()).filter(u => u);
      const nomor = button.dataset.laporan || '';
      const container = document.getElementById('modalHasilFotoContainer');
      const noPhoto = document.getElementById('modalHasilNoPhoto');

      document.getElementById('modalHasilNomorLaporan').textContent = nomor ? `(${nomor})` : '';
      container.innerHTML = '';

      if (fotoUrls.length === 0) {
        noPhoto.style.display = 'block';
      } else {
        noPhoto.style.display = 'none';
        fotoUrls.forEach(url => {
          const col = document.createElement('div');
          col.className = 'col-md-3';
          col.innerHTML = `<a href="${url}" target="_blank" class="d-block mb-2"><img src="${url}" class="img-fluid rounded shadow-sm border" style="height: 170px; width: 100%; object-fit: cover;"></a>`;
          container.appendChild(col);
        });
      }

      new bootstrap.Modal(document.getElementById('modalHasilPerbaikan')).show();
    }

    // =======================================================
    // FUNGSI EXPORT EXCEL (Dengan SweetAlert)
    // =======================================================
    const stripHtml = (html) => {
      if (!html) return "-";
      let tmp = document.createElement("DIV");
      tmp.innerHTML = html;
      return tmp.textContent || tmp.innerText || "-";
    };

    window.exportToExcel = async function () {
      const btn = document.querySelector(".btn-outline-success");
      const originalText = btn ? btn.innerHTML : "Excel";
      const table = $("#riwayatTable").DataTable();
      const allData = table.rows({ search: "applied" }).data().toArray();

      if (allData.length === 0) {
        Swal.fire("Info", "Tidak ada data untuk diexport.", "info");
        return;
      }

      if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generate Data...';
      }

      try {
        const workbook = new ExcelJS.Workbook();
        const worksheet = workbook.addWorksheet("Data Riwayat Pelapor");

        worksheet.columns = [
          { header: "No.", key: "nomor_urut", width: 5 },
          { header: "No. Laporan", key: "no", width: 15 },
          { header: "Tanggal", key: "tgl", width: 20 },
          { header: "Tgl Perbaikan", key: "tgl_perbaikan", width: 20 },
          { header: "Nama Alat", key: "alat", width: 25 },
          { header: "Lokasi", key: "lokasi", width: 35 },
          { header: "Unit", key: "unit", width: 15 },
          { header: "Status Kerusakan", key: "status_kerusakan", width: 20 },
          { header: "Status Perbaikan", key: "status_akhir", width: 18 },
          { header: "Kerusakan/Keluhan", key: "keluhan", width: 30 },
          { header: "Hasil Perbaikan", key: "diagnosa", width: 35 }
        ];

        worksheet.spliceRows(1, 0, []);
        worksheet.mergeCells("A1:K1");

        const titleCell = worksheet.getCell("A1");
        titleCell.value = "REKAPITULASI RIWAYAT LAPORAN SAYA";
        titleCell.font = { name: "Arial", size: 16, bold: true, color: { argb: "FFFFFFFF" } };
        titleCell.alignment = { horizontal: "center", vertical: "middle" };
        titleCell.fill = { type: "pattern", pattern: "solid", fgColor: { argb: "FF003366" } };
        worksheet.getRow(1).height = 40;

        const headerRow = worksheet.getRow(2);
        headerRow.height = 35;
        headerRow.eachCell((cell) => {
          cell.font = { name: "Arial", bold: true, color: { argb: "FFFFFFFF" }, size: 11 };
          cell.alignment = { horizontal: "center", vertical: "middle", wrapText: true };
          cell.fill = { type: "pattern", pattern: "solid", fgColor: { argb: "FF0d6efd" } };
          cell.border = { top: { style: "thin" }, left: { style: "thin" }, bottom: { style: "thin" }, right: { style: "thin" } };
        });

        let totalLaporan = 0;

        for (const d of allData) {
          totalLaporan++;
          const alatText = stripHtml(d[3]);
          const lokasiText = stripHtml(d[4]);
          const keluhanText = stripHtml(d[8]);
          const diagnosaText = stripHtml(d[9]);

          const newRow = worksheet.addRow({
            nomor_urut: totalLaporan,
            no: stripHtml(d[0]),
            tgl: stripHtml(d[1]),
            tgl_perbaikan: stripHtml(d[2]),
            alat: alatText,
            lokasi: lokasiText || "-",
            unit: stripHtml(d[5]) || "-",
            status_akhir: stripHtml(d[6]).toUpperCase() || "-",
            status_kerusakan: stripHtml(d[7]) || "Belum Dicek",
            keluhan: keluhanText || "-",
            diagnosa: diagnosaText || "-",
          });

          const maxLength = Math.max(alatText.length, lokasiText.length, keluhanText.length, diagnosaText.length);
          let dynamicHeight = 35;
          if (maxLength > 35) {
            const extraLines = Math.floor(maxLength / 35);
            dynamicHeight += extraLines * 18;
          }
          newRow.height = dynamicHeight;
        }

        worksheet.eachRow((row, rowNumber) => {
          if (rowNumber > 2) {
            row.eachCell((cell) => {
              cell.border = { top: { style: "thin" }, left: { style: "thin" }, bottom: { style: "thin" }, right: { style: "thin" } };
              cell.alignment = { horizontal: "center", vertical: "middle", wrapText: true };
            });
          }
        });

        const buffer = await workbook.xlsx.writeBuffer();
        const blob = new Blob([buffer], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" });
        saveAs(blob, `Riwayat_Laporan_Saya_${new Date().toISOString().slice(0, 10)}.xlsx`);

        // Notifikasi Sukses Excel
        Swal.fire({
          icon: 'success',
          title: 'Berhasil!',
          text: 'Data Excel berhasil diunduh.',
          timer: 2000,
          showConfirmButton: false
        });

      } catch (error) {
        console.error("Export Error:", error);
        Swal.fire("Error!", "Gagal export: " + error.message, "error");
      } finally {
        if (btn) {
          btn.disabled = false;
          btn.innerHTML = originalText;
        }
      }
    };

    // =======================================================
    // FUNGSI EXPORT PDF (Dengan SweetAlert)
    // =======================================================
    window.exportToPDFKerusakan = async function (btn) {
      const originalText = btn.innerHTML;
      btn.disabled = true;
      btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Proses PDF...';

      try {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF("l", "mm", [215, 330]);
        const pageWidth = doc.internal.pageSize.getWidth();
        doc.setFontSize(16);
        doc.setFont("helvetica", "bold");
        doc.text("REKAPITULASI RIWAYAT LAPORAN SAYA", pageWidth / 2, 15, { align: "center" });

        doc.setFontSize(10);
        doc.setFont("helvetica", "normal");
        doc.text(`Dicetak pada: ${new Date().toLocaleString("id-ID")}`, pageWidth / 2, 22, { align: "center" });

        const tableBody = [];
        const table = $("#riwayatTable").DataTable();
        const allData = table.rows({ search: "applied" }).data().toArray();

        if (allData.length === 0) {
          Swal.fire("Info", "Tidak ada data untuk diexport ke PDF.", "info");
          btn.disabled = false;
          btn.innerHTML = originalText;
          return;
        }

        let nomorUrut = 1;

        for (let i = 0; i < allData.length; i++) {
          const d = allData[i];
          let statusCek = stripHtml(d[6]).toUpperCase();
          let statusFisik = stripHtml(d[7]) || "Belum Dicek";
          statusFisik = statusFisik.charAt(0).toUpperCase() + statusFisik.slice(1);
          let diagnosaText = stripHtml(d[9]);

          tableBody.push([
            nomorUrut++,
            stripHtml(d[0]), // No. Laporan
            stripHtml(d[1]), // Tgl Laporan
            stripHtml(d[2]) || "-", // Tgl Perbaikan
            stripHtml(d[3]), // Nama Alat
            stripHtml(d[4]) || "-", // Lokasi
            stripHtml(d[5]) || "-", // Unit
            statusFisik,
            statusCek,
            stripHtml(d[8]) || "-", // Keluhan
            diagnosaText,
          ]);
        }

        doc.autoTable({
          head: [["No", "No. Laporan", "Tgl Laporan", "Tgl Perbaikan", "Nama Alat", "Lokasi", "Unit", "Kondisi", "Status", "Keluhan", "Hasil Perbaikan"]],
          body: tableBody,
          startY: 30,
          theme: "grid",
          styles: { halign: "center", valign: "middle", fontSize: 8, textColor: [0, 0, 0], lineColor: [0, 0, 0], lineWidth: 0.1, cellPadding: 2, overflow: "linebreak", minCellHeight: 15 },
          headStyles: { fillColor: [13, 110, 253], textColor: [255, 255, 255], fontStyle: "bold", lineWidth: 0.1 },
          columnStyles: {
            0: { cellWidth: 8 },
            1: { cellWidth: 20 },
            2: { cellWidth: 20 },
            3: { cellWidth: 20 },
            9: { cellWidth: 40, halign: "left" }, // Keluhan 
            10: { cellWidth: 40, halign: "left" }, // Hasil Perbaikan 
          },
          didParseCell: function (data) {
            if (data.section === "body" && data.column.index === 8) { // Kolom status
              const text = data.cell.raw.toString();
              if (text.includes("RUSAK") || text.includes("DIPERBAIKI") || text.includes("PROSES")) {
                data.cell.styles.textColor = [255, 0, 0];
                data.cell.styles.fontStyle = "bold";
              } else if (text.includes("SELESAI")) {
                data.cell.styles.textColor = [0, 128, 0];
                data.cell.styles.fontStyle = "bold";
              } else {
                data.cell.styles.textColor = [128, 128, 128];
                data.cell.styles.fontStyle = "italic";
              }
            }
          }
        });

        doc.save(`Riwayat_Laporan_Saya_${new Date().toISOString().slice(0, 10)}.pdf`);

        // Notifikasi Sukses PDF
        Swal.fire({
          icon: 'success',
          title: 'Berhasil!',
          text: 'Data PDF berhasil diunduh.',
          timer: 2000,
          showConfirmButton: false
        });

      } catch (error) {
        console.error(error);
        Swal.fire("Error!", "Gagal export PDF: " + error.message, "error");
      } finally {
        btn.disabled = false;
        btn.innerHTML = originalText;
      }
    };

    async function hapusLaporan(nomorLaporan) {

      const konfirmasi = await Swal.fire({
        title: "Hapus laporan?",
        text: "Laporan yang sudah dihapus tidak dapat dikembalikan.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya, Hapus",
        cancelButtonText: "Batal",
        confirmButtonColor: "#dc3545"
      });

      if (!konfirmasi.isConfirmed) return;

      const formData = new FormData();
      formData.append("nomor_laporan", nomorLaporan);

      const response = await fetch(
        BASE_URL + "pelapor/hapus_laporan",
        {
          method: "POST",
          body: formData
        }
      );

      const result = await response.json();

      if (result.status === "success") {

        Swal.fire({
          icon: "success",
          title: "Berhasil",
          text: result.message,
          timer: 1500,
          showConfirmButton: false
        }).then(() => {
          location.reload();
        });

      } else {

        Swal.fire({
          icon: "error",
          title: "Gagal",
          text: result.message
        });

      }
    }
  </script>

  <div class="modal fade" id="modalHasilPerbaikan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
      <div class="modal-content border-0 shadow-lg">
        <div class="modal-header bg-primary text-white border-0">
          <h5 class="modal-title fw-bold text-white"><i class="fas fa-camera me-2"></i> Hasil Perbaikan <span
              id="modalHasilNomorLaporan"></span></h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-4">
          <div id="modalHasilFotoContainer" class="row g-3"></div>
          <div id="modalHasilNoPhoto" class="text-center py-5" style="display: none;">
            <div class="text-muted mb-3"><i class="fas fa-image fa-3x"></i></div>
            <p class="text-muted small mb-0">Tidak ada foto hasil perbaikan.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>

</html>