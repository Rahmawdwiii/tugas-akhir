<?php
// Ambil segmen ke-2 dari URL, misalnya: admin/data_master_unit → "data_master_unit"
$uri = service('uri');
$segment = $uri->getSegment(2);
?>

<aside id="sidebar">
  <ul class="nav flex-column mt-3">
    <li class="nav-item">
      <a href="<?= base_url('teknisi/dashboard') ?>"
        class="nav-link <?= ($segment == 'dashboard' || $segment == '') ? 'active' : '' ?>">
        <i class="fas fa-th"></i> Dashboard
      </a>
    </li>

    <li class="nav-item">
      <a href="<?= base_url('teknisi/jadwal') ?>"
        class="nav-link <?= ($segment == 'jadwal' || $segment == '') ? 'active' : '' ?>">
        <i class="fas fa-house-damage"></i> Jadwal Perbaikan
      </a>
    </li>

    <li class="nav-item">
      <a href="<?= base_url('teknisi/riwayat') ?>"
        class="nav-link <?= ($segment == 'riwayat' || $segment == '') ? 'active' : '' ?>">
        <i class="fas fa-user-cog"></i> Riwayat
      </a>
    </li>
  </ul>
</aside>