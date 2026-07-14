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

  // Init Pagination & Search
  initPagination();

  // Init Sidebar & Footer
  setupSidebar();

  // Init Listener Copy
  setupCopyButton();

  // Init Logika Dependent Dropdown (Unit -> Gedung)
  setupDependentDropdown();
});

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

// =======================================================
// LOGIKA DEPENDENT DROPDOWN (UNIT -> GEDUNG)
// =======================================================
function setupDependentDropdown() {
  const selectUnit = document.getElementById("selectUnit");
  const selectGedung = document.getElementById("selectGedung");

  if (!selectUnit || !selectGedung || selectGedung.tagName !== "SELECT") {
    return;
  }

  const allGedungOptions = Array.from(
    selectGedung.querySelectorAll('option:not([value=""])')
  );

  selectUnit.addEventListener("change", function () {
    const selectedUnitId = this.value;

    selectGedung.value = "";

    if (!selectedUnitId) {
      selectGedung.disabled = true;
      selectGedung.innerHTML =
        '<option value="">-- Pilih Unit Dulu --</option>';
      allGedungOptions.forEach((opt) => selectGedung.appendChild(opt));
      return;
    }

    selectGedung.disabled = false;
    selectGedung.innerHTML = '<option value="">-- Pilih Gedung --</option>';

    let countAvailable = 0;

    allGedungOptions.forEach((option) => {
      const rawData = option.getAttribute("data-units");
      try {
        const owners = JSON.parse(rawData);
        if (owners.map(String).includes(String(selectedUnitId))) {
          selectGedung.appendChild(option);
          countAvailable++;
        }
      } catch (e) {
        console.error("Gagal parse JSON gedung:", rawData);
      }
    });

    if (countAvailable === 0) {
      selectGedung.innerHTML +=
        '<option value="" disabled>Tidak ada gedung untuk unit ini</option>';
    }
  });
}

// ==========================================
// BAGIAN LOGIKA CRUD LOKASI
// ==========================================

// BUKA MODAL TAMBAH
window.tambahModalLokasi = function () {
  document.getElementById("form_tambah_lokasi").reset();
  const selectGedung = document.getElementById("selectGedung");
  if (selectGedung) {
    selectGedung.value = "";
  }
  const modal = new bootstrap.Modal(document.getElementById("modal_lokasi"));
  modal.show();
};

// SIMPAN LOKASI BARU
// SIMPAN LOKASI BARU
window.simpanLokasi = function () {
  const form = document.getElementById("form_tambah_lokasi");

  if (!form.checkValidity()) {
    form.reportValidity();
    return;
  }

  // 1. Ambil data asli dari form
  const formData = new window.FormData(form);

  // 2. CEGAT DAN FORMAT NILAI LANTAI
  let nilaiLantai = formData.get("lantai").trim();

  // Jika inputnya bukan teks kosong
  if (nilaiLantai !== "") {
    // Ubah semua huruf menjadi kecil (lowercase) untuk pengecekan
    let cekLantai = nilaiLantai.toLowerCase();

    // Jika tidak mengandung kata "lantai", maka tambahkan kata "Lantai " di depannya
    if (!cekLantai.includes("lantai")) {
      nilaiLantai = "Lantai " + nilaiLantai;
    } else {
      // (Opsional) Rapikan jika user iseng ketik "lantai 2" menjadi "Lantai 2"
      nilaiLantai = nilaiLantai.replace(/lantai/i, "Lantai");
    }

    // 3. Timpa nilai lama di FormData dengan nilai yang sudah dirapikan
    formData.set("lantai", nilaiLantai);
  }

  Swal.fire({
    title: "Menyimpan...",
    position: "center",
    width: "20rem",
    allowOutsideClick: false,
    showConfirmButton: false,
    didOpen: () => {
      Swal.showLoading();
    },
  });

  const url = `${BASE_URL}admin/tambah_lokasi`;

  fetch(url, {
    method: "POST",
    body: formData,
    headers: { "X-Requested-With": "XMLHttpRequest" },
  })
    .then((response) => response.json())
    .then((data) => {
      Swal.close();
      if (data.status === "success") {
        const modalEl = document.getElementById("modal_lokasi");
        const modalInstance = bootstrap.Modal.getInstance(modalEl);
        if (modalInstance) modalInstance.hide();

        Swal.fire({
          icon: "success",
          title: "Berhasil",
          text: data.message,
          position: "center",
          width: "20rem",
          timer: 1500,
          showConfirmButton: false,
        }).then(() => {
          location.reload();
        });
      } else {
        Swal.fire({
          icon: "error",
          title: "Gagal",
          text: data.message,
          position: "center",
          width: "20rem",
        });
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "Terjadi kesalahan sistem.",
        position: "center",
        width: "20rem",
      });
    });
};

// EDIT LOKASI (AMBIL DATA)
window.editLokasi = function (id) {
  const form = document.getElementById("formLokasi");
  if (form) form.reset();

  const url = `${BASE_URL}admin/get_lokasi/${id}`;

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
        document.getElementById("id_lokasi_edit").value = data.id_lokasi;
        document.getElementById("id_unit_edit").value = data.id_unit;
        document.getElementById("gedung_edit").value = data.gedung;
        document.getElementById("lantai_edit").value = data.lantai;
        document.getElementById("ruangan_edit").value = data.ruangan;
        document.getElementById("kampus_edit").value = data.kampus;

        const modalEl = document.getElementById("modalLokasi");
        let modal = bootstrap.Modal.getInstance(modalEl);
        if (!modal) {
          modal = new bootstrap.Modal(modalEl);
        }
        modal.show();
      } else {
        Swal.fire({
          icon: "warning",
          title: "Tidak Ditemukan",
          text: "Data lokasi tidak ditemukan.",
          position: "center",
          width: "20rem",
          showConfirmButton: false,
          timer: 2000,
        });
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      Swal.fire({
        icon: "error",
        title: "Gagal",
        text: "Gagal mengambil data lokasi.",
        position: "center",
        width: "20rem",
        showConfirmButton: false,
        timer: 2000,
      });
    });
};

// UPDATE LOKASI
window.updateLokasi = function () {
  const form = document.getElementById("formLokasi");

  if (!form.checkValidity()) {
    form.reportValidity();
    return;
  }

  const formData = new window.FormData(form);

  Swal.fire({
    title: "Menyimpan Data...",
    position: "center",
    width: "20rem",
    allowOutsideClick: false,
    showConfirmButton: false,
    didOpen: () => {
      Swal.showLoading();
    },
  });

  const url = `${BASE_URL}admin/update_lokasi`;

  fetch(url, {
    method: "POST",
    body: formData,
    headers: { "X-Requested-With": "XMLHttpRequest" },
  })
    .then((response) => response.json())
    .then((data) => {
      Swal.close();

      if (data.status === "success") {
        const modalEl = document.getElementById("modalLokasi");
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();

        Swal.fire({
          icon: "success",
          title: "Berhasil!",
          text: data.message,
          position: "center",
          width: "20rem",
          timer: 2500,
          showConfirmButton: false,
        }).then(() => {
          location.reload();
        });
      } else {
        Swal.fire({
          icon: "error",
          title: "Gagal",
          text: data.message,
          position: "center",
          width: "20rem",
        });
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "Terjadi kesalahan sistem.",
        position: "center",
        width: "20rem",
      });
    });
};

// HAPUS LOKASI
window.hapusLokasi = function (id) {
  Swal.fire({
    title: "Apakah Anda yakin?",
    text: "Data lokasi ini akan dihapus secara permanen!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#6c757d",
    confirmButtonText: "Ya, Hapus!",
    cancelButtonText: "Batal",
  }).then((result) => {
    if (result.isConfirmed) {
      Swal.fire({
        title: "Sedang memproses...",
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        },
      });

      const url = `${BASE_URL}admin/hapus_lokasi/${id}`;
      const formData = new window.FormData();
      formData.append(CSRF_TOKEN, CSRF_HASH); // Token Delete

      fetch(url, {
        method: "POST", // Codeigniter seringkali butuh POST jika pakai token CSRF (Tergantung router)
        body: formData,
        headers: { "X-Requested-With": "XMLHttpRequest" },
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.status === "success") {
            Swal.fire({
              icon: "success",
              title: "Berhasil!",
              text: data.message,
              timer: 1500,
              showConfirmButton: false,
            }).then(() => {
              location.reload();
            });
          } else {
            Swal.fire({ icon: "error", title: "Gagal!", text: data.message });
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          Swal.fire({
            icon: "error",
            title: "Error Sistem",
            text: "Terjadi kesalahan saat menghubungi server.",
          });
        });
    }
  });
};

// ---------------------------------------------------
// LISTENER TOMBOL COPY (VERSI SMART FILTER)
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
// FUNGSI EXPORT EXCEL LOKASI (TANPA GRAFIK)
// =======================================================
window.exportToExcel = async function () {
  const btn = document.querySelector(
    'button[onclick="window.exportToExcel()"]'
  );
  const originalText = btn
    ? btn.innerHTML
    : '<i class="fas fa-file-excel"></i> Export Excel';

  if (btn) {
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
  }

  try {
    const workbook = new window.ExcelJS.Workbook();
    const worksheet = workbook.addWorksheet("Data Lokasi");

    worksheet.columns = [
      { header: "No", key: "no", width: 5 },
      { header: "Unit / Jurusan", key: "unit", width: 30 },
      { header: "Gedung", key: "gedung", width: 20 },
      { header: "Lantai", key: "lantai", width: 10 },
      { header: "Ruangan", key: "ruangan", width: 35 },
      { header: "Kampus", key: "kampus", width: 20 },
    ];

    worksheet.spliceRows(1, 0, []);
    worksheet.mergeCells("A1:F1");

    const titleRow = worksheet.getCell("A1");
    titleRow.value = "DATA LOKASI LENGKAP - UPT TP3A";
    titleRow.font = {
      name: "Arial",
      size: 14,
      bold: true,
      color: { argb: "FFFFFFFF" },
    };
    titleRow.alignment = { vertical: "middle", horizontal: "center" };
    titleRow.fill = {
      type: "pattern",
      pattern: "solid",
      fgColor: { argb: "FF0d6efd" },
    };
    titleRow.border = { bottom: { style: "medium" } };

    const headerRow = worksheet.getRow(2);
    headerRow.eachCell((cell) => {
      cell.font = { bold: true, color: { argb: "FFFFFFFF" }, size: 11 };
      cell.fill = {
        type: "pattern",
        pattern: "solid",
        fgColor: { argb: "FF0d6efd" },
      };
      cell.alignment = { vertical: "middle", horizontal: "center" };
      cell.border = {
        top: { style: "thin" },
        left: { style: "thin" },
        bottom: { style: "thin" },
        right: { style: "thin" },
      };
    });

    const rowsToExport =
      typeof globalRows !== "undefined" && globalRows.length > 0
        ? globalRows
        : document.querySelectorAll("#table tbody tr:not(.empty-row)");

    if (rowsToExport.length === 0)
      throw new Error("Tidak ada data di tabel untuk diexport.");

    let nomorUrut = 1;

    rowsToExport.forEach((row) => {
      const cells = row.querySelectorAll("td");

      if (cells.length >= 6) {
        const unitVal = cells[1]?.innerText.trim() || "-";
        const gedungVal = cells[2]?.innerText.trim() || "-";
        const lantaiVal = cells[3]?.innerText.trim() || "-";
        const ruanganVal = cells[4]?.innerText.trim() || "-";
        const kampusVal = cells[5]?.innerText.trim() || "-";

        const newRow = worksheet.addRow({
          no: nomorUrut++,
          unit: unitVal,
          gedung: gedungVal,
          lantai: lantaiVal,
          ruangan: ruanganVal,
          kampus: kampusVal,
        });

        newRow.eachCell((cell, colNumber) => {
          cell.border = {
            top: { style: "thin" },
            left: { style: "thin" },
            bottom: { style: "thin" },
            right: { style: "thin" },
          };
          if (
            colNumber === 1 ||
            colNumber === 3 ||
            colNumber === 4 ||
            colNumber === 6
          ) {
            cell.alignment = { vertical: "middle", horizontal: "center" };
          } else {
            cell.alignment = { vertical: "middle", horizontal: "left" };
          }
        });
      }
    });

    const buffer = await workbook.xlsx.writeBuffer();
    const today = new window.Date().toISOString().slice(0, 10);
    window.saveAs(new window.Blob([buffer]), `Data_Lokasi_${today}.xlsx`);
  } catch (error) {
    console.error("Export Error:", error);
    Swal.fire({
      icon: "error",
      title: "Gagal Export",
      text: error.message,
    });
  } finally {
    if (btn) {
      btn.disabled = false;
      btn.innerHTML = originalText;
    }
  }
};
