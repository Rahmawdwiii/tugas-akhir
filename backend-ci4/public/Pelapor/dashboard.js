// Ambil elemen footer content (bukan footer wrapper)
// Karena di footer.php Anda pakai class .footer-content untuk margin
const footerContent = document.querySelector(".footer-content");

let pelaporChartUnit, pelaporChartSeverity, pelaporChartAlat, pelaporChartStatus;

// =======================================================
// === EVENT DOM CONTENT LOADED (JALANKAN SEMUA DISINI) ===
// =======================================================
document.addEventListener("DOMContentLoaded", () => {
  // 1. LOGIKA FORM PELAPOR
  const form = document.getElementById("formLaporanPelapor");
  if (!form) {
    //console.error("Form laporan tidak ditemukan");
  } else {
    form.addEventListener("submit", function (e) {
      e.preventDefault();
      const formData = new FormData(form);

      fetch(`${BASE_URL}pelapor/submit`, {
        method: "POST",
        body: formData,
        credentials: "same-origin",
      })
        .then((res) => res.json())
        .then((res) => {
          if (res.status === "success") {
            Swal.fire({
              icon: "success",
              title: "Berhasil",
              text: "Laporan berhasil dikirim\nNo: " + res.nomor,
            });
            form.reset();
            if (typeof loadCardList === "function") loadCardList("all");
          } else {
            Swal.fire({
              icon: "error",
              title: "Gagal",
              text: res.message,
            });
          }
        })
        .catch((err) => {
          console.error(err);
          Swal.fire({
            icon: "error",
            title: "Error",
            text: "Terjadi kesalahan sistem",
          });
        });
    });
  }

  // 2. JALANKAN COUNTER
  if (typeof updateDashboardCounters === "function") {
    updateDashboardCounters();
    setInterval(updateDashboardCounters, 3000);
  }

  // 3. DAFTAR LAPORAN TIDAK DILOAD OTOMATIS
  // User harus klik salah satu card terlebih dahulu.

  // 4. LOAD DATA GRAFIK PELAPOR
  if (typeof loadPelaporDashboardCharts === "function") {
    loadPelaporDashboardCharts();
    setInterval(loadPelaporDashboardCharts, 10000);
  }

  // =======================================================
  // 5. LOGIKA SIDEBAR (PERBAIKAN FOOTER)
  // =======================================================
  const toggleSidebar = document.getElementById("toggleSidebar");
  const sidebar = document.getElementById("sidebar");
  const content = document.querySelector(".content");
  const links = document.querySelectorAll("#sidebar .nav-link");

  if (toggleSidebar) {
    toggleSidebar.addEventListener("click", () => {
      // Toggle class 'collapsed' pada sidebar
      sidebar.classList.toggle("collapsed");

      // Toggle class 'full' pada content utama
      content.classList.toggle("full");

      // Toggle class 'full' pada footer content
      // Ini akan otomatis mengubah margin-left dari 250px menjadi 0px via CSS
      if (footerContent) {
        footerContent.classList.toggle("full");
      }
    });
  }

  // Active link handler
  links.forEach((link) => {
    link.addEventListener("click", function () {
      links.forEach((l) => l.classList.remove("active"));
      this.classList.add("active");
    });
  });
}); // --- BATAS AKHIR DOM CONTENT LOADED ---

/* ===============================
   FUNGSI-FUNGSI GLOBAL
================================ */

function fmt(d) {
  if (!d) return "-";
  const x = new Date(d);
  if (isNaN(x)) return d;

  const tanggal = x.toLocaleDateString("id-ID", {
    day: "numeric",
    month: "long",
    year: "numeric"
  });

  const jam = x.toLocaleTimeString("id-ID", {
    hour: "2-digit",
    minute: "2-digit",
    hour12: false
  });

  return `${tanggal} ${jam}`;

}

function fmtDate(d) {

  if (!d) return "-";

  const x = new Date(d);

  if (isNaN(x)) return d;

  return x.toLocaleDateString("id-ID", {
    day: "numeric",
    month: "long",
    year: "numeric"
  });

}

function getStatusKerusakanBadge(status) {
  const statusText = status ? String(status).trim() : "Belum Dicek";
  const normalizedStatus = statusText.toLowerCase();
  let badgeColor = "bg-secondary text-white";
  if (normalizedStatus === "ringan") badgeColor = "bg-success text-white";
  else if (normalizedStatus === "sedang") badgeColor = "bg-warning text-dark";
  else if (normalizedStatus === "berat") badgeColor = "bg-danger text-white";
  else if (normalizedStatus === "rusak") badgeColor = "bg-dark text-white";
  return `<span class="badge ${badgeColor}">${statusText}</span>`;
}

function getCardStatusLabel(lpr) {

  const statusLaporan =
    String(lpr.status_laporan || "")
      .trim()
      .toUpperCase();

  const statusPerbaikan =
    String(lpr.status_perbaikan || "")
      .trim()
      .toUpperCase();

  const statusKerusakan =
    String(lpr.status_kerusakan || "")
      .trim()
      .toUpperCase();

  const hasilPerbaikan =
    String(lpr.hasil_perbaikan || "")
      .trim()
      .toUpperCase();

  if (statusPerbaikan === "PROSES")
    return "DIPROSES";

  if (statusPerbaikan === "PENDING")
    return "PENDING";

  // KHUSUS BARANG RUSAK TOTAL
  if (
    statusLaporan === "MENUNGGU KONFIRMASI" &&
    hasilPerbaikan === "RUSAK TOTAL"
  ) {
    return "RUSAK - MENUNGGU KONFIRMASI";
  }

  return statusLaporan || "UNKNOWN";
}

function getCardBadgeClass(status) {
  const normalizedStatus = String(status || '').trim().toUpperCase();
  if (normalizedStatus === 'BARU') return 'bg-primary';
  if (normalizedStatus === 'DIPROSES' || normalizedStatus === 'PENDING') return 'bg-warning text-dark';
  if (normalizedStatus === 'MENUNGGU KONFIRMASI') return 'bg-info text-dark';
  if (normalizedStatus === 'SELESAI') return 'bg-success';
  if (normalizedStatus === 'RUSAK - MENUNGGU KONFIRMASI') return 'bg-danger';
  if (normalizedStatus === 'DIJADWALKAN') return 'bg-warning text-dark';
  return 'bg-secondary text-white';
}

// =========================================================
// LOGIKA TIMELINE BARU (SANGAT PRESISI & BERURUTAN)
// =========================================================
// =========================================================
// LOGIKA TIMELINE BARU (DENGAN TIMESTAMP WAKTU)
// =========================================================
function getTimelineHtml(lpr) {
  let html = `<div class="tracking-timeline">`;
  const invalidStatus = ["", "-", "Belum Dicek", "null", null];
  const invalidDates = ["0000-00-00", "0000-00-00 00:00:00", null, ""];

  // 1. LAPORAN DIKIRIM (Selalu Tampil)
  html += `
    <div class="timeline-item completed">
      <div class="timeline-icon-box"><i class="fas fa-file-alt"></i></div>
      <div class="timeline-content">
        <h6 class="fw-bold mb-1">Laporan Terkirim</h6>
        <p class="small text-muted mb-0"><i class="far fa-clock me-1"></i> Terkirim: ${fmt(lpr.tanggal_laporan)}</p>
      </div>
    </div>`;

  // 2. STATUS KERUSAKAN DITENTUKAN (Tampil jika sudah dicek fisik)
  if (lpr.status_kerusakan && !invalidStatus.includes(lpr.status_kerusakan)) {
    html += `
      <div class="timeline-item completed">
        <div class="timeline-icon-box"><i class="fas fa-search-plus"></i></div>
        <div class="timeline-content">
          <h6 class="fw-bold mb-1">Status Kerusakan Ditentukan</h6>
          <div class="small text-muted mt-1">
            <i class="far fa-clock me-1"></i>
            Waktu Pemeriksaan:
            <span class="text-dark fw-bold">
                ${fmt(lpr.waktu_cek_kerusakan)}
            </span>

            <br>

            <i class="fas fa-tools me-1"></i>
            Kondisi Kerusakan:
            <span class="text-dark fw-bold">
                ${lpr.status_kerusakan ?? "-"}
            </span>

            <br>

            <i class="fas fa-user-cog me-1"></i>
            Oleh Teknisi:
            <span class="text-dark fw-bold">
                ${lpr.nama_teknisi ?? "-"}
            </span>
        </div>
        </div>
      </div>`;
  }

  // 3. PERBAIKAN DIJADWALKAN (Tampil begitu laporan sudah lewat status BARU)
  if (lpr.status_laporan !== "BARU") {
    html += `
      <div class="timeline-item completed">
        <div class="timeline-icon-box"><i class="fas fa-calendar-check"></i></div>
        <div class="timeline-content">
          <h6 class="fw-bold mb-1">Perbaikan Dijadwalkan</h6>
          <p class="small text-muted mb-1">
              <i class="far fa-clock me-1"></i>
              Jadwal Pengerjaan:
              <span class="text-primary fw-bold">
                 ${lpr.tgl_dijadwalkan ? fmtDate(lpr.tgl_dijadwalkan) : "Segera"}
              </span>
          </p>
          <div class="small mt-1">
            Teknisi Pelaksana: <strong>${lpr.nama_teknisi ?? "-"}</strong>
          </div>
        </div>
      </div>`;
  }

  // =======================================================
  // GERBANG PENJAGA (GATEKEEPER)
  // Stop di sini jika Teknisi belum klik Mulai Kerja.
  // =======================================================
  if (!lpr.status_perbaikan || lpr.status_perbaikan === "MENUNGGU") {
    html += `</div>`; // Tutup div
    return html;      // Keluar dari fungsi agar tidak merender progress bawahnya
  }

  // 4. PERBAIKAN DIMULAI (Tampil jika status PROSES tanpa pernah pending)
  if (
    (lpr.status_perbaikan === "PROSES" ||
      lpr.status_perbaikan === "SELESAI") &&
    lpr.waktu_mulai &&
    !lpr.waktu_dilanjutkan
  ) {
    let isActive = (lpr.status_perbaikan === "PROSES") ? "active" : "completed";
    let anim = (lpr.status_perbaikan === "PROSES") ? "animation: pulseBlue 2s infinite;" : "";

    html += `
      <div class="timeline-item ${isActive}">
        <div class="timeline-icon-box" style="${anim}"><i class="fas fa-tools"></i></div>
        <div class="timeline-content">
          <h6 class="fw-bold mb-1">Perbaikan Berlangsung</h6>
          <p class="small text-muted mb-0"><i class="far fa-clock me-1"></i> Waktu Mulai: ${fmt(lpr.waktu_mulai)}</p>
        </div>
      </div>`;
  }

  // 5. PENDING / DILANJUTKAN KEMBALI (Tampil jika pernah pending)
  if (lpr.status_perbaikan === "PENDING") {
    html += `
      <div class="timeline-item attention">
        <div class="timeline-icon-box" style="animation: pulseWarning 2s infinite;"><i class="fas fa-pause-circle"></i></div>
        <div class="timeline-content border-warning bg-warning bg-opacity-10">
          <h6 class="fw-bold text-dark mb-1">Perbaikan Ditunda (Pending)</h6>
          <p class="small text-muted mb-1"><i class="far fa-pause-circle me-1"></i> Ditunda Pada: ${lpr.waktu_pending ? fmt(lpr.waktu_pending) : "Belum tercatat"}</p>
          ${lpr.alasan_pending ? `<div class="alert alert-warning small py-1 px-2 mb-0 mt-2"><i class="fas fa-exclamation-triangle me-1"></i> Alasan: ${lpr.alasan_pending}</div>` : ""}
        </div>
      </div>`;
  } else if (
    (lpr.status_perbaikan === "PROSES" ||
      lpr.status_perbaikan === "SELESAI")
    &&
    lpr.alasan_pending
    &&
    lpr.waktu_dilanjutkan
  ) {
    html += `
      <div class="timeline-item completed">
        <div class="timeline-icon-box bg-warning text-dark border-warning" style="box-shadow: none;"><i class="fas fa-pause-circle"></i></div>
        <div class="timeline-content border-warning">
          <h6 class="fw-bold text-dark mb-1">Pernah Ditunda (Pending)</h6>
          <p class="small text-muted mb-1"><i class="far fa-clock me-1"></i> Ditunda Pada: ${lpr.waktu_pending ? fmt(lpr.waktu_pending) : "Belum tercatat"}</p>
          <p class="small text-muted mb-1"><i class="fas fa-history me-1"></i> Sempat ditunda dengan alasan:</p>
          <div class="alert alert-warning small py-1 px-2 mb-0 mt-2"><i class="fas fa-exclamation-triangle me-1"></i> ${lpr.alasan_pending}</div>
        </div>
      </div>
      <div class="timeline-item ${(lpr.status_perbaikan === "PROSES") ? "active" : "completed"}">
        <div class="timeline-icon-box" style="${(lpr.status_perbaikan === "PROSES") ? "animation: pulseBlue 2s infinite;" : ""}"><i class="fas fa-play-circle"></i></div>
        <div class="timeline-content border-primary bg-primary bg-opacity-10">
          <h6 class="fw-bold text-primary mb-1">Perbaikan Dilanjutkan</h6>
          <p class="small text-muted mb-0"><i class="far fa-clock me-1"></i> Kembali dikerjakan: ${fmt(lpr.waktu_dilanjutkan)}</p>
        </div>
      </div>`;
  }

  // 6. SELESAI PENGERJAAN FISIK (OLEH TEKNISI)
  // 6A. TIDAK BISA DIPERBAIKI
  if (lpr.hasil_perbaikan === "RUSAK TOTAL") {

    html += `
    <div class="timeline-item completed">
        <div class="timeline-icon-box bg-danger text-white border-danger">
            <i class="fas fa-times-circle"></i>
        </div>

        <div class="timeline-content border-danger bg-danger bg-opacity-10">
            <h6 class="fw-bold text-danger mb-1">
                Tidak Bisa Diperbaiki
            </h6>

            <p class="small text-muted mb-1">
                <i class="far fa-clock me-1"></i>
                Waktu Penetapan:
                ${fmt(lpr.waktu_selesai)}
            </p>

            <div class="alert alert-danger small py-2 px-3 mt-2 mb-0">
                <strong>Diagnosa:</strong>
                <br>
                ${lpr.diagnosa_rusak || "-"}
            </div>
        </div>
    </div>`;
  }
  else if (
    lpr.waktu_selesai ||
    lpr.status_perbaikan === "SELESAI"
  ) {

    html += `
    <div class="timeline-item completed">
        <div class="timeline-icon-box">
            <i class="fas fa-check-circle text-success"></i>
        </div>
        <div class="timeline-content">
            <h6 class="fw-bold mb-1">
                Perbaikan Selesai
            </h6>
            <p class="small text-muted mb-1">
                <i class="far fa-clock me-1"></i>
                Waktu Selesai:
                ${fmt(lpr.waktu_selesai)}
            </p>
            <div class="small mt-2">
                Uraian Pekerjaan:
                <span class="fst-italic">
                    ${lpr.catatan_teknisi || "-"}
                </span>
            </div>
        </div>
    </div>`;
  }

  // 7. STATUS LAPORAN FINAL
  if (lpr.status_laporan === "MENUNGGU KONFIRMASI") {
    html += `
      <div class="timeline-item attention">
        <div class="timeline-icon-box bg-warning text-dark border-warning"><i class="fas fa-star-half-alt"></i></div>
        <div class="timeline-content border-warning bg-light">
          <h6 class="fw-bold text-dark mb-1">Menunggu Konfirmasi Anda</h6>
          <p class="small text-muted mb-0">Silakan klik tombol di bawah untuk menyelesaikan laporan dan memberi rating.</p>
        </div>
      </div>`;
  } else if (lpr.status_laporan === "SELESAI") {
    html += `
      <div class="timeline-item completed">
        <div class="timeline-icon-box bg-success text-white border-success"><i class="fas fa-star"></i></div>
        <div class="timeline-content border-success bg-success bg-opacity-10">
          <h6 class="fw-bold text-success mb-1">Laporan Selesai & Ditutup</h6>
          <p class="small text-muted mb-1"><i class="far fa-clock me-1"></i> Waktu Selesai: ${lpr.waktu_selesai ? fmt(lpr.waktu_selesai) : "Selesai"}</p>
          <p class="small text-muted mb-0">Terima kasih telah menggunakan layanan kami.</p>
        </div>
      </div>`;
  }

  html += `</div>`;
  return html;
}

window.loadCardList = function (filter) {
  const container = document.getElementById("dynamic_content");
  const header = document.getElementById("content_header");

  const alertAwal = document.getElementById("antrian_dynamic_content");
  if (alertAwal) alertAwal.style.display = "none";

  container.innerHTML = `<div class="text-center p-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted">Memuat data...</p></div>`;

  fetch(`${BASE_URL}pelapor/get_laporan/${filter}`, { credentials: "same-origin" })
    .then((res) => res.json())
    .then((data) => {
      let judulHeader = "Daftar Laporan";
      if (filter === "all") judulHeader = "Daftar Laporan Aktif";
      else if (filter === "proses") judulHeader = "Daftar Laporan Sedang Diproses";
      else if (filter === "validasi") judulHeader = "Daftar Laporan Perlu Validasi";
      else if (filter === "selesai") judulHeader = "Daftar Laporan Selesai";

      header.innerHTML = `<i class="fas fa-list-alt me-2"></i> ${judulHeader} (${data.length})`;

      if (!data.length) {
        container.innerHTML = `<div class="col-12"><div class="alert alert-info text-center"><i class="fas fa-info-circle me-2"></i> Tidak ada laporan pada kategori ini.</div></div>`;
        return;
      }

      container.innerHTML = data
        .map((lpr) => {
          const displayStatus = getCardStatusLabel(lpr);
          const badgeClass = getCardBadgeClass(displayStatus);
          const borderClass =
            displayStatus === 'BARU'
              ? 'border-primary'
              : displayStatus === 'DIPROSES' || displayStatus === 'PENDING' || displayStatus === 'DIJADWALKAN'
                ? 'border-warning'
                : displayStatus === 'MENUNGGU KONFIRMASI'
                  ? 'border-info'
                  : displayStatus === 'RUSAK - MENUNGGU KONFIRMASI'
                    ? 'border-danger'
                    : 'border-success';

          return `
        <div class="col-12 mb-2 fade-in">
          <div class="laporan-card border-start border-4 shadow-sm ${borderClass}" onclick="openTimelineModal('${lpr.id_laporan}')">
            <div>
              <div class="fw-bold text-dark">${lpr.nama_alat}</div>
              <div class="detail-text text-muted">
                <i class="fas fa-map-marker-alt me-1"></i> ${lpr.lokasi} <span class="mx-1">|</span> No: ${lpr.nomor_laporan}
              </div>
              <small class="text-muted" style="font-size: 0.75rem;">
                <i class="far fa-clock me-1"></i> ${fmt(lpr.created_at)}
              </small>
            </div>

            <span class="badge rounded-pill ${badgeClass}">${displayStatus}</span>
          </div>
        </div>
      `;
        })
        .join("");
    })
    .catch((error) => {
      console.error(error);
      container.innerHTML = `<div class="alert alert-danger text-center">Gagal memuat data. Silakan refresh halaman.</div>`;
    });
};

window.openTimelineModal = function (id) {
  fetch(`${BASE_URL}pelapor/get_detail/${id}`, { credentials: "same-origin" })
    .then((res) => res.json())
    .then((lpr) => {
      document.getElementById("modalTimelineTitle").textContent = `Lacak Laporan: ${lpr.nomor_laporan}`;
      document.getElementById("detail_modal_lpr_id").textContent = lpr.nomor_laporan;
      document.getElementById("detail_modal_tgl").textContent = fmt(lpr.tanggal_laporan);
      document.getElementById("detail_modal_alat_display").textContent = lpr.nama_alat;
      document.getElementById("detail_modal_inv").textContent = lpr.nomor_inventaris ?? "-";
      document.getElementById("detail_modal_lokasi").textContent = lpr.lokasi;
      document.getElementById("detail_modal_unit").textContent = lpr.unit;
      document.getElementById("detail_modal_keluhan").textContent = lpr.kerusakan;
      document.getElementById("detail_modal_status_perbaikan").textContent = lpr.status_laporan;
      document.getElementById("detail_modal_teknisi").textContent = lpr.nama_teknisi ?? "-";
      document.getElementById("detail_modal_tgl_perbaikan").textContent =
        lpr.tgl_dijadwalkan
          ? new Date(lpr.tgl_dijadwalkan).toLocaleDateString("id-ID", {
            day: "numeric",
            month: "long",
            year: "numeric"
          })
          : "-";
      const validasiKepala = document.getElementById("detail_modal_validasi_kepala");

      if (validasiKepala) {

        if (lpr.validasi_kepala === "Disetujui") {

          validasiKepala.innerHTML =
            '<span class="badge bg-success">Disetujui</span>';

        } else {

          validasiKepala.innerHTML =
            '<span class="badge bg-warning text-dark">-</span>';

        }

      }

      document.getElementById("detail_modal_kerusakan_display").innerHTML = getStatusKerusakanBadge(lpr.status_kerusakan);

      // ===============================
      // FOTO PELAPOR
      // ===============================

      const pelaporFotoContainer =
        document.getElementById("detail_modal_pelapor_foto_container");

      const noPelaporFoto =
        document.getElementById("detail_modal_no_pelapor_foto");

      pelaporFotoContainer.innerHTML = "";

      if (
        lpr.path_foto_bukti &&
        lpr.path_foto_bukti.trim() !== ""
      ) {

        noPelaporFoto.style.display = "none";
        pelaporFotoContainer.style.display = "flex";

        lpr.path_foto_bukti
          .split(",")
          .map(f => f.trim())
          .filter(f => f)
          .forEach(foto => {

            const col = document.createElement("div");
            col.className = "col-6";

            const img = document.createElement("img");

            img.src = BASE_URL + "uploads/laporan/" + foto;

            img.className =
              "img-fluid rounded shadow-sm border";

            img.style.width = "100%";
            img.style.height = "150px";
            img.style.objectFit = "cover";
            img.style.cursor = "zoom-in";

            img.onclick = function () {

              document.getElementById("fotoPreviewZoom").src =
                this.src;

              new bootstrap.Modal(
                document.getElementById("modalFotoPreview")
              ).show();

            };

            col.appendChild(img);

            pelaporFotoContainer.appendChild(col);

          });

      } else {

        pelaporFotoContainer.style.display = "none";
        noPelaporFoto.style.display = "block";

      }
      // ===============================
      // HASIL PEKERJAAN TEKNISI / DIAGNOSA
      // ===============================

      const hasilTeknisi = document.getElementById("detail_modal_hasil_teknisi");
      const diagnosaSection = document.getElementById("detail_modal_diagnosa_section");

      const teknisiFotoContainer = document.getElementById("detail_modal_teknisi_foto_container");
      const noTeknisiFoto = document.getElementById("detail_modal_no_teknisi_foto");

      // ===============================
      // TAMPILKAN URAIAN / DIAGNOSA
      // ===============================

      if (lpr.hasil_perbaikan === "RUSAK TOTAL") {

        hasilTeknisi.style.display = "none";
        diagnosaSection.style.display = "block";

        document.getElementById("detail_modal_diagnosa").textContent =
          lpr.diagnosa_rusak || "-";

      } else {

        if (hasilTeknisi && diagnosaSection) {

          if (lpr.hasil_perbaikan === "RUSAK TOTAL") {

            hasilTeknisi.style.display = "none";
            diagnosaSection.style.display = "block";

            document.getElementById("detail_modal_diagnosa").textContent =
              lpr.diagnosa_rusak || "-";

          } else {

            hasilTeknisi.style.display = "block";
            diagnosaSection.style.display = "none";

            document.getElementById("detail_modal_catatan_teknisi").textContent =
              lpr.catatan_teknisi || "-";

          }

        }

        document.getElementById("detail_modal_catatan_teknisi").textContent =
          lpr.catatan_teknisi || "-";

      }

      // ===============================
      // FOTO TEKNISI
      // ===============================

      teknisiFotoContainer.innerHTML = "";

      if (
        lpr.foto_bukti &&
        lpr.foto_bukti.trim() !== ""
      ) {

        noTeknisiFoto.style.display = "none";
        teknisiFotoContainer.style.display = "flex";

        lpr.foto_bukti
          .split(",")

          .map(f => f.trim())

          .filter(f => f)

          .forEach(foto => {

            const col = document.createElement("div");
            col.className = "col-6";

            const img = document.createElement("img");

            img.src = BASE_URL + "uploads/perbaikan/" + foto;

            img.className = "img-fluid rounded shadow-sm border";

            img.style.width = "100%";
            img.style.height = "150px";
            img.style.objectFit = "cover";
            img.style.cursor = "zoom-in";

            img.onclick = function () {

              document.getElementById("fotoPreviewZoom").src = this.src;

              new bootstrap.Modal(
                document.getElementById("modalFotoPreview")
              ).show();

            };

            col.appendChild(img);

            teknisiFotoContainer.appendChild(col);

          });

      } else {

        teknisiFotoContainer.style.display = "none";
        noTeknisiFoto.style.display = "block";

      }
      document.getElementById("detail_modal_ulasan").textContent =
        lpr.ulasan || "Pelapor belum memberikan ulasan.";

      const ratingContainer = document.getElementById("detail_modal_rating");

      if (ratingContainer) {

        const rating = Number(lpr.rating || 0);

        let html = "";

        for (let i = 1; i <= 5; i++) {

          html += i <= rating
            ? '<i class="fas fa-star text-warning"></i>'
            : '<i class="far fa-star text-warning"></i>';

        }

        html += `
        <span class="ms-2 text-dark fw-semibold">
            ${rating > 0 ? rating + " / 5" : "Belum ada rating"}
        </span>
    `;

        ratingContainer.innerHTML = html;

      }
      document.getElementById("modalTimelineBody").innerHTML = getTimelineHtml(lpr);
      const modalActionCard = document.getElementById("modalActionCard");

      if (modalActionCard) {

        const tampilkanAksi =
          lpr.status_laporan === "MENUNGGU KONFIRMASI" &&
          !lpr.rating;

        modalActionCard.style.display =
          tampilkanAksi ? "block" : "none";
      }

      new bootstrap.Modal(document.getElementById("modalTimeline")).show();
    })
    .catch((err) => {
      console.error("Error openTimelineModal:", err);
      Swal.fire({
        icon: "error",
        title: "Gagal",
        text: "Gagal memuat detail laporan. Silakan coba lagi.",
      });
    });
};

window.updateDashboardCounters = function () {
  fetch(`${BASE_URL}pelapor/get_counters`, { headers: { "X-Requested-With": "XMLHttpRequest" } })
    .then((response) => response.json())
    .then((data) => {
      const elAll = document.getElementById("counter_all");
      if (elAll) elAll.innerText = data.all ?? 0;

      const elProses = document.getElementById("counter_proses");
      if (elProses) elProses.innerText = data.proses ?? 0;

      const elValidasi = document.getElementById("counter_validasi");
      if (elValidasi) elValidasi.innerText = data.validasi ?? 0;

      const elSelesai = document.getElementById("counter_selesai");
      if (elSelesai) elSelesai.innerText = data.selesai ?? 0;
    })
    .catch((error) => console.error("Gagal memuat counter:", error));
};

window.loadPelaporDashboardCharts = function () {
  fetch(`${BASE_URL}pelapor/get_dashboard_charts`, { headers: { "X-Requested-With": "XMLHttpRequest" } })
    .then((response) => response.json())
    .then((res) => {
      if (!res.charts) return;

      const renderChart = (ctxId, type, labels, data, backgroundColor) => {
        const canvas = document.getElementById(ctxId);
        if (!canvas) return;

        let chart = null;
        if (ctxId === "pelaporChartUnit") chart = pelaporChartUnit;
        if (ctxId === "pelaporChartSeverity") chart = pelaporChartSeverity;
        if (ctxId === "pelaporChartAlat") chart = pelaporChartAlat;
        if (ctxId === "pelaporChartStatus") chart = pelaporChartStatus;

        if (chart) chart.destroy();

        const config = {
          type,
          data: {
            labels,
            datasets: [
              {
                label: "Data",
                data,
                backgroundColor,
                borderRadius: 4,
              },
            ],
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: {
                display: type === "doughnut" || type === "pie",
                position: "bottom",
              },
            },
          },
        };

        switch (ctxId) {
          case "pelaporChartUnit": pelaporChartUnit = new Chart(canvas.getContext("2d"), config); break;
          case "pelaporChartSeverity": config.type = "doughnut"; pelaporChartSeverity = new Chart(canvas.getContext("2d"), config); break;
          case "pelaporChartAlat": pelaporChartAlat = new Chart(canvas.getContext("2d"), config); break;
          case "pelaporChartStatus": config.type = "pie"; pelaporChartStatus = new Chart(canvas.getContext("2d"), config); break;
        }
      };

      renderChart("pelaporChartUnit", "bar", res.charts.unit.labels, res.charts.unit.data, "#0d6efd");
      renderChart("pelaporChartSeverity", "doughnut", res.charts.severity.labels, res.charts.severity.data, ["#198754", "#ffc107", "#dc3545"]);
      renderChart("pelaporChartAlat", "bar", res.charts.alat.labels, res.charts.alat.data, "#fd7e14");
      renderChart("pelaporChartStatus", "pie", res.charts.status.labels, res.charts.status.data, ["#0d6efd", "#ffc107", "#6c757d", "#198754"]);
    })
    .catch((error) => console.error("Gagal memuat grafik Pelapor:", error));
};

// ==========================================
// FUNGSI KOMPLAIN (MASIH RUSAK)
// ==========================================
/*window.bukaModalKomplain = function () {
  const idLaporan = document.getElementById("detail_modal_lpr_id").textContent;

  const modalTimelineEl = document.getElementById("modalTimeline");
  const timelineInstance = bootstrap.Modal.getInstance(modalTimelineEl);
  if (timelineInstance) timelineInstance.hide();

  setTimeout(() => {
    Swal.fire({
      title: 'Laporan Belum Selesai?',
      text: 'Silakan jelaskan bagian mana yang masih rusak:',
      input: 'textarea',
      inputPlaceholder: 'Tulis keluhan / kerusakan yang masih terjadi di sini...',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Kirim Komplain',
      cancelButtonText: 'Batal',
      confirmButtonColor: '#d33',
      preConfirm: (alasan) => {
        if (!alasan) {
          Swal.showValidationMessage('Keluhan tidak boleh kosong!');
        }
        return alasan;
      }
    }).then((result) => {
      if (result.isConfirmed) {

        Swal.fire({ title: "Memproses...", allowOutsideClick: false, didOpen: () => Swal.showLoading() });

        const formData = new FormData();
        formData.append('nomor_laporan', idLaporan);
        formData.append('alasan', result.value);

        fetch(`${BASE_URL}pelapor/komplain`, {
          method: 'POST',
          body: formData,
          headers: { "X-Requested-With": "XMLHttpRequest" }
        })
          .then(res => res.json())
          .then(data => {
            if (data.status === 'success') {
              Swal.fire('Terkirim', 'Komplain berhasil dikirim ke Admin/Teknisi.', 'success')
                .then(() => {
                  loadCardList('all');
                  updateDashboardCounters();
                });
            } else {
              Swal.fire('Error', data.message, 'error');
            }
          })
          .catch(err => Swal.fire('Error', 'Gagal koneksi ke server', 'error'));
      }
    });
  }, 400);
};*/

// ==========================================
// FUNGSI RATING & SELESAI
// ==========================================
window.bukaModalRating = function () {
  const modalTimelineEl = document.getElementById("modalTimeline");
  const timelineInstance = bootstrap.Modal.getInstance(modalTimelineEl);
  if (timelineInstance) {
    timelineInstance.hide();
  }

  setTimeout(() => {
    const modalRatingEl = document.getElementById("modalRating");
    const modalRating = new bootstrap.Modal(modalRatingEl);
    const ratingContainer = document.getElementById("ratingContainer");
    const hiddenInput = document.getElementById("hiddenRatingInput");

    if (ratingContainer) {
      const oldStarIcon = ratingContainer.querySelector(".star-icon");
      if (oldStarIcon) {
        const newStarIcon = oldStarIcon.cloneNode(true);
        oldStarIcon.parentNode.replaceChild(newStarIcon, oldStarIcon);
        const activeStars = newStarIcon.querySelectorAll(".fas.fa-star");

        function setRatingVisuals(score) {
          activeStars.forEach((star) => {
            const starRating = parseInt(star.dataset.rating, 10);
            if (starRating <= score) {
              star.classList.add("selected");
            } else {
              star.classList.remove("selected");
            }
          });
          hiddenInput.value = score;
        }

        setRatingVisuals(0);

        activeStars.forEach((star) => {
          star.addEventListener("mouseover", () => {
            const ratingValue = parseInt(star.dataset.rating, 10);
            activeStars.forEach((s) => {
              const sRating = parseInt(s.dataset.rating, 10);
              if (sRating <= ratingValue) s.classList.add("hovered");
              else s.classList.remove("hovered");
            });
          });

          star.addEventListener("mouseleave", () => {
            activeStars.forEach((s) => s.classList.remove("hovered"));
            setRatingVisuals(parseInt(hiddenInput.value, 10));
          });

          star.addEventListener("click", () => {
            const ratingValue = parseInt(star.dataset.rating, 10);
            setRatingVisuals(ratingValue);
          });
        });
      }
    }
    modalRating.show();
  }, 300);
};
// ==========================================
// FUNGSI KIRIM RATING & SELESAIKAN LAPORAN
// ==========================================
window.kirimRating = async function () {
  try {
    const hiddenInput = document.getElementById("hiddenRatingInput");
    const rating = parseInt(hiddenInput.value, 10);
    const ulasanInput = document.getElementById("inputUlasan");
    const ulasan = ulasanInput.value.trim();
    const idLaporan = document.getElementById("detail_modal_lpr_id").textContent;

    if ((!rating || rating === 0) && ulasan === "") {
      showSwalAfterClose({ icon: "warning", title: "Data Belum Lengkap", text: "Mohon berikan rating dan ulasan.", confirmButtonText: "Mengerti" }).then(() => bukaModalRating());
      return;
    }
    if (rating > 0 && ulasan === "") {
      showSwalAfterClose({ icon: "warning", title: "Ulasan Wajib Diisi", text: "Mohon isi ulasan.", confirmButtonText: "Isi Ulasan" }).then(() => {
        bukaModalRating(); document.getElementById("inputUlasan").focus();
      });
      return;
    }
    if ((!rating || rating === 0) && ulasan !== "") {
      showSwalAfterClose({ icon: "warning", title: "Rating Belum Dipilih", text: "Mohon pilih rating.", confirmButtonText: "Pilih Rating" }).then(() => bukaModalRating());
      return;
    }

    const btnSubmit = document.querySelector("#modalRating .btn-primary");
    const originalText = btnSubmit.innerHTML;
    btnSubmit.disabled = true;
    btnSubmit.innerHTML = "Memproses Data...";

    const formData = new FormData();
    formData.append('nomor_laporan', idLaporan);
    formData.append('rating', rating);
    formData.append('ulasan', ulasan);

    const response = await fetch(`${BASE_URL}pelapor/selesaikan_laporan`, {
      method: 'POST',
      body: formData,
      headers: { "X-Requested-With": "XMLHttpRequest" }
    });
    const result = await response.json();

    btnSubmit.disabled = false;
    btnSubmit.innerHTML = originalText;

    if (result.status === 'success') {
      showSwalAfterClose({
        icon: "success",
        title: "Terima Kasih!",
        html: `
        <div class="text-center">

            <div class="mb-2 text-warning fs-3">
                ${'<i class="fas fa-star"></i>'.repeat(rating)}
                ${'<i class="far fa-star"></i>'.repeat(5 - rating)}
            </div>

            <h5 class="fw-bold mb-3">
                ${rating} / 5
            </h5>

            <div class="alert alert-light border">
                <em>"${ulasan}"</em>
            </div>

            <p class="text-success mb-0">
                Laporan telah berhasil ditutup.
            </p>

        </div>
        `,
        confirmButtonText: "OK",
      }).then(() => {
        const stars = document.getElementById("ratingContainer").querySelectorAll(".fas.fa-star");
        stars.forEach((star) => star.classList.remove("selected", "hovered"));
        hiddenInput.value = 0;
        ulasanInput.value = "";

        const modalEl = document.getElementById("modalRating");
        bootstrap.Modal.getInstance(modalEl).hide();

        loadCardList('selesai');
        updateDashboardCounters();
      });
    } else {
      throw new Error(result.message);
    }
  } catch (e) {
    Swal.fire({
      icon: "error",
      title: "Terjadi Kesalahan",
      text: e.message || "Terjadi kesalahan saat mengirim rating.",
      confirmButtonText: "Tutup",
    });
    console.error("Error di kirimRating:", e);
  }
};

// Fungsi untuk menampilkan SweetAlert setelah modal Bootstrap benar-benar tertutup
function showSwalAfterClose(options) {
  return new Promise((resolve) => {
    const modalEl = document.getElementById("modalRating");
    const modalInstance = bootstrap.Modal.getInstance(modalEl);
    const showSwal = () => Swal.fire(options).then(resolve);

    if (modalInstance) {
      modalInstance.hide();
      modalEl.addEventListener("hidden.bs.modal", () => showSwal(), { once: true });
    } else {
      showSwal();
    }
  });
}