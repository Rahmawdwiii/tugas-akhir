<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UPAPP | Analisis FP-Growth</title>

    <!-- Bootstrap & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
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

        /* Tambahkan ini di bagian bawah <style> kamu */
        #formAnalisis .form-control,
        #formAnalisis .form-select {
            height: 38px !important;
            /* Tinggi standar Bootstrap md */
            width: 100% !important;
        }
    </style>
</head>

<body>

    <?= $this->include('layout/header') ?>

    <?= $this->include('layout/sidebar_admin') ?>
    <main class="content" id="mainContent">
        <div class="container-fluid">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Analisis Pola Alat Yang Rusak (FP-Growth)</h4>
                    <button type="button" class="btn btn-primary" id="btnGenerate">
                        <i class="fas fa-sync-alt"></i> Generate Analisis
                    </button>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="card border-start border-primary border-4 shadow-sm p-3 text-center">
                                <h6 class="text-muted">Total Laporan</h6>
                                <h2 class="fw-bold" id="stat_total_laporan">0</h2>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card border-start border-success border-4 shadow-sm p-3 text-center">
                                <h6 class="text-muted">Total Rule</h6>
                                <h2 class="fw-bold" id="stat_total_rule">0</h2>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card border-start border-warning border-4 shadow-sm p-3 text-center">
                                <h6 class="text-muted">Min Support</h6>
                                <h2 class="fw-bold" id="stat_min_support">0</h2>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card border-start border-danger border-4 shadow-sm p-3 text-center">
                                <h6 class="text-muted">Min Confidence</h6>
                                <h2 class="fw-bold" id="stat_min_confidence">0</h2>
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="card border-start border-info border-4 shadow-sm p-3 text-center">
                                <h6 class="text-muted">
                                    Total Transaksi
                                </h6>

                                <h2 class="fw-bold" id="stat_total_transaksi">
                                    0
                                </h2>
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="card border-start border-dark border-4 shadow-sm p-3 text-center">
                                <h6 class="text-muted">
                                    Waktu Proses
                                </h6>

                                <h2 class="fw-bold" id="stat_waktu_proses">
                                    0
                                </h2>
                            </div>
                        </div>
                    </div>

                    <form id="formAnalisis" class="card mb-4 p-3 border-0 shadow-sm">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label fw-bold">
                                    Tanggal Awal
                                </label>

                                <input type="date" id="tanggal_awal" class="form-control">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-bold">
                                    Tanggal Akhir
                                </label>

                                <input type="date" id="tanggal_akhir" class="form-control">
                            </div>

                            <div class="col-md-6">

                                <div class="row">

                                    <div class="col-md-6">

                                        <label class="form-label fw-bold">
                                            Tahun Awal
                                        </label>

                                        <select id="tahun_awal" class="form-select">

                                            <option value="">Semua</option>

                                            <option value="2021">2021</option>
                                            <option value="2022">2022</option>
                                            <option value="2023">2023</option>
                                            <option value="2024">2024</option>
                                            <option value="2025">2025</option>
                                            <option value="2026">2026</option>

                                        </select>

                                    </div>

                                    <div class="col-md-6">

                                        <label class="form-label fw-bold">
                                            Tahun Akhir
                                        </label>

                                        <select id="tahun_akhir" class="form-select">

                                            <option value="">Semua</option>

                                            <option value="2021">2021</option>
                                            <option value="2022">2022</option>
                                            <option value="2023">2023</option>
                                            <option value="2024">2024</option>
                                            <option value="2025">2025</option>
                                            <option value="2026">2026</option>

                                        </select>

                                    </div>

                                </div>

                            </div>

                            <div class="col-12">
                                <small class="text-muted">
                                    Kosongkan jika ingin menggunakan seluruh data.
                                </small>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-bold">Jenis Analisis</label>
                                <select class="form-select form-select-md" id="jenis_analisis" name="jenis_analisis">
                                    <option value="" selected disabled>Pilih Status</option>
                                    <option value="SELURUH_STATUS">Seluruh Status</option>
                                    <option value="STATUS_OK">Status OK</option>
                                    <option value="BELUM_DIPERBAIKI">Belum Diperbaiki</option>
                                    <option value="TIDAK_DAPAT_DIPERBAIKI">Tidak Dapat Diperbaiki</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-bold">Metode Grouping</label>
                                <select class="form-select form-select-md" id="metode_grouping" name="metode_grouping">
                                    <option value="" selected disabled>Pilih Grouping</option>
                                    <option value="ruangan">Tanggal + Lokasi + Unit</option>
                                    <option value="tanggal">Grouping Tanggal</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-bold">Min Support</label>
                                <input type="number" class="form-control form-select-md" id="min_support"
                                    name="min_support" placeholder="Contoh: 0.002" step="0.001">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-bold">Min Confidence</label>
                                <input type="number" class="form-control form-select-md" id="min_confidence"
                                    name="min_confidence" placeholder="Contoh: 0.7" step="0.1">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-bold">
                                    Jenis Item
                                </label>

                                <select class="form-select form-select-md" id="jenis_item" name="jenis_item">

                                    <option value="" selected disabled>
                                        Pilih Jenis Item
                                    </option>

                                    <option value="alat">
                                        Nama Alat
                                    </option>

                                    <option value="alat_lokasi_unit">
                                        Nama Alat + Lokasi + Unit
                                    </option>

                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-bold">
                                    Jenis Filter
                                </label>

                                <select id="jenis_filter" class="form-select">

                                    <option value="filter">
                                        Filter > 1 Item
                                    </option>

                                    <option value="tanpa_filter">
                                        Tanpa Filter
                                    </option>
                                </select>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table id="fpGrowthTable" class="table table-bordered table-hover align-middle">
                            <thead class="table-primary">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Alat Pemicu</th>
                                    <th>Alat Terkait</th>
                                    <th width="12%">Support</th>
                                    <th width="12%">Confidence</th>
                                    <th width="12%">Lift</th>
                                    <th style="min-width:35px">
                                        Temuan Pola
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="tbody_hasil_fp_growth"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        Pembuktian Perhitungan Manual
                    </h5>
                </div>

                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-bordered table-striped">

                            <thead>
                                <tr>
                                    <th>Kode Rule</th>
                                    <th>Antecedent</th>
                                    <th>Consequent</th>
                                    <th>Support</th>
                                    <th>Confidence</th>
                                    <th>Lift</th>
                                </tr>
                            </thead>

                            <tbody id="tbodyRuleManual">

                                <tr>
                                    <td colspan="6" class="text-center">
                                        Belum ada data
                                    </td>
                                </tr>

                            </tbody>

                        </table>

                    </div>

                </div>
            </div> -->
            <div class="card mt-4">
                <div class="card-header">
                    Riwayat Pengujian
                </div>

                <div class="card-body">
                    <table id="tblRiwayat" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Analisis</th>
                                <th>Grouping</th>
                                <th>Jenis Item</th>
                                <th>Support</th>
                                <th>Confidence</th>
                                <th>Rule</th>
                                <th>Waktu</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- DI AKTIFKAN -->
    <?php echo $this->include('layout/footer'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
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

        const fpGrowthTable = $('#fpGrowthTable').DataTable({
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50, 100],
            autoWidth: false,
            language: {
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ baris',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ entri',
                infoEmpty: 'Menampilkan 0 sampai 0 dari 0 entri',
                emptyTable: 'Klik "Generate Analisis" untuk memulai.',
                paginate: {
                    previous: 'Sebelumnya',
                    next: 'Berikutnya'
                }
            }
        });

        const riwayatTable = $('#tblRiwayat').DataTable({

            pageLength: 10,

            lengthMenu: [5, 10, 25, 50],

            autoWidth: false,

            order: [[0, "desc"]],

            language: {

                search: "Cari:",

                lengthMenu: "Tampilkan _MENU_ baris",

                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",

                paginate: {

                    previous: "Sebelumnya",

                    next: "Berikutnya"

                }

            }

        });

        document.getElementById('btnGenerate').addEventListener('click', function () {
            const params = {
                jenis_analisis:
                    document.getElementById('jenis_analisis').value,

                jenis_filter:
                    document.getElementById('jenis_filter').value,

                metode_grouping:
                    document.getElementById('metode_grouping').value,

                jenis_item:
                    document.getElementById('jenis_item').value,

                min_support:
                    document.getElementById('min_support').value,

                min_confidence:
                    document.getElementById('min_confidence').value,

                tanggal_awal:
                    document.getElementById('tanggal_awal')?.value || null,

                tanggal_akhir:
                    document.getElementById('tanggal_akhir')?.value || null,
                tahun_awal:
                    document.getElementById('tahun_awal')?.value || null,

                tahun_akhir:
                    document.getElementById('tahun_akhir')?.value || null,
            };

            // Validasi sederhana
            if (
                !params.jenis_analisis ||
                !params.metode_grouping ||
                !params.jenis_item
            ) {
                alert(
                    "Mohon lengkapi semua parameter analisis!"
                );

                return;
            }

            fetch(`${BASE_URL}admin/generate_fp_growth`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                    [CSRF_TOKEN_NAME]: CSRF_HASH
                },
                body: JSON.stringify(params)
            })
                .then(response => response.json())
                .then(data => {

                    if (!data.success) {

                        alert("Generate gagal");
                        return;
                    }

                    // ==========================
                    // UPDATE CARD STATISTIK
                    // ==========================

                    document.getElementById(
                        "stat_total_laporan"
                    ).innerText =
                        Number(data.jumlah_data).toLocaleString('id-ID');

                    document.getElementById(
                        "stat_total_rule"
                    ).innerText =
                        Number(data.jumlah_rule).toLocaleString('id-ID');

                    document.getElementById(
                        "stat_min_support"
                    ).innerText =
                        Number(data.min_support).toLocaleString('id-ID');

                    document.getElementById(
                        "stat_min_confidence"
                    ).innerText =
                        Number(data.min_confidence).toLocaleString('id-ID');

                    document.getElementById(
                        "stat_total_transaksi"
                    ).innerText =
                        Number(data.jumlah_transaksi).toLocaleString('id-ID');

                    document.getElementById(
                        "stat_waktu_proses"
                    ).innerText =
                        Number(data.waktu_proses).toLocaleString('id-ID');

                    // ==========================
                    // UPDATE TABEL RULE
                    // ==========================

                    const rows = data.data.map((row, index) => [

                        index + 1,
                        row.antecedent,
                        row.consequent,
                        row.support,
                        row.confidence,
                        row.lift,
                        row.knowledge
                    ]);

                    fpGrowthTable.clear();

                    fpGrowthTable.rows.add(rows).draw();

                    // reload riwayat setelah generate

                    loadRiwayat();

                });

            // LOAD DATA MANUAL
            loadRuleManual(
                params.jenis_analisis,
                params.jenis_item,
                params.jenis_filter
            );

        });

        async function loadRuleManual(
            jenisAnalisis,
            jenisItem,
            jenisFilter
        ) {

            const response = await fetch(
                "<?= base_url('admin/get_rule_manual') ?>",
                {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        jenis_analisis: jenisAnalisis,
                        jenis_item: jenisItem,
                        jenis_filter: jenisFilter
                    })
                }
            );

            const result = await response.json();

            let html = "";

            if (!result.data || result.data.length === 0) {

                html = `
            <tr>
                <td colspan="6" class="text-center">
                    Tidak ada data manual
                </td>
            </tr>
        `;

            } else {

                result.data.forEach(item => {

                    html += `
                <tr>
                    <td>${item.kode_rule}</td>
                    <td>${item.antecedent}</td>
                    <td>${item.consequent}</td>
                    <td>${item.support}</td>
                    <td>${item.confidence}</td>
                    <td>${item.lift}</td>
                </tr>
            `;
                });
            }

            document.getElementById(
                "tbodyRuleManual"
            ).innerHTML = html;
        }

        async function loadRiwayat() {

            const response = await fetch(
                "http://127.0.0.1:8000/riwayat-eksperimen"
            );

            const data = await response.json();

            riwayatTable.clear();

            data.forEach(item => {

                riwayatTable.row.add([

                    item.tanggal_eksperimen.replace("T", " "),

                    item.jenis_analisis,

                    item.metode_grouping,

                    item.jenis_item,

                    item.min_support,

                    item.min_confidence,

                    item.jumlah_rule,

                    item.waktu_proses + " detik"

                ]);

            });

            riwayatTable.draw();

        }

    </script>

    <script src="<?= base_url('admin/dashboard.js') ?>"></script>

</body>

</html>