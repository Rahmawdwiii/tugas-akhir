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

    .modal-sm {
      max-width: 355px;
    }

    #imagePreviewModal .modal-dialog {
      margin-top: 80px;
      /* 60px (tinggi header) + 20px (spasi) */
    }
  </style>
</head>

<body>
  <!-- HEADER -->
  <?= $this->include('layout/header') ?>

  <!-- SIDEBAR -->
  <?= $this->include('layout/sidebar_pelapor') ?>

  <!-- CONTENT -->
  <div class="content">
    <div class="card shadow-sm p-3 mb-4 fade-in">
      <div class="card-header bg-primary text-white">
        <h5><i class="fas fa-tools me-2"></i> Form Laporan Kerusakan</h5>
      </div>

      <div class="card-body">
        <form id="formLaporanPelapor" enctype="multipart/form-data">
          <input type="hidden" name="pelaksana" id="inputPelaksana">
          <div class="row g-3">

            <div class="col-md-6">
              <label for="nama_pelapor">Nama Pelapor <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-user"></i></span>
                <input type="text" class="form-control" name="nama_pelapor" id="nama_pelapor" placeholder="Masukkan nama pelapor" required>
              </div>
            </div>

            <div class="col-md-6">
              <label for="nomorInventarisInput">Nomor Inventaris <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                <input type="text" class="form-control" name="nomor_inventaris" id="nomorInventarisInput" value="<?= esc($nomor_inventaris ?? '') ?>" placeholder="Nomor Inventaris" readonly required>
              </div>
            </div>

            <div class="col-md-6">
              <label>Nama Alat <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-tools"></i></span>
                <select class="form-select" name="nama_alat" id="namaAlatSelect" required>
                  <option value="" data-inventaris="">-- Pilih Alat --</option>
                  <?php if (!empty($daftar_alat)): ?>
                    <?php foreach ($daftar_alat as $alat): ?>
                      <option value="<?= esc($alat['nama_alat']) ?>" data-inventaris="<?= esc($alat['nomor_inventaris']) ?>" data-pelaksana="<?= esc($alat['nama_teknisi'] ?? '') ?>"> <?= esc($alat['nama_alat']) ?>
                      </option>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </select>
              </div>
            </div>

            <div class="col-md-6">
              <label>Lokasi Alat <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                <select class="form-select" name="lokasi_alat" id="selectLokasi" required disabled>
                  <option value="">-- Pilih Jurusan Terlebih Dahulu --</option>
                </select>
              </div>
            </div>

            <div class="col-md-6">
              <label>Jurusan / Unit <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-building"></i></span>
                <select class="form-select" name="unit" id="selectJurusan" required>
                  <option value="">-- Pilih Jurusan/Unit --</option>
                  <?php if (!empty($lokasi_per_jurusan)): ?>
                    <?php foreach (array_keys($lokasi_per_jurusan) as $daftar_jurusan): ?>
                      <option value="<?= esc($daftar_jurusan) ?>"><?= esc($daftar_jurusan) ?></option>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </select>
              </div>
            </div>

            <div class="col-md-6">
              <label>Kerusakan / Keluhan <span class="text-danger">*</span></label>
              <textarea class="form-control" name="kerusakan_keluhan" rows="2" id="inputkerusakan_keluhan" placeholder="Jelaskan kerusakan..." required></textarea>
            </div>

            <div class="col-md-6">
              <label>Foto Bukti <span class="text-danger">*</span> <small class="text-muted">(Max 2MB/foto)</small></label>
              <input type="file" class="form-control" name="foto[]" id="fileInput" accept="image/*" multiple onchange="handleFileSelect(event)" required>
              <div id="previewContainer" class="mt-2 d-flex flex-wrap gap-2"></div>
            </div>

            <div class="col-md-6">
              <label>Link Pendukung <small class="text-muted">(Opsional)</small></label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-link"></i></span>
                <input type="text" class="form-control" name="link_pendukung" id="linkPendukung" placeholder="Contoh: Google Drive / Dropbox link">
              </div>
              <div class="form-text" style="font-size: 0.8rem; color: #6c757d;">
                Jika ukuran foto/video terlalu besar, lampirkan link di sini.
              </div>
            </div>

            <div class="col-12 mt-4 d-flex align-items-end">
              <button type="submit" class="btn btn-primary w-100" id="btnSubmit">
                <i class="fas fa-paper-plane me-2"></i> Kirim Laporan
                <span class="spinner-border spinner-border-sm ms-2" role="status" style="display:none;" id="loadingSpinner"></span>
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Tambahan CSS untuk hover & efek -->
  <style>
    .card:hover {
      transform: translateY(-3px);
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
      transition: 0.3s;
    }

    .fade-in {
      animation: fadeIn 0.6s ease forwards;
      opacity: 0;
    }

    @keyframes fadeIn {
      to {
        opacity: 1;
      }
    }

    .input-group-text {
      background-color: #e9ecef;
    }
  </style>

  <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-labelledby="imagePreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header py-2">
          <h5 class="modal-title fs-6 mb-0" id="imagePreviewModalLabel">Preview Gambar</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          <img src="" id="modalImagePreview" class="img-fluid" alt="Preview Gambar">
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow-lg">
        <div class="modal-body text-center p-5">

          <i class="fas fa-check-circle text-success mb-3" style="font-size: 80px;"></i>

          <h3 class="modal-title h4" id="successModalLabel">Laporan Terkirim!</h3>
          <p class="text-muted">
            Terima kasih. Laporan Anda akan segera kami tindaklanjuti.
          </p>

          <button type="button" class="btn btn-primary mt-3" data-bs-dismiss="modal">Tutup</button>

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

  <!-- SCRIPT -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // =======================================================
    // === 1. PENGATURAN MODAL & PREVIEW GAMBAR
    // =======================================================
    const myImagePreviewModal = new bootstrap.Modal(document.getElementById('imagePreviewModal'));
    const modalImageElement = document.getElementById('modalImagePreview');


    // =======================================================
    // === 2. FUNGSI UPLOAD MULTI-FOTO & PREVIEW (DIPERBAIKI)
    // =======================================================
    let fileStore = new DataTransfer();
    const fileInput = document.getElementById('fileInput');
    const previewContainer = document.getElementById('previewContainer');

    // --- BATASAN UPLOAD ---
    const MAX_FILES = 4;
    const MAX_WIDTH = 2560;
    const MAX_HEIGHT = 1440;
    // ----------------------

    // Fungsi asinkron untuk memeriksa resolusi
    function checkResolution(file) {
      return new Promise((resolve) => {
        const img = new Image();
        const reader = new FileReader();

        reader.onload = (e) => {
          img.onload = () => {
            if (img.width > MAX_WIDTH || img.height > MAX_HEIGHT) {
              resolve({
                valid: false,
                message: `Resolusi terlalu besar (${img.width}x${img.height}). Maksimal ${MAX_WIDTH}x${MAX_HEIGHT} piksel.`
              });
            } else {
              resolve({
                valid: true
              });
            }
          };
          img.src = e.target.result;
        };
        reader.readAsDataURL(file);
      });
    }


    async function handleFileSelect(event) {
      const incomingFiles = Array.from(event.target.files);
      let filesAddedCount = fileStore.files.length;

      // Kosongkan input file sementara
      fileInput.files = new DataTransfer().files;

      for (const file of incomingFiles) {
        // Cek Batasan Jumlah File
        if (filesAddedCount >= MAX_FILES) {
          Swal.fire({
            icon: "warning",
            title: "Batas File",
            text: `Gagal upload: Maksimal ${MAX_FILES} file diizinkan.`,
          });
          break;
        }

        // Cek Resolusi
        const resolutionCheck = await checkResolution(file);
        if (!resolutionCheck.valid) {
          Swal.fire({
            icon: "error",
            title: "Gagal Upload",
            text: `Gagal upload file '${file.name}': ${resolutionCheck.message}`,
          });
          continue; // Lanjut ke file berikutnya
        }

        // Cek Duplikasi dan Tambahkan ke store
        if (![...fileStore.files].find(f => f.name === file.name)) {
          fileStore.items.add(file);
          createPreview(file);
          filesAddedCount++;
        }
      }

      // Sinkronkan DataTransfer kembali ke input file
      fileInput.files = fileStore.files;
      checkFormValidity();
    }

    function createPreview(file) {
      const reader = new FileReader();
      reader.onload = function(e) {
        const previewWrapper = document.createElement('div');
        previewWrapper.className = 'position-relative';
        previewWrapper.setAttribute('data-filename', file.name);

        // Gambar preview
        const img = document.createElement('img');
        img.src = e.target.result;
        img.style.height = '100px';
        img.style.width = '100px';
        img.style.objectFit = 'cover';
        img.className = 'rounded border';
        img.style.cursor = 'pointer';
        img.onclick = function() {
          modalImageElement.src = this.src;
          myImagePreviewModal.show();
        };

        // Tombol "x"
        const closeButton = document.createElement('button');
        closeButton.type = 'button';
        closeButton.className = 'btn btn-light btn-sm rounded-circle d-flex align-items-center justify-content-center position-absolute top-0 end-0 m-1';
        closeButton.style.width = '24px';
        closeButton.style.height = '24px';
        closeButton.style.padding = '0';
        closeButton.setAttribute('aria-label', 'Hapus');
        closeButton.onclick = function() {
          removeFile(file.name);
        };
        const icon = document.createElement('i');
        icon.className = 'fas fa-times text-danger';
        closeButton.appendChild(icon);

        previewWrapper.appendChild(img);
        previewWrapper.appendChild(closeButton);
        previewContainer.appendChild(previewWrapper);
      };
      reader.readAsDataURL(file);
    }

    // Fungsi removeFile juga harus diupdate agar sinkron dengan DataTransfer baru
    function removeFile(filename) {
      const newFileStore = new DataTransfer();
      for (let i = 0; i < fileStore.files.length; i++) {
        if (fileStore.files[i].name !== filename) {
          newFileStore.items.add(fileStore.files[i]);
        }
      }
      fileStore = newFileStore;
      fileInput.files = fileStore.files;

      const previewToRemove = previewContainer.querySelector(`[data-filename="${filename}"]`);
      if (previewToRemove) {
        previewContainer.removeChild(previewToRemove);
      }
      checkFormValidity();
    }

    // =======================================================
    // === 3. FUNGSI DROPDOWN BERTINGKAT (LOKASI)
    // =======================================================
    // (Pastikan variabel $lokasi_per_jurusan dikirim dari controller)
    const lokasiData = <?= json_encode($lokasi_per_jurusan ?? []) ?>;
    const jurusanSelect = document.getElementById('selectJurusan');
    const lokasiSelect = document.getElementById('selectLokasi');

    jurusanSelect.addEventListener('change', function() {
      const selectedJurusan = this.value;
      lokasiSelect.innerHTML = '';
      if (selectedJurusan && lokasiData[selectedJurusan]) {
        lokasiSelect.disabled = false;
        lokasiSelect.innerHTML = '<option value="">-- Pilih Lokasi Alat --</option>';
        lokasiData[selectedJurusan].forEach(function(lokasiString) {
          lokasiSelect.innerHTML += `<option value="${lokasiString}">${lokasiString}</option>`;
        });
      } else {
        lokasiSelect.innerHTML = '<option value="">-- Pilih Jurusan Terlebih Dahulu --</option>';
        lokasiSelect.disabled = true;
      }
      checkFormValidity(); // Panggil validasi setelah jurusan diubah
    });


    // =======================================================
    // === 4. FUNGSI AUTO-FILL NOMOR INVENTARIS
    // =======================================================

    const alatDropdown = document.getElementById('namaAlatSelect');
    const inventarisInput = document.getElementById('nomorInventarisInput');
    const pelaksanaInput = document.getElementById('inputPelaksana');

    // HAPUS VARIABEL const pelaksanaMap = { ... } KARENA SUDAH TIDAK DIPAKAI

    // Event Listener saat Alat dipilih
    alatDropdown.addEventListener('change', function() {
      // Ambil option yang sedang dipilih
      const selectedOption = this.options[this.selectedIndex];

      // 1. Ambil Data Inventaris dari atribut HTML (Database)
      const nomorInventaris = selectedOption.getAttribute('data-inventaris');
      inventarisInput.value = nomorInventaris ? nomorInventaris : '';

      // 2. Ambil Data Pelaksana dari atribut HTML (Database)
      const namaPelaksana = selectedOption.getAttribute('data-pelaksana');
      pelaksanaInput.value = namaPelaksana ? namaPelaksana : '';

      // Panggil fungsi validasi tombol
      checkFormValidity(); 
    });

    // =======================================================
    // === 5. FUNGSI VALIDASI FORM (UNTUK TOMBOL KIRIM)
    // =======================================================
    const form = document.getElementById('formLaporanPelapor');
    const submitButton = document.getElementById('btnSubmit');

    // DAFTAR INPUT YANG WAJIB DIISI
    const requiredInputs = [
      document.getElementById('nama_pelapor'), // Baru
      document.getElementById('nomorInventarisInput'), // Baru
      document.getElementById('namaAlatSelect'),
      document.getElementById('selectJurusan'),
      document.getElementById('selectLokasi'),
      document.getElementById('inputkerusakan_keluhan'),
      document.getElementById('fileInput') // Baru (Foto)
    ];

    function checkFormValidity() {
      let allValid = true;
      for (const input of requiredInputs) {
        // Cek jika input ada DAN nilainya tidak kosong
        if (!input || !input.value.trim()) {
          allValid = false;
          break;
        }
      }
      submitButton.disabled = !allValid;
    }

    // Tambahkan "pendengar" ke setiap input wajib
    requiredInputs.forEach(input => {
      if (input) {
        input.addEventListener('change', checkFormValidity);
        input.addEventListener('keyup', checkFormValidity);
        input.addEventListener('input', checkFormValidity); // Tambahan 'input' untuk deteksi lebih cepat
      }
    });

    // Panggil sekali saat memuat halaman
    checkFormValidity();

    // Spinner saat submit
    form.addEventListener('submit', async function(e) {
      e.preventDefault(); // <-- SANGAT PENTING: Mencegah halaman reload

      submitButton.disabled = true;
      const spinner = document.getElementById('loadingSpinner');
      spinner.style.display = 'inline-block';

      // Kumpulkan semua data form (termasuk file)
      const formData = new FormData(form);

      try {
        // Kirim data ke controller di latar belakang
        const response = await fetch('<?= base_url('pelapor/submit') ?>', {
          method: 'POST',
          body: formData
        });

        // Tunggu balasan dari server
        const result = await response.json();

        if (result.status === 'success') {
          // Jika server bilang SUKSES:
          spinner.style.display = 'none';

          // Inisialisasi dan tampilkan modal sukses
          const successModal = new bootstrap.Modal(document.getElementById('successModal'));
          successModal.show();

          // Bersihkan form
          form.reset();
          previewContainer.innerHTML = '';
          fileStore = new DataTransfer();
          fileInput.files = fileStore.files;
          lokasiSelect.innerHTML = '<option value="">-- Pilih Jurusan Terlebih Dahulu --</option>';
          lokasiSelect.disabled = true;
          inventarisInput.value = ''; // Kosongkan inventaris

          checkFormValidity(); // Nonaktifkan tombol kirim lagi

        } else {
          // Jika server bilang GAGAL:
          throw new Error(result.message || 'Gagal menyimpan laporan.');
        }

      } catch (error) {
        // Jika ada error jaringan atau JavaScript
        console.error('Error:', error);
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Terjadi kesalahan: ' + error.message,
      });
        spinner.style.display = 'none';
      }
    });

    // =======================================================
    // === 6. FUNGSI SIDEBAR TOGGLE
    // =======================================================
    const toggleSidebar = document.getElementById('toggleSidebar');
    const sidebar = document.getElementById('sidebar');
    const content = document.querySelector('.content');
    const links = document.querySelectorAll('#sidebar .nav-link');
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

    links.forEach(link => {
      link.addEventListener('click', function() {
        links.forEach(l => l.classList.remove('active'));
        this.classList.add('active');
      });
    });
  </script>
</body>

</html>