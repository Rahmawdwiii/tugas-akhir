<?php
// Ambil segmen ke-2 dari URL, misalnya: admin/data_master_unit → "data_master_unit"
$uri = service('uri');
$segment = $uri->getSegment(2);
?>

<aside id="sidebar">
  <ul class="nav flex-column mt-3">
    <li class="nav-item">
      <a href="<?= base_url('admin/dashboard') ?>"
        class="nav-link <?= ($segment == 'dashboard' || $segment == '') ? 'active' : '' ?>">
        <i class="fas fa-th"></i> Dashboard
      </a>
    </li>

    <li class="nav-item">
      <a href="<?= base_url('admin/data_barang_rusak') ?>"
        class="nav-link <?= ($segment == 'data_barang_rusak') ? 'active' : '' ?>">
        <i class="fas fa-house-damage"></i> Data Barang Rusak
      </a>
    </li>

    <!-- Cek apakah salah satu submenu aktif -->
    <?php
    $isDataMasterActive = in_array($segment, ['data_master_unit', 'data_master_lokasi', 'data_master_alat', 'data_master_stok']);
    ?>

    <li class="nav-item">
      <a href="#"
        class="nav-link <?= $isDataMasterActive ? 'active' : '' ?>"
        data-bs-toggle="collapse"
        data-bs-target="#dataMaster"
        aria-expanded="<?= $isDataMasterActive ? 'true' : 'false' ?>">
        <i class="fas fa-copy"></i> Data Master
        <i class="fas <?= $isDataMasterActive ? 'fa-angle-down' : 'fa-angle-left' ?> ms-auto toggle-icon"></i>
      </a>

      <ul class="submenu collapse <?= $isDataMasterActive ? 'show' : '' ?>" id="dataMaster">
        <li>
          <a href="<?= base_url('admin/data_master_unit') ?>"
            class="nav-link <?= ($segment == 'data_master_unit') ? 'active' : '' ?>">
            <i class="far fa-circle"></i> Unit
          </a>
        </li>
        <li>
          <a href="<?= base_url('admin/data_master_lokasi') ?>"
            class="nav-link <?= ($segment == 'data_master_lokasi') ? 'active' : '' ?>">
            <i class="far fa-circle"></i> Lokasi
          </a>
        </li>
        <li>
          <a href="<?= base_url('admin/data_master_alat') ?>"
            class="nav-link <?= ($segment == 'data_master_alat') ? 'active' : '' ?>">
            <i class="far fa-circle"></i> Alat
          </a>
        </li>
        <li>
          <a href="<?= base_url('admin/data_master_stok') ?>"
            class="nav-link <?= ($segment == 'data_master_stok') ? 'active' : '' ?>">
            <i class="far fa-circle"></i> Stok Barang
          </a>
        </li>
      </ul>
    </li>

    <li class="nav-item">
      <a href="<?= base_url('admin/antrian_perbaikan') ?>"
        class="nav-link <?= ($segment == 'antrian_perbaikan') ? 'active' : '' ?>">
        <i class="fas fa-tools"></i> Antrian Perbaikan
      </a>
    </li>

    <li class="nav-item">
      <a href="<?= base_url('admin/laporan_kerusakan') ?>"
        class="nav-link <?= ($segment == 'laporan_kerusakan') ? 'active' : '' ?>">
        <i class="fas fa-book"></i> Laporan Kerusakan
      </a>
    </li>

    <li class="nav-item">
      <a href="<?= base_url('admin/laporan_peminjaman') ?>"
        class="nav-link <?= ($segment == 'laporan_peminjaman') ? 'active' : '' ?>">
        <i class="fas fa-book"></i> Laporan Peminjaman
      </a>
    </li>

    <!-- <li class="nav-item">
      <a href="<?= base_url('admin/riwayat') ?>"
        class="nav-link <?= ($segment == 'riwayat') ? 'active' : '' ?>">
        <i class="fas fa-user-cog"></i> Riwayat
      </a>
    </li> -->

    <li class="nav-item">
      <a href="<?= base_url('admin/user') ?>"
        class="nav-link <?= ($segment == 'user') ? 'active' : '' ?>">
        <i class="fas fa-user-cog"></i> User
      </a>
    </li>

    <li class="nav-item">
      <a href="<?= base_url('admin/fp_growth') ?>"
        class="nav-link <?= ($segment == 'fp_growth') ? 'active' : '' ?>">
        <i class="fas fa-user-cog"></i> Analisis FP-Growth
      </a>
    </li>
  </ul>
</aside>

<script>
  document.addEventListener("DOMContentLoaded", function() {
    const dataMasterToggle = document.querySelector('[data-bs-target="#dataMaster"]');
    const dataMasterMenu = document.getElementById('dataMaster');
    if (!dataMasterToggle || !dataMasterMenu) return;

    const icon = dataMasterToggle.querySelector('.toggle-icon');

    // ✅ Jika salah satu submenu aktif, pastikan ikon panah ke bawah
    if (dataMasterMenu.classList.contains('show')) {
      icon.classList.remove('fa-angle-left');
      icon.classList.add('fa-angle-down');
    }

    // ✅ Saat submenu dibuka (collapse Bootstrap)
    dataMasterMenu.addEventListener('shown.bs.collapse', () => {
      icon.classList.remove('fa-angle-left');
      icon.classList.add('fa-angle-down');
    });

    // ✅ Saat submenu ditutup
    dataMasterMenu.addEventListener('hidden.bs.collapse', () => {
      icon.classList.remove('fa-angle-down');
      icon.classList.add('fa-angle-left');
    });
  });
</script>