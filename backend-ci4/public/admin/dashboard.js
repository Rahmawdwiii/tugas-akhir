// --- VARIABEL GLOBAL ---
let modalUnitInstance;
let globalRows = [];
let globalCurrentPage = 1;
const rowsPerPage = 10;

// =======================================================
// === EVENT DOM CONTENT LOADED ===
// =======================================================
window.addEventListener("DOMContentLoaded", () => {
  // 1. Inisialisasi Modal
  const modalEl = document.getElementById("modal_unit");
  if (modalEl) modalUnitInstance = new bootstrap.Modal(modalEl);

  // 2. Inisialisasi Pagination
  // initPagination();

  // 3. Listener Reload
  const btnReload = document.getElementById("btnReload");
  if (btnReload) {
    btnReload.addEventListener("click", function () {
      const icon = this.querySelector("i");
      if (icon) {
        icon.classList.remove("fa-undo");
        icon.classList.add("fa-spinner", "fa-spin");
      }
      location.reload();
    });
  }

  // 4. Inisialisasi Sidebar & Footer
  setupSidebar();

  // 5. Inisialisasi Listener Copy
  setupCopyButton();
  // Mulai polling status teknisi (jika elemen dashboard ada)
  try {
    fetchTeknisiStatusAdmin();
    setInterval(fetchTeknisiStatusAdmin, 10000);
  } catch (e) {
    console.debug('fetchTeknisiStatusAdmin not available', e);
  }
});

// =======================================================
// POLLING STATUS TEKNISI UNTUK DASHBOARD ADMIN
// Jika elemen DOM terkait tidak ada, fungsi ini tidak melakukan apa-apa.
// =======================================================
async function fetchTeknisiStatusAdmin() {
  if (typeof BASE_URL === 'undefined') return;
  try {
    const res = await fetch(`${BASE_URL}admin/get_teknisi_json`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    if (!res.ok) return;
    const data = await res.json();
    if (data.status !== 'success') return;

    // Update admin upt status jika ada
    const statusEl = document.getElementById('status_admin_upt');
    if (statusEl) {
      const admin = data.admin_upt;
      if (admin && (admin.is_online == 1 || admin.is_online === '1')) {
        statusEl.innerHTML = `<span class="badge rounded-pill bg-success small"><i class="fas fa-circle me-1" style="font-size:8px"></i> Online</span>`;
      } else if (admin && admin.last_active) {
        const minutes = Math.floor((Date.now() - Number(new Date(admin.last_active))) / 60000);
        statusEl.innerHTML = `<span class="badge rounded-pill bg-danger small"><i class="fas fa-clock me-1"></i> ${minutes}m lalu</span>`;
      } else {
        statusEl.innerHTML = `<span class="badge rounded-pill bg-secondary small">Offline</span>`;
      }
    }

    // Update teknisi list container if present
    const container = document.getElementById('teknisi_list_container');
    if (container) {
      const list = data.teknisi || [];
      if (!Array.isArray(list) || list.length === 0) {
        container.innerHTML = '<small class="text-muted">Belum ada data teknisi.</small>';
      } else {
        container.innerHTML = list.map(tek => {
          const name = tek.nama || tek.username || 'Unknown';
          const online = (tek.is_online == 1 || tek.is_online === '1');
          const badge = online
            ? `<span class="badge bg-success bg-opacity-10 text-success border border-success px-2 rounded-pill" style="font-size:0.7rem;">Online</span>`
            : `<span class="badge bg-light text-muted border px-2 rounded-pill" style="font-size:0.7rem;">Offline</span>`;
          return `<div class="d-flex justify-content-between align-items-center"><div class="d-flex align-items-center"><i class="fas fa-user-cog text-primary me-2"></i><span class="small fw-bold">${escapeHtml(name)}</span></div>${badge}</div>`;
        }).join('');
      }
    }
  } catch (err) {
    console.debug('Gagal fetch teknisi status admin', err);
  }
}

function escapeHtml(str) {
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

// =======================================================
// === FUNGSI PAGINATION ===
// =======================================================
function initPagination() {
  const tableBody = document.getElementById("tableBody");
  const paginationContainer = document.querySelector(
    "#table_paginate ul.pagination"
  );

  if (!tableBody) {
    console.error("Error: <tbody id='tableBody'> tidak ditemukan!");
    return;
  }
  if (!paginationContainer) {
    console.error("Error: Container pagination tidak ditemukan!");
    return;
  }

  globalRows = Array.from(
    tableBody.querySelectorAll(":scope > tr:not(.empty-row)")
  );
  globalCurrentPage = 1;
  renderPage(globalCurrentPage);
}

window.showPage = function (page) {
  const totalRows = globalRows.length;
  const totalPages = Math.ceil(totalRows / rowsPerPage);

  if (page < 1) page = 1;
  if (page > totalPages && totalPages > 0) page = totalPages;

  globalCurrentPage = page;

  globalRows.forEach((row) => (row.style.display = "none"));

  const start = (page - 1) * rowsPerPage;
  const end = start + rowsPerPage;

  globalRows.slice(start, end).forEach((row) => (row.style.display = ""));

  const tableInfo = document.getElementById("table_info");
  if (tableInfo) {
    const startInfo = totalRows === 0 ? 0 : start + 1;
    const endInfo = Math.min(end, totalRows);
    tableInfo.innerText = `Showing ${startInfo} to ${endInfo} of ${totalRows} entries`;
  }

  updatePaginationUI(totalPages, page);
};

function updatePaginationUI(totalPages, currentPage) {
  const paginationContainer = document.querySelector(
    "#table_paginate ul.pagination"
  );
  if (!paginationContainer) return;

  paginationContainer.innerHTML = "";

  const prevDisabled = currentPage === 1 ? "disabled" : "";
  paginationContainer.innerHTML += `
    <li class="page-item ${prevDisabled}">
      <a class="page-link" href="javascript:void(0)" onclick="window.showPage(${currentPage - 1
    })">Previous</a>
    </li>`;

  for (let i = 1; i <= totalPages; i++) {
    const activeClass = i === currentPage ? "active" : "";
    paginationContainer.innerHTML += `
      <li class="page-item ${activeClass}">
        <a class="page-link" href="javascript:void(0)" onclick="window.showPage(${i})">${i}</a>
      </li>`;
  }

  const nextDisabled =
    currentPage === totalPages || totalPages === 0 ? "disabled" : "";
  paginationContainer.innerHTML += `
    <li class="page-item ${nextDisabled}">
      <a class="page-link" href="javascript:void(0)" onclick="window.showPage(${currentPage + 1
    })">Next</a>
    </li>`;
}

function renderPage(page) {
  window.showPage(page);
}

// =======================================================
// === PERBAIKAN LOGIKA SIDEBAR & FOOTER ===
// =======================================================
function setupSidebar() {
  const toggleSidebar = document.getElementById("toggleSidebar");
  const sidebar = document.getElementById("sidebar");
  const content = document.querySelector(".content");
  const footerContent = document.querySelector(".footer-content");
  const links = document.querySelectorAll("#sidebar .nav-link");

  if (toggleSidebar) {
    toggleSidebar.addEventListener("click", () => {
      if (sidebar) sidebar.classList.toggle("collapsed");
      if (content) content.classList.toggle("full");
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

// =======================================================
// === LISTENER TOMBOL COPY (VERSI SMART FILTER) ===
// =======================================================
function setupCopyButton() {
  const btnCopy = document.getElementById("btnCopy");
  if (btnCopy) {
    btnCopy.addEventListener("click", function () {
      const tableHead = document.querySelector("#table thead tr");
      let clipboardText = "";
      let excludedIndices = [];

      if (tableHead) {
        const headers = Array.from(tableHead.querySelectorAll("th"));
        const validHeaders = headers
          .filter((th, index) => {
            const text = th.innerText.trim().toUpperCase();
            if (text === "ID ALAT" || text === "AKSI") {
              excludedIndices.push(index);
              return false;
            }
            return true;
          })
          .map((th) => th.innerText.trim());

        clipboardText += validHeaders.join("\t") + "\n";
      }

      if (globalRows.length > 0) {
        globalRows.forEach((row) => {
          const cells = Array.from(row.querySelectorAll("td"));
          const rowData = cells
            .filter((td, index) => {
              return !excludedIndices.includes(index);
            })
            .map((td) => {
              return td.innerText.replace(/(\r\n|\n|\r)/gm, " ").trim();
            });
          clipboardText += rowData.join("\t") + "\n";
        });

        navigator.clipboard
          .writeText(clipboardText)
          .then(() => {
            const icon = btnCopy.querySelector("i");
            const originalClass = icon.className;
            icon.className = "fas fa-check text-success";

            setTimeout(() => {
              icon.className = originalClass;
            }, 2000);
          })
          .catch((err) => {
            console.error("Gagal menyalin:", err);
            Swal.fire({
              icon: "error",
              title: "Gagal",
              text: "Browser tidak mengizinkan copy otomatis.",
            });
          });
      } else {
        Swal.fire({
          icon: "warning",
          title: "Perhatian",
          text: "Tidak ada data untuk disalin.",
        });
      }
    });
  }
}

// =======================================================
// === FUNGSI EXPORT EXCEL (VERTIKAL TENGAH / MIDDLE) ===
// =======================================================
window.exportToExcel = async function () {
  const btn = document.querySelector(".btn-outline-success");
  const originalText = btn.innerHTML;

  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Proses Export...';

  try {
    const workbook = new window.ExcelJS.Workbook();
    const worksheet = workbook.addWorksheet("Laporan Selesai");

    const styleCenterMiddle = {
      alignment: { horizontal: "center", vertical: "middle", wrapText: true },
    };
    const styleLeftMiddle = {
      alignment: { horizontal: "left", vertical: "middle", wrapText: true },
    };

    worksheet.columns = [
      { header: "No", key: "no", width: 5, style: styleCenterMiddle },
      {
        header: "Nomor Laporan",
        key: "nomor_laporan",
        width: 25,
        style: styleCenterMiddle,
      },
      {
        header: "Tanggal Laporan",
        key: "tgl_laporan",
        width: 20,
        style: styleCenterMiddle,
      },
      {
        header: "Nama Alat",
        key: "nama_alat",
        width: 25,
        style: styleCenterMiddle,
      },
      {
        header: "Nomor Inventaris",
        key: "no_inventaris",
        width: 25,
        style: styleCenterMiddle,
      },
      {
        header: "Lokasi Alat",
        key: "lokasi",
        width: 35,
        style: styleLeftMiddle,
      },
      {
        header: "Jurusan / Unit",
        key: "jurusan",
        width: 25,
        style: styleLeftMiddle,
      },
    ];

    worksheet.spliceRows(1, 0, []);
    worksheet.mergeCells("A1:G1");
    const titleCell = worksheet.getCell("A1");
    titleCell.value = "DATA REKAPITULASI LAPORAN KERUSAKAN SELESAI";
    titleCell.font = { name: "Arial", size: 14, bold: true };
    titleCell.alignment = { horizontal: "center", vertical: "middle" };
    worksheet.getRow(1).height = 30;

    const headerRow = worksheet.getRow(2);
    headerRow.height = 30;
    for (let i = 1; i <= 7; i++) {
      const cell = headerRow.getCell(i);
      cell.fill = {
        type: "pattern",
        pattern: "solid",
        fgColor: { argb: "FF0d6efd" },
      };
      cell.font = {
        name: "Calibri",
        size: 11,
        bold: true,
        color: { argb: "FFFFFFFF" },
      };
      cell.alignment = { horizontal: "center", vertical: "middle" };
      cell.border = {
        top: { style: "thin", color: { argb: "FFFFFFFF" } },
        left: { style: "thin", color: { argb: "FFFFFFFF" } },
        bottom: { style: "thin", color: { argb: "FFFFFFFF" } },
        right: { style: "thin", color: { argb: "FFFFFFFF" } },
      };
    }

    const rowsToExport =
      typeof globalRows !== "undefined" && globalRows.length > 0
        ? globalRows
        : document.querySelectorAll("tbody tr:not(.empty-row)");

    let counter = 1;
    const statsAlat = {};
    const statsJurusan = {};

    rowsToExport.forEach((row) => {
      const cells = row.querySelectorAll("td");
      if (cells.length < 1) return;

      const nomorLaporan = cells[0]?.innerText.trim() || "-";
      const tglLaporan = cells[1]?.innerText.trim() || "-";
      const namaAlat = cells[2]?.innerText.trim() || "-";
      const noInventaris = cells[3]?.innerText.trim() || "-";
      const lokasi = cells[4]?.innerText.trim() || "-";
      const jurusan = cells[5]?.innerText.trim() || "-";

      worksheet.addRow({
        no: counter++,
        nomor_laporan: nomorLaporan,
        tgl_laporan: tglLaporan,
        nama_alat: namaAlat,
        no_inventaris: noInventaris,
        lokasi: lokasi,
        jurusan: jurusan,
      });

      statsAlat[namaAlat] = (statsAlat[namaAlat] || 0) + 1;
      statsJurusan[jurusan] = (statsJurusan[jurusan] || 0) + 1;
    });

    worksheet.eachRow((row, rowNumber) => {
      if (rowNumber > 2) {
        row.eachCell((cell) => {
          cell.border = {
            top: { style: "thin" },
            left: { style: "thin" },
            bottom: { style: "thin" },
            right: { style: "thin" },
          };
        });
      }
    });

    // SHEET 2: GRAFIK
    const sheetGrafik = workbook.addWorksheet("Dashboard Statistik");
    sheetGrafik.mergeCells("B2:E2");
    sheetGrafik.getCell("B2").value = "STATISTIK LAPORAN SELESAI";
    sheetGrafik.getCell("B2").font = { size: 14, bold: true };

    sheetGrafik.getCell("B4").value = "Total Laporan Selesai";
    sheetGrafik.getCell("C4").value = counter - 1;
    sheetGrafik.getCell("B4").font = { bold: true };

    const sortedAlat = Object.entries(statsAlat)
      .sort((a, b) => b[1] - a[1])
      .slice(0, 10);
    const imgAlat = await generateGenericChart({
      type: "bar",
      labels: sortedAlat.map((i) => i[0]),
      data: sortedAlat.map((i) => i[1]),
      colors: "#36a2eb",
      title: "Top 10 Alat Selesai",
    });

    const sortedJurusan = Object.entries(statsJurusan)
      .sort((a, b) => b[1] - a[1])
      .slice(0, 5);
    const imgJurusan = await generateGenericChart({
      type: "pie",
      labels: sortedJurusan.map((i) => i[0]),
      data: sortedJurusan.map((i) => i[1]),
      colors: ["#ff6384", "#36a2eb", "#ffce56", "#4bc0c0", "#9966ff"],
      title: "Perbaikan per Jurusan",
    });

    const addChartToSheet = async (base64, col, row, width, height) => {
      const res = await fetch(base64);
      const buff = await res.arrayBuffer();
      const id = workbook.addImage({ buffer: buff, extension: "png" });
      sheetGrafik.addImage(id, {
        tl: { col: col, row: row },
        br: { col: col + width, row: row + height },
      });
    };

    await addChartToSheet(imgAlat, 1, 6, 10, 12);
    await addChartToSheet(imgJurusan, 12, 6, 8, 12);

    const buffer = await workbook.xlsx.writeBuffer();
    const fileName = `Laporan_Selesai_${new window.Date()
      .toISOString()
      .slice(0, 10)}.xlsx`;
    window.saveAs(new window.Blob([buffer]), fileName);
  } catch (error) {
    console.error(error);
    Swal.fire("Gagal", "Terjadi kesalahan saat export excel.", "error");
  } finally {
    btn.disabled = false;
    btn.innerHTML = originalText;
  }
};

// === HELPER: GENERIC CHART GENERATOR ===
function generateGenericChart(config) {
  return new window.Promise((resolve) => {
    const canvas = document.getElementById("tempChartCanvas");
    if (!canvas) {
      console.error("Canvas #tempChartCanvas tidak ditemukan!");
      resolve(null);
      return;
    }
    const ctx = canvas.getContext("2d");

    if (window.tempChartInstance) {
      window.tempChartInstance.destroy();
    }

    let bgColors = config.colors;
    if (!Array.isArray(config.colors)) {
      bgColors = Array(config.data.length).fill(config.colors);
    }

    window.tempChartInstance = new Chart(ctx, {
      type: config.type,
      data: {
        labels: config.labels,
        datasets: [
          {
            label: "Jumlah",
            data: config.data,
            backgroundColor: bgColors,
            borderWidth: 1,
          },
        ],
      },
      options: {
        responsive: false,
        animation: false,
        indexAxis: config.indexAxis || "x",
        plugins: {
          title: { display: true, text: config.title, font: { size: 14 } },
          legend: {
            display: config.type === "doughnut" || config.type === "pie",
            position: "bottom",
          },
          datalabels: { display: true, color: "black" },
        },
      },
    });

    setTimeout(() => {
      resolve(canvas.toDataURL("image/png"));
    }, 300);
  });
}

// =======================================================
// === FUNGSI EXPORT PDF KHUSUS LAPORAN SELESAI ===
// =======================================================
window.exportToPDFSelesai = async function (btn) {
  const originalText = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Proses...';

  try {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF("l", "mm", [215, 330]);

    const pageWidth = doc.internal.pageSize.getWidth();
    doc.setFontSize(16);
    doc.setFont("helvetica", "bold");
    doc.text("DATA REKAPITULASI LAPORAN KERUSAKAN SELESAI", pageWidth / 2, 15, {
      align: "center",
    });

    doc.setFontSize(10);
    doc.setFont("helvetica", "normal");
    doc.text(
      `Dicetak pada: ${new window.Date().toLocaleString("id-ID")}`,
      pageWidth / 2,
      22,
      { align: "center" }
    );

    const dataRows =
      typeof globalRows !== "undefined" && globalRows.length > 0
        ? globalRows
        : document.querySelectorAll("tbody tr:not(.empty-row)");

    const tableBody = [];
    let counter = 1;

    for (let i = 0; i < dataRows.length; i++) {
      const row = dataRows[i];
      const cells = row.querySelectorAll("td");
      if (cells.length < 1) continue;

      const nomorLaporan = cells[0]?.innerText.trim() || "-";
      const tglLaporan = cells[1]?.innerText.trim() || "-";
      const namaAlat = cells[2]?.innerText.trim() || "-";
      const noInventaris = cells[3]?.innerText.trim() || "-";
      const lokasi = cells[4]?.innerText.trim() || "-";
      const jurusan = cells[5]?.innerText.trim() || "-";

      tableBody.push([
        counter++,
        nomorLaporan,
        tglLaporan,
        namaAlat,
        noInventaris,
        lokasi,
        jurusan,
      ]);
    }

    doc.autoTable({
      head: [
        [
          "No",
          "Nomor Laporan",
          "Tanggal",
          "Nama Alat",
          "No. Inventaris",
          "Lokasi Alat",
          "Jurusan / Unit",
        ],
      ],
      body: tableBody,
      startY: 30,
      theme: "grid",
      styles: {
        fontSize: 10,
        textColor: [0, 0, 0],
        lineColor: [0, 0, 0],
        lineWidth: 0.1,
        cellPadding: 3,
        valign: "middle",
        overflow: "linebreak",
      },
      headStyles: {
        fillColor: [13, 110, 253],
        textColor: [255, 255, 255],
        fontStyle: "bold",
        halign: "center",
        lineWidth: 0.1,
        lineColor: [0, 0, 0],
      },
      columnStyles: {
        0: { halign: "center", cellWidth: 15 },
        1: { halign: "center", cellWidth: 40 },
        2: { halign: "center", cellWidth: 30 },
        3: { halign: "center", cellWidth: 40 },
        4: { halign: "center", cellWidth: 35 },
        5: { halign: "left", cellWidth: 70 },
        6: { halign: "center", cellWidth: "auto" },
      },
      didDrawPage: function (data) {
        let str = "" + doc.internal.getNumberOfPages();
        doc.setFontSize(9);
        doc.text(
          str,
          data.settings.margin.left,
          doc.internal.pageSize.height - 10
        );
      },
    });

    const fileName = `Laporan_Selesai_${new window.Date()
      .toISOString()
      .slice(0, 10)}.pdf`;
    doc.save(fileName);
  } catch (error) {
    console.error(error);
    Swal.fire("Gagal", "Terjadi kesalahan saat export PDF.", "error");
  } finally {
    btn.disabled = false;
    btn.innerHTML = originalText;
  }
};

// =====================================
// === STATUS ONLINE (ADMIN) ===
// =====================================
window.setOnlineStatus = async function (checkbox) {
  const label = document.getElementById("labelStatus");
  const isOnline = checkbox.checked ? 1 : 0;

  // Ubah tampilan UI sementara agar terasa responsif
  if (isOnline === 1) {
    label.innerText = "ONLINE";
    label.classList.remove("text-muted");
    label.classList.add("text-success");
  } else {
    label.innerText = "OFFLINE";
    label.classList.remove("text-success");
    label.classList.add("text-muted");
  }

  checkbox.disabled = true;
  const formData = new window.FormData();
  formData.append("is_online", isOnline);
  formData.append(CSRF_TOKEN_NAME, CSRF_HASH);

  try {
    const response = await fetch(`${BASE_URL}admin/update_status_online`, {
      method: "POST",
      body: formData,
      headers: { "X-Requested-With": "XMLHttpRequest" },
    });
    const data = await response.json();

    checkbox.disabled = false;
    if (data.status !== "success") {
      checkbox.checked = !checkbox.checked;
      label.innerText = checkbox.checked ? "ONLINE" : "OFFLINE";
      label.className = checkbox.checked ? "small fw-bold text-success" : "small fw-bold text-muted";
      Swal.fire("Gagal", "Tidak dapat mengubah status: " + data.message, "error");
      return;
    }

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

    if (typeof fetchTeknisiStatusAdmin === "function") {
      fetchTeknisiStatusAdmin();
    }
  } catch (error) {
    checkbox.disabled = false;
    checkbox.checked = !checkbox.checked;
    label.innerText = checkbox.checked ? "ONLINE" : "OFFLINE";
    label.className = checkbox.checked ? "small fw-bold text-success" : "small fw-bold text-muted";
    console.error("Error Status:", error);
    Swal.fire("Error", "Terjadi kesalahan jaringan.", "error");
  }
};

// ======================================================
// 2. MENGGAMBAR GRAFIK (REALTIME DARI DATABASE)
// ======================================================

// Kita buat variabel penampung di luar agar saat data di-update,
// grafik yang lama bisa dihancurkan (destroy) dulu biar tidak error tumpang-tindih.
let chartJurusan, chartSeverity, chartAlat, chartStatus;

function loadDashboardData() {
  // Panggil data JSON dari Controller PHP yang kita buat
  // Pastikan variabel BASE_URL sudah ada di HTML Bung
  fetch(BASE_URL + "admin/get_dashboard_admin", {
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
                backgroundColor: ["#198754", "#0dcaf0", "#ffc107", "#dc3545"],
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

function fetchStatistikBarang() {
  fetch(`${BASE_URL}admin/get_statistik_barang`)
    .then(response => response.json())
    .then(data => {
      // Pastikan ID ini ada di HTML Anda
      const pbAktif = document.getElementById('pb_aktif');
      const pbDipinjam = document.getElementById('pb_dipinjam');
      const pbRusak = document.getElementById('pb_rusak');

      if (pbAktif) {
        pbAktif.style.width = data.aktif + '%';
        pbAktif.innerText = data.aktif + '% Barang Aktif';
      }
      if (pbDipinjam) {
        pbDipinjam.style.width = data.dipinjam + '%';
        pbDipinjam.innerText = data.dipinjam + '% Dipinjam';
      }
      if (pbRusak) {
        pbRusak.style.width = data.rusak + '%';
        pbRusak.innerText = data.rusak + '% Rusak';
      }
    })
    .catch(err => console.error("Gagal update statistik:", err));
}

// Jalankan fungsi setiap 5 detik
setInterval(fetchStatistikBarang, 5000);

// Panggil pertama kali saat halaman dibuka
fetchStatistikBarang();