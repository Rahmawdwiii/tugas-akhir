// --- VARIABEL GLOBAL ---
let allRows = [];
let currentRows = [];
let currentPage = 1;
const rowsPerPage = 10;
let tableBody, paginationContainer, tableInfo;
let antrianData = [];
let activeTaskId = null;

document.addEventListener("DOMContentLoaded", () => {
  // 1. Inisialisasi Modal
  const modalEl = document.getElementById("modal_unit");
  if (modalEl) new bootstrap.Modal(modalEl);

  // 2. Setup Elemen Tabel & Pagination
  tableBody = document.getElementById("tableBody");
  paginationContainer = document.querySelector("#table_paginate ul.pagination");
  tableInfo = document.getElementById("table_info");

  if (tableBody) {
    allRows = Array.from(
      tableBody.querySelectorAll(":scope > tr:not(.empty-row)")
    );
    currentRows = [...allRows];
    renderTable();
  }

  // 3. Setup Sidebar
  setupSidebar();

  // 4. Jalankan Realtime Data
  updateLiveCounts();
  setInterval(updateLiveCounts, 3000);
});

// --- LOGIKA FILTER ---
window.applyFilter = function () {
  let nama = document.getElementById("filterNamaAlat").value.toLowerCase();
  let tanggal = document.getElementById("filterTanggal").value;
  let status = document.getElementById("filterStatus").value;
  let pelaksana = document
    .getElementById("filterPelaksana")
    .value.toLowerCase();

  currentRows = allRows.filter((row) => {
    let tds = row.getElementsByTagName("td");
    let matchNama = tds[2].textContent.toLowerCase().includes(nama);
    let matchTanggal = tanggal === "" || tds[1].textContent === tanggal;
    let matchStatus = status === "" || tds[5].textContent === status;
    let matchPelaksana =
      pelaksana === "" || tds[6].textContent.toLowerCase().includes(pelaksana);

    return matchNama && matchTanggal && matchStatus && matchPelaksana;
  });

  currentPage = 1;
  renderTable();
};

window.resetFilter = function () {
  document.getElementById("filterNamaAlat").value = "";
  document.getElementById("filterTanggal").value = "";
  document.getElementById("filterStatus").value = "";
  document.getElementById("filterPelaksana").value = "";

  currentRows = [...allRows];
  currentPage = 1;
  renderTable();
};

// --- LOGIKA RENDER & PAGINATION ---
function renderTable() {
  if (!tableBody) return;

  const totalRows = currentRows.length;
  const totalPages = Math.ceil(totalRows / rowsPerPage);

  if (currentPage < 1) currentPage = 1;
  if (currentPage > totalPages && totalPages > 0) currentPage = totalPages;

  allRows.forEach((row) => (row.style.display = "none"));

  const start = (currentPage - 1) * rowsPerPage;
  const end = start + rowsPerPage;
  const visibleRows = currentRows.slice(start, end);

  visibleRows.forEach((row) => (row.style.display = ""));

  if (tableInfo) {
    const startInfo = totalRows === 0 ? 0 : start + 1;
    const endInfo = Math.min(end, totalRows);
    tableInfo.innerText = `Showing ${startInfo} to ${endInfo} of ${totalRows} entries`;
  }

  updatePaginationUI(totalPages);
}

function updatePaginationUI(totalPages) {
  if (!paginationContainer) return;
  paginationContainer.innerHTML = "";

  if (totalPages <= 1 && currentRows.length > 0) return;

  const prevDisabled = currentPage === 1 ? "disabled" : "";
  paginationContainer.innerHTML += `
    <li class="page-item ${prevDisabled}">
        <a class="page-link" href="javascript:void(0)" onclick="goToPage(${currentPage - 1
    })">Previous</a>
    </li>`;

  for (let i = 1; i <= totalPages; i++) {
    const activeClass = i === currentPage ? "active" : "";
    paginationContainer.innerHTML += `
        <li class="page-item ${activeClass}">
            <a class="page-link" href="javascript:void(0)" onclick="goToPage(${i})">${i}</a>
        </li>`;
  }

  const nextDisabled =
    currentPage === totalPages || totalPages === 0 ? "disabled" : "";
  paginationContainer.innerHTML += `
    <li class="page-item ${nextDisabled}">
        <a class="page-link" href="javascript:void(0)" onclick="goToPage(${currentPage + 1
    })">Next</a>
    </li>`;
}

window.goToPage = function (page) {
  currentPage = page;
  renderTable();
};

// --- FUNGSI GENERATOR KARTU ANTRIAN ---
function generateNewAntrianCard(laporan) {
  const keru_level = laporan.kerusakan;
  const keru_color = "primary";
  const card_class = "antrian-card-riwayat-validasi";
  const target_bg = "bg-primary text-white";

  const tglAda =
    laporan.tgl_perbaikan &&
    laporan.tgl_perbaikan !== "-" &&
    laporan.tgl_perbaikan !== "0000-00-00";
  let btnActionHTML = "";

  if (tglAda) {
    btnActionHTML = `
            <button class="btn btn-${keru_color} w-100 fw-bold py-2 rounded-3 shadow-sm" onclick="openKerjakanModal('${laporan.id}')">
               Kerjakan
            </button>`;
  } else {
    btnActionHTML = `
            <button class="btn btn-secondary w-100 fw-bold py-2 rounded-3 shadow-sm" disabled title="Menunggu jadwal dari Admin">
               <i class="fas fa-clock me-1"></i> Belum Dijadwalkan
            </button>`;
  }

  return `
        <div class="col-md-6 col-lg-4">
            <div class="antrian-task-card ${card_class} d-flex flex-column">
                <div class="antrian-card-body" onclick="openDetailModal('${laporan.id
    }')">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="badge bg-${keru_color} px-3 py-2 rounded-pill">
                            ⚡ ${keru_level}
                        </span>
                        <small class="text-muted fw-bold">No: ${laporan.id
    }</small>
                    </div>
                    <div class="schedule-strip ${tglAda
      ? target_bg
      : "bg-danger-subtle text-danger border-danger"
    }" style="border:1px dashed;">
                        <i class="fas ${tglAda ? "fa-calendar-check" : "fa-calendar-times"
    } me-2"></i> 
                        ${tglAda
      ? "Jadwal: " + formatDate(laporan.tgl_perbaikan)
      : "Belum Dijadwalkan"
    }
                    </div>
                    <h5 class="fw-bold mb-2 text-dark">${laporan.alat}</h5>
                    <div class="badge-lokasi mb-3">
                        <i class="fas fa-building text-secondary"></i> ${laporan.gedung
    }
                    </div>
                    <p class="text-muted small mb-0 bg-light p-3 rounded border border-light">
                        "${laporan.keluhan_lengkap.substring(0, 30)}..."
                    </p>
                </div>
                <div class="card-footer-action">
                    ${btnActionHTML} 
                </div>
            </div>
        </div>
    `;
}

function generateProsesAntrianCard(laporan) {
  let badgeHtml = "";
  let cardClass = "";
  let actionHtml = "";

  if (laporan.revisi) {
    cardClass = "antrian-card-komplain";
    badgeHtml = `<span class="badge bg-danger px-2 py-2 rounded-pill"><i class="fas fa-exclamation-triangle me-1"></i> Masih Rusak</span>`;
    actionHtml = `
            <div class="alert bg-komplain-subtle text-danger small p-2 rounded mb-3">
                <strong class="d-block mb-1"><i class="fas fa-comment-dots"></i> Pesan Pelapor:</strong> "${laporan.pesan_pelapor}"
            </div>
            <button class="btn btn-danger w-100 fw-bold py-2 rounded-3 shadow-sm" onclick="Swal.fire({icon:'info',title:'Info',text:'Status diubah kembali menjadi: Sedang Dikerjakan'})">
                <i class="fas fa-tools me-2"></i> PERBAIKI LAGI
            </button>`;
  } else {
    const keru_level = laporan.kerusakan;
    const keru_color =
      keru_level === "Berat"
        ? "danger"
        : keru_level === "Sedang"
          ? "warning"
          : "success";
    cardClass =
      keru_level === "Berat"
        ? "antrian-card-berat"
        : keru_level === "Sedang"
          ? "antrian-card-sedang"
          : "antrian-card-ringan";
    badgeHtml = `<span class="badge bg-${keru_color} px-3 py-2 rounded-pill">${laporan.proses_status}</span>`;
    actionHtml = `
            <div class="d-grid gap-2 mt-4">
                <div class="d-flex gap-2">
                    <button class="btn btn-warning flex-fill fw-bold py-2" onclick="klikTombolPending('${laporan.id}')">Pending</button>
                    <button class="btn btn-success flex-fill fw-bold py-2" onclick="klikTombolSelesai('${laporan.id}')">Selesai</button>
                </div>
                <button class="btn btn-outline-dark fw-bold py-2 mt-2" onclick="vonisRusakTotal('${laporan.id}')"><i class="fas fa-ban me-2"></i> Tidak Bisa Diperbaiki</button>
            </div>`;
  }

  return `
        <div class="col-md-6 col-lg-4">
            <div class="antrian-task-card ${cardClass} d-flex flex-column">
                <div class="antrian-card-body" onclick="openDetailModal('${laporan.id
    }')">
                    <div class="d-flex justify-content-between mb-3">
                        ${badgeHtml}
                        <small class="fw-bold text-dark">${laporan.tgl_laporan
    }</small>
                    </div>
                     <div class="schedule-strip bg-primary-subtle text-primary" style="border:1px dashed #0d6efd;">
                        <i class="fas fa-calendar-check me-2"></i> Tanggal Perbaikan: ${formatDate(
      laporan.tgl_perbaikan
    )}
                    </div>
                    <h5 class="fw-bold mb-1 text-dark">${laporan.alat}</h5>
                    <p class="text-muted small">${laporan.gedung}, ${laporan.lokasi
    }</p>
                    ${laporan.revisi
      ? ""
      : `<p class="text-muted small mb-0 bg-light p-3 rounded border border-light">"${laporan.keluhan_lengkap || laporan.keluhan || ""
      }"</p>`
    }
                </div>
                <div class="card-footer-action">
                    ${actionHtml}
                </div>
            </div>
        </div>
    `;
}

function generatePendingAntrianCard(laporan) {
  return `
        <div class="col-md-6 col-lg-4">
            <div class="antrian-task-card antrian-card-pending-detail d-flex flex-column">
                <div class="antrian-card-body" onclick="openDetailModal('${laporan.id
    }')">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill"><i class="fas fa-pause me-1"></i> Pending</span>
                        <small class="text-muted">${laporan.tgl_laporan}</small>
                    </div>
                    <div class="schedule-strip bg-primary-subtle text-primary" style="border:1px dashed #0d6efd;">
                        <i class="fas fa-calendar-check me-2"></i> Tanggal Perbaikan: ${formatDate(
      laporan.tgl_perbaikan
    )}
                    </div>
                    <h5 class="fw-bold">${laporan.alat}</h5>
                    <small class="text-secondary d-block mb-3"><i class="fas fa-building me-1"></i> ${laporan.gedung
    }</small>
                    <div class="alert alert-warning border-0 small mb-0">
                        <strong>Alasan:</strong> "${laporan.alasan_pending}"
                    </div>
                </div>
                <div class="card-footer-action">
                    <button class="btn btn-primary w-100 fw-bold py-2 rounded-3" onclick="lanjutkanKerja('${laporan.id
    }')">
                        <i class="fas fa-play me-2"></i> LANJUTKAN KERJA
                    </button>
                </div>
            </div>
        </div>
    `;
}

function generateRiwayatAntrianCard(laporan) {
  let cardClass,
    badgeText,
    badgeClass,
    footerText,
    footerIcon,
    footerBg,
    statusColor,
    footerSmallText;

  if (laporan.admin_status === "Valid") {
    cardClass = "antrian-card-riwayat-arsip opacity-75";
    badgeText = "Laporan Selesai";
    badgeClass = "bg-secondary";
    footerText = "Laporan Ditutup";
    footerIcon = '<i class="fas fa-check-double text-success"></i>';
    footerBg = "bg-arsip-footer";
    statusColor = "text-arsip";
    footerSmallText = "Validasi: Admin UPT";
  } else if (laporan.rating) {
    cardClass = "antrian-card-riwayat-validasi";
    badgeText = "Rating Diterima";
    badgeClass = "bg-primary";
    footerText = "Menunggu Validasi";
    footerIcon = '<i class="fas fa-user-shield text-primary-validate"></i>';
    footerBg = "bg-primary-subtle";
    statusColor = "text-primary-validate";
    footerSmallText = "Admin sedang mengecek";
  } else {
    cardClass = "antrian-card-riwayat-konfirm";
    const statusCek = (laporan.kerusakan || "").toUpperCase();

    if (
      statusCek === "BERAT" ||
      statusCek === "RUSAK" ||
      statusCek === "RUSAK BERAT"
    ) {
      badgeText = "Rusak Berat";
      badgeClass = "bg-danger";
    } else {
      badgeText = "Selesai Perbaikan";
      badgeClass = "bg-success";
    }

    footerText = "Menunggu Konfirmasi";
    footerIcon = '<i class="fas fa-user-clock text-konfirm"></i>';
    footerBg = "bg-konfirm-footer";
    statusColor = "text-konfirm";
    footerSmallText = "Pelapor belum memberi rating";
  }

  const ulasanSingkat = laporan.ulasan
    ? laporan.ulasan.substring(0, 35) + "..."
    : "Pelapor belum memberi rating.";
  const ratingDisplay = laporan.rating
    ? `<div class="text-warning"><i class="fas fa-star"></i> ${laporan.rating}.0</div>`
    : '<small class="text-muted">N/A</small>';

  return `
        <div class="col-md-6 col-lg-4">
            <div class="antrian-task-card ${cardClass} d-flex flex-column">
                <div class="antrian-card-body" onclick="openDetailModal('${laporan.id
    }')">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="badge ${badgeClass} px-3 py-2 rounded-pill">${badgeText}</span>
                        <small class="text-muted">${laporan.tgl_laporan}</small>
                    </div>
                    <div class="schedule-strip bg-primary-subtle text-primary" style="border:1px dashed #0d6efd;">
                        <i class="fas fa-calendar-check me-2"></i> Selesai: ${formatDate(
      laporan.tgl_perbaikan
    )}
                    </div>
                    <h5 class="fw-bold mb-1">${laporan.alat}</h5>
                    <small class="text-secondary d-block mb-2"><i class="fas fa-building me-1"></i> ${laporan.gedung
    }</small>
                    <p class="text-muted small mb-0 bg-light p-3 rounded border border-light">
                        "${ulasanSingkat}"
                    </p>
                </div>
                <div class="card-footer-riwayat d-flex align-items-center ${footerBg}">
                    ${footerIcon}
                    <div class="lh-1 flex-grow-1 ms-2">
                        <div class="fw-bold ${statusColor}">${footerText}</div>
                        <small class="fw-normal opacity-75">${footerSmallText}</small>
                    </div>
                    ${ratingDisplay}
                </div>
            </div>
        </div>
    `;
}

function formatDate(dateString) {
  if (!dateString || dateString === "-") return "-";
  const date = new window.Date(dateString);
  if (isNaN(date.getTime())) return "-";
  return date.toLocaleDateString("id-ID", {
    year: "numeric",
    month: "long",
    day: "numeric",
  });
}

// === FUNGSI LOAD DATA DARI SERVER (AJAX) ===
window.loadAntrian = async function (status) {
  const container = document.getElementById("antrian_dynamic_content");
  const header = document.getElementById("antrian_header");

  container.innerHTML = `<div class="text-center p-5 col-12"><i class="fas fa-spinner fa-spin me-2"></i> Memuat Data Server...</div>`;

  try {
    const response = await fetch(`${BASE_URL}teknisi/get_tugas_json/${status}`);
    if (!response.ok) throw new Error("Gagal mengambil data");

    antrianData = await response.json();

    let htmlContent = "";
    let judul = "";

    if (status === "baru") {
      judul = "Daftar Tugas Baru";
      antrianData.forEach(
        (lpr) => (htmlContent += generateNewAntrianCard(lpr))
      );
    } else if (status === "proses") {
      judul = "Daftar Tugas Sedang Dikerjakan";
      antrianData.forEach(
        (lpr) => (htmlContent += generateProsesAntrianCard(lpr))
      );
    } else if (status === "pending") {
      judul = "Daftar Tugas Pending";
      antrianData.forEach(
        (lpr) => (htmlContent += generatePendingAntrianCard(lpr))
      );
    } else if (status === "riwayat") {
      judul = "Riwayat Tugas Selesai";
      antrianData.forEach(
        (lpr) => (htmlContent += generateRiwayatAntrianCard(lpr))
      );
    }

    header.innerText = `${judul} (${antrianData.length} Laporan)`;
    const badgeCount = document.getElementById(`count_${status}`);
    if (badgeCount) badgeCount.innerText = antrianData.length;

    if (antrianData.length === 0) {
      container.innerHTML = `<div class="alert alert-info mt-3 col-12 text-center">Tidak ada tugas pada status ${status.toUpperCase()} saat ini.</div>`;
    } else {
      container.innerHTML = htmlContent;
    }
  } catch (error) {
    console.error(error);
    container.innerHTML = `<div class="alert alert-danger col-12">Gagal memuat data: ${error.message}</div>`;
  }
};

// === FUNGSI LOGIKA AKSI TEKNISI ===
window.openAmbilTugasModal = function (id) {
  activeTaskId = id;
  document.getElementById("input_kerusakan").value = "";
  new bootstrap.Modal(document.getElementById("modalInputKerusakan")).show();
};

window.simpanTugas = function () {
  const kerusakan = document.getElementById("input_kerusakan").value;

  if (!kerusakan) {
    Swal.fire(
      "Peringatan",
      "Pilih kategori kerusakan terlebih dahulu.",
      "warning"
    );
    return;
  }

  Swal.fire({
    title: "Memproses...",
    didOpen: () => Swal.showLoading(),
  });

  const formData = new window.FormData();
  formData.append("nomor_laporan", activeTaskId);
  formData.append("status_kerusakan", kerusakan);

  fetch(`${BASE_URL}teknisi/jadwal/mulai_kerja`, {
    method: "POST",
    body: formData,
  })
    .then((res) => res.json())
    .then((res) => {
      if (res.status === "success") {
        Swal.fire({
          icon: "success",
          title: "Berhasil",
          text: "Tugas mulai dikerjakan",
          timer: 1500,
          showConfirmButton: false,
        }).then(() => {
          bootstrap.Modal.getInstance(
            document.getElementById("modalInputKerusakan")
          ).hide();
          loadAntrian("proses");
        });
      } else {
        Swal.fire("Gagal", res.message || "Gagal memulai tugas", "error");
      }
    })
    .catch(() => {
      Swal.fire("Error", "Kesalahan sistem", "error");
    });
};

window.openKerjakanModal = function (id) {
  const data = antrianData.find((item) => item.id == id);
  if (!data) return;

  if (
    !data.tgl_perbaikan ||
    data.tgl_perbaikan === "-" ||
    data.tgl_perbaikan === "0000-00-00"
  ) {
    Swal.fire({
      icon: "error",
      title: "Akses Ditolak",
      text: "Laporan ini belum dijadwalkan oleh Admin. Anda belum bisa memulainya.",
      confirmButtonColor: "#dc3545",
    });
    return;
  }

  if (
    !data.kerusakan ||
    data.kerusakan === "Belum Dicek" ||
    data.kerusakan === "-"
  ) {
    Swal.fire({
      icon: "warning",
      title: "Tidak Bisa Dikerjakan",
      html: `Status kerusakan alat ini <b>belum ditentukan</b>.<br><br>
                   Silakan cek <b class="text-warning">detail</b> laporan, lakukan pengecekan, lalu update status kerusakan (Ringan/Sedang/Berat) terlebih dahulu.`,
      confirmButtonColor: "#ffc107",
      confirmButtonText: "Oke, Saya Cek Dulu",
    });
    return;
  }

  Swal.fire({
    title: "Mulai Perbaikan?",
    text: `Kerusakan teridentifikasi: ${data.kerusakan}. Pekerjaan akan dimulai.`,
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#0d6efd",
    cancelButtonColor: "#6c757d",
    confirmButtonText: "Ya, Mulai Kerjakan",
    cancelButtonText: "Batal",
  }).then((result) => {
    if (result.isConfirmed) {
      prosesMulaiKerja(id);
    }
  });
};

function prosesMulaiKerja(id) {
  Swal.fire({
    title: "Memproses...",
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading(),
  });

  fetch(`${BASE_URL}teknisi/mulai-kerja/${id}`, { method: "POST" })
    .then((res) => res.json())
    .then((res) => {
      if (res.status === "success") {
        Swal.fire({
          icon: "success",
          title: "Berhasil!",
          text: "Status diubah menjadi Dikerjakan.",
          timer: 1500,
          showConfirmButton: false,
        }).then(() => {
          loadAntrian("proses");
        });
      } else {
        Swal.fire(
          "Gagal",
          res.message || "Tidak bisa memulai pekerjaan",
          "error"
        );
      }
    })
    .catch((err) => {
      console.error(err);
      Swal.fire("Error", "Terjadi kesalahan sistem.", "error");
    });
}

window.openDetailModal = function (id) {
  const data = antrianData.find((item) => item.id === id);
  if (!data) {
    Swal.fire({
      icon: "error",
      title: "Data tidak ditemukan",
      text: "Data laporan tidak ditemukan.",
    });
    return;
  }

  document.getElementById("detail_modal_lpr_id").innerText = data.id;
  document.getElementById("detail_modal_tgl").innerText = formatDate(
    data.tgl_laporan
  );
  document.getElementById("detail_modal_tgl_perbaikan").innerText = formatDate(
    data.tgl_perbaikan
  );
  document.getElementById("detail_modal_inv").innerText = data.inv_no || "-";
  document.getElementById("detail_modal_inv_display").innerText =
    data.inv_no || "-";
  document.getElementById("detail_modal_alat_display").innerText = data.alat;
  document.getElementById("detail_modal_lokasi").innerText = `${data.gedung}, ${data.lokasi || ""
    }`;
  document.getElementById("detail_modal_unit").innerText = data.gedung || "-";
  document.getElementById("detail_modal_pelapor").innerText = data.pelapor;
  document.getElementById("detail_modal_teknisi").innerText =
    data.teknisi_pelaksana || "Belum Ditugaskan";
  document.getElementById("detail_modal_keluhan").innerText =
    data.keluhan_lengkap || "-";

  let rawStatus = data.kerusakan || "Belum Dicek";
  let statusFix = rawStatus;

  if (rawStatus !== "Belum Dicek" && rawStatus !== "-") {
    statusFix =
      rawStatus.charAt(0).toUpperCase() + rawStatus.slice(1).toLowerCase();
  }

  const elBadge = document.getElementById("detail_modal_kerusakan_display");
  elBadge.innerText = statusFix;

  let warna = "bg-secondary";
  if (statusFix === "Berat") warna = "bg-danger";
  else if (statusFix === "Rusak") warna = "bg-dark";
  else if (statusFix === "Sedang") warna = "bg-warning text-dark";
  else if (statusFix === "Ringan") warna = "bg-success";
  elBadge.className = `badge fs-6 ${warna}`;

  const fotoContainer = document.getElementById("detail_modal_foto_container");
  const noFotoElement = document.getElementById("detail_modal_no_foto");
  fotoContainer.innerHTML = "";

  if (data.foto_urls && data.foto_urls.length > 0) {
    noFotoElement.style.display = "none";
    fotoContainer.style.display = "flex";

    data.foto_urls.forEach((url) => {
      const colDiv = document.createElement("div");
      colDiv.className = "col-6";

      const img = document.createElement("img");
      img.src = url;
      img.className = "img-fluid rounded shadow-sm border";
      img.style.width = "100%";
      img.style.height = "150px";
      img.style.objectFit = "cover";
      img.style.cursor = "zoom-in";

      img.onclick = function () {
        const zoomImg = document.getElementById("fotoPreviewZoom");
        zoomImg.src = this.src;
        new bootstrap.Modal(document.getElementById("modalFotoPreview")).show();
      };

      colDiv.appendChild(img);
      fotoContainer.appendChild(colDiv);
    });
  } else {
    fotoContainer.style.display = "none";
    noFotoElement.style.display = "block";
  }

  const containerLink = document.getElementById("container_link_pendukung");
  const btnLink = document.getElementById("btn_link_pendukung");

  if (data.link_pendukung && data.link_pendukung.trim() !== "") {
    containerLink.style.display = "block";
    let urlLink = data.link_pendukung;
    if (!urlLink.match(/^https?:\/\//i)) {
      urlLink = "http://" + urlLink;
    }
    btnLink.href = urlLink;
  } else {
    containerLink.style.display = "none";
    btnLink.href = "#";
  }

  const dropdown = document.getElementById("input_kerusakan");
  const btnKirim = dropdown.nextElementSibling;

  if (statusFix !== "Belum Dicek" && statusFix !== "-") {
    dropdown.value = statusFix;
  } else {
    dropdown.value = "";
  }

  dropdown.disabled = false;
  dropdown.classList.remove("bg-light");

  if (btnKirim) {
    btnKirim.disabled = false;
    if (dropdown.value !== "") {
      btnKirim.innerHTML = '<i class="fas fa-edit"></i> Ubah';
      btnKirim.classList.add("btn-warning");
      btnKirim.classList.remove("btn-secondary", "btn-primary");
    } else {
      btnKirim.innerHTML = '<i class="fas fa-paper-plane"></i> Kirim';
      btnKirim.classList.add("btn-primary");
      btnKirim.classList.remove("btn-secondary", "btn-warning");
    }
  }

  const diagnosaSection = document.getElementById("container_alasan_rusak");
  const textAlasan = document.getElementById("detail_modal_alasan_rusak");

  if (data.hasil_perbaikan === "RUSAK TOTAL") {

    diagnosaSection.style.display = "block";
    textAlasan.innerText = data.diagnosa_rusak || "-";

  } else {

    diagnosaSection.style.display = "none";
    textAlasan.innerText = "-";

  }

  // ===============================
  // ULASAN & RATING PELAPOR
  // ===============================

  const ulasanElement = document.getElementById("detail_modal_ulasan");

  if (ulasanElement) {
    ulasanElement.innerText =
      data.ulasan || "Pelapor belum memberikan ulasan.";
  }

  const ratingContainer =
    document.getElementById("detail_modal_rating");

  if (ratingContainer) {

    const rating = Number(data.rating || 0);

    let html = "";

    for (let i = 1; i <= 5; i++) {

      if (i <= rating) {
        html += '<i class="fas fa-star text-warning"></i>';
      } else {
        html += '<i class="far fa-star text-warning"></i>';
      }

    }

    html += `
        <span class="ms-2 text-dark fw-semibold">
            ${rating > 0 ? rating + " / 5" : "Belum ada rating"}
        </span>
    `;

    ratingContainer.innerHTML = html;
  }

  new bootstrap.Modal(document.getElementById("modalDetailLaporan")).show();
};

window.klikTombolPending = function (id) {
  activeTaskId = id;
  document.getElementById("inputAlasanPending").value = "";
  new bootstrap.Modal(document.getElementById("modalPending")).show();
};

window.klikTombolSelesai = function (id) {
  activeTaskId = id;
  document.getElementById("inputFileSelesai").value = "";
  new bootstrap.Modal(document.getElementById("modalSelesai")).show();
};

window.submitPending = function () {
  if (!activeTaskId) {
    Swal.fire("Error", "ID laporan tidak ditemukan (frontend)", "error");
    return;
  }

  const alasan = document.getElementById("inputAlasanPending").value.trim();
  if (!alasan) {
    Swal.fire("Peringatan", "Alasan wajib diisi", "warning");
    return;
  }

  const formData = new window.FormData();
  formData.append("nomor_laporan", activeTaskId);
  formData.append("alasan", alasan);

  fetch(`${BASE_URL}teknisi/pending`, {
    method: "POST",
    body: formData,
  })
    .then((res) => res.json())
    .then((res) => {
      if (res.status === "success") {
        Swal.fire({
          icon: "success",
          title: "Berhasil",
          text: "Tugas berhasil dipending",
          timer: 1000,
          showConfirmButton: false,
        }).then(() => {
          bootstrap.Modal.getInstance(
            document.getElementById("modalPending")
          ).hide();
          loadAntrian("pending");
          if (typeof setActiveCard === "function") setActiveCard("pending");
        });
      } else {
        Swal.fire("Gagal", res.message, "error");
      }
    });
};

window.submitSelesai = function () {
  const fileInput = document.getElementById("inputFileSelesai");
  const uraian = document.getElementById("inputUraianPekerjaa").value;

  if (!fileInput.files.length) {
    Swal.fire("Peringatan", "Upload foto bukti perbaikan!", "warning");
    return;
  }

  Swal.fire({ title: "Menyimpan...", didOpen: () => Swal.showLoading() });

  const formData = new window.FormData();
  formData.append("nomor_laporan", activeTaskId);
  for (let i = 0; i < fileInput.files.length; i++) {
    formData.append("foto[]", fileInput.files[i]);
  }
  formData.append("uraian", uraian);

  fetch(`${BASE_URL}teknisi/jadwal/selesai`, {
    method: "POST",
    body: formData,
  })
    .then((res) => res.json())
    .then((res) => {
      if (res.status === "success") {
        Swal.fire({
          icon: "success",
          title: "Berhasil",
          text: "Tugas selesai. Menunggu konfirmasi pelapor.",
          timer: 1500,
          showConfirmButton: false,
        }).then(() => {
          bootstrap.Modal.getInstance(
            document.getElementById("modalSelesai")
          ).hide();
          loadAntrian("riwayat");
        });
      } else {
        Swal.fire("Gagal", res.message || "Gagal menyimpan", "error");
      }
    })
    .catch(() => {
      Swal.fire("Error", "Terjadi kesalahan sistem", "error");
    });
};

window.vonisRusakTotal = function (id) {
  Swal.fire({
    title: '<span class="fw-bold text-dark">Konfirmasi Rusak Total</span>',
    html: `
            <div class="text-start">
                <p class="text-secondary small mb-3">
                    Anda akan menyatakan alat ini <b>tidak dapat diperbaiki</b>. 
                    Tindakan ini akan memindahkan status ke arsip kerusakan.
                </p>
                <div class="form-floating">
                    <textarea class="form-control border-danger-subtle shadow-sm" 
                              placeholder="Jelaskan alasan kerusakan" 
                              id="alasan_rusak_input" 
                              style="height: 120px; resize: none;"></textarea>
                    <label for="alasan_rusak_input" class="text-muted">
                        <i class="fas fa-pencil-alt me-1"></i> Jelaskan diagnosa teknis...
                    </label>
                </div>
                <div class="form-text text-end text-muted me-1 mt-2" style="font-size: 11px;">
                  *Wajib diisi
                </div>
            </div>
        `,
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: '<i class="fas fa-paper-plane me-3"></i>Kirim',
    cancelButtonText: "Batal",
    confirmButtonColor: "#dc3545",
    cancelButtonColor: "#f8f9fa",
    customClass: {
      popup: "rounded-4 shadow-lg border-0",
      confirmButton: "btn btn-danger btn-lg px-4 rounded-3 shadow-sm",
      cancelButton: "btn btn-light btn-lg px-4 rounded-3 text-muted border",
      actions: "gap-2",
    },
    buttonsStyling: false,
    preConfirm: () => {
      const alasan = document.getElementById("alasan_rusak_input").value;
      if (!alasan) {
        Swal.showValidationMessage(
          "Mohon isi alasan kerusakan terlebih dahulu!"
        );
        document
          .getElementById("alasan_rusak_input")
          .classList.add("is-invalid");
        return false;
      }
      return alasan;
    },
  }).then((result) => {
    if (result.isConfirmed && result.value) {
      kirimVonisServer(id, result.value);
    }
  });
};

function kirimVonisServer(id, alasan) {
  Swal.fire({
    title: "Menyimpan...",
    text: "Sedang mengirim laporan kerusakan...",
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading(),
  });

  const formData = new window.FormData();
  formData.append("alasan_rusak", alasan);

  fetch(`${BASE_URL}teknisi/jadwal/rusak_total/${id}`, {
    method: "POST",
    body: formData,
  })
    .then((res) => res.json())
    .then((res) => {
      if (res.status === "success") {
        Swal.fire({
          icon: "success",
          title: "Laporan Terkirim",
          text: "Alat berhasil dilaporkan sebagai Rusak Total.",
          timer: 2000,
          showConfirmButton: false,
        }).then(() => {
          loadAntrian("riwayat");
        });
      } else {
        Swal.fire("Gagal", res.message || "Gagal memproses data.", "error");
      }
    })
    .catch((err) => {
      console.error(err);
      Swal.fire("Error", "Terjadi kesalahan sistem.", "error");
    });
}

window.updateStatusKerusakan = function () {
  const idLaporan = document.getElementById("detail_modal_lpr_id").innerText;
  const statusBaru = document.getElementById("input_kerusakan").value;

  if (!statusBaru || statusBaru === "") {
    Swal.fire({
      icon: "warning",
      title: "Peringatan",
      text: "Harap pilih status kerusakan terlebih dahulu!",
      confirmButtonColor: "#ffc107",
      confirmButtonText: "Oke",
    });
    return;
  }

  Swal.fire({
    title: "Apakah Anda yakin?",
    text: `Status akan diubah menjadi "${statusBaru}".`,
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#0d6efd",
    cancelButtonColor: "#6c757d",
    confirmButtonText: "Ya, Ubah Status",
    cancelButtonText: "Batal",
  }).then((result) => {
    if (result.isConfirmed) {
      Swal.fire({
        title: "Sedang memproses...",
        text: "Mohon tunggu sebentar.",
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        },
      });

      const formData = new window.FormData();
      formData.append("nomor_laporan", idLaporan);
      formData.append("status_kerusakan", statusBaru);

      fetch(`${BASE_URL}teknisi/update_status`, {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.status === "success") {
            Swal.fire({
              icon: "success",
              title: "Berhasil",
              text: "Status kerusakan berhasil disimpan",
              timer: 1500,
              showConfirmButton: false,
            }).then(() => {
              bootstrap.Modal.getInstance(
                document.getElementById("modalDetailLaporan")
              ).hide();
              if (typeof loadAntrian === "function") {
                loadAntrian("baru");
              } else {
                location.reload();
              }
            });
          } else {
            Swal.fire({
              icon: "error",
              title: "Gagal",
              text: "Gagal update: " + data.message,
            });
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          Swal.fire({
            icon: "error",
            title: "Terjadi Kesalahan",
            text: "Terjadi kesalahan sistem atau koneksi terputus.",
          });
        });
    }
  });
};

window.lanjutkanKerja = function (id) {
  Swal.fire({
    title: "Lanjutkan pekerjaan?",
    text: "Tugas akan dipindahkan ke sedang dikerjakan.",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Ya, Lanjutkan",
    cancelButtonText: "Batal",
  }).then((result) => {
    if (!result.isConfirmed) return;

    Swal.fire({ title: "Memproses...", didOpen: () => Swal.showLoading() });

    fetch(`${BASE_URL}teknisi/lanjutkan_kerja/${id}`)
      .then((res) => res.json())
      .then((res) => {
        if (res.status === "success") {
          Swal.fire({
            icon: "success",
            title: "Berhasil",
            text: "Tugas kembali dikerjakan",
            timer: 1200,
            showConfirmButton: false,
          }).then(() => {
            loadAntrian("proses");
            if (typeof setActiveCard === "function") setActiveCard("proses");
          });
        } else {
          Swal.fire("Gagal", res.message, "error");
        }
      });
  });
};

// --- FUNGSI SIDEBAR TOGGLE ---
function setupSidebar() {
  const toggleSidebar = document.getElementById("toggleSidebar");
  const sidebar = document.getElementById("sidebar");
  const content = document.querySelector(".content");
  const links = document.querySelectorAll("#sidebar .nav-link");
  const footerContent = document.querySelector(".footer-content");

  if (toggleSidebar) {
    toggleSidebar.addEventListener("click", () => {
      sidebar.classList.toggle("collapsed");
      content.classList.toggle("full");
      if (footerContent) {
        footerContent.classList.toggle("full");
      }
    });
  }

  links.forEach((link) => {
    link.addEventListener("click", function () {
      links.forEach((l) => l.classList.remove("active"));
      this.classList.add("active");
    });
  });
}

// --- LOGIKA REALTIME DASHBOARD ---
function updateLiveCounts() {
  fetch(`${BASE_URL}teknisi/get_count_dashboard`, {
    method: "GET",
    headers: {
      "X-Requested-With": "XMLHttpRequest",
      "Content-Type": "application/json",
    },
  })
    .then((response) => {
      if (!response.ok) throw new Error("Network response was not ok");
      return response.json();
    })
    .then((data) => {
      if (data.status === "error" || data.baru === undefined) return;

      updateTextNative("count_baru", data.baru);
      updateTextNative("count_proses", data.proses);
      updateTextNative("count_pending", data.pending);
      updateTextNative("count_riwayat", data.riwayat);
    })
    .catch((error) => { });
}

function updateTextNative(elementId, newValue) {
  const element = document.getElementById(elementId);
  if (element) {
    const currentHTML = element.innerHTML;
    const newString = String(newValue);

    if (currentHTML.includes("<i") || element.innerText !== newString) {
      element.innerText = newString;
      element.style.opacity = 0.5;
      setTimeout(() => (element.style.opacity = 1), 200);
    }
  }
}
