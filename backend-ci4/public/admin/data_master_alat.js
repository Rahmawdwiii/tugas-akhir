// ==========================================
// BAGIAN 1: INISIALISASI HALAMAN
// ==========================================
let globalRows = [];
let globalCurrentPage = 1;
const rowsPerPage = 10;
let currentRows = []; // Untuk filter pencarian

window.addEventListener("DOMContentLoaded", () => {
  // 1. Jalankan Pagination
  initPagination();

  // 2. Listener Reload
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

  // 3. Logika Sidebar & Footer
  setupSidebar();

  // 4. Listener Copy
  setupCopyButton();
});

function setupSidebar() {
  const toggleSidebar = document.getElementById("toggleSidebar");
  const sidebar = document.getElementById("sidebar");
  const content = document.querySelector(".content");
  const footerContent = document.querySelector(".footer-content");

  if (toggleSidebar) {
    toggleSidebar.addEventListener("click", () => {
      if (sidebar) sidebar.classList.toggle("collapsed");
      if (content) content.classList.toggle("full");
      if (footerContent) {
        footerContent.classList.toggle("full");
      }
    });
  }

  const links = document.querySelectorAll("#sidebar .nav-link");
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
// BAGIAN 2: LOGIKA CRUD ALAT (TAMBAH, EDIT, HAPUS)
// ==========================================

window.tambahModalAlat = function () {
  const container = document.getElementById("alat-form-container");
  container.innerHTML = "";
  window.tambahAlatRow();

  const modalEl = document.getElementById("modal_alat");
  const modal = new bootstrap.Modal(modalEl);
  modal.show();
};

window.tambahAlatRow = function () {
  const container = document.getElementById("alat-form-container");
  const rowId = new window.Date().getTime(); // Hapus warning VS Code

  const html = `
    <div class="row mb-2 align-items-center" id="row-${rowId}">
        <div class="col-md-5">
            <input type="text" class="form-control" name="nomor_inventaris[]" placeholder="Contoh: INV-2025-001" required>
        </div>
        <div class="col-md-6">
            <input type="text" class="form-control" name="nama_alat[]" placeholder="Contoh: Mikroskop" required>
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-danger btn-sm" onclick="window.hapusAlatRow('${rowId}')">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </div>`;

  container.insertAdjacentHTML("beforeend", html);
};

window.hapusAlatRow = function (id) {
  const row = document.getElementById("row-" + id);
  const container = document.getElementById("alat-form-container");

  if (container.children.length > 1) {
    row.remove();
  } else {
    Swal.fire("Info", "Minimal harus ada satu data alat.", "info");
  }
};

window.saveAlat = function () {
  const form = document.getElementById("form_alat");

  if (!form.checkValidity()) {
    form.reportValidity();
    return;
  }

  const formData = new window.FormData(form);
  const url = `${BASE_URL}admin/simpan_alat`;

  Swal.fire({
    title: "Menyimpan Data...",
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading(),
  });

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
        const modalEl = document.getElementById("modal_alat");
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();

        Swal.fire({
          icon: "success",
          title: "Berhasil",
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
        "Terjadi kesalahan sistem (Cek Console/Routes).",
        "error"
      );
    });
};

window.editAlat = function (id) {
  document.getElementById("form_edit_alat").reset();
  const url = `${BASE_URL}admin/get_alat/${id}`;

  fetch(url, {
    method: "GET",
    headers: { "X-Requested-With": "XMLHttpRequest" },
  })
    .then((response) => response.json())
    .then((data) => {
      if (data) {
        document.getElementById("edit_id").value = data.id_alat;
        const valNo = data.no_inventaris || data.nomor_inventaris || "";
        document.getElementById("edit_no_inv").value = valNo;
        document.getElementById("edit_nama").value = data.nama_alat;

        const modal = new bootstrap.Modal(
          document.getElementById("modal_edit_alat")
        );
        modal.show();
      }
    })
    .catch((error) => {
      console.error(error);
      Swal.fire({
        icon: "error",
        title: "Gagal",
        text: "Gagal mengambil data.",
      });
    });
};

window.updateAlat = function () {
  const form = document.getElementById("form_edit_alat");

  if (!form.checkValidity()) {
    form.reportValidity();
    return;
  }

  const formData = new window.FormData(form);
  const url = `${BASE_URL}admin/update_alat`;

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
      if (!response.ok) throw new Error("Server Error: " + response.status);
      return response.json();
    })
    .then((data) => {
      if (data.status === "success") {
        const modalEl = document.getElementById("modal_edit_alat");
        const modalInstance = bootstrap.Modal.getInstance(modalEl);
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

window.hapusAlat = function (id) {
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

      const url = `${BASE_URL}admin/hapus_alat/${id}`;
      const formData = new window.FormData();
      formData.append(CSRF_TOKEN, CSRF_HASH); // Inject Token

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
          Swal.fire("Error", "Gagal menghapus: " + error.message, "error");
        });
    }
  });
};

// ---------------------------------------------------
// 4. LISTENER TOMBOL COPY (VERSI SMART FILTER)
// ---------------------------------------------------
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
// FUNGSI EXPORT EXCEL DATA ALAT (RAPAT & HIJAU)
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
    const workbook = new window.ExcelJS.Workbook();
    const worksheet = workbook.addWorksheet("Data Alat");

    worksheet.columns = [
      { header: "No", key: "no", width: 8 },
      { header: "Nomor Inventaris", key: "nomor", width: 25 },
      { header: "Nama Alat", key: "nama", width: 40 },
    ];

    worksheet.spliceRows(1, 0, []);
    worksheet.mergeCells("A1:C1");

    const titleRow = worksheet.getCell("A1");
    titleRow.value =
      "DATA ALAT - UNIT PENUNJANG AKADEMIK PERAWATAN DAN PERBAIKAN";
    titleRow.font = {
      name: "Arial",
      size: 16,
      bold: true,
      color: { argb: "FF000000" },
    };
    titleRow.alignment = { vertical: "middle", horizontal: "center" };
    titleRow.fill = {
      type: "pattern",
      pattern: "solid",
      fgColor: { argb: "FFFFC107" },
    };
    titleRow.border = { bottom: { style: "medium" } };

    for (let i = 1; i <= 3; i++) {
      const cell = worksheet.getRow(2).getCell(i);
      cell.font = { bold: true, color: { argb: "FF000000" }, size: 12 };
      cell.fill = {
        type: "pattern",
        pattern: "solid",
        fgColor: { argb: "FFFFC107" },
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

    rowsToExport.forEach((row) => {
      const cells = row.querySelectorAll("td");
      let nomorVal, namaVal;

      if (cells.length >= 6) {
        nomorVal = cells[2].innerText.trim();
        namaVal = cells[3].innerText.trim();
      } else if (cells.length >= 4) {
        nomorVal = cells[1].innerText.trim();
        namaVal = cells[2].innerText.trim();
      }

      if (nomorVal) {
        const newRow = worksheet.addRow({
          no: nomorUrut++,
          nomor: nomorVal,
          nama: namaVal,
        });
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
      }
    });

    const buffer = await workbook.xlsx.writeBuffer();
    const today = new window.Date().toISOString().slice(0, 10);
    window.saveAs(new window.Blob([buffer]), `Data_Alat_${today}.xlsx`);
  } catch (error) {
    console.error("Export Error:", error);
    Swal.fire({
      icon: "error",
      title: "Gagal",
      text: "Gagal export Excel: " + error.message,
    });
  } finally {
    if (btn) {
      btn.disabled = false;
      btn.innerHTML = originalText;
    }
  }
};
