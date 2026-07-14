// ==========================================
// VARIABLE GLOBAL
// ==========================================
let globalRows = [];
let currentRows = [];
let globalCurrentPage = 1;
const rowsPerPage = 10;
let modalUnitInstance;

// ==========================================
// 1. SAAT HALAMAN DIMUAT
// ==========================================
window.addEventListener("DOMContentLoaded", () => {
  // Init Modal Unit
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
// LOGIKA PAGINATION + PENCARIAN (INTEGRATED)
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
// BAGIAN CRUD SEDERHANA (TAMBAH, EDIT, UPDATE, DELETE)
// (Sesuaikan logika fetch-nya bila dibutuhkan)
// ==========================================

window.tambahModalUnit = function () {
  const form = document.getElementById("form_unit");
  if (form) form.reset();
  if (modalUnitInstance) modalUnitInstance.show();
};

window.simpanUnit = function () {
  const form = document.getElementById("form_unit");

  // 1. Cek apakah form sudah diisi (Validasi HTML5)
  if (!form.checkValidity()) {
    form.reportValidity();
    return;
  }

  // 2. Ambil data dari form
  const formData = new window.FormData(form);

  // 3. Tampilkan Loading SweetAlert
  Swal.fire({
    title: "Menyimpan...",
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading(),
  });

  // 4. Kirim data ke Controller Admin::simpan_unit
  fetch(`${BASE_URL}admin/simpan_unit`, {
    method: "POST",
    body: formData,
    headers: { "X-Requested-With": "XMLHttpRequest" },
  })
    .then((response) => response.json())
    .then((data) => {
      Swal.close();

      if (data.status === "success") {
        // Tutup modal
        if (modalUnitInstance) modalUnitInstance.hide();

        // Tampilkan pesan sukses & refresh halaman
        Swal.fire({
          icon: "success",
          title: "Berhasil",
          text: data.message,
          timer: 1500,
          showConfirmButton: false,
        }).then(() => {
          location.reload();
        });
      } else {
        // Tampilkan pesan gagal dari validasi Controller
        Swal.fire("Gagal", data.message || "Gagal menyimpan data", "error");
      }
    })
    .catch((error) => {
      console.error(error);
      Swal.fire("Error", "Terjadi kesalahan sistem.", "error");
    });
};

window.editUnit = function (id) {
  const form = document.getElementById("formUnit");
  if (form) form.reset();

  fetch(`${BASE_URL}admin/get_unit/${id}`, {
    method: "GET",
    headers: { "X-Requested-With": "XMLHttpRequest" },
  })
    .then((response) => response.json())
    .then((data) => {
      if (data) {
        document.getElementById("id_unit_edit").value = data.id_unit;
        document.getElementById("nama_unit_edit").value = data.nama_unit;
        document.getElementById("kategori_edit").value = data.kategori;

        const modal = new bootstrap.Modal(document.getElementById("modalUnit"));
        modal.show();
      } else {
        Swal.fire("Error", "Data tidak ditemukan.", "error");
      }
    })
    .catch((error) => {
      console.error(error);
      Swal.fire("Error", "Gagal mengambil data dari server.", "error");
    });
};

window.updateUnit = function () {
  const form = document.getElementById("formUnit");
  if (!form.checkValidity()) {
    form.reportValidity();
    return;
  }

  const formData = new window.FormData(form);

  Swal.fire({
    title: "Menyimpan...",
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading(),
  });

  fetch(`${BASE_URL}admin/update_unit`, {
    method: "POST",
    body: formData,
    headers: { "X-Requested-With": "XMLHttpRequest" },
  })
    .then((response) => response.json())
    .then((data) => {
      Swal.close();
      if (data.status === "success") {
        const modalEl = document.getElementById("modalUnit");
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();

        Swal.fire({
          icon: "success",
          title: "Berhasil",
          text: data.message,
          timer: 1500,
          showConfirmButton: false,
        }).then(() => {
          location.reload();
        });
      } else {
        Swal.fire("Gagal", data.message, "error");
      }
    })
    .catch((error) => {
      console.error(error);
      Swal.fire("Error", "Terjadi kesalahan sistem.", "error");
    });
};

window.hapusUnitRow = function (id) {
  const row = document.getElementById("row-" + id);
  const container = document.getElementById("unit-form-container");
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

window.hapusUnit = function (id) {
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

      const formData = new window.FormData();
      formData.append(CSRF_TOKEN, CSRF_HASH);

      fetch(`${BASE_URL}admin/hapus_unit/${id}`, {
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
              title: "Berhasil!",
              text: data.message,
              icon: "success",
              timer: 1500,
              showConfirmButton: false,
            }).then(() => {
              location.reload();
            });
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

// ==========================================
// FUNGSI EXPORT KE EXCEL (FIX WARNA ARGB & JEDA LOADING)
// ==========================================
window.exportToExcel = async function () {
  if (typeof ExcelJS === "undefined" || typeof saveAs === "undefined") {
    Swal.fire(
      "Error",
      "Library pembuat Excel belum dimuat dengan benar.",
      "error"
    );
    return;
  }

  // Munculkan loading
  Swal.fire({
    title: "Menyiapkan Laporan...",
    html: "Mohon tunggu sebentar, sedang merapikan data.",
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading(),
  });

  try {
    const workbook = new ExcelJS.Workbook();
    const worksheet = workbook.addWorksheet("Data Unit");

    // 1. Atur Lebar Kolom Manual
    worksheet.getColumn(1).width = 8; // Kolom NO
    worksheet.getColumn(2).width = 45; // Kolom JURUSAN / UNIT
    worksheet.getColumn(3).width = 25; // Kolom KATEGORI

    // 2. Buat Kop / Header Utama Laporan (Baris 1)
    worksheet.addRow(["DATA MASTER UNIT - UPAPP POLSRI"]);
    worksheet.mergeCells("A1:C1");
    worksheet.getRow(1).height = 35;

    // FIX: Gunakan 'argb' bukan 'arg'
    const titleCell = worksheet.getCell("A1");
    titleCell.font = {
      name: "Arial",
      size: 14,
      bold: true,
      color: { argb: "FFFFFFFF" }, // Putih
    };
    titleCell.fill = {
      type: "pattern",
      pattern: "solid",
      fgColor: { argb: "FF003366" }, // Biru Gelap UPAPP
    };
    titleCell.alignment = { vertical: "middle", horizontal: "center" };

    // 3. Buat Tabel Header (Baris 3)
    const headerRow = worksheet.addRow(["NO", "JURUSAN / UNIT", "KATEGORI"]);
    headerRow.height = 25;

    headerRow.eachCell((cell) => {
      cell.font = { name: "Arial", bold: true, color: { argb: "FFFFFFFF" } };
      cell.fill = {
        type: "pattern",
        pattern: "solid",
        fgColor: { argb: "FF4FA5FF" }, // Biru Terang
      };
      cell.alignment = { vertical: "middle", horizontal: "center" };
      cell.border = {
        top: { style: "thin" },
        left: { style: "thin" },
        bottom: { style: "thin" },
        right: { style: "thin" },
      };
    });

    // 5. Ambil Data
    const rows = document.querySelectorAll("#tableBody tr:not(.empty-row)");

    if (rows.length === 0) {
      Swal.fire("Peringatan", "Tidak ada data untuk diekspor!", "warning");
      return;
    }

    rows.forEach((row, index) => {
      const cells = row.querySelectorAll("td");
      if (cells.length >= 3) {
        const dataRow = worksheet.addRow([
          index + 1,
          cells[1].innerText.trim(),
          cells[2].innerText.trim(),
        ]);

        dataRow.getCell(1).alignment = {
          vertical: "middle",
          horizontal: "center",
        };
        dataRow.getCell(2).alignment = {
          vertical: "middle",
          horizontal: "left",
          indent: 1,
        };
        dataRow.getCell(3).alignment = {
          vertical: "middle",
          horizontal: "center",
        };
        dataRow.height = 22;

        const isEven = index % 2 === 0;
        dataRow.eachCell({ includeEmpty: true }, function (cell) {
          cell.fill = {
            type: "pattern",
            pattern: "solid",
            fgColor: { argb: isEven ? "FFF2F8FF" : "FFFFFFFF" }, // Belang-belang
          };
          cell.border = {
            top: { style: "thin" },
            left: { style: "thin" },
            bottom: { style: "thin" },
            right: { style: "thin" },
          };
        });
      }
    });

    // 6. Buat Jeda Loading (Delay UX) selama 1.5 detik (1500 ms) sebelum file terdownload
    setTimeout(async () => {
      const buffer = await workbook.xlsx.writeBuffer();
      const blob = new Blob([buffer], {
        type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
      });
      saveAs(blob, "Data_Master_Unit_UPAPP.xlsx");

      // Tutup animasi loading setelah file terunduh
      Swal.close();
    }, 1500); // Angka 1500 bisa diperbesar misal jadi 2000 untuk jeda 2 detik
  } catch (error) {
    console.error("Gagal Export Excel:", error);
    Swal.fire("Error", "Gagal mengekspor data ke Excel.", "error");
  }
};