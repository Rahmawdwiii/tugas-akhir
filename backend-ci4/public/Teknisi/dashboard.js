const toggleSidebar = document.getElementById("toggleSidebar");
const sidebar = document.getElementById("sidebar");
const content = document.querySelector(".content");
const links = document.querySelectorAll("#sidebar .nav-link");
// Optional footer elements — may be absent in some layouts
const footerContent = document.querySelector('.footer-content');
const footer = document.querySelector('footer');

toggleSidebar.addEventListener("click", () => {
  sidebar.classList.toggle("collapsed");
  content.classList.toggle("full");

  /* Kalau mau pakai footer yg satunya aktifkan ini*/
  if (footerContent) {
    footerContent.classList.toggle("full");
  }
  // Tambahkan logika untuk footer
  if (footer) {
    if (sidebar.classList.contains("collapsed")) {
      footer.style.marginLeft = "0";
    } else {
      // Jika di mobile (layar kecil), margin tetap 0
      if (window.innerWidth > 768) {
        footer.style.marginLeft = "250px";
      }
    }
  }
});

links.forEach((link) => {
  link.addEventListener("click", function () {
    links.forEach((l) => l.classList.remove("active"));
    this.classList.add("active");
  });
});

let modalUnitInstance;

window.addEventListener("DOMContentLoaded", () => {
  // Inisialisasi modal
  const modalEl = document.getElementById("modal_unit");
  if (modalEl) modalUnitInstance = new bootstrap.Modal(modalEl);

  // Inisialisasi Pagination hanya jika tabel ada
  if (document.getElementById("tableBody")) {
    initPagination();
  }
});

// Jadikan variabel global agar bisa diakses ulang jika data berubah
let globalRows = [];
let globalCurrentPage = 1;
const rowsPerPage = 10;

function initPagination() {
  const tableBody = document.getElementById("tableBody");
  const paginationContainer = document.querySelector(
    "#table_paginate ul.pagination"
  );
  const tableInfo = document.getElementById("table_info");

  // DEBUGGING: Cek apakah elemen ditemukan
  if (!tableBody) {
    console.error("Error: <tbody id='tableBody'> tidak ditemukan!");
    return;
  }
  if (!paginationContainer) {
    console.error("Error: Container pagination tidak ditemukan!");
    return;
  }

  // Ambil semua TR KECUALI yang punya class 'empty-row'
  globalRows = Array.from(
    tableBody.querySelectorAll(":scope > tr:not(.empty-row)")
  );

  // Reset halaman ke 1 setiap kali init ulang
  globalCurrentPage = 1;

  // Fungsi render dibuat terpisah agar lebih rapi
  renderPage(globalCurrentPage);
}

// Fungsi Render dipisah agar bisa dipanggil manual
window.showPage = function (page) {
  const totalRows = globalRows.length;
  const totalPages = Math.ceil(totalRows / rowsPerPage);

  if (page < 1) page = 1;
  if (page > totalPages && totalPages > 0) page = totalPages;

  globalCurrentPage = page;

  // 1. Sembunyikan semua baris dulu
  globalRows.forEach((row) => (row.style.display = "none"));

  // 2. Hitung start dan end
  const start = (page - 1) * rowsPerPage;
  const end = start + rowsPerPage;

  // 3. Munculkan baris yang sesuai slice
  globalRows.slice(start, end).forEach((row) => (row.style.display = ""));

  // 4. Update Teks Info
  const tableInfo = document.getElementById("table_info");
  if (tableInfo) {
    const startInfo = totalRows === 0 ? 0 : start + 1;
    const endInfo = Math.min(end, totalRows);
    tableInfo.innerText = `Showing ${startInfo} to ${endInfo} of ${totalRows} entries`;
  }

  // 5. Update Tombol Pagination
  updatePaginationUI(totalPages, page);
};

function updatePaginationUI(totalPages, currentPage) {
  const paginationContainer = document.querySelector(
    "#table_paginate ul.pagination"
  );
  if (!paginationContainer) return;

  paginationContainer.innerHTML = "";

  // Tombol Previous
  const prevDisabled = currentPage === 1 ? "disabled" : "";
  paginationContainer.innerHTML += `
      <li class="page-item ${prevDisabled}">
        <a class="page-link" href="javascript:void(0)" onclick="showPage(${
          currentPage - 1
        })">Previous</a>
      </li>`;

  // Loop Angka Halaman
  // (Opsional: Jika halaman ribuan, logic ini perlu dibatasi agar tidak meluap)
  for (let i = 1; i <= totalPages; i++) {
    const activeClass = i === currentPage ? "active" : "";
    paginationContainer.innerHTML += `
        <li class="page-item ${activeClass}">
          <a class="page-link" href="javascript:void(0)" onclick="showPage(${i})">${i}</a>
        </li>`;
  }

  // Tombol Next
  const nextDisabled =
    currentPage === totalPages || totalPages === 0 ? "disabled" : "";
  paginationContainer.innerHTML += `
      <li class="page-item ${nextDisabled}">
        <a class="page-link" href="javascript:void(0)" onclick="showPage(${
          currentPage + 1
        })">Next</a>
      </li>`;
}

// Helper function untuk memanggil render page pertama kali
function renderPage(page) {
  window.showPage(page);
}

// ======================================================
// 2. MENGGAMBAR GRAFIK (REALTIME DARI DATABASE)
// ======================================================

// Kita buat variabel penampung di luar agar saat data di-update,
// grafik yang lama bisa dihancurkan (destroy) dulu biar tidak error tumpang-tindih.
let chartJurusan, chartSeverity, chartAlat, chartStatus;

function loadDashboardData() {
  // Panggil data JSON dari Controller PHP yang kita buat
  // Pastikan variabel BASE_URL sudah ada di HTML Bung
  fetch(BASE_URL + "teknisi/get_dashboard_charts", {
    headers: { "X-Requested-With": "XMLHttpRequest" },
  })
    .then((response) => response.json())
    .then((res) => {
      // A. UPDATE ANGKA DI KOTAK KARTU ATAS
      if (document.getElementById("txtLaporanKerusakan")) {
        document.getElementById("txtLaporanKerusakan").innerText =
          res.cards.laporan_kerusakan;
      }
      if (document.getElementById("txtBelumDiperbaiki")) {
        document.getElementById("txtBelumDiperbaiki").innerText =
          res.cards.belum_diperbaiki;
      }
      if (document.getElementById("txtSelesaiDiperbaiki")) {
        document.getElementById("txtSelesaiDiperbaiki").innerText =
          res.cards.selesai_diperbaiki;
      }
      if (document.getElementById("txtPeminjaman")) {
        document.getElementById("txtPeminjaman").innerText =
          res.cards.peminjaman;
      }
      if (document.getElementById("txtBarangRusak")) {
        document.getElementById("txtBarangRusak").innerText =
          res.cards.barang_rusak;
      }

      // B. UPDATE GRAFIK JURUSAN
      const ctxJurusan = document.getElementById("webChartJurusan");
      if (ctxJurusan) {
        if (chartJurusan) chartJurusan.destroy();
        chartJurusan = new Chart(ctxJurusan.getContext("2d"), {
          type: "bar",
          data: {
            labels: res.charts.jurusan.labels, // Data Asli dari Database
            datasets: [
              {
                label: "Jumlah Laporan",
                data: res.charts.jurusan.data, // Data Asli dari Database
                backgroundColor: "#0d6efd",
                borderRadius: 4,
              },
            ],
          },
          options: {
            indexAxis: "y",
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
          },
        });
      }

      // C. UPDATE GRAFIK TINGKAT KERUSAKAN
      const ctxSeverity = document.getElementById("webChartSeverity");
      if (ctxSeverity) {
        if (chartSeverity) chartSeverity.destroy();
        chartSeverity = new Chart(ctxSeverity.getContext("2d"), {
          type: "doughnut",
          data: {
            labels: res.charts.severity.labels,
            datasets: [
              {
                data: res.charts.severity.data,
                backgroundColor: ["#198754", "#ffc107", "#dc3545"],
              },
            ],
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: "bottom" } },
          },
        });
      }

      // D. UPDATE GRAFIK ALAT
      const ctxAlat = document.getElementById("webChartAlat");
      if (ctxAlat) {
        if (chartAlat) chartAlat.destroy();
        chartAlat = new Chart(ctxAlat.getContext("2d"), {
          type: "bar",
          data: {
            labels: res.charts.alat.labels,
            datasets: [
              {
                label: "Total Rusak",
                data: res.charts.alat.data,
                backgroundColor: "#fd7e14",
                borderRadius: 4,
              },
            ],
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
          },
        });
      }

      // E. UPDATE GRAFIK STATUS PERBAIKAN
      const ctxStatus = document.getElementById("webChartStatus");
      if (ctxStatus) {
        if (chartStatus) chartStatus.destroy();
        chartStatus = new Chart(ctxStatus.getContext("2d"), {
          type: "pie",
          data: {
            labels: res.charts.status.labels,
            datasets: [
              {
                data: res.charts.status.data,
                backgroundColor: ["#198754", "#0dcaf0", "#6c757d", "#dc3545"],
              },
            ],
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: "bottom" } },
          },
        });
      }
    })
    .catch((error) => console.error("Gagal mengambil data statistik:", error));
}

window.addEventListener("DOMContentLoaded", () => {
  // 1. Eksekusi pertama kali saat web dibuka
  loadDashboardData();

  // 2. JADIKAN REALTIME: Auto-refresh data setiap 10 detik (10000 milidetik)
  setInterval(loadDashboardData, 10000);
});

// ======================================================
// 3. LOGIKA TOGGLE STATUS ONLINE / OFFLINE
// ======================================================
window.setOnlineStatus = function (checkbox) {
  const label = document.getElementById("labelStatus");
  const isOnline = checkbox.checked ? 1 : 0;

  // 1. Ubah tampilan UI (Warna dan Teks) secara instan biar responsif
  if (isOnline === 1) {
    label.innerText = "ONLINE";
    label.classList.remove("text-muted");
    label.classList.add("text-success");
  } else {
    label.innerText = "OFFLINE";
    label.classList.remove("text-success");
    label.classList.add("text-muted");
  }

  // 2. Kunci sementara toggle-nya agar tidak di-spam klik
  checkbox.disabled = true;

  // 3. Siapkan data untuk dikirim ke Server (PHP)
  const formData = new FormData();
  formData.append("is_online", isOnline);

  // 4. Kirim data via AJAX (Fetch)
  fetch(BASE_URL + "teknisi/update_status_online", {
    method: "POST",
    body: formData,
    headers: { "X-Requested-With": "XMLHttpRequest" },
  })
    .then((response) => response.json())
    .then((data) => {
      checkbox.disabled = false; // Buka kunci toggle

      if (data.status !== "success") {
        // Jika gagal simpan di database, kembalikan ke posisi semula
        Swal.fire(
          "Gagal",
          "Tidak dapat mengubah status: " + data.message,
          "error"
        );
        checkbox.checked = !checkbox.checked;
        setOnlineStatus(checkbox); // Panggil fungsi ini lagi untuk membalikkan teks
      } else {
        // (Opsional) Munculkan notif kecil sukses di pojok
        const Toast = Swal.mixin({
          toast: true,
          position: "bottom-end",
          showConfirmButton: false,
          timer: 2000,
        });
        Toast.fire({
          icon: "success",
          title: isOnline ? "Anda sekarang ONLINE" : "Anda sedang OFFLINE",
        });
      }
    })
    .catch((error) => {
      checkbox.disabled = false;
      console.error("Error Status:", error);
      checkbox.checked = !checkbox.checked; // Kembalikan posisi jika error jaringan
      Swal.fire("Error", "Terjadi kesalahan jaringan.", "error");
    });
};
