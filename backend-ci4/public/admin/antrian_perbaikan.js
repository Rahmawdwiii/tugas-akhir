// --- VARIABEL GLOBAL ---
let antrianData = [];
let tempAssignment = {
  laporanId: null,
  teknisiNama: null,
  isManual: false,
};

// --- FUNGSI FORMAT TANGGAL ---
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

function formatDateTime(dateString) {
  if (!dateString || dateString === "-") return "-";
  const date = new window.Date(dateString);
  if (isNaN(date.getTime())) return "-";

  const formattedDate = date.toLocaleDateString("id-ID", {
    year: "numeric",
    month: "long",
    day: "numeric",
  });
  const formattedTime = date.toLocaleTimeString("id-ID", {
    hour: "2-digit",
    minute: "2-digit",
  });

  return `${formattedDate} ${formattedTime}`;
}

function normalizeStatusKerusakan(status) {
  if (!status || status === "-" || status === "Belum Dicek") return "Belum Dicek";
  const cleaned = status.toString().trim();
  if (cleaned === "" || cleaned.toLowerCase() === "belum dicek") return "Belum Dicek";
  return cleaned.charAt(0).toUpperCase() + cleaned.slice(1).toLowerCase();
}

// === 1. FUNGSI UTAMA: LOAD DATA DARI DATABASE (AJAX) ===
window.loadAntrian = async function (kategori) {
  const container = document.getElementById("antrian_dynamic_content");
  const header = document.getElementById("antrian_header");

  container.innerHTML = `<div class="text-center p-5"><i class="fas fa-spinner fa-spin me-2"></i> Memuat Data Server...</div>`;

  try {
    const response = await fetch(`${BASE_URL}admin/get_antrian/${kategori}`);
    if (!response.ok) throw new Error("Gagal load antrian");

    const dataLaporan = await response.json();
    antrianData = dataLaporan;

    let htmlContent = "";
    let judulHeader = "";

    if (kategori === "new") {
      judulHeader = "Penugasan Baru";
      dataLaporan.forEach(
        (lpr) => (htmlContent += generateNewAntrianCard(lpr))
      );
    } else if (kategori === "proses") {
      judulHeader = "Sedang Diproses";
      dataLaporan.forEach(
        (lpr) => (htmlContent += generateProsesAntrianCard(lpr))
      );
    } else if (kategori === "validasi_akhir") {
      judulHeader = "Validasi Akhir";
      dataLaporan.forEach(
        (lpr) => (htmlContent += generateValidasiAntrianCard(lpr))
      );
    } else if (kategori === "riwayat") {
      judulHeader = "Riwayat Laporan";
      dataLaporan.forEach(
        (lpr) => (htmlContent += generateRiwayatAntrianCard(lpr))
      );
    }

    header.innerText = `Daftar Laporan (${judulHeader}: ${dataLaporan.length} Laporan)`;

    if (dataLaporan.length === 0) {
      container.innerHTML = `
            <div class="alert alert-success mt-3 text-center">
                <i class="fas fa-check-circle me-2"></i> Tidak ada laporan antrian pada status ini.
            </div>`;
      return;
    }

    container.innerHTML = htmlContent;
  } catch (error) {
    console.error(error);
    container.innerHTML = `<div class="alert alert-danger text-center">Gagal memuat data: ${error.message}</div>`;
  }
};

// === 2. GENERATOR KARTU (DISESUAIKAN DENGAN JSON DATABASE) ===

function generateNewAntrianCard(laporan) {
  const teknisi = laporan.teknisi_nama || "Belum Ditentukan";
  const statusText = normalizeStatusKerusakan(laporan.status_kerusakan) || "Belum Dicek";
  let keru_color = "secondary";

  if (statusText === "Berat") keru_color = "danger";
  else if (statusText === "Sedang") keru_color = "warning text-dark";
  else if (statusText === "Ringan") keru_color = "success";

  const avatarUrl = `https://ui-avatars.com/api/?name=${teknisi.replace(
    /\s/g,
    "+"
  )}&background=0d6efd&color=fff`;

  return `
    <div class="col-12">
        <div class="card antrian-card-detail antrian-penugasan-card shadow-sm mb-3">
            <div class="card-body p-3">
                <div class="row align-items-center">
                    <div class="col-md-5">
                        <small class="text-muted d-block fw-bold" style="font-size: 10px;">No: ${laporan.id}</small>
                        <h6 class="fw-bolder text-dark mb-1">${laporan.alat}</h6>
                        <span class="badge bg-${keru_color} rounded-pill me-2">${statusText}</span>
                        <small class="text-secondary d-block"><i class="fas fa-building me-1"></i> ${laporan.gedung}</small>
                    </div>
                    <div class="col-md-4 py-2 py-md-0 border-start border-end d-flex align-items-center">
                        <img src="${avatarUrl}" class="rounded-circle me-2 border" width="35" alt="Avatar">
                        <div>
                            <small class="text-muted d-block" style="font-size: 10px;">Rekomendasi Sistem:</small>
                            <strong class="text-primary d-block" style="line-height: 1.2;">${teknisi}</strong>
                        </div>
                    </div>
                    <div class="col-md-3 text-md-end pt-2 pt-md-0">
                        <button class="btn btn-primary btn-sm fw-bold w-100 mb-1" onclick="window.assign('${laporan.id}', '${teknisi}')">
                            <i class="fas fa-user-check me-1"></i> Tugaskan
                        </button>
                        <button class="btn btn-outline-secondary btn-sm w-100 mb-1" onclick="window.openPilihLainModal('${laporan.id}')">Pilih Lain</button>
                        
                        <button class="btn btn-outline-info btn-sm w-100" onclick="window.openDetailModal('${laporan.id}')">
                            <i class="fas fa-info-circle me-1"></i> Detail
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>`;
}

function generateProsesAntrianCard(laporan) {
  const tglLaporan = formatDate(laporan.tgl_laporan);
  const tglJadwal = laporan.tgl_dijadwalkan ? formatDate(laporan.tgl_dijadwalkan) : null;
  const statusText = laporan.status_laporan || "DIPROSES";

  const badgeClass =
    statusText === "BARU"
      ? "bg-primary"
      : statusText === "DIPROSES" || statusText === "PENDING"
        ? "bg-warning text-dark"
        : "bg-success";

  return `
    <div class="col-12 mb-2 fade-in">
      <div class="laporan-card border-start border-4 shadow-sm ${statusText === "BARU"
      ? "border-primary"
      : statusText === "DIPROSES" || statusText === "PENDING"
        ? "border-warning"
        : "border-success"
    }" onclick="window.openDetailModal('${laporan.id}')">
        <div>
          <div class="fw-bold text-dark">${laporan.alat}</div>
          <div class="detail-text">
            <i class="fas fa-map-marker-alt me-1"></i> ${laporan.lokasi} • ${laporan.pelapor}
            ${tglJadwal ? `<span class="d-block mt-1"><i class="fas fa-calendar-alt me-1"></i> Jadwal: ${tglJadwal}</span>` : ""}
          </div>
          <small class="text-muted" style="font-size: 0.75rem; display: block; margin-top: 5px;">
            <i class="far fa-clock me-1"></i> ${tglLaporan}
          </small>
        </div>

        <div class="text-end">
          <span class="badge rounded-pill ${badgeClass}">${statusText}</span>
        </div>
      </div>
    </div>`;
}

function generateValidasiAntrianCard(laporan) {

  // ===============================
  // DATA DASAR
  // ===============================
  const ulasan = laporan.keluhan_lengkap || "Menunggu validasi...";
  const ratingVal = Number(laporan.rating ?? 0);

  const sudahKonfirmasi =
    laporan.rating !== null &&
    laporan.rating !== undefined &&
    laporan.rating !== "";

  const hasilPerbaikan = String(laporan.hasil_perbaikan || "")
    .trim()
    .toUpperCase();

  const isRusakTotal = hasilPerbaikan === "RUSAK TOTAL";

  // ===============================
  // RATING
  // ===============================
  const stars = '<i class="fas fa-star"></i>'.repeat(Math.floor(ratingVal));

  // ===============================
  // STATUS
  // ===============================
  let statusBadge = "bg-warning";
  let statusText = "MENUNGGU KONFIRMASI PELAPOR";

  if (sudahKonfirmasi) {

    if (isRusakTotal) {

      statusBadge = "bg-danger";
      statusText = "RUSAK TOTAL - MENUNGGU VALIDASI";

    } else {

      statusBadge = "bg-success";
      statusText = "MENUNGGU VALIDASI";

    }

  }

  // ===============================
  // ULASAN PELAPOR
  // ===============================
  const infoKonfirmasi = !sudahKonfirmasi
    ? `
            <small class="text-warning fw-bold d-block">
                Pelapor belum memberi rating
            </small>
            <small class="text-muted">
                N/A
            </small>
        `
    : `
            <span class="small-rating text-warning d-block">
                ${stars} (${ratingVal})
            </span>

            <p class="small mb-0 text-dark fst-italic">
                "${ulasan.substring(0, 40)}${ulasan.length > 40 ? '...' : ''}"
            </p>
        `;

  // ===============================
  // HTML CARD
  // ===============================
  return `
    <div class="col-12">

        <div class="card antrian-card-detail antrian-validasi-card shadow-sm mb-3">

            <div class="card-body p-3">

                <div class="row align-items-center">

                    <div class="col-md-5">

                        <small class="text-muted d-block fw-bold" style="font-size:10px;">
                            No: ${laporan.id}
                        </small>

                        <h6 class="fw-bolder text-dark mb-1">
                            ${laporan.alat}
                        </h6>

                        <span class="badge ${statusBadge} rounded-pill me-2">
                            ${statusText}
                        </span>

                        <small class="text-secondary d-block">
                            Teknisi : ${laporan.teknisi_nama}
                        </small>

                    </div>

                    <div class="col-md-4 py-2 py-md-0 border-start border-end">

                        <small class="d-block mb-1 text-success fw-bold">
                            Ulasan Pelapor:
                        </small>

                        ${infoKonfirmasi}

                    </div>

                    <div class="col-md-3">
                    <button
                        class="btn btn-outline-info btn-sm fw-bold w-100 mt-2"
                        onclick="window.openDetailModal('${laporan.id}')">

                        <i class="fas fa-info-circle me-1"></i>
                        Detail
                    </button>
                </div>
              </div>

            </div>

        </div>

    </div>
    `;
}

function generateRiwayatAntrianCard(laporan) {

  const isRusakTotal =
    String(laporan.hasil_perbaikan || "")
      .trim()
      .toUpperCase() === "RUSAK TOTAL";

  const badgeClass = isRusakTotal
    ? "bg-dark"
    : "bg-success";

  const badgeText = isRusakTotal
    ? "RUSAK TOTAL"
    : "SELESAI";

  const judul = isRusakTotal
    ? "Diagnosa:"
    : "Uraian Pekerjaan:";

  const isi = isRusakTotal
    ? (laporan.diagnosa_rusak || "-")
    : (laporan.catatan_teknisi || "-");

  return `
    <div class="card antrian-card-detail shadow-sm mb-3">
      <div class="card-body p-3">
        <div class="row align-items-center">
          <div class="col-md-5">
            <small class="text-muted d-block fw-bold"
                   style="font-size:10px;">
              No: ${laporan.id}
            </small>

            <h6 class="fw-bolder text-dark mb-1">
              ${laporan.alat}
            </h6>

            <span class="badge ${badgeClass} rounded-pill me-2">
              ${badgeText}
            </span>

          </div>

          <div class="col-md-4 border-start border-end">
            <small class="d-block mb-1 text-dark fw-bold">
              ${judul}
            </small>
            <p class="small mb-0 text-muted fst-italic">
              "${isi.substring(0, 60)}${isi.length > 60 ? "..." : ""}"
            </p>

          </div>

          <div class="col-md-3 text-md-end pt-2 pt-md-0">
            <button
              class="btn btn-dark btn-sm fw-bold w-100"
              onclick="window.openDetailModal('${laporan.id}')">
              <i class="fas fa-info-circle me-1"></i>
              Detail
            </button>
          </div>
        </div>
      </div>
    </div>`;
}

// === 3. FUNGSI LOGIKA MODAL (PENUGASAN) ===
window.assign = function (laporanId, teknisiNama) {
  const data = antrianData.find((item) => item.id == laporanId);

  if (
    !data ||
    !data.status_kerusakan ||
    data.status_kerusakan === "Belum Dicek"
  ) {
    Swal.fire({
      icon: "warning",
      title: "Tidak Bisa Dijadwalkan",
      html: `Status kerusakan belum ditentukan oleh teknisi.<br>
                   <small class="text-muted">Mohon tunggu teknisi melakukan pengecekan awal (Input Status Kerusakan) sebelum menjadwalkan perbaikan.</small>`,
      confirmButtonText: "Oke, Mengerti",
    });
    return;
  }
  prepareAssignment(laporanId, teknisiNama, false);
};

function prepareAssignment(laporanId, teknisiNama, isManual) {
  tempAssignment.laporanId = laporanId;
  tempAssignment.teknisiNama = teknisiNama;
  tempAssignment.isManual = isManual;

  document.getElementById("jadwal_laporan_id").textContent = laporanId;
  document.getElementById("jadwal_teknisi_name").textContent = teknisiNama;
  document.getElementById("inputTanggal").value = "";

  new bootstrap.Modal(document.getElementById("modalSetJadwal")).show();
}

window.finalAssign = function () {
  const tanggal = document.getElementById("inputTanggal").value;
  kirimPenugasan(tempAssignment.laporanId, tempAssignment.teknisiNama, tanggal);
};

window.assignManualHandler = function () {
  const teknisi = document.getElementById("selectTeknisi").value;
  const tanggal = document.getElementById("inputTanggalPilihLain").value;
  kirimPenugasan(tempAssignment.laporanId, teknisi, tanggal);
};

// === 4. FUNGSI MODAL PILIH LAIN ===
window.openPilihLainModal = function (laporanId) {
  const data = antrianData.find((item) => item.id == laporanId);

  if (
    !data ||
    !data.status_kerusakan ||
    data.status_kerusakan === "Belum Dicek"
  ) {
    Swal.fire({
      icon: "warning",
      title: "Tidak Bisa Dijadwalkan",
      html: `Status kerusakan belum ditentukan oleh teknisi.<br>
                   <small class="text-muted">Mohon tunggu teknisi melakukan pengecekan awal terlebih dahulu.</small>`,
      confirmButtonText: "Oke, Mengerti",
    });
    return;
  }

  tempAssignment.laporanId = laporanId;
  document.querySelector(
    "#modalPilihLain p.small.text-muted"
  ).innerHTML = `Laporan ID: <strong>${laporanId}</strong>`;
  document.getElementById("selectTeknisi").value = "";
  document.getElementById("inputTanggalPilihLain").value = "";

  new bootstrap.Modal(document.getElementById("modalPilihLain")).show();
};

function kirimPenugasan(nomorLaporan, namaTeknisi, tanggal) {
  if (!namaTeknisi || !tanggal) {
    Swal.fire("Error", "Mohon lengkapi teknisi dan tanggal!", "warning");
    return;
  }

  const formData = new window.FormData();
  formData.append("nomor_laporan", nomorLaporan);
  formData.append("nama_teknisi", namaTeknisi);
  formData.append("tanggal_perbaikan", tanggal);

  Swal.fire({
    title: "Menyimpan...",
    didOpen: () => Swal.showLoading(),
  });

  fetch(`${BASE_URL}admin/tugaskan_teknisi`, {
    method: "POST",
    body: formData,
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.status === "success") {
        Swal.fire("Berhasil", data.message, "success").then(() => {
          const modalSetJadwal = document.getElementById("modalSetJadwal");
          const modalPilihLain = document.getElementById("modalPilihLain");
          if (modalSetJadwal)
            bootstrap.Modal.getInstance(modalSetJadwal)?.hide();
          if (modalPilihLain)
            bootstrap.Modal.getInstance(modalPilihLain)?.hide();
          window.loadAntrian("new");
        });
      } else {
        Swal.fire("Gagal", data.message, "error");
      }
    })
    .catch((err) => {
      console.error(err);
      Swal.fire("Error", "Terjadi kesalahan server.", "error");
    });
}

// === 5. FUNGSI DETAIL MODAL (FINAL VERSION) ===
window.openDetailModal = function (id) {
  const data = antrianData.find((item) => item.id == id);

  if (!data) {
    Swal.fire({
      icon: "error",
      title: "Gagal",
      text: "Data tidak ditemukan.",
    });
    return;
  }

  const setText = (elementId, value) => {
    const el = document.getElementById(elementId);
    if (el) {
      el.innerText = value;
    }
  };

  setText("detail_modal_keluhan", data.keluhan_lengkap || "-");
  setText("detail_modal_lpr_id", data.id);
  setText("detail_modal_tgl", formatDateTime(data.tgl_laporan));
  setText("detail_modal_tgl_perbaikan", formatDate(data.tgl_dijadwalkan));
  setText("detail_modal_inv", data.inv_no || "-");
  setText("detail_modal_inv_display", data.inv_no || "-");
  setText("detail_modal_alat_display", data.alat);
  setText(
    "detail_modal_lokasi",
    data.lokasi ? `${data.gedung}, ${data.lokasi}` : data.gedung || "-"
  );
  setText("detail_modal_unit", data.gedung || "-");
  setText("detail_modal_pelapor", data.pelapor);
  setText("detail_modal_teknisi", data.teknisi_nama || "Belum Ditugaskan");

  // ===============================
  // ULASAN & RATING PELAPOR
  // ===============================

  setText(
    "detail_modal_ulasan",
    data.ulasan_pelapor || "Pelapor belum memberikan ulasan."
  );

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


  const elBadge = document.getElementById("detail_modal_kerusakan_display");
  if (elBadge) {
    const statusFix = normalizeStatusKerusakan(data.status_kerusakan);
    elBadge.innerText = statusFix;

    let warna = "bg-secondary";
    if (statusFix === "Berat") warna = "bg-danger";
    else if (statusFix === "Rusak") warna = "bg-dark";
    else if (statusFix === "Sedang") warna = "bg-warning text-dark";
    else if (statusFix === "Ringan") warna = "bg-success";
    elBadge.className = `badge fs-6 ${warna}`;
  }

  const fotoContainer = document.getElementById("detail_modal_foto_container");
  const noFotoElement = document.getElementById("detail_modal_no_foto");

  if (fotoContainer) fotoContainer.innerHTML = "";

  if (data.foto_urls && data.foto_urls.length > 0) {
    if (noFotoElement) noFotoElement.style.display = "none";
    if (fotoContainer) fotoContainer.style.display = "flex";

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
        if (zoomImg) {
          zoomImg.src = this.src;
          new bootstrap.Modal(
            document.getElementById("modalFotoPreview")
          ).show();
        }
      };

      colDiv.appendChild(img);
      fotoContainer.appendChild(colDiv);
    });
  } else {
    if (fotoContainer) fotoContainer.style.display = "none";
    if (noFotoElement) noFotoElement.style.display = "block";
  }
  // ================= FOTO TEKNISI =================

  const teknisiContainer = document.getElementById(
    "detail_modal_teknisi_foto_container"
  );

  const noTeknisi = document.getElementById(
    "detail_modal_no_teknisi_foto"
  );

  if (teknisiContainer) teknisiContainer.innerHTML = "";

  if (
    data.foto_bukti_teknisi_urls &&
    data.foto_bukti_teknisi_urls.length > 0
  ) {

    noTeknisi.style.display = "none";
    teknisiContainer.style.display = "flex";

    data.foto_bukti_teknisi_urls.forEach((url) => {

      const colDiv = document.createElement("div");
      colDiv.className = "col-6";

      const img = document.createElement("img");

      img.src = url;
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

      colDiv.appendChild(img);

      teknisiContainer.appendChild(colDiv);

    });

  } else {

    if (teknisiContainer)
      teknisiContainer.style.display = "none";

    if (noTeknisi)
      noTeknisi.style.display = "block";

  }

  const containerLink = document.getElementById("container_link_pendukung");
  const btnLink = document.getElementById("btn_link_pendukung");

  if (containerLink && btnLink) {
    if (data.link_pendukung && data.link_pendukung.trim() !== "") {
      containerLink.style.display = "block";
      let urlLink = data.link_pendukung;
      if (!urlLink.match(/^https?:\/\//i)) urlLink = "http://" + urlLink;
      btnLink.href = urlLink;
    } else {
      containerLink.style.display = "none";
      btnLink.href = "#";
    }
  }

  const timelineContainer = document.getElementById("detail_modal_timeline");

  if (timelineContainer) {
    timelineContainer.innerHTML = getTimelineHtml(data);
  }

  currentLaporanId = id;
  currentDetailValidateButton = document.getElementById("detail_modal_validate_button");
  const canValidate =
    data.validasi_kepala === "Menunggu" &&
    (
      data.status_laporan === "MENUNGGU KONFIRMASI" ||
      data.status_laporan === "SELESAI" ||
      data.status_laporan === "RUSAK TOTAL"
    );
  if (currentDetailValidateButton) {
    currentDetailValidateButton.style.display =
      canValidate ? "block" : "none";
    if (canValidate) {
      if (data.hasil_perbaikan === "RUSAK TOTAL") {
        currentDetailValidateButton.innerHTML =
          '<i class="fas fa-check-double me-2"></i> VALIDASI RUSAK TOTAL';
      } else {
        currentDetailValidateButton.innerHTML =
          '<i class="fas fa-check-double me-2"></i> VALIDASI';
      }
    }

  }

  const hasilTeknisi = document.getElementById("detail_modal_hasil_teknisi");

  if (
    (data.status_laporan === "MENUNGGU KONFIRMASI" ||
      data.status_laporan === "SELESAI") &&
    data.hasil_perbaikan !== "RUSAK TOTAL"
  ) {

    hasilTeknisi.style.display = "block";

    setText(
      "detail_modal_catatan_teknisi",
      data.catatan_teknisi || "-"
    );

  } else {

    hasilTeknisi.style.display = "none";

  }

  // ====================================
  // DIAGNOSA RUSAK TOTAL
  // ====================================

  const diagnosaSection =
    document.getElementById(
      "detail_modal_diagnosa_section"
    );

  if (data.hasil_perbaikan === "RUSAK TOTAL") {

    diagnosaSection.style.display = "block";

    setText(
      "detail_modal_diagnosa",
      data.diagnosa_rusak || "-"
    );

  } else {

    diagnosaSection.style.display = "none";

  }

  new bootstrap.Modal(document.getElementById("modalDetailLaporan")).show();
};

let currentValidateButton = null;
let currentLaporanId = null;

window.openValidateModal = function (button, nomorLaporan) {
  currentValidateButton = button;
  currentLaporanId = nomorLaporan;

  const laporan = antrianData.find((item) => item.id == nomorLaporan);
  if (!laporan) {
    Swal.fire({
      icon: "error",
      title: "Data tidak ditemukan",
      text: "Tidak dapat menemukan laporan untuk validasi.",
    });
    return;
  }

  const setText = (elementId, value) => {
    const el = document.getElementById(elementId);
    if (el) el.innerText = value || "-";
  };

  setText("validasi_modal_lpr_no", laporan.id);
  setText("validasi_modal_nama_alat", laporan.alat || "-");
  setText("validasi_modal_inv_no", laporan.inv_no || "-");
  setText("validasi_modal_unit", laporan.gedung || "-");
  setText("validasi_modal_lokasi", laporan.lokasi || "-");
  setText("validasi_modal_tanggal_laporan", laporan.tgl_laporan || "-");
  setText("validasi_modal_keluhan", laporan.keluhan_lengkap || "-");
  setText("validasi_modal_link_pendukung", laporan.link_pendukung || "-");
  setText("validasi_modal_teknisi", laporan.teknisi_nama || "-");
  setText("validasi_modal_tanggal", formatDateTime(laporan.tgl_perbaikan));
  setText("validasi_modal_status_laporan", laporan.status_laporan || "-");
  setText("validasi_modal_status_kerusakan", laporan.status_kerusakan || "-");
  if (laporan.hasil_perbaikan !== "RUSAK TOTAL") {

    setText(
      "validasi_modal_catatan_teknisi",
      laporan.catatan_teknisi || "-"
    );

  }

  const label = document.getElementById("label_hasil_teknisi");

  if (laporan.hasil_perbaikan === "RUSAK TOTAL") {
    label.innerText = "Diagnosa Kerusakan";
    setText("validasi_modal_catatan_teknisi", laporan.diagnosa_rusak || "-");
  } else {
    label.innerText = "Uraian Pekerjaan Teknisi";
    setText("validasi_modal_catatan_teknisi", laporan.catatan_teknisi || "-");
  }

  const statusLabel = document.getElementById("validasi_modal_status");
  if (statusLabel) {
    statusLabel.innerHTML = `<span class="badge ${laporan.status_perbaikan === "SELESAI" ? "bg-success" : "bg-info text-dark"
      }">${laporan.status_perbaikan || "-"}</span>`;
  }

  const ulasanText = laporan.ulasan_pelapor || "Belum ada ulasan pelapor";
  const ratingVal = laporan.rating || 0;
  const stars = "<i class=\"fas fa-star\"></i>".repeat(Math.floor(ratingVal));
  setText("validasi_modal_ulasan", `"${ulasanText}"`);
  const ratingElement = document.getElementById("validasi_modal_rating");
  if (ratingElement) {
    ratingElement.innerHTML = ratingVal > 0 ? `${stars} <span class="ms-1">${ratingVal} / 5</span>` : "-";
  }

  const fotoContainer = document.getElementById("validasi_modal_foto_container");
  const noFotoElement = document.getElementById("validasi_modal_no_foto");
  if (fotoContainer) fotoContainer.innerHTML = "";
  if (laporan.foto_urls && laporan.foto_urls.length > 0) {
    if (noFotoElement) noFotoElement.style.display = "none";
    fotoContainer.style.display = "flex";
    laporan.foto_urls.forEach((url) => {
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
        if (zoomImg) {
          zoomImg.src = this.src;
          new bootstrap.Modal(document.getElementById("modalFotoPreview")).show();
        }
      };
      colDiv.appendChild(img);
      fotoContainer.appendChild(colDiv);
    });
  } else {
    if (fotoContainer) fotoContainer.style.display = "none";
    if (noFotoElement) noFotoElement.style.display = "block";
  }

  const teknisiFotoContainer = document.getElementById("validasi_modal_teknisi_foto_container");
  const noTeknisiFoto = document.getElementById("validasi_modal_no_teknisi_foto");
  if (teknisiFotoContainer) teknisiFotoContainer.innerHTML = "";
  if (laporan.foto_bukti_teknisi_urls && laporan.foto_bukti_teknisi_urls.length > 0) {
    if (noTeknisiFoto) noTeknisiFoto.style.display = "none";
    teknisiFotoContainer.style.display = "flex";
    laporan.foto_bukti_teknisi_urls.forEach((url) => {
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
        if (zoomImg) {
          zoomImg.src = this.src;
          new bootstrap.Modal(document.getElementById("modalFotoPreview")).show();
        }
      };
      colDiv.appendChild(img);
      teknisiFotoContainer.appendChild(colDiv);
    });
  } else {
    if (teknisiFotoContainer) teknisiFotoContainer.style.display = "none";
    if (noTeknisiFoto) noTeknisiFoto.style.display = "block";
  }

  new bootstrap.Modal(document.getElementById("modalValidate")).show();
};

window.confirmValidasi = async function () {

  if (!currentLaporanId) return;

  const btn = document.getElementById("validasi_modal_button");
  if (!btn) return;
  const originalText = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML = "Memproses...";

  const formData = new window.FormData();
  formData.append("nomor_laporan", currentLaporanId);

  try {
    const response = await fetch(`${BASE_URL}admin/validasi`, {
      method: "POST",
      body: formData,
    });
    const result = await response.json();

    if (result.status === "success") {

      Swal.fire({
        icon: "success",
        title: "Berhasil",
        text: result.message
      });

      if (currentValidateButton) {
        currentValidateButton.classList.remove("btn-success");
        currentValidateButton.classList.add("btn-secondary");
        currentValidateButton.innerHTML =
          '<i class="fas fa-check"></i> DIVALIDASI';
        currentValidateButton.disabled = true;
      }

      bootstrap.Modal
        .getInstance(
          document.getElementById("modalValidate")
        )
        ?.hide();

      refreshStatistikCards();

      window.loadAntrian("validasi_akhir");
      window.loadAntrian("riwayat");

    } else {

      Swal.fire({
        icon: "error",
        title: "Gagal",
        text: "Gagal memvalidasi laporan: " + result.message,
      });

    }
  } finally {

    btn.disabled = false;
    btn.innerHTML = originalText;

  }
};

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
        <p class="small text-muted mb-0"><i class="far fa-clock me-1"></i> Terkirim: ${formatDateTime(lpr.tgl_laporan)}</p>
      </div>
    </div>`;

  // 2. STATUS KERUSAKAN DITENTUKAN (Tampil jika sudah dicek fisik)
  const statusKerusakanText = normalizeStatusKerusakan(lpr.status_kerusakan);
  if (statusKerusakanText && statusKerusakanText !== "Belum Dicek") {
    html += `
      <div class="timeline-item completed">
        <div class="timeline-icon-box"><i class="fas fa-search-plus"></i></div>
        <div class="timeline-content">
          <h6 class="fw-bold mb-1">Status Kerusakan Ditentukan</h6>
          <div class="small text-muted mt-1">
              <i class="far fa-clock me-1"></i>
              Waktu Pemeriksaan:
              ${formatDateTime(lpr.waktu_cek_kerusakan)}
              <br>

              <i class="fas fa-tools me-1"></i>
              Kondisi Kerusakan:
              ${statusKerusakanText}
              <br>

              <i class="fas fa-user-cog me-1"></i>
              Oleh Teknisi:
              ${lpr.teknisi_nama ?? "-"}
              
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
          <p class="small text-muted mb-1"><i class="far fa-clock me-1"></i> Jadwal Pengerjaan: <span class="text-muted">${lpr.tgl_dijadwalkan ? formatDate(lpr.tgl_dijadwalkan) : "Segera"}</span></p>
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
  if ((lpr.status_perbaikan === "PROSES" || lpr.status_perbaikan === "SELESAI") && lpr.waktu_mulai && !lpr.waktu_dilanjutkan) {
    let isActive = (lpr.status_perbaikan === "PROSES") ? "active" : "completed";
    let anim = (lpr.status_perbaikan === "PROSES") ? "animation: pulseBlue 2s infinite;" : "";

    html += `
      <div class="timeline-item ${isActive}">
        <div class="timeline-icon-box" style="${anim}"><i class="fas fa-tools"></i></div>
        <div class="timeline-content">
          <h6 class="fw-bold mb-1">Perbaikan Berlangsung</h6>
          <p class="small text-muted mb-0"><i class="far fa-clock me-1"></i> Waktu Mulai: ${formatDateTime(lpr.waktu_mulai)}</p>
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
          <p class="small text-muted mb-1"><i class="far fa-pause-circle me-1"></i> Ditunda Sementara: ${lpr.waktu_pending ? formatDateTime(lpr.waktu_pending) : "Belum tercatat"}</p>
         ${lpr.alasan_pending ? `
          <div class="alert alert-warning small py-1 px-2 mb-0 mt-2">
              <i class="fas fa-exclamation-triangle me-1"></i>
              Alasan: ${lpr.alasan_pending}
          </div>
          ` : ""}
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
          <p class="small text-muted mb-1"><i class="far fa-clock me-1"></i> Mulai Dikerjakan: ${lpr.waktu_mulai ? formatDateTime(lpr.waktu_mulai) : "Belum tercatat"}</p>
          <p class="small text-muted mb-1"><i class="fas fa-history me-1"></i> Sempat ditunda dengan alasan:</p>
          <div class="alert alert-warning small py-1 px-2 mb-0 mt-2"><i class="fas fa-exclamation-triangle me-1"></i> ${lpr.alasan_pending}</div>
        </div>
      </div>
      <div class="timeline-item ${(lpr.status_perbaikan === "PROSES") ? "active" : "completed"}">
        <div class="timeline-icon-box" style="${(lpr.status_perbaikan === "PROSES") ? "animation: pulseBlue 2s infinite;" : ""}"><i class="fas fa-play-circle"></i></div>
        <div class="timeline-content border-primary bg-primary bg-opacity-10">
          <h6 class="fw-bold text-primary mb-1">Perbaikan Dilanjutkan</h6>
          <p class="small text-muted mb-0"><i class="far fa-clock me-1"></i> Kembali dikerjakan: ${formatDateTime(lpr.waktu_dilanjutkan)}</p>
        </div>
      </div>`;
  }

  // 6. SELESAI PENGERJAAN FISIK (OLEH TEKNISI)
  // 6A. TIDAK BISA DIPERBAIKI (RUSAK TOTAL)
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

            <p class="small text-muted mb-2">
                <i class="far fa-clock me-1"></i>
                Waktu Penetapan:
                ${formatDateTime(lpr.waktu_selesai)}
            </p>

            <p class="small text-muted mb-1">
                <i class="fas fa-user-cog me-1"></i>
                Oleh Teknisi:
                ${lpr.teknisi_nama ?? "-"}
            </p>

            <div class="alert alert-danger small py-2 px-3 mt-2 mb-0">

                <strong>
                    Diagnosa:
                </strong>

                <br>

                ${lpr.diagnosa_rusak || "-"}

            </div>

        </div>

    </div>`;
  }
  // 6B. PERBAIKAN SELESAI
  else if (lpr.waktu_selesai || lpr.status_perbaikan === "SELESAI") {

    html += `
    <div class="timeline-item completed">

        <div class="timeline-icon-box">
            <i class="fas fa-check-circle text-success"></i>
        </div>

        <div class="timeline-content">

            <h6 class="fw-bold mb-1">
                Perbaikan Selesai
            </h6>

            <p class="small text-muted mb-2">

                <i class="far fa-clock me-1"></i>

                Waktu Selesai:

                ${formatDateTime(lpr.waktu_selesai)}

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
          <p class="small text-muted mb-1"><i class="far fa-clock me-1"></i> Waktu Selesai: ${lpr.waktu_selesai ? formatDateTime(lpr.waktu_selesai) : "Selesai"}</p>
          <p class="small text-muted mb-0">Terima kasih telah menggunakan layanan kami.</p>
        </div>
      </div>`;
  }

  html += `</div>`;
  return html;
}

// === INISIALISASI DOM LOADED ===
document.addEventListener("DOMContentLoaded", () => {
  // Load Data Awal
  window.loadAntrian("new");

  // Setup Foto Preview Klik
  const modalFotoEl = document.getElementById("detail_modal_foto");
  if (modalFotoEl) {
    modalFotoEl.onclick = function () {
      const zoomImg = document.getElementById("fotoPreviewZoom");
      zoomImg.src = this.src;
      new bootstrap.Modal(document.getElementById("modalFotoPreview")).show();
    };
  }

  // Setup Sidebar
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

  const validasiModalButton = document.getElementById("validasi_modal_button");
  if (validasiModalButton) {
    validasiModalButton.addEventListener("click", window.confirmValidasi);
  }

  const detailValidateButton = document.getElementById("detail_modal_validate_button");
  if (detailValidateButton) {
    detailValidateButton.addEventListener("click", window.confirmValidasi);
  }
});

// Fungsi untuk update angka di kartu
async function refreshStatistikCards() {
  try {

    const response = await fetch(
      `${BASE_URL}admin/get_statistik_antrian`
    );

    const data = await response.json();

    const countPenugasan =
      document.getElementById("count_penugasan");

    const countProses =
      document.getElementById("count_proses");

    const countValidasi =
      document.getElementById("count_validasi");

    const countRiwayat =
      document.getElementById("count_riwayat");

    if (countPenugasan)
      countPenugasan.innerText =
        `${data.new} Laporan`;

    if (countProses)
      countProses.innerText =
        `${data.proses} Laporan`;

    if (countValidasi)
      countValidasi.innerText =
        `${data.validasi_akhir} Laporan`;

    if (countRiwayat)
      countRiwayat.innerText =
        `${data.riwayat} Laporan`;

  } catch (error) {

    console.error(
      "Gagal update statistik antrian:",
      error
    );

  }
}

// Jalankan otomatis setiap 5 detik
setInterval(refreshStatistikCards, 5000);
// Jalankan pertama kali saat halaman dimuat
refreshStatistikCards();