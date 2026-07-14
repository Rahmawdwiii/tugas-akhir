<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>UPAPP POLSRI | Pelaporan & Peminjaman</title>

  <!-- Import Bootstrap dan Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    /* === STYLE DASAR === */
    body {
      font-family: 'Poppins', sans-serif;
      scroll-behavior: smooth;
      background-color: #b3d9ff;
      /* pastel blue */
    }

    /* === HEADER === */
    header {
      background: linear-gradient(90deg, #00416A, #00B3E6);
      color: white;
      padding: 10px 0;
    }

    header h1 {
      font-size: medium;
      margin: 0;
      font-weight: 600;
    }


    /* === HERO SECTION === */
    .hero {
      background: #b3d9ff;
      /* pastel blue */
      height: 80vh;
      display: flex;
      align-items: center;
      background-attachment: fixed;
    }

    .hero h2 {
      font-size: 2.2rem;
      font-weight: 700;
      color: #003366;
    }

    .hero p {
      font-size: 1.1rem;
      color: #002244;
      margin-top: 10px;
    }

    .hero img {
      border: 3px solid white;
    }

    /* Responsif untuk mobile */
    @media (max-width: 768px) {
      .hero {
        height: auto;
        padding: 40px 0;
      }

      .hero .col-md-8,
      .hero .col-md-4 {
        text-align: center;
      }

      .hero img {
        width: 80px;
      }
    }


    /* === SECTION TITLE === */
    .section-title {
      font-weight: 700;
      margin-bottom: 1rem;
      color: #00416A;
      text-align: center;
    }

    #anggota {
      background-color: #e6f2ff;
    }

    #anggota img {
      border: 4px solid white;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s;
    }

    #anggota img:hover {
      transform: scale(1.05);
    }


    /* === FOOTER === */
    footer {
      background-color: #002c43;
      color: white;
      padding: 30px 0;
    }

    footer a {
      color: #00B3E6;
      text-decoration: none;
    }

    footer a:hover {
      text-decoration: underline;
    }

    body {
      background-color: #e3f2fd;
      /* pastel blue background */
    }
  </style>
</head>

<body>

  <!-- === HEADER === -->
  <header>
    <div class="container d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-center">
        <img src="<?= base_url('images/polsri.png') ?>" alt="Logo Polsri"
          style="height: 55px; width: auto; margin-right: 15px; opacity: 0.9;">
        <h1>UNIT PENUNJANG AKADEMIK PERAWATAN DAN PERBAIKAN</< /h1>
      </div>

      <!-- Navbar kanan atas -->
      <nav>
        <ul class="nav">
          <li class="nav-item">
            <a class="nav-link text-white fw-bold px-3" href="#grafik">Grafik</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white fw-bold px-3" href="#kontak">Kontak</a>
          </li>
        </ul>
      </nav>
    </div>
  </header>

  <style>
    header {
      background: linear-gradient(90deg, #00416A, #00B3E6);
      color: white;
      padding: 10px 0;
    }

    header .nav-link {
      transition: color 0.3s;
    }

    header .nav-link:hover {
      color: #FFD700;
      /* warna saat hover */
    }
  </style>

  <!-- === HERO SECTION === -->
  <!-- Tampilan awal dengan background besar -->
  <!-- Hero Section (dibagi dua secara horizontal) -->
  <section class="hero d-flex align-items-center">
    <div class="container">
      <div class="row align-items-center">

        <!-- Bagian kiri: teks -->
        <div class="col-md-8 text-white">
          <h2>Selamat Datang di Website Unit Penunjang Akademik Perawatan dan Perbaikan!</h2>
          <p>Unit Penunjang Akdemik Perawatan dan Perbaikan adalah unit pelaksana teknis di lingkungan Politeknik Negeri
            Sriwijaya yang bertugas melaksanakan kegiatan pelaksana teknis teknologi permesinan dan peralatan untuk
            menunjang kegiatan Akademik .</p>
          <a href="#" class="btn btn-light btn-lg px-5 py-3 fw-bold text-primary shadow" data-bs-toggle="modal"
            data-bs-target="#loginModal">Login</a>
        </div>

        <!-- Kolom kanan: slideshow member -->
        <div class="col-md-4">
          <div id="memberCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="2500">
            <div class="carousel-inner text-center">

              <div class="carousel-item active">
                <img src="<?= base_url('images/profile.png') ?>" class="rounded-circle mb-3" alt="Harba Ario Sukha">
                <h5 class="fw-bold text-dark mb-0">Harba Ario Sukha</h5>
                <p class="text-secondary mb-0">Kepala UPA-Perawatan dan Perbaikan</p>
              </div>

              <div class="carousel-item">
                <img src="<?= base_url('images/profile.png') ?>" class="rounded-circle mb-3" alt="Riadi Putra">
                <h5 class="fw-bold text-dark mb-0">Riadi Putra</h5>
                <p class="text-secondary mb-0">Sekretaris UPA-Perawatan dan Perbaikan</p>
              </div>

              <div class="carousel-item">
                <img src="<?= base_url('images/profile.png') ?>" class="rounded-circle mb-3" alt="Sukri">
                <h5 class="fw-bold text-dark mb-0">Sukri</h5>
                <p class="text-secondary mb-0">Anggota UPA-Perawatan dan Perbaikan</p>
              </div>

              <div class="carousel-item">
                <img src="<?= base_url('images/profile.png') ?>" class="rounded-circle mb-3" alt="Muhammad Karison">
                <h5 class="fw-bold text-dark mb-0">Muhammad Karison</h5>
                <p class="text-secondary mb-0">Anggota UPA-Perawatan dan Perbaikan</p>
              </div>

              <div class="carousel-item">
                <img src="<?= base_url('images/profile.png') ?>" class="rounded-circle mb-3" alt="Edial Salmes">
                <h5 class="fw-bold text-dark mb-0">Edial Salmes</h5>
                <p class="text-secondary mb-0">Anggota UPA-Perawatan dan Perbaikan</p>
              </div>

              <div class="carousel-item">
                <img src="<?= base_url('images/profile.png') ?>" class="rounded-circle mb-3" alt="Eko">
                <h5 class="fw-bold text-dark mb-0">Cipto</h5>
                <p class="text-secondary mb-0">Anggota UPA-Perawatan dan Perbaikan</p>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Login -->

    <div class="modal fade <?= (session()->getFlashdata('msg')) ? 'show' : '' ?>" id="loginModal" tabindex="-1"
      aria-labelledby="loginModalLabel" aria-hidden="<?= (session()->getFlashdata('msg')) ? 'false' : 'true' ?>"
      style="<?= (session()->getFlashdata('msg')) ? 'display:block; background-color: rgba(0,0,0,0.5);' : '' ?>">

      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">

          <div class="modal-header border-0" style="background: linear-gradient(135deg,#00416A,#00B3E6); color:white;">
            <h5 class="modal-title fw-bold" id="loginModalLabel">
              <i class="bi bi-person-circle me-2"></i> Login Sistem
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"
              onclick="this.closest('.modal').style.display='none';"></button>
          </div>

          <div class="modal-body p-4">
            <?php if (session()->getFlashdata('msg')): ?>
              <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-4"></i>
                <div><?= session()->getFlashdata('msg'); ?></div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
            <?php endif; ?>

            <form action="<?= base_url('auth/login') ?>" method="post">
              <?= csrf_field() ?>
              <div class="mb-3">
                <label for="usernameModal" class="form-label fw-semibold text-secondary">Username</label>
                <div class="input-group">
                  <span class="input-group-text bg-light border-end-0"><i class="bi bi-person"></i></span>
                  <input type="text" class="form-control border-start-0 ps-0" id="usernameModal" name="username"
                    placeholder="Masukkan username" required autofocus>
                </div>
              </div>

              <div class="mb-4">
                <label for="passwordModal" class="form-label fw-semibold text-secondary">Password</label>
                <div class="input-group">
                  <span class="input-group-text bg-light border-end-0"><i class="bi bi-key"></i></span>
                  <input type="password" class="form-control border-start-0 ps-0" id="passwordModal" name="password"
                    placeholder="Masukkan password" required>
                  <button type="button" class="btn btn-light border-start-0 text-secondary" id="togglePasswordLogin"
                    tabindex="-1" aria-label="Tampilkan password">
                    <i class="bi bi-eye"></i>
                  </button>
                </div>
              </div>

              <button type="submit" class="btn w-100 py-2 fw-bold text-white shadow-sm"
                style="background: linear-gradient(135deg,#00416A,#00B3E6); border:none; border-radius: 8px;">
                MASUK
              </button>
            </form>
          </div>

          <div class="modal-footer justify-content-center bg-light border-0 py-3">
            <small class="text-muted">Lupa password? Hubungi Administrator.</small>
          </div>

        </div>
      </div>
    </div>
  </section>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const passwordInput = document.getElementById('passwordModal');
      const toggleButton = document.getElementById('togglePasswordLogin');

      if (!passwordInput || !toggleButton) return;

      toggleButton.addEventListener('click', function () {
        const isPassword = passwordInput.type === 'password';
        passwordInput.type = isPassword ? 'text' : 'password';
        const icon = this.querySelector('i');
        if (icon) {
          icon.classList.toggle('bi-eye');
          icon.classList.toggle('bi-eye-slash');
        }
      });
    });
  </script>

  <!-- === GRAFIK LAPORAN KERUSAKAN === -->
  <section id="grafik" class="py-5">
    <div class="container">
      <h3 class="section-title mb-5">
        Statistik Laporan Kerusakan Tahun
        <span id="yearDisplay"></span>
      </h3>
      <div class="row g-4 align-items-center">

        <!-- === 1/3: BOX KATEGORI === -->
        <div class="col-lg-4 col-md-12">
          <div class="row g-3">
            <!-- Box Rusak Berat -->
            <div class="col-12">
              <div class="card shadow border-0"
                style="background: linear-gradient(135deg, #d9534f, #c9302c); color: white;">
                <div class="card-body text-center">
                  <h5 class="fw-bold mb-2">Rusak Berat</h5>
                  <h2 class="fw-bold" id="statBeratValue">-</h2>
                </div>
              </div>
            </div>
            <!-- Box Rusak Sedang -->
            <div class="col-12">
              <div class="card shadow border-0"
                style="background: linear-gradient(135deg, #f0ad4e, #ec971f); color: white;">
                <div class="card-body text-center">
                  <h5 class="fw-bold mb-2">Rusak Sedang</h5>
                  <h2 class="fw-bold" id="statSedangValue">-</h2>
                </div>
              </div>
            </div>
            <!-- Box Rusak Ringan -->
            <div class="col-12">
              <div class="card shadow border-0"
                style="background: linear-gradient(135deg, #5cb85c, #449d44); color: white;">
                <div class="card-body text-center">
                  <h5 class="fw-bold mb-2">Rusak Ringan</h5>
                  <h2 class="fw-bold" id="statRinganValue">-</h2>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- === 2/3: GRAFIK === -->
        <div class="col-lg-8 col-md-12">
          <div class="card shadow-sm border-0">
            <div class="card-body">
              <canvas id="chartKerusakan"></canvas>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- === KONTAK === -->
  <section id="kontak" class="py-5" style="background: #b3d9ff;">
    <div class="container">
      <h3 class="text-center text-primary fw-bold mb-5">KONTAK & INFORMASI</h3>
      <div class="row g-4">

        <!-- Alamat -->
        <div class="col-lg-4 col-md-6">
          <div class="card h-100 border-0 shadow-lg text-white"
            style="background: linear-gradient(135deg, #00416A, #00B3E6);">
            <div class="card-body text-center">
              <div class="mb-3">
                <i class="bi bi-geo-alt-fill fs-2 text-white"></i>
              </div>
              <h5 class="fw-bold text-white mb-3">Alamat</h5>
              <p class="mb-2 text-light">Jl. Srijaya Negara Bukit Besar, Palembang, Sumatera Selatan</p>
              <p class="text-light mb-0">Kode Pos: 30139</p>
            </div>
          </div>
        </div>

        <!-- Kontak -->
        <div class="col-lg-4 col-md-6">
          <div class="card h-100 border-0 shadow-lg text-white"
            style="background: linear-gradient(135deg, #00416A, #00B3E6);">
            <div class="card-body text-center">
              <div class="mb-3">
                <i class="bi bi-telephone-fill fs-2 text-white"></i>
              </div>
              <h5 class="fw-bold text-white mb-3">Kontak</h5>
              <p class="mb-1 text-light"><i class="bi bi-telephone me-2"></i> (0711) 353414</p>
              <p class="mb-1 text-light"><i class="bi bi-telephone-forward me-2"></i> (0711) 355918</p>
              <p class="mb-0 text-light"><i class="bi bi-envelope me-2"></i> upttp@polsri.ac.id</p>
            </div>
          </div>
        </div>

        <!-- Sosial Media -->
        <div class="col-lg-4 col-md-12">
          <div class="card h-100 border-0 shadow-lg text-white"
            style="background: linear-gradient(135deg, #00416A, #00B3E6);">
            <div class="card-body text-center">
              <div class="mb-3">
                <i class="bi bi-share-fill fs-2 text-white"></i>
              </div>
              <h5 class="fw-bold text-white mb-3">Sosial Media</h5>
              <p class="mb-1 text-light">
                <a href="https://www.instagram.com/polsriofficial?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw=="
                  target="_blank" class="text-light text-decoration-none">
                  <i class="bi bi-instagram me-2 text-light"></i> polsriofficial
                </a>
              </p>

              <p class="mb-1 text-light">
                <a href="https://x.com/polsriofficial" target="_blank" class="text-light text-decoration-none">
                  <i class="bi bi-twitter me-2 text-light"></i> polsriofficial
                </a>
              </p>

              <p class="mb-0 text-light">
                <a href="https://www.facebook.com/Polsri" target="_blank" class="text-light text-decoration-none">
                  <i class="bi bi-facebook me-2 text-light"></i> Politeknik Negeri Sriwijaya
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- === FOOTER === -->
  <footer class="text-center">
    <p>© 2025 UPAPP POLSRI. All Rights Reserved.</p>
  </footer>

  <!-- === SCRIPT === -->
  <!-- Bootstrap dan Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <script>
    // Variabel chart global untuk update
    let chartInstance = null;
    const BASE_URL = "<?= base_url() ?>";

    // Fungsi fetch statistik dari server
    async function loadStatisticsData() {
      try {
        const response = await fetch(`${BASE_URL}get_statistics_year`, {
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const result = await response.json();

        console.log(result);

        if (result.status === 'success') {
          const { data, year } = result;

          // Update year display
          const yearDisplay = document.getElementById('yearDisplay');
          if (yearDisplay) {
            yearDisplay.innerText = year;
          } else {
          }

          // Update card values
          const statBeratValue = document.getElementById('statBeratValue');
          if (statBeratValue) statBeratValue.innerText = data.berat || 0;

          const statSedangValue = document.getElementById('statSedangValue');
          if (statSedangValue) statSedangValue.innerText = data.sedang || 0;

          const statRinganValue = document.getElementById('statRinganValue');
          if (statRinganValue) statRinganValue.innerText = data.ringan || 0;

          // Update chart
          updateChart(data);
        }
      } catch (error) {
        console.error('Error loading statistics:', error);
      }
    }

    // Fungsi update chart
    function updateChart(data) {
      const canvas = document.getElementById('chartKerusakan');
      if (!canvas) return;

      const ctx = canvas.getContext('2d');

      // Destroy chart lama jika ada
      if (chartInstance) {
        chartInstance.destroy();
      }

      // Buat chart baru dengan data terbaru
      chartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: ['Rusak Berat', 'Rusak Sedang', 'Rusak Ringan'],
          datasets: [{
            label: 'Jumlah Kerusakan',
            data: [data.berat || 0, data.sedang || 0, data.ringan || 0],
            backgroundColor: ['#d9534f', '#f0ad4e', '#14a317'],
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          scales: {
            y: {
              beginAtZero: true
            }
          }
        }
      });
    }

    // Load data saat halaman pertama kali dimuat
    document.addEventListener('DOMContentLoaded', function () {
      loadStatisticsData();
      // Refresh data setiap 10 detik untuk real-time updates
      setInterval(loadStatisticsData, 10000);
    });
  </script>

</body>

</html>