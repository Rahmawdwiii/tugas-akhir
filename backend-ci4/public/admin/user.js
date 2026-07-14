// =========================================================
// VARIABEL GLOBAL & INISIALISASI
// =========================================================
let globalRows = [];
let filteredRows = [];
let globalCurrentPage = 1;
const rowsPerPage = 10;

window.addEventListener("DOMContentLoaded", function () {
  // 1. Jalankan Pagination Awal
  initPagination();

  // 2. Setup Filter (Search + Dropdown)
  setupFilters();

  // 3. Setup Tombol Reload
  setupReloadButton();

  // 4. Setup Sidebar & Footer
  setupSidebar();

  // 5. Setup Tombol Tambah User
  setupBtnAddUser();

  // 6. Refresh Status Admin (Simulasi)
  setInterval(refreshAdminStatus, 30000);
  refreshAdminStatus();
});

// Small wrapper used by older code to trigger the newer fetch function
function refreshAdminStatus() {
  if (typeof fetchTeknisiStatus === 'function') {
    try { fetchTeknisiStatus(); } catch (e) { console.warn('refreshAdminStatus failed', e); }
  }
}

// =========================================================
// LOGIKA FILTER GABUNGAN (SEARCH + DROPDOWN)
// =========================================================
function setupFilters() {
  const searchInput = document.getElementById("searchUser");
  const filterSelect = document.getElementById("filterAkses");

  function applyFilter() {
    const keyword = searchInput ? searchInput.value.toLowerCase() : "";
    const accessVal = filterSelect ? filterSelect.value.toLowerCase() : "";

    filteredRows = globalRows.filter((row) => {
      const rowText = row.innerText.toLowerCase();
      const textMatch = rowText.includes(keyword);

      // Cek Kolom ke-6 (Index 5) untuk Akses
      let accessMatch = true;
      if (row.cells && row.cells.length > 5) {
        const colAkses = row.cells[5].innerText.toLowerCase().trim();
        accessMatch = accessVal === "" || colAkses === accessVal;
      }

      return textMatch && accessMatch;
    });

    globalCurrentPage = 1;
    renderPage(globalCurrentPage);
  }

  if (searchInput) searchInput.addEventListener("keyup", applyFilter);
  if (filterSelect) filterSelect.addEventListener("change", applyFilter);
}

// =========================================================
// LOGIKA PAGINATION & RENDER
// =========================================================
function initPagination() {
  const tableBody = document.getElementById("tableBody");
  if (!tableBody) return;

  globalRows = Array.from(
    tableBody.querySelectorAll(":scope > tr:not(.empty-row)")
  );
  filteredRows = globalRows;

  globalCurrentPage = 1;
  renderPage(globalCurrentPage);
}

window.showPage = function (page) {
  const totalRows = filteredRows.length;
  const totalPages = Math.ceil(totalRows / rowsPerPage);

  if (page < 1) page = 1;
  if (page > totalPages && totalPages > 0) page = totalPages;

  globalCurrentPage = page;

  globalRows.forEach((row) => (row.style.display = "none"));

  const start = (page - 1) * rowsPerPage;
  const end = start + rowsPerPage;

  filteredRows.slice(start, end).forEach((row) => (row.style.display = ""));

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

// =========================================================
// PERBAIKAN LOGIKA SIDEBAR, FOOTER & RELOAD
// =========================================================
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
      if (footerContent) footerContent.classList.toggle("full");
    });
  }

  links.forEach((link) => {
    link.addEventListener("click", function () {
      links.forEach((l) => l.classList.remove("active"));
      this.classList.add("active");
    });
  });
}

function setupReloadButton() {
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
}

// =========================================================
// LOGIKA CRUD USER (TAMBAH, EDIT, SIMPAN, HAPUS)
// =========================================================
function setupBtnAddUser() {
  const btnAdd = document.getElementById("btnAddUser");
  if (btnAdd) {
    btnAdd.addEventListener("click", function () {
      document.getElementById("formUser").reset();
      document.getElementById("edit_id_user").value = "";
      document.getElementById("modalUserLabel").innerText = "Tambah User";
      document.getElementById("password").required = true;

      const passHelp = document.getElementById("passwordHelp");
      if (passHelp) passHelp.classList.add("d-none");

      const modal = new bootstrap.Modal(document.getElementById("modalUser"));
      modal.show();
    });
  }
}

window.togglePassword = function () {
  const pw = document.getElementById("password");
  if (pw) pw.type = pw.type === "password" ? "text" : "password";
};

window.editUser = function (id) {
  document.getElementById("formUser").reset();

  Swal.fire({
    title: "Memuat Data...",
    text: "Mohon tunggu sebentar",
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading(),
  });

  fetch(`${BASE_URL}admin/get_user/${id}`, {
    method: "GET",
    headers: { "X-Requested-With": "XMLHttpRequest" },
  })
    .then((response) => {
      if (!response.ok) throw new Error(response.statusText);
      return response.json();
    })
    .then((data) => {
      Swal.close();
      if (data) {
        document.getElementById("edit_id_user").value = data.id_user;
        document.getElementById("nama").value = data.nama;
        document.getElementById("jabatan").value = data.jabatan;
        document.getElementById("akses").value = data.akses;
        document.getElementById("username").value = data.username;
        document.getElementById("password").value = "";
        document.getElementById("password").required = false;
        document.getElementById("email").value = data.email ?? "";

        document.getElementById("modalUserLabel").innerText = "Edit User";
        const passHelp = document.getElementById("passwordHelp");
        if (passHelp) passHelp.classList.remove("d-none");

        const modal = new bootstrap.Modal(document.getElementById("modalUser"));
        modal.show();
      } else {
        Swal.fire("Error", "Data user tidak ditemukan.", "error");
      }
    })
    .catch((error) => {
      console.error(error);
      Swal.fire("Error", "Gagal mengambil data user (Cek Console).", "error");
    });
};

window.saveUser = function () {
  const form = document.getElementById("formUser");

  if (!form.checkValidity()) {
    form.reportValidity();
    return;
  }

  const formData = new window.FormData(form);

  Swal.fire({
    title: "Menyimpan Data...",
    text: "Mohon tunggu sebentar",
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading(),
  });

  fetch(`${BASE_URL}admin/simpan_user`, {
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
        const modalEl = document.getElementById("modalUser");
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();

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
        let errorHtml = "";
        if (typeof data.errors === "object") {
          errorHtml = '<ul style="text-align: left; margin-left: 20px;">';
          Object.values(data.errors).forEach((msg) => {
            errorHtml += `<li>${msg}</li>`;
          });
          errorHtml += "</ul>";
        } else {
          errorHtml = data.message;
        }
        Swal.fire({ icon: "error", title: "Gagal Menyimpan", html: errorHtml });
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

window.hapusUser = function (id) {
  Swal.fire({
    title: "Yakin hapus user?",
    text: "User yang dihapus tidak bisa dikembalikan!",
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
        text: "Mohon tunggu sebentar",
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading(),
      });

      const formData = new window.FormData();
      formData.append(CSRF_TOKEN_NAME, CSRF_HASH);

      fetch(`${BASE_URL}admin/hapus_user/${id}`, {
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
              title: "Terhapus!",
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
          Swal.fire("Error", "Gagal menghapus data (Cek Console).", "error");
        });
    }
  });
};

// ==========================================================
// POLLING STATUS TEKNISI + ADMIN UPT (AJAX)
// ==========================================================
const ONLINE_TOLERANCE_SECONDS = 60;

async function fetchTeknisiStatus() {
  try {
    const res = await fetch(`${BASE_URL}admin/get_teknisi_json`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    if (!res.ok) throw new Error('Network response was not ok');
    const data = await res.json();
    if (data.status !== 'success') throw new Error('invalid response');

    // Update admin upt status
    renderAdminStatus(data.admin_upt);

    // Update teknisi list
    renderTeknisiList(data.teknisi || []);
  } catch (err) {
    console.error('Gagal mengambil status teknisi:', err);
  }
}

function renderAdminStatus(admin) {
  const statusContainer = document.getElementById('status_admin_upt');
  if (!statusContainer) return;

  if (!admin) {
    statusContainer.innerHTML = `<span class="badge rounded-pill bg-secondary small">Offline</span>`;
    return;
  }

  // If model has is_online flag use it, otherwise fallback to last_active timestamp
  if (admin.is_online == 1 || admin.is_online === '1') {
    statusContainer.innerHTML = `<span class="badge rounded-pill bg-success small"><i class="fas fa-circle me-1" style="font-size:8px"></i> Online</span>`;
    return;
  }

  // fallback: compute minutes since last_active if available
  const last = admin.last_active ? Number(new Date(admin.last_active)) : null;
  if (last) {
    const minutes = Math.floor((Date.now() - last) / 60000);
    statusContainer.innerHTML = `<span class=\"badge rounded-pill bg-secondary small\">${minutes}m lalu</span>`;
  } else {
    statusContainer.innerHTML = `<span class="badge rounded-pill bg-secondary small">Offline</span>`;
  }
}

function renderTeknisiList(list) {
  const container = document.getElementById('teknisi_list_container');
  if (!container) return;

  if (!Array.isArray(list) || list.length === 0) {
    container.innerHTML = '<small class="text-muted">Belum ada data teknisi.</small>';
    return;
  }

  function timeAgo(ts) {
    if (!ts) return null;
    const t = Number(new Date(ts));
    if (isNaN(t)) return null;
    const diff = Date.now() - t;
    const sec = Math.floor(diff / 1000);
    if (sec < 60) return `${sec}s lalu`;
    const min = Math.floor(sec / 60);
    if (min < 60) return `${min}m lalu`;
    const hr = Math.floor(min / 60);
    if (hr < 24) return `${hr}h lalu`;
    const days = Math.floor(hr / 24);
    return `${days}d lalu`;
  }

  const html = list.map((tek) => {
    const name = tek.nama || tek.username || 'Unknown';
    const online = tek.is_online == 1 || tek.is_online === '1';
    const last = tek.last_active || tek.lastActive || tek.last_active_at || null;
    const lastText = timeAgo(last);
    const statusDisplay = online ? 'Online' : (lastText || 'Offline');

    const badge = online
      ? `<span class=\"badge bg-success bg-opacity-10 text-success border border-success px-2 rounded-pill\" style=\"font-size: 0.7rem;\">Online</span>`
      : `<span class=\"badge bg-light text-muted border px-2 rounded-pill\" style=\"font-size: 0.7rem;\">${escapeHtml(statusDisplay)}</span>`;

    return `<div class=\"d-flex justify-content-between align-items-center\">\n              <div class=\"d-flex align-items-center\">\n                <i class=\"fas fa-user-cog text-primary me-2\"></i>\n                <span class=\"small fw-bold\">${escapeHtml(name)}</span>\n              </div>\n              ${badge}\n            </div>`;
  }).join('');

  container.innerHTML = html;
}

function escapeHtml(str) {
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

// Start polling on load
window.addEventListener('DOMContentLoaded', () => {
  fetchTeknisiStatus();
  setInterval(fetchTeknisiStatus, 10000); // setiap 10 detik
});