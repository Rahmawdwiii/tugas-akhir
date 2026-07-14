<?php
  $uri = service('uri');
  $segment = $uri->getSegment(2);
?>

<!-- Sidebar Pelapor -->
<aside id="sidebar">
  <ul class="nav flex-column mt-3">
    <li class="nav-item">
      <a href="<?= base_url('pelapor/dashboard') ?>"
         class="nav-link <?= ($segment == 'dashboard' || $segment == '') ? 'active' : '' ?>">
        <i class="fas fa-tachometer-alt"></i> Dashboard
      </a>
    </li>
    <li class="nav-item">
      <a href="<?= base_url('pelapor/form_laporan') ?>"
         class="nav-link <?= ($segment == 'form_laporan') ? 'active' : '' ?>">
        <i class="fas fa-calendar-check"></i> Form Laporan
      </a>
    </li>
    <li class="nav-item">
      <a href="<?= base_url('pelapor/riwayat') ?>"
         class="nav-link <?= ($segment == 'riwayat') ? 'active' : '' ?>">
        <i class="fas fa-edit"></i> Riwayat
      </a>
    </li>
  </ul>
</aside>