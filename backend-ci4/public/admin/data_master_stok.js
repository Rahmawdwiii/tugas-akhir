// ==========================================
// VARIABLE GLOBAL
// ==========================================
let globalRows = [];
let currentRows = [];
let globalCurrentPage = 1;
const rowsPerPage = 10;
let modalUnitInstance;

// ==========================================
// 1. SAAT HALAMAN DIMUAT (GABUNGAN)
// ==========================================
window.addEventListener("DOMContentLoaded", () => {
  // Init Modal
  const modalEl = document.getElementById("modal_unit");
  if (modalEl) modalUnitInstance = new bootstrap.Modal(modalEl);

  // Listener Reload
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

  // Init Sidebar & Footer
  setupSidebar();

  // Init Pagination & Search
  initPagination();
});

// =======================================================
// PERBAIKAN LOGIKA SIDEBAR & FOOTER
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

// =========================================
// LOGIKA PAGINATION + PENCARIAN
// =========================================
function initPagination() {
  const tableBody = document.getElementById("tableBody");
  const paginationContainer = document.querySelector(
    "#table_paginate ul.pagination"
  );

  if (!tableBody || !paginationContainer) return;

  globalRows = Array.from(
    tableBody.querySelectorAll(":scope > tr:not(.empty-row)")
  );
  currentRows = globalRows;
  globalCurrentPage = 1;
  renderPage(1);
  setupSearch();
}

function setupSearch() {
  const searchInput = document.getElementById("searchInput");
  if (!searchInput) return;

  searchInput.addEventListener("keyup", function () {
    const keyword = this.value.toLowerCase();

    if (keyword === "") {
      currentRows = globalRows;
    } else {
      currentRows = globalRows.filter((row) => {
        return row.innerText.toLowerCase().includes(keyword);
      });
    }
    globalCurrentPage = 1;
    renderPage(1);
  });
}

window.showPage = function (page) {
  const totalRows = currentRows.length;
  const totalPages = Math.ceil(totalRows / rowsPerPage);

  if (page < 1) page = 1;
  if (page > totalPages && totalPages > 0) page = totalPages;
  globalCurrentPage = page;

  globalRows.forEach((row) => (row.style.display = "none"));

  let emptyRow = document.getElementById("row-empty-search");
  if (totalRows === 0) {
    if (!emptyRow) {
      const tbody = document.getElementById("tableBody");
      emptyRow = document.createElement("tr");
      emptyRow.id = "row-empty-search";
      emptyRow.innerHTML = `<td colspan="100%" class="text-center py-4 text-muted">Data tidak ditemukan</td>`;
      tbody.appendChild(emptyRow);
    }
    emptyRow.style.display = "";
  } else {
    if (emptyRow) emptyRow.style.display = "none";
  }

  const start = (page - 1) * rowsPerPage;
  const end = start + rowsPerPage;

  currentRows.slice(start, end).forEach((row) => (row.style.display = ""));

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

  if (totalPages === 0) return;

  const prevDisabled = currentPage === 1 ? "disabled" : "";
  paginationContainer.innerHTML += `
    <li class="page-item ${prevDisabled}">
      <a class="page-link" href="javascript:void(0)" onclick="window.showPage(${
        currentPage - 1
      })">Previous</a>
    </li>`;

  for (let i = 1; i <= totalPages; i++) {
    const activeClass = i === currentPage ? "active" : "";
    paginationContainer.innerHTML += `
      <li class="page-item ${activeClass}">
        <a class="page-link" href="javascript:void(0)" onclick="window.showPage(${i})">${i}</a>
      </li>`;
  }

  const nextDisabled = currentPage === totalPages ? "disabled" : "";
  paginationContainer.innerHTML += `
    <li class="page-item ${nextDisabled}">
      <a class="page-link" href="javascript:void(0)" onclick="window.showPage(${
        currentPage + 1
      })">Next</a>
    </li>`;
}

function renderPage(page) {
  window.showPage(page);
}

// ==========================================
// BAGIAN LOGIKA CRUD STOK
// ==========================================

window.tambahModalStok = function () {
  document.getElementById("form_stok").reset();
  document.getElementById("stok-form-container").innerHTML = "";
  window.tambahStokRow();
  new bootstrap.Modal(document.getElementById("modal_stok")).show();
};

window.tambahStokRow = function () {
  const container = document.getElementById("stok-form-container");
  const rowId = Date.now();

  const html = `
    <div class="row align-items-center mb-2 fade-in" id="row-${rowId}">
      <div class="col-md-3">
        <input type="text" name="kode_alat[]" class="form-control form-control-sm" placeholder="Contoh: TL-01">
      </div>
      <div class="col-md-5">
        <input type="text" name="nama_alat[]" class="form-control form-control-sm" placeholder="Nama Alat / Sparepart" required>
      </div>
      <div class="col-md-2">
        <input type="number" name="jumlah[]" class="form-control form-control-sm" placeholder="0" min="1" required>
      </div>
      <div class="col-md-2">
        <button type="button" class="btn btn-danger btn-sm w-100" onclick="window.hapusStokRow('${rowId}')">
          <i class="fa fa-trash"></i>
        </button>
      </div>
    </div>
  `;
  container.insertAdjacentHTML("beforeend", html);
};

window.hapusStokRow = function (id) {
  const row = document.getElementById("row-" + id);
  const container = document.getElementById("stok-form-container");
  if (container.children.length > 1) {
    row.remove();
  } else {
    Swal.fire({
      icon: "warning",
      title: "Perhatian",
      text: "Minimal satu data harus diisi.",
    });
  }
};

window.saveStok = function () {
  const form = document.getElementById("form_stok");

  if (!form.checkValidity()) {
    form.reportValidity();
    return;
  }

  const formData = new FormData(form);
  const url = `${BASE_URL}admin/simpan_stok`;

  Swal.fire({
    title: "Menyimpan Data...",
    text: "Mohon tunggu sebentar",
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading(),
  });

  fetch(url, {
    method: "POST",
    body: formData,
    headers: { "X-Requested-With": "XMLHttpRequest" },
  })
    .then((response) => {
      if (!response.ok)
        throw new Error("Server merespon dengan status: " + response.status);
      return response.json();
    })
    .then((data) => {
      if (data.status === "success") {
        Swal.fire({
          icon: "success",
          title: "Berhasil!",
          text: data.message,
          timer: 1500,
          showConfirmButton: false,
        }).then(() => location.reload());
      } else {
        Swal.fire({ icon: "error", title: "Gagal", text: data.message });
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      Swal.fire({
        icon: "error",
        title: "Terjadi Kesalahan",
        text: "Gagal menghubungi server. Cek koneksi atau log console.",
      });
    });
};

window.hapusStok = function (id) {
  Swal.fire({
    title: "Yakin hapus data?",
    text: "Data yang dihapus tidak bisa dikembalikan!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#6c757d",
    confirmButtonText: "Ya, Hapus!",
    cancelButtonText: "Batal",
  }).then((result) => {
    if (result.isConfirmed) {
      Swal.fire({
        title: "Sedang Memproses...",
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading(),
      });

      const url = `${BASE_URL}admin/hapus_stok/${id}`;
      const formData = new FormData();
      formData.append(CSRF_TOKEN, CSRF_HASH);

      fetch(url, {
        method: "POST",
        body: formData,
        headers: { "X-Requested-With": "XMLHttpRequest" },
      })
        .then((response) => {
          if (!response.ok) throw new Error(response.statusText);
          return response.json();
        })
        .then((data) => {
          if (data.status === "success") {
            Swal.fire({
              icon: "success",
              title: "Berhasil!",
              text: data.message,
              timer: 1500,
              showConfirmButton: false,
            }).then(() => location.reload());
          } else {
            Swal.fire("Gagal", data.message, "error");
          }
        })
        .catch((error) => {
          console.error(error);
          Swal.fire(
            "Error",
            "Gagal menghapus (Cek Console/CSRF Token).",
            "error"
          );
        });
    }
  });
};

window.editStok = function (id) {
  document.getElementById("formStok").reset();
  const url = `${BASE_URL}admin/get_stok/${id}`;

  fetch(url, {
    method: "GET",
    headers: { "X-Requested-With": "XMLHttpRequest" },
  })
    .then((response) => {
      if (!response.ok) throw new Error(response.statusText);
      return response.json();
    })
    .then((data) => {
      if (data) {
        document.getElementById("edit_id_stok").value = data.id_stok;

        // PERBAIKAN: Langsung ambil data.nomor_inventaris, HAPUS || data.kode_barang
        document.getElementById("nomor_inventaris").value =
          data.nomor_inventaris || "-";

        // PERBAIKAN: Langsung ambil data.nama_barang
        document.getElementById("nama_barang").value =
          data.nama_barang || "Tanpa Nama";

        document.getElementById("jumlah").value = data.jumlah;

        document.getElementById("modalStokLabel").innerText = "Edit Stok";
        const modal = new bootstrap.Modal(document.getElementById("modalStok"));
        modal.show();
      }
    })
    .catch((error) => {
      Swal.fire({
        icon: "error",
        title: "Gagal",
        text: "Gagal mengambil data stok.",
      });
    });
};

window.updateStok = function () {
  const form = document.getElementById("formStok");

  if (!form.checkValidity()) {
    form.reportValidity();
    return;
  }

  const formData = new FormData(form);
  const url = `${BASE_URL}admin/update_stok`;

  Swal.fire({
    title: "Menyimpan Perubahan...",
    text: "Mohon tunggu sebentar",
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading(),
  });

  fetch(url, {
    method: "POST",
    body: formData,
    headers: { "X-Requested-With": "XMLHttpRequest" },
  })
    .then((response) => {
      if (!response.ok) throw new Error("Server error: " + response.status);
      return response.json();
    })
    .then((data) => {
      if (data.status === "success") {
        const modalElement = document.getElementById("modalStok");
        const modalInstance = bootstrap.Modal.getInstance(modalElement);
        if (modalInstance) modalInstance.hide();

        Swal.fire({
          icon: "success",
          title: "Berhasil!",
          text: data.message,
          timer: 1500,
          showConfirmButton: false,
        }).then(() => location.reload());
      } else {
        Swal.fire({ icon: "error", title: "Gagal", text: data.message });
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      Swal.fire({
        icon: "error",
        title: "Terjadi Kesalahan",
        text: "Gagal menghubungi server. Silakan coba lagi.",
      });
    });
};

// =======================================================
// FUNGSI EXPORT EXCEL DATA STOK (RAPAT & HIJAU)
// =======================================================
window.exportToExcel = async function () {
  const btn = document.querySelector(
    'button[onclick="window.exportToExcel()"]'
  );
  const originalText = btn ? btn.innerHTML : "Export";

  if (btn) {
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
  }

  try {
    const workbook = new ExcelJS.Workbook();
    const worksheet = workbook.addWorksheet("Data Stok");

    worksheet.columns = [
      { header: "No", key: "no", width: 8 },
      { header: "Nomor Inventaris", key: "kode", width: 25 },
      { header: "Nama Alat", key: "nama", width: 40 },
      { header: "Jumlah Stok", key: "jumlah", width: 15 },
    ];

    worksheet.spliceRows(1, 0, []);
    worksheet.mergeCells("A1:D1");

    const titleRow = worksheet.getCell("A1");
    titleRow.value = "DATA STOK LENGKAP - UPT TP3A";
    titleRow.font = {
      name: "Arial",
      size: 16,
      bold: true,
      color: { argb: "FFFFFFFF" },
    };
    titleRow.alignment = { vertical: "middle", horizontal: "center" };
    titleRow.fill = {
      type: "pattern",
      pattern: "solid",
      fgColor: { argb: "FF198754" },
    };
    titleRow.border = { bottom: { style: "medium" } };

    for (let i = 1; i <= 4; i++) {
      const cell = worksheet.getRow(2).getCell(i);
      cell.font = { bold: true, color: { argb: "FFFFFFFF" }, size: 12 };
      cell.fill = {
        type: "pattern",
        pattern: "solid",
        fgColor: { argb: "FF198754" },
      };
      cell.alignment = { vertical: "middle", horizontal: "center" };
      cell.border = {
        top: { style: "thin" },
        left: { style: "thin" },
        bottom: { style: "thin" },
        right: { style: "thin" },
      };
    }

    const rowsToExport =
      typeof globalRows !== "undefined" && globalRows.length > 0
        ? globalRows
        : document.querySelectorAll("#table tbody tr:not(.empty-row)");

    if (rowsToExport.length === 0) throw new Error("Tidak ada data tabel.");

    let nomorUrut = 1;
    const statsNama = [];
    const statsJumlah = [];

    rowsToExport.forEach((row) => {
      const cells = row.querySelectorAll("td");
      let kodeVal, namaVal, jumlahVal;

      if (cells.length >= 6) {
        kodeVal = cells[2].innerText.trim();
        namaVal = cells[3].innerText.trim();
        jumlahVal = parseInt(cells[4].innerText.trim()) || 0;
      } else if (cells.length >= 4) {
        kodeVal = cells[1].innerText.trim();
        namaVal = cells[2].innerText.trim();
        jumlahVal = parseInt(cells[3].innerText.trim()) || 0;
      }

      if (kodeVal || namaVal) {
        const newRow = worksheet.addRow({
          no: nomorUrut++,
          kode: kodeVal,
          nama: namaVal,
          jumlah: jumlahVal,
        });
        statsNama.push(namaVal);
        statsJumlah.push(jumlahVal);

        newRow.eachCell((cell, colNumber) => {
          cell.border = {
            top: { style: "thin" },
            left: { style: "thin" },
            bottom: { style: "thin" },
            right: { style: "thin" },
          };
          cell.alignment = {
            vertical: "middle",
            horizontal: colNumber === 3 ? "left" : "center",
          };
        });

        if (jumlahVal < 5) {
          newRow.getCell("jumlah").font = {
            color: { argb: "FFFF0000" },
            bold: true,
          };
        }
      }
    });

    const top10 = statsNama
      .map((name, i) => ({ name, count: statsJumlah[i] }))
      .sort((a, b) => b.count - a.count)
      .slice(0, 10);

    if (top10.length > 0) {
      const imgChart = await generateGenericChart({
        type: "bar",
        labels: top10.map((item) => item.name),
        data: top10.map((item) => item.count),
        colors: "#0d6efd",
        title: "TOP 10 STOK TERBANYAK",
      });

      const imageId = workbook.addImage({ base64: imgChart, extension: "png" });

      worksheet.mergeCells("F2:L2");
      const chartTitle = worksheet.getCell("F2");
      chartTitle.value = "VISUALISASI GRAFIK";
      chartTitle.font = { bold: true, size: 12, color: { argb: "FFFFFFFF" } };
      chartTitle.fill = {
        type: "pattern",
        pattern: "solid",
        fgColor: { argb: "FF198754" },
      };
      chartTitle.alignment = { vertical: "middle", horizontal: "center" };
      chartTitle.border = {
        top: { style: "thin" },
        left: { style: "thin" },
        bottom: { style: "thin" },
        right: { style: "thin" },
      };

      worksheet.addImage(imageId, "F3:L23");
    }

    const buffer = await workbook.xlsx.writeBuffer();
    const today = new Date().toISOString().slice(0, 10);
    saveAs(new Blob([buffer]), `Data_Stok_${today}.xlsx`);
  } catch (error) {
    console.error("Export Error:", error);
    Swal.fire({
      icon: "error",
      title: "Gagal",
      text: "Gagal export: " + error.message,
    });
  } finally {
    if (btn) {
      btn.disabled = false;
      btn.innerHTML = originalText;
    }
  }
};

function generateGenericChart(config) {
  return new Promise((resolve) => {
    let canvas = document.getElementById("tempChartCanvas");
    if (!canvas) {
      canvas = document.createElement("canvas");
      canvas.id = "tempChartCanvas";
      canvas.style.display = "none";
      document.body.appendChild(canvas);
    }
    const ctx = canvas.getContext("2d");
    if (window.tempChartInstance) window.tempChartInstance.destroy();

    window.tempChartInstance = new Chart(ctx, {
      type: config.type,
      data: {
        labels: config.labels,
        datasets: [
          {
            label: "Jumlah",
            data: config.data,
            backgroundColor: config.colors,
            borderWidth: 1,
          },
        ],
      },
      options: {
        responsive: false,
        animation: false,
        plugins: {
          legend: { display: false },
          title: { display: true, text: config.title, font: { size: 14 } },
        },
        scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
      },
    });
    setTimeout(() => {
      resolve(canvas.toDataURL("image/png"));
    }, 300);
  });
}
