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

    .modal {
      z-index: 9999 !important;
      /* Pastikan ini angka tertinggi */
    }

    .modal-backdrop {
      z-index: 9998 !important;
      /* Sedikit di bawah modal, tapi di atas header */
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

    @media (max-width: 768px) {
      .content {
        margin-left: 0;
      }

      #sidebar {
        width: 200px;
      }
    }

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

    /* Pastikan opsi di dalam list tetap berwarna hitam pekat */
    #filterAkses option {
      color: #212529;
      /* Warna hitam standar Bootstrap */
    }

    /* Khusus opsi pertama (placeholder) warnanya abu-abu */
    #filterAkses option[value=""] {
      color: #6c757d;
    }

    /* Memastikan SweetAlert (swal2-container) selalu di atas segalanya */
    .swal2-container {
      z-index: 20000 !important;
      /* Angka ini harus LEBIH BESAR dari 9999 */
    }
  </style>
</head>

<body>
  <!-- HEADER -->
  <?= $this->include('layout/header') ?>

  <!-- SIDEBAR -->
  <?= $this->include('layout/sidebar_admin') ?>

  <!-- CONTENT -->
  <div class="content">
    <div class="container-fluid">
      <div class="justify-content-between align-items-center mb-4">
        <div class="card shadow-sm border-0 mt-4">
          <div class="card-body py-3">
            <h6 class="fw-bold mb-1"><i class="fas fa-user-shield me-2"></i> Kontak Administrasi</h6>

            <div class="d-flex justify-content-between align-items-center mt-3 border-bottom pb-3">
              <strong class="text-dark">Admin UPAPP</strong>
              <div id="status_admin_upt">
                <?php if (!empty($admin_upt) && $admin_upt['is_online'] == 1): ?>
                  <span class="badge rounded-pill bg-success small">
                    <i class="fas fa-circle me-1" style="font-size: 8px;"></i> Online
                  </span>
                <?php else: ?>
                  <span class="badge rounded-pill bg-secondary small">Offline</span>
                <?php endif; ?>
              </div>
            </div>

            <h6 class="fw-bold mt-3 mb-2 small text-muted">TIM TEKNISI</h6>

            <div id="teknisi_list_container" class="d-flex flex-column gap-2">
              <?php if (!empty($teknisi)): ?>
                <?php foreach ($teknisi as $tek): ?>
                  <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                      <i class="fas fa-user-cog text-primary me-2"></i>
                      <span class="small fw-bold"><?= esc($tek['nama']) ?></span>
                    </div>

                    <?php if ($tek['is_online'] == 1): ?>
                      <span class="badge bg-success bg-opacity-10 text-success border border-success px-2 rounded-pill"
                        style="font-size: 0.7rem;">
                        Online
                      </span>
                    <?php else: ?>
                      <span class="badge bg-light text-muted border px-2 rounded-pill" style="font-size: 0.7rem;">
                        Offline
                      </span>
                    <?php endif; ?>
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <small class="text-muted">Belum ada data teknisi.</small>
              <?php endif; ?>
            </div>

          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-12">
          <div class="card shadow-sm">

            <!-- CARD HEADER -->
            <div class="card-header d-flex justify-content-between align-items-center">
              <h3 class="card-title fw-bold mb-0">DATA USER</h3>

              <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip"
                title="Collapse">
                <i class="fas fa-minus"></i>
              </button>
            </div>

            <!-- CARD BODY -->
            <div class="card-body">

              <!-- FILTER & ACTION -->
              <div class="row mb-3 align-items-center">
                <div class="col-md-4">
                  <div class="input-group">
                    <span class="input-group-text bg-white">
                      <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" id="searchUser" class="form-control" placeholder="Cari Nama / Username..." />
                  </div>
                </div>

                <div class="col-md-3">
                  <select id="filterAkses" class="form-select text-muted"
                    onchange="this.classList.toggle('text-muted', this.value === '')">
                    <option value="">– Semua Akses –</option>
                    <option value="admin">Admin</option>
                    <option value="teknisi">Teknisi</option>
                    <option value="pelapor">Pelapor</option>
                    <option value="operator">Operator</option>
                  </select>
                </div>

                <div class="col-md-5 text-end">
                  <button class="btn btn-success me-1" id="btnAddUser">
                    <i class="fas fa-plus me-1"></i> Tambah
                  </button>
                  <button class="btn btn-primary" id="btnReload">
                    <i class="fas fa-sync-alt me-1"></i> Reload
                  </button>
                </div>
              </div>

              <!-- TABLE -->
              <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped text-center align-middle" id="userTable">
                  <thead class="table-dark">
                    <tr>
                      <th width="5%">ID</th>
                      <th>USERNAME</th>
                      <th>EMAIL</th>
                      <th>PASSWORD</th>
                      <th>NAMA</th>
                      <th>JABATAN</th>
                      <th>AKSES</th>
                      <th width="15%">AKSI</th>
                    </tr>
                  </thead>

                  <tbody id="tableBody">
                    <?php if (!empty($daftar_user)): ?>
                      <?php $no = 1; ?>
                      <?php foreach ($daftar_user as $user): ?>
                        <tr>
                          <td><?= $no++ ?></td>
                          <td><?= esc($user['username']) ?></td>
                          <td>
                            <?= !empty($user['email']) ? esc($user['email']) : '-' ?>
                          </td>
                          <td class="text-muted">••••••••</td>
                          <td><?= esc($user['nama']) ?></td>
                          <td><?= esc($user['jabatan']) ?></td>
                          <td>
                            <?= esc(ucfirst($user['akses'])) ?>
                          </td>
                          <td>
                            <div class="d-flex justify-content-center gap-1">
                              <button class="btn btn-warning btn-sm text-white" onclick="editUser(<?= $user['id_user'] ?>)">
                                <i class="fas fa-edit"></i>
                              </button>
                              <button class="btn btn-danger btn-sm" onclick="hapusUser(<?= $user['id_user'] ?>)">
                                <i class="fas fa-trash"></i>
                              </button>
                            </div>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <tr>
                        <td colspan="7" class="text-muted py-4">
                          <i class="fas fa-inbox fa-2x mb-2 d-block opacity-50"></i>
                          Data user belum ditambahkan
                        </td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>

            </div>
          </div>
        </div>
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

  <!-- Modal Tambah User -->
  <div class="modal fade" id="modalUser" tabindex="-1" aria-labelledby="modalUserLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title fw-bold" id="modalUserLabel">Tambah User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body p-4">
          <form id="formUser">
            <input type="hidden" id="edit_id_user" name="id_user">

            <div class="row mb-3 align-items-center">
              <label for="nama" class="col-sm-3 col-form-label fw-bold">NAMA</label>
              <div class="col-sm-9">
                <input type="text" class="form-control" id="nama" name="nama" placeholder="">
              </div>
            </div>

            <div class="row mb-3 align-items-center">
              <label for="jabatan" class="col-sm-3 col-form-label fw-bold">JABATAN</label>
              <div class="col-sm-9">
                <input type="text" class="form-control" id="jabatan" name="jabatan" placeholder="">
              </div>
            </div>

            <div class="row mb-3 align-items-center">
              <label for="akses" class="col-sm-3 col-form-label fw-bold">AKSES <span
                  class="text-danger">*</span></label>
              <div class="col-sm-9">
                <select class="form-select" id="akses" name="akses" required>
                  <option value="">-- Pilih Hak Akses --</option>
                  <option value="operator">Operator</option>
                  <option value="admin">Admin</option>
                  <option value="teknisi">Teknisi</option>
                  <option value="pelapor">Pelapor</option>
                </select>
              </div>
            </div>

            <div class="row mb-3 align-items-center">
              <label for="username" class="col-sm-3 col-form-label fw-bold">USERNAME <span
                  class="text-danger">*</span></label>
              <div class="col-sm-9">
                <input type="text" class="form-control" id="username" name="username" placeholder="" required>
              </div>
            </div>

            <div class="row mb-3 align-items-center">

              <label for="email" class="col-sm-3 col-form-label fw-bold">
                EMAIL
              </label>

              <div class="col-sm-9">
                <input type="email" class="form-control" id="email" name="email" placeholder="contoh@email.com">
              </div>

            </div>

            <div class="row mb-3">
              <label for="password" class="col-sm-3 col-form-label fw-bold">PASSWORD <span
                  class="text-danger">*</span></label>
              <div class="col-sm-9">
                <input type="password" class="form-control" id="password" name="password" minlength="4" maxlength="20"
                  placeholder="Minimal 4, Maksimal 20 Karakter">

                <div id="passwordHelp" class="form-text small text-muted d-none mt-1">
                  <i class="fas fa-info-circle"></i> Biarkan kosong jika tidak ingin mengubah password. (Min: 4, Max: 20
                  Karakter).
                </div>

                <div class="form-check mt-2">
                  <input class="form-check-input" type="checkbox" id="showPassword" onclick="togglePassword()">
                  <label class="form-check-label user-select-none" for="showPassword" style="cursor:pointer;">
                    Tampilkan Password
                  </label>
                </div>
              </div>
            </div>
          </form>
        </div>

        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-danger px-4" data-bs-dismiss="modal">BATAL</button>
          <button type="button" class="btn btn-primary px-4" id="btnSave" onclick="saveUser()">SIMPAN</button>
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

  <script>
    // Menyimpan base_url dan token CSRF agar bisa diakses oleh JavaScript eksternal
    const BASE_URL = "<?= base_url() ?>";
    const CSRF_TOKEN_NAME = "<?= csrf_token() ?>";
    const CSRF_HASH = "<?= csrf_hash() ?>";
  </script>

  <script src="<?= base_url('admin/user.js') . '?v=' . @filemtime(FCPATH . 'admin/user.js') ?>"></script>

</body>

</html>