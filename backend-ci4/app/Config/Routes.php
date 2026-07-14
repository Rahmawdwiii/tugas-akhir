<?php

use CodeIgniter\Router\RouteCollection;

// =======================================================
// MANTRA UNTUK MENGHILANGKAN WARNING VS CODE
// =======================================================
use App\Controllers\Home;
use App\Controllers\Auth;
use App\Controllers\Admin;
use App\Controllers\Teknisi;
use App\Controllers\Pelapor;
use App\Controllers\Cetak;
use App\Controllers\LaporanController;
use App\Controllers\TesLaporan;
use App\Controllers\TesJadwal;
use App\Controllers\TesPerbaikan;
// =======================================================

/**
 * @var RouteCollection $routes
 */

// =======================================================
// 1. PUBLIC & AUTH ROUTES
// =======================================================
$routes->get('/', 'Home::index');
$routes->get('get_statistics_year', 'Home::get_statistics_year');
$routes->get('logout', 'Auth::logout');

$routes->group('auth', function ($routes) {
    $routes->get('login', 'Auth::login');
    $routes->post('login', 'Auth::login');
    $routes->get('logout', 'Auth::logout');
});

// =======================================================
// 2. GROUP ADMIN
// =======================================================
// Kita pasang filter 'auth' dengan argumen 'admin'
$routes->group('admin', ['filter' => 'auth:admin'], function ($routes) {
    // Dashboard
    $routes->get('dashboard', 'Admin::dashboard');

    // --- Master Data Views ---
    $routes->get('data_master_stok', 'Admin::data_master_stok');
    $routes->get('data_barang_rusak', 'Admin::data_barang_rusak');
    $routes->get('data_master_alat', 'Admin::data_master_alat');
    $routes->get('data_master_lokasi', 'Admin::data_master_lokasi');
    $routes->get('data_master_unit', 'Admin::data_master_unit');
    $routes->get('riwayat', 'Admin::riwayat');
    $routes->get('user', 'Admin::user');

    // --- Laporan Views ---
    $routes->get('laporan_kerusakan', 'Admin::laporan_kerusakan');
    $routes->get('laporan_peminjaman', 'Admin::laporan_peminjaman');
    $routes->get('antrian_perbaikan', 'Admin::antrian_perbaikan');
    // Tambahkan ini agar JavaScript bisa memanggil data
    $routes->get('get_antrian/(:segment)', 'Admin::get_antrian/$1');

    // --- Validasi & Penugasan ---
    $routes->post('validasi', 'Admin::validasi');
    $routes->post('updateJadwalPerbaikan', 'Admin::updateJadwalPerbaikan'); // Perbaiki method jika hanya POST
    $routes->get('updateJadwalPerbaikan', 'Admin::updateJadwalPerbaikan'); // Jika butuh GET juga
    // Tambahkan baris ini agar URL admin/tugaskan_teknisi bisa diakses
    $routes->post('tugaskan_teknisi', 'Admin::tugaskan_teknisi');

    // --- CRUD API (User) ---
    $routes->post('simpan_user', 'Admin::simpan_user');
    $routes->delete('delete/(:num)', 'Admin::hapus_user/$1');
    $routes->post('hapus_user/(:num)', 'Admin::hapus_user/$1'); // Alternatif POST
    $routes->get('get_user/(:num)', 'Admin::get_user/$1');

    // --- CRUD API (Stok) ---
    $routes->post('simpan_stok', 'Admin::simpan_stok');
    $routes->post('update_stok', 'Admin::update_stok');
    $routes->get('get_stok/(:num)', 'Admin::get_stok/$1');
    // Note: rute 'stok/delete' ada di luar grup ini di kode lama, saya pindahkan ke sini biar rapi
    // Jika fetch JS Anda mengarah ke 'stok/delete', ubah JS nya jadi 'admin/hapus_stok' atau sesuaikan route ini.
    $routes->delete('hapus_stok/(:num)', 'Admin::hapus_stok/$1');
    // Tambahkan ini agar URL hapus bisa diakses via POST
    $routes->post('hapus_stok/(:num)', 'Admin::hapus_stok/$1');

    // --- CRUD API (Alat) ---
    $routes->get('get_alat/(:num)', 'Admin::get_alat/$1');
    $routes->post('update_alat', 'Admin::update_alat');
    $routes->post('hapus_alat/(:num)', 'Admin::hapus_alat/$1');
    $routes->post('simpan_alat', 'Admin::simpan_alat');

    // --- CRUD API (Lokasi) ---
    $routes->get('get_lokasi/(:num)', 'Admin::get_lokasi/$1');
    $routes->post('update_lokasi', 'Admin::update_lokasi');
    $routes->post('hapus_lokasi/(:num)', 'Admin::hapus_lokasi/$1');
    $routes->post('tambah_lokasi', 'Admin::tambah_lokasi');
    $routes->get('get_lokasi_by_unit/(:num)', 'Admin::get_lokasi_by_unit/$1');

    // --- CRUD API (Unit) ---
    $routes->get('get_unit/(:num)', 'Admin::get_unit/$1');
    $routes->post('update_unit', 'Admin::update_unit');
    $routes->post('hapus_unit/(:num)', 'Admin::hapus_unit/$1');
    $routes->post('simpan_unit', 'Admin::simpan_unit');

    $routes->get('get_barang_rusak', 'Admin::get_barang_rusak');

    // --- Transaksi Lain ---
    $routes->get('get_data_by_nomor/(:any)', 'Admin::get_data_by_nomor/$1');
    $routes->post('update_data', 'Admin::update_data');
    $routes->post('tambah_data', 'Admin::tambah_data');
    $routes->get('get_last_nomor_peminjaman', 'Admin::get_last_nomor_peminjaman');
    $routes->post('simpan_peminjaman', 'Admin::simpan_peminjaman');
    $routes->post('hapus_peminjaman', 'Admin::hapus_peminjaman');
    $routes->get('get_peminjaman/(:num)', 'Admin::get_peminjaman/$1');
    $routes->get('get_dashboard_admin', 'Admin::get_dashboard_admin');
    $routes->get('get_notifikasi', 'Admin::get_notifikasi');
    $routes->get('get_statistik_json', 'Admin::get_statistik_json');
    $routes->get('get_statistik_barang', 'Admin::get_statistik_barang');
    $routes->get('get_statistik_antrian', 'Admin::get_statistik_antrian');
    $routes->get('get_laporan', 'Admin::get_laporan');
    $routes->get('get_laporan_riwayat', 'Admin::get_laporan_riwayat');
    $routes->get('get_nomor_otomatis', 'Admin::get_nomor_otomatis');
    $routes->post('hapus_laporan', 'Admin::hapus_laporan');

    // Masukkan ini di dalam tanda kurung kurawal group admin Bung ya!
    $routes->post('proses_review_antrian', 'Admin::proses_review_antrian');
    $routes->get('fp_growth', 'Admin::fp_growth');
    $routes->post('generate_fp_growth', 'Admin::generate_fp_growth');

    $routes->post(
        'get_rule_manual',
        'Admin::get_rule_manual'
    );

    $routes->get('get_laporan_historis', 'Admin::get_laporan_historis');
});

// =======================================================
// 3. GROUP TEKNISI
// =======================================================
$routes->group('teknisi', ['filter' => 'auth:teknisi'], function ($routes) {
    // Dashboard & Menu Utama
    $routes->get('dashboard', 'Teknisi::dashboard');
    $routes->get('jadwal', 'Teknisi::jadwal');
    $routes->get('riwayat', 'Teknisi::riwayat');

    // Update Status Manual (View & Action)
    $routes->get('update_status', 'Teknisi::update_status');
    $routes->post('update_status', 'Teknisi::update_status');

    // Aksi Kerja (Menggunakan Controller di folder Teknisi)
    $routes->post('mulai-kerja/(:segment)', 'Teknisi::mulai_kerja/$1');

    // Route untuk mengambil data JSON tugas teknisi
    // (:segment) artinya akan menangkap kata 'baru', 'proses', dll dan dikirim sebagai parameter
    $routes->get('get_tugas_json/(:segment)', 'Teknisi::get_tugas_json/$1');
    // app/Config/Routes.php (Grup Teknisi)
    $routes->post('pending', 'Teknisi::pending');
    $routes->post('revisi_lagi', 'Teknisi::revisi_lagi');
    $routes->get('lanjutkan_kerja/(:any)', 'Teknisi::lanjutkan_kerja/$1');
    $routes->post('jadwal/selesai', 'Teknisi::selesai');
    $routes->get('jadwal/rusak_total/(:segment)', 'Teknisi::rusak_total/$1');
    $routes->post('jadwal/rusak_total/(:segment)', 'Teknisi::rusak_total/$1');
    $routes->get('get_statistik_json', 'Teknisi::get_statistik_json');
    $routes->get('get_riwayat_ajax', 'Teknisi::get_riwayat_ajax');
    $routes->get('get_riwayat_datatable', 'Teknisi::get_riwayat_datatable');
    // Di dalam group admin atau route biasa
    $routes->post('tambah_data', 'Teknisi::tambah_data');
    // Pastikan ini ada!
    $routes->get('get_nomor_otomatis', 'Teknisi::get_nomor_otomatis');
    $routes->post('hapus_laporan', 'Teknisi::hapus_laporan');
    $routes->get('get_count_dashboard', 'Teknisi::get_count_dashboard');
    // Di dalam group 'teknisi'
    $routes->get('get_data_by_nomor/(:segment)', 'Teknisi::get_data_by_nomor/$1');
    $routes->post('update_data', 'Teknisi::update_data');
    $routes->get('get_dashboard_charts', 'Teknisi::get_dashboard_charts');
    $routes->get('get_notifikasi', 'Teknisi::get_notifikasi');
});

// =======================================================
// 4. GROUP PELAPOR (SUDAH DIPERBAIKI UNTUK DASHBOARD)
// =======================================================
$routes->group('pelapor', ['filter' => 'auth:pelapor'], function ($routes) {
    // Dashboard & Form
    $routes->get('dashboard', 'Pelapor::dashboard');
    $routes->get('form_laporan', 'Pelapor::form_laporan'); // Form Input
    $routes->get('riwayat', 'Pelapor::riwayat');

    // API & Aksi
    $routes->post('submit', 'Pelapor::submit'); // Simpan Laporan
    $routes->post('validasi/(:num)', 'Pelapor\ValidasiController::submit/$1'); // Simpan Validasi

    // --- TAMBAHKAN 2 BARIS INI BUNG! ---
    $routes->post('komplain', 'Pelapor::komplain');
    $routes->post('selesaikan_laporan', 'Pelapor::selesaikan_laporan');
    // -----------------------------------

    // --- INI YANG WAJIB ADA AGAR DASHBOARD MUNCUL ---
    $routes->get('get_laporan/(:any)', 'Pelapor::get_laporan/$1');
    $routes->get('get_detail/(:num)', 'Pelapor::get_detail/$1');

    $routes->get('get_counters', 'Pelapor::get_counters');
    $routes->get('get_dashboard_charts', 'Pelapor::get_dashboard_charts');
    $routes->get('get_notifikasi', 'Pelapor::get_notifikasi');
    $routes->post('hapus_laporan', 'Pelapor::hapus_laporan');
});

// =======================================================
// 5. CETAK & TESTING ROUTES
// =======================================================
$routes->group('cetak', function ($routes) {
    // SATU-SATUNYA ROUTE YANG DIBUTUHKAN UNTUK CETAK PDF
    $routes->get('cetak_laporan/(:any)', 'Cetak::cetakLaporanPdf/$1');
    $routes->get('cetak_filter_pdf', 'Cetak::cetakFilterPdf');
});

$routes->get('laporan/cetak_kerusakan/(:any)', 'LaporanController::cetak_kerusakan/$1');

// Testing (Hapus jika sudah production)
$routes->get('teslaporan', 'TesLaporan::index');
$routes->get('tesjadwal', 'TesJadwal::index');
$routes->get('tesperbaikan', 'TesPerbaikan::index');

// Route Stok Delete Legacy (Jika masih dipanggil langsung tanpa prefix admin)
$routes->delete('stok/delete/(:num)', 'Admin::hapus_stok/$1');
// Tambahkan ini di bagian Route Definitions
$routes->post('admin/hapus_peminjaman/(:num)', 'Admin::hapus_peminjaman/$1');
// Tambahkan Route untuk GET data (Edit)
$routes->get('admin/get_peminjaman/(:num)', 'Admin::get_peminjaman/$1');
// Tambahkan Route untuk POST data (Update)
$routes->post('admin/update_peminjaman', 'Admin::update_peminjaman');

// Tambahkan baris ini agar AJAX bisa mengakses controller
$routes->post('teknisi/update_status_online', 'Teknisi::update_status_online');
$routes->post('admin/update_status_online', 'Admin::update_status_online');
// Route to return teknisi statuses as JSON (used by admin UI polling)
$routes->get('admin/get_teknisi_json', 'Admin::get_teknisi_json');

$routes->get('email-test', 'EmailTest::index');