// =======================================================
// === 1. PENGATURAN GLOBAL DAN INISIALISASI (DIGABUNGKAN)
// =======================================================

// Variabel Global Umum
const statusStorageKeyPerbaikan = "statusPerbaikanData";
const statusStorageKeyKerusakan = "statusKerusakanData";

let currentButton = null;
let currentLaporanId = null;

// Elemen Modal Perbaikan
const modalElementPerbaikan = document.getElementById("ModalStatusPerbaikan");
let statusModalPerbaikan;
let btnSimpanStatusPerbaikan;
let statusSelectPerbaikan;
let laporanIdPerbaikanInput;

// Elemen Modal Kerusakan
const modalElementKerusakan = document.getElementById("ModalStatusKerusakan");
let statusModalKerusakan;
let btnSimpanStatusKerusakan;
let statusSelectKerusakan;
let laporanIdKerusakanInput;

// Sidebar Logic
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

// =======================================================
// === 2. FUNGSI AJAX UNIVERSAL (KRITIS)
// =======================================================
async function simpanStatus(
  endpoint,
  newStatus,
  statusKey,
  storageKey,
  updateUICallback,
  submitButton,
  modalInstance
) {
  if (!newStatus || !currentLaporanId) {
    Swal.fire({
      icon: "warning",
      title: "Perhatian",
      text: "Silakan pilih status terlebih dahulu.",
    });
    return;
  }

  const formData = new FormData();
  formData.append("nomor_laporan", currentLaporanId);
  formData.append(statusKey, newStatus);

  submitButton.disabled = true;
  submitButton.innerHTML =
    '<span class="spinner-border spinner-border-sm"></span> Menyimpan...';

  try {
    const response = await fetch(`${BASE_URL}${endpoint}`, {
      method: "POST",
      body: formData,
    });
    const result = await response.json();

    if (result.status === "success") {
      const savedData = JSON.parse(localStorage.getItem(storageKey)) || {};
      savedData[currentLaporanId] = newStatus;
      localStorage.setItem(storageKey, JSON.stringify(savedData));

      updateUICallback(currentButton, newStatus);
      modalInstance.hide();

      Swal.fire({
        icon: "success",
        title: "Berhasil",
        text: "Status berhasil diperbarui!",
      }).then(() => {
        window.location.reload();
      });
    } else {
      throw new Error(result.message || "Gagal memperbarui status di server.");
    }
  } catch (error) {
    Swal.fire({
      icon: "error",
      title: "Error",
      text: "Terjadi kesalahan: " + error.message,
    });
  } finally {
    submitButton.disabled = false;
    submitButton.innerHTML = "Simpan";
  }
}

// =======================================================
// === 3. LOGIKA KHUSUS STATUS PERBAIKAN
// =======================================================
window.openModalStatusPerbaikan = function (button) {
  currentButton = button;
  const row = button.closest("tr");
  currentLaporanId = row.cells[0].textContent.trim();

  if (laporanIdPerbaikanInput) laporanIdPerbaikanInput.value = currentLaporanId;

  const savedData =
    JSON.parse(localStorage.getItem(statusStorageKeyPerbaikan)) || {};
  const status = savedData[currentLaporanId];
  if (statusSelectPerbaikan) statusSelectPerbaikan.value = status ? status : "";

  statusModalPerbaikan?.show();
};

function updateButtonUIPerbaikan(button, status) {
  button.classList.remove(
    "bg-primary",
    "bg-success",
    "bg-warning",
    "bg-danger",
    "text-white",
    "text-dark",
    "text-grey-30",
    "text-white-50"
  );

  if (status === "Selesai") {
    button.textContent = "Selesai";
    button.classList.add("bg-success", "text-white");
  } else if (status === "Menunggu") {
    button.textContent = "Menunggu";
    button.classList.add("bg-warning", "text-dark");
  } else if (status === "Diperbaiki" || status === "Rusak") {
    button.textContent = status;
    button.classList.add("bg-danger", "text-white");
  } else {
    button.textContent = "Pilih Status";
    button.classList.add("bg-primary", "text-grey-30");
  }
}

function handleSubmitPerbaikan(e) {
  e.preventDefault();
  simpanStatus(
    "teknisi/update_status",
    statusSelectPerbaikan.value,
    "status_perbaikan",
    statusStorageKeyPerbaikan,
    updateButtonUIPerbaikan,
    btnSimpanStatusPerbaikan,
    statusModalPerbaikan
  );
}

// =======================================================
// === 4. LOGIKA KHUSUS STATUS KERUSAKAN
// =======================================================
window.openModalStatusKerusakan = function (button) {
  currentButton = button;
  const row = button.closest("tr");
  currentLaporanId = row.cells[0].textContent.trim();

  if (laporanIdKerusakanInput) laporanIdKerusakanInput.value = currentLaporanId;

  const savedData =
    JSON.parse(localStorage.getItem(statusStorageKeyKerusakan)) || {};
  const status = savedData[currentLaporanId];
  if (statusSelectKerusakan) statusSelectKerusakan.value = status ? status : "";

  statusModalKerusakan?.show();
};

function updateButtonUIKerusakan(button, status) {
  button.classList.remove(
    "bg-primary",
    "bg-success",
    "bg-warning",
    "bg-danger",
    "text-white",
    "text-dark",
    "text-grey-30",
    "text-white-50"
  );
  button.textContent = status || "Tentukan Status";

  if (status === "Ringan") {
    button.classList.add("bg-success", "text-white");
  } else if (status === "Sedang") {
    button.classList.add("bg-warning", "text-dark");
  } else if (status === "Berat" || status === "Rusak") {
    button.classList.add("bg-danger", "text-white");
  } else {
    button.classList.add("bg-primary", "text-white-50");
  }
}

function handleSubmitKerusakan(e) {
  e.preventDefault();
  simpanStatus(
    "teknisi/update_status",
    statusSelectKerusakan.value,
    "status_kerusakan",
    statusStorageKeyKerusakan,
    updateButtonUIKerusakan,
    btnSimpanStatusKerusakan,
    statusModalKerusakan
  );
}

// =======================================================
// === 5. INISIALISASI DOM (DOM Ready)
// =======================================================
window.addEventListener("DOMContentLoaded", () => {
  // --- INISIALISASI MODAL PERBAIKAN ---
  if (modalElementPerbaikan) {
    statusModalPerbaikan = new bootstrap.Modal(modalElementPerbaikan);
    const formStatusPerbaikan = document.getElementById("formStatusPerbaikan");

    laporanIdPerbaikanInput = document.getElementById("laporanIdPerbaikan");
    btnSimpanStatusPerbaikan = formStatusPerbaikan?.querySelector(
      "button[type='submit']"
    );
    statusSelectPerbaikan = document.getElementById("statusSelectPerbaikan");

    formStatusPerbaikan?.addEventListener("submit", handleSubmitPerbaikan);

    const savedDataPerbaikan =
      JSON.parse(localStorage.getItem(statusStorageKeyPerbaikan)) || {};
    document
      .querySelectorAll(
        "table tbody button[onclick*='openModalStatusPerbaikan']"
      )
      .forEach((button) => {
        const row = button.closest("tr");
        const nomorLaporan = row.cells[0].textContent.trim();
        const status = savedDataPerbaikan[nomorLaporan];
        if (status) updateButtonUIPerbaikan(button, status);
      });

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

    const elModalEdit = document.getElementById("ModalEditLaporan");
    if (elModalEdit) {
      window.statusModalEdit = new bootstrap.Modal(elModalEdit);
      const formEdit = document.getElementById("formEditLaporan");
      if (formEdit) formEdit.addEventListener("submit", handleSubmitEdit);
    }
  }

  // --- INISIALISASI MODAL KERUSAKAN ---
  if (modalElementKerusakan) {
    statusModalKerusakan = new bootstrap.Modal(modalElementKerusakan);
    const formStatusKerusakan = document.getElementById("formStatusKerusakan");

    laporanIdKerusakanInput = document.getElementById("laporanIdKerusakan");
    btnSimpanStatusKerusakan = formStatusKerusakan?.querySelector(
      "button[type='submit']"
    );
    statusSelectKerusakan = document.getElementById("statusSelectKerusakan");

    formStatusKerusakan?.addEventListener("submit", handleSubmitKerusakan);

    const savedDataKerusakan =
      JSON.parse(localStorage.getItem(statusStorageKeyKerusakan)) || {};
    document
      .querySelectorAll(
        "table tbody button[onclick*='openModalStatusKerusakan']"
      )
      .forEach((button) => {
        const row = button.closest("tr");
        const nomorLaporan = row.cells[0].textContent.trim();
        const status = savedDataKerusakan[nomorLaporan];
        if (status) updateButtonUIKerusakan(button, status);
      });
  }
});

// ---------------------------------------------------
// LISTENER TOMBOL COPY
// ---------------------------------------------------
const btnCopy = document.getElementById("btnCopy");
if (btnCopy) {
  btnCopy.addEventListener("click", function () {
    const tableHead = document.querySelector("#tableLaporan thead tr");
    let clipboardText = "";
    let excludedIndices = [];

    if (tableHead) {
      const headers = Array.from(tableHead.querySelectorAll("th"));
      const validHeaders = headers
        .filter((th, index) => {
          const text = th.innerText.trim().toUpperCase();
          if (text === "AKSI" || text === "FOTO") {
            excludedIndices.push(index);
            return false;
          }
          return true;
        })
        .map((th) => th.innerText.trim());
      clipboardText += validHeaders.join("\t") + "\n";
    }

    const rows = document.querySelectorAll("#tableLaporan tbody tr");

    if (rows.length > 0) {
      rows.forEach((row) => {
        if (row.classList.contains("dataTables_empty")) return;
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
            text: "Browser tidak mengizinkan copy otomatis atau terjadi error.",
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

// =======================================================
// FUNGSI EXPORT EXCEL (VERSI FIX SERVER-SIDE)
// =======================================================

const stripHtml = (html) => {
  if (!html) return "-";
  let tmp = document.createElement("DIV");
  tmp.innerHTML = html;
  return tmp.textContent || tmp.innerText || "-";
};

window.exportToExcel = async function () {
  const btn = document.querySelector(".btn-outline-success");
  const originalText = btn ? btn.innerHTML : "Excel";

  // 1. Ambil instance DataTables
  const table = $("#tableLaporan").DataTable();
  // Ambil semua data yang sudah terfilter (untuk Server-side)
  const allData = table.rows({ search: "applied" }).data().toArray();

  if (allData.length === 0) {
    Swal.fire({
      icon: "warning",
      title: "Perhatian",
      text: "Tidak ada data untuk diexport.",
    });
    return;
  }

  // --- TAMBAHKAN / PASTIKAN BAGIAN INI ADA (INISIALISASI) ---
  const statsJurusan = {};
  const statsAlat = {};
  const statsPerbaikan = {};
  let totalLaporan = 0;
  // ---------------------------------------------------------

  if (btn) {
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generate Data...';
  }

  try {
    const workbook = new ExcelJS.Workbook();
    const worksheet = workbook.addWorksheet("Data Riwayat");

    // ... (Bagian worksheet.columns tetap sama seperti kode Bung) ...
    worksheet.columns = [
      { header: "No.", key: "nomor_urut", width: 5 },
      { header: "No. Laporan", key: "no", width: 15 },
      { header: "Tanggal", key: "tgl", width: 20 },
      { header: "Tgl Perbaikan", key: "tgl_perbaikan", width: 20 }, 
      { header: "Nama Alat", key: "alat", width: 25 },
      { header: "No. Inventaris", key: "inv", width: 20 },
      { header: "Lokasi", key: "lokasi", width: 35 },
      { header: "Unit", key: "unit", width: 15 },
      { header: "Status Kerusakan", key: "status_kerusakan", width: 20 },
      { header: "Pelaksana", key: "pelaksana", width: 15 },
      { header: "Validasi", key: "validasi", width: 15 },
      { header: "Status Perbaikan", key: "status_akhir", width: 18 },
      { header: "Kerusakan/Keluhan", key: "keluhan", width: 30 },
      { header: "Alasan", key: "diagnosa", width: 35 },
      { header: "Foto", key: "foto", width: 18 },
    ];

    // ... (Bagian Judul A1 dan Header tetap sama) ...
    worksheet.spliceRows(1, 0, []);

    // Pastikan range mergeCells sesuai dengan jumlah kolom Bung (A sampai N = 14 Kolom)
    worksheet.mergeCells("A1:O1");

    const titleCell = worksheet.getCell("A1");
    titleCell.value = "REKAPITULASI RIWAYAT PERBAIKAN ASET";

    // 1. MENGATUR FONT (Tebal, Ukuran 16, Warna Teks Putih)
    titleCell.font = {
      name: "Arial",
      size: 16,
      bold: true,
      color: { argb: "FFFFFFFF" }, // FFFFFFFF = Putih
    };

    // 2. MENGATUR POSISI (Rata Tengah Vertikal & Horizontal)
    titleCell.alignment = {
      horizontal: "center",
      vertical: "middle",
    };

    // 3. MENGATUR WARNA BACKGROUND KOTAK
    titleCell.fill = {
      type: "pattern",
      pattern: "solid",
      // Format warnanya adalah ARGB (Alpha, Red, Green, Blue).
      // Tambahkan 'FF' di depan kode Hex HTML biasa.
      // Contoh: FF0d6efd = Biru Bootstrap, FF198754 = Hijau, FFFFFF00 = Kuning
      fgColor: { argb: "FF003366" }, // Saya beri warna Biru Dongker (Navy) agar sangat elegan!
    };

    worksheet.getRow(1).height = 40; // Mempertinggi baris judul agar lebih lega

    // ==========================================
    // 1. MENGATUR STYLE HEADER (Baris ke-2)
    // ==========================================
    const headerRow = worksheet.getRow(2);
    headerRow.height = 35;

    headerRow.eachCell((cell) => {
      // Font
      cell.font = {
        name: "Arial",
        bold: true,
        color: { argb: "FFFFFFFF" },
        size: 11,
      };
      // Rata Tengah
      cell.alignment = {
        horizontal: "center",
        vertical: "middle",
        wrapText: true,
      };
      // Warna Background
      cell.fill = {
        type: "pattern",
        pattern: "solid",
        fgColor: { argb: "FF0d6efd" },
      };
      // Garis
      cell.border = {
        top: { style: "thin" },
        left: { style: "thin" },
        bottom: { style: "thin" },
        right: { style: "thin" },
      };
    }); // <--- PERHATIKAN: Kurung tutup headerRow berakhir di sini!

    // Inisialisasi variabel statistik (Pastikan ini ada agar tidak error statsJurusan)
    const statsJurusan = {};
    const statsAlat = {};
    const statsPerbaikan = {};
    let totalLaporan = 0;

    // ==========================================
    // 2. LOOPING MEMASUKKAN ISI DATA
    // ==========================================
    for (const d of allData) {
      totalLaporan++;

      // 1. Tampung teks rawan panjang ke variabel agar bisa dihitung
      const alatText = stripHtml(d[3]);
      const lokasiText = stripHtml(d[5]);
      const keluhanText = stripHtml(d[7]);
      const diagnosaText = stripHtml(d[11]);

      // Tambahkan baris ke Excel
      const newRow = worksheet.addRow({
        nomor_urut: totalLaporan,
        no: stripHtml(d[0]),
        tgl: stripHtml(d[1]),
        tgl_perbaikan: stripHtml(d[2]),
        alat: alatText,
        inv: stripHtml(d[4]) || "-",
        lokasi: lokasiText || "-",
        unit: stripHtml(d[6]) || "-",
        status_kerusakan: stripHtml(d[8]) || "Belum Dicek",
        pelaksana: stripHtml(d[9]) || "-",
        validasi: stripHtml(d[12]),
        status_akhir: stripHtml(d[10]).toUpperCase() || "-",
        keluhan: keluhanText || "-",
        diagnosa: diagnosaText || "-",
        foto: "",
      });

      // ---------------------------------------------------------
      // LOGIKA TINGGI BARIS DINAMIS (DISEMPURNAKAN)
      // ---------------------------------------------------------

      // Cari teks mana yang paling panjang dari semua kolom lebar tersebut
      const maxLength = Math.max(
        alatText.length,
        lokasiText.length,
        keluhanText.length,
        diagnosaText.length
      );

      // Tinggi minimal dasar (agak lega untuk 1 baris)
      let dynamicHeight = 35;

      // Asumsi lebar kolom rata-rata muat 35 karakter sebelum dia turun/wrap ke baris baru.
      if (maxLength > 35) {
        // Hitung butuh berapa baris ekstra
        const extraLines = Math.floor(maxLength / 35);
        // Setiap baris ekstra, kita tambah tinggi 18 poin (lebih besar dari sebelumnya) agar tidak terpotong bawahnya
        dynamicHeight += extraLines * 18;
      }

      // Terapkan tingginya
      newRow.height = dynamicHeight;

      // ---------------------------------------------------------
      // 3. PROSES FOTO (DISEMPURNAKAN UNTUK MENGAMBIL DARI HTML)
      // ---------------------------------------------------------
      const rawFotoHtml = d[13] || "";
      let imgUrl = "";

      // Cek apakah di dalam kolom tersebut ada elemen gambar (tag HTML img)
      if (rawFotoHtml.includes("<img")) {
        // Buat elemen bayangan untuk mengekstrak URL (src) gambarnya
        let tempDiv = document.createElement("div");
        tempDiv.innerHTML = rawFotoHtml;
        let imgEl = tempDiv.querySelector("img");
        if (imgEl) {
          imgUrl = imgEl.src; // Langsung mengambil Full URL gambar
        }
      } else {
        // Jika formatnya hanya berupa teks nama file biasa (misal: "foto1.jpg, foto2.png")
        let firstFoto = stripHtml(rawFotoHtml).split(",")[0].trim();
        if (firstFoto && firstFoto !== "-" && firstFoto !== "") {
          imgUrl = `${BASE_URL}uploads/laporan/${firstFoto}`;
        }
      }

      // Jika URL gambar berhasil didapatkan, saatnya mendownload & menempelkan ke Excel
      if (imgUrl !== "") {
        try {
          const response = await fetch(imgUrl);
          if (response.ok) {
            const buffer = await response.arrayBuffer();
            const imageId = workbook.addImage({
              buffer: buffer,
              extension: "jpeg",
            });

            // Tempelkan gambar persis di kolom N (indeks ke-13)
            worksheet.addImage(imageId, {
              tl: { col: 14, row: newRow.number - 1 },
              br: { col: 15, row: newRow.number },
              editAs: "oneCell", // Gambar akan menyesuaikan ukuran kotak Excel
            });

            // JIKA ADA FOTO: Pastikan tinggi baris minimal 80 agar gambar tidak gepeng
            newRow.height = Math.max(dynamicHeight, 80);
          } else {
            console.warn("Gambar tidak ditemukan di server:", imgUrl);
          }
        } catch (err) {
          console.warn("Gagal menarik gambar dari URL:", err);
        }
      }
      // ---------------- AKHIR PROSES FOTO ----------------

      // ... (Sisa kode perhitungan statistik seperti statsJurusan tetap di bawah ini) ...

      // --- HITUNG STATISTIK ---
      const unitName = stripHtml(d[5]) || "Lainnya";
      statsJurusan[unitName] = (statsJurusan[unitName] || 0) + 1;

      const alatName = stripHtml(d[2]) || "Tanpa Nama";
      statsAlat[alatName] = (statsAlat[alatName] || 0) + 1;

      let statusAkhirStat = stripHtml(d[9]).toUpperCase() || "MENUNGGU";
      statsPerbaikan[statusAkhirStat] =
        (statsPerbaikan[statusAkhirStat] || 0) + 1;
      // -----------------------------------------------------------------------
    }

    // ==========================================
    // 3. MENGATUR STYLE ISI DATA (Baris 3 ke bawah)
    // ==========================================
    worksheet.eachRow((row, rowNumber) => {
      if (rowNumber > 2) {
        // Mengabaikan baris 1 (Judul) dan baris 2 (Header)
        row.eachCell((cell) => {
          // 1. Garis Kotak (Border)
          cell.border = {
            top: { style: "thin" },
            left: { style: "thin" },
            bottom: { style: "thin" },
            right: { style: "thin" },
          };

          // 2. Posisi Rata Tengah (Center) --> TAMBAHKAN KODE INI BUNG!
          cell.alignment = {
            horizontal: "center",
            vertical: "middle",
            wrapText: true, // Sangat penting agar keluhan yang panjang tidak bablas ke samping
          };
        });
      }
    });

    // ==========================================
    // 4. MEMBUAT DASHBOARD STATISTIK EXCEL
    // ==========================================
    const sheetGrafik = workbook.addWorksheet("Dashboard Statistik");

    // Bikin Judul Sheet Lebih Lebar
    sheetGrafik.mergeCells("B2:R2");
    const titleStat = sheetGrafik.getCell("B2");
    titleStat.value = "DASHBOARD STATISTIK RIWAYAT PERBAIKAN ASET";
    titleStat.font = { name: "Arial", size: 16, bold: true };
    titleStat.alignment = { horizontal: "center", vertical: "middle" };
    titleStat.fill = {
      type: "pattern",
      pattern: "solid",
      fgColor: { argb: "FFFFFF00" },
    };

    // Total Data
    sheetGrafik.getCell("B4").value = "Total Laporan Masuk:";
    sheetGrafik.getCell("C4").value = totalLaporan;
    sheetGrafik.getCell("B4").font = { bold: true };
    sheetGrafik.getCell("C4").font = {
      bold: true,
      color: { argb: "FF0d6efd" },
    };

    // --------------------------------------------------
    // GRAFIK 1: JURUSAN / UNIT TERBANYAK
    // --------------------------------------------------
    const sortedJurusan = Object.entries(statsJurusan).sort(
      (a, b) => b[1] - a[1]
    );
    const imgJurusan = await generateGenericChart({
      type: "bar",
      indexAxis: "y", // Bar Horizontal
      labels: sortedJurusan.map((i) => i[0]),
      data: sortedJurusan.map((i) => i[1]),
      colors: "#0d6efd", // Biru
      title: "Unit / Jurusan Terbanyak Lapor",
    });

    // --------------------------------------------------
    // GRAFIK 2: NAMA ALAT TERBANYAK (TOP 10 SAJA)
    // --------------------------------------------------
    // Kita slice(0, 10) agar grafiknya tidak kepenuhan kalau jenis alatnya banyak
    const sortedAlat = Object.entries(statsAlat)
      .sort((a, b) => b[1] - a[1])
      .slice(0, 10);
    const imgAlat = await generateGenericChart({
      type: "bar",
      indexAxis: "x", // Bar Vertikal
      labels: sortedAlat.map((i) => i[0]),
      data: sortedAlat.map((i) => i[1]),
      colors: "#fd7e14", // Orange
      title: "Top 10 Alat Paling Sering Rusak",
    });

    // --------------------------------------------------
    // GRAFIK 3: HASIL PERBAIKAN
    // --------------------------------------------------
    const sortedStatus = Object.entries(statsPerbaikan).sort(
      (a, b) => b[1] - a[1]
    );
    const imgStatus = await generateGenericChart({
      type: "doughnut",
      labels: sortedStatus.map((i) => i[0]),
      data: sortedStatus.map((i) => i[1]),
      colors: ["#198754", "#dc3545", "#ffc107", "#6c757d", "#0dcaf0"], // Hijau, Merah, Kuning, Abu
      title: "Persentase Hasil Perbaikan",
    });

    // Fungsi Pembantu Menempelkan Gambar ke Excel
    const addChartToSheet = async (b64, c, r, w, h) => {
      const res = await fetch(b64);
      const buff = await res.arrayBuffer();
      const id = workbook.addImage({ buffer: buff, extension: "png" });
      sheetGrafik.addImage(id, {
        tl: { col: c, row: r },
        br: { col: c + w, row: r + h },
      });
    };

    // --------------------------------------------------
    // PENEMPATAN POSISI GRAFIK DI EXCEL (Kiri, Kanan, Bawah)
    // --------------------------------------------------
    // Col 1 = Kolom B. Row 6 = Baris 7. Lebar 8 kolom, Tinggi 16 baris.
    await addChartToSheet(imgJurusan, 1, 6, 8, 16); // Kiri Atas
    await addChartToSheet(imgAlat, 10, 6, 8, 16); // Kanan Atas (Geser ke kolom K)
    await addChartToSheet(imgStatus, 5, 23, 8, 15); // Tengah Bawah

    const buffer = await workbook.xlsx.writeBuffer();
    const blob = new Blob([buffer], {
      type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
    });
    saveAs(blob, `Rekap_Riwayat_${new Date().toISOString().slice(0, 10)}.xlsx`);
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
      canvas.width = 800;
      canvas.height = 400;
      canvas.style.display = "none";
      document.body.appendChild(canvas);
    }
    const ctx = canvas.getContext("2d");
    if (window.tempChartInstance) {
      window.tempChartInstance.destroy();
    }
    let bgColors = config.colors;
    if (!Array.isArray(config.colors)) {
      bgColors = Array(config.data.length).fill(config.colors);
    }
    const datalabelsConfig = {
      display: true,
      font: { weight: "bold", size: 12 },
      formatter: Math.round,
      color: config.type === "doughnut" ? "white" : "black",
      anchor: config.type === "doughnut" ? "center" : "end",
      align:
        config.type === "doughnut"
          ? "center"
          : config.indexAxis === "y"
          ? "end"
          : "top",
      offset: 4,
    };

    window.tempChartInstance = new Chart(ctx, {
      type: config.type,
      plugins: [ChartDataLabels],
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
        indexAxis: config.indexAxis || "x",
        responsive: false,
        animation: false,
        layout: { padding: { top: 20, right: 30, bottom: 20, left: 10 } },
        plugins: {
          title: {
            display: true,
            text: config.title,
            font: { size: 16, weight: "bold" },
            padding: { bottom: 20 },
          },
          legend: { display: config.type === "doughnut", position: "bottom" },
          datalabels: datalabelsConfig,
        },
        scales: {
          x: { display: config.type === "bar" },
          y: { display: config.type === "bar" },
        },
      },
    });
    setTimeout(() => {
      resolve(canvas.toDataURL("image/png"));
    }, 300);
  });
}

// =======================================================
// FUNGSI EXPORT PDF (VERSI FIX SERVER-SIDE)
// =======================================================
window.exportToPDFKerusakan = async function (btn) {
  const originalText = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Proses PDF...';

  // Fungsi pembantu pembersih HTML (jaga-jaga jika belum ada di scope ini)
  const stripHtml = (html) => {
    if (!html) return "-";
    let tmp = document.createElement("DIV");
    tmp.innerHTML = html;
    return tmp.textContent || tmp.innerText || "-";
  };

  try {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF("l", "mm", [215, 330]);
    const pageWidth = doc.internal.pageSize.getWidth();
    doc.setFontSize(16);
    doc.setFont("helvetica", "bold");
    doc.text("REKAPITULASI RIWAYAT PERBAIKAN ASET", pageWidth / 2, 15, {
      align: "center",
    });

    doc.setFontSize(10);
    doc.setFont("helvetica", "normal");
    doc.text(
      `Dicetak pada: ${new Date().toLocaleString("id-ID")}`,
      pageWidth / 2,
      22,
      { align: "center" }
    );

    const tableBody = [];
    const rowImages = {};

    // 1. AMBIL DATA DARI DATATABLES (Bukan dari HTML)
    const table = $("#tableLaporan").DataTable();
    const allData = table.rows({ search: "applied" }).data().toArray();

    if (allData.length === 0) {
      Swal.fire({
        icon: "warning",
        title: "Perhatian",
        text: "Tidak ada data untuk diexport ke PDF.",
      });
      btn.disabled = false;
      btn.innerHTML = originalText;
      return;
    }

    let nomorUrut = 1;

    // 2. LOOPING DATA
    for (let i = 0; i < allData.length; i++) {
      const d = allData[i];

      // Bersihkan teks
      let statusCek = stripHtml(d[10]).toUpperCase(); // Dulu d[9]
      let diagnosaText = stripHtml(d[11]);

      if (statusCek === "RUSAK" || statusCek.includes("RUSAK")) {
        if (!diagnosaText || diagnosaText === "-") {
          diagnosaText = "Rusak Berat (Tanpa Keterangan)";
        }
      }

      let statusFisik = stripHtml(d[8]) || "Belum Dicek"; // Dulu d[7]
      statusFisik = statusFisik.charAt(0).toUpperCase() + statusFisik.slice(1);

      let validasiText = stripHtml(d[12]);            // Dulu d[11]

      // 3. PROSES FOTO
      const rawFotoHtml = d[13] || "";
      let imgUrl = "";

      if (rawFotoHtml.includes("<img")) {
        let tempDiv = document.createElement("div");
        tempDiv.innerHTML = rawFotoHtml;
        let imgEl = tempDiv.querySelector("img");
        if (imgEl) {
          imgUrl = imgEl.src;
        }
      } else {
        let firstFoto = stripHtml(rawFotoHtml).split(",")[0].trim();
        if (firstFoto && firstFoto !== "-" && firstFoto !== "") {
          imgUrl = `${BASE_URL}uploads/laporan/${firstFoto}`;
        }
      }

      // Jika URL foto ada, ubah jadi base64
      if (imgUrl !== "") {
        try {
          const response = await fetch(imgUrl);
          if (response.ok) {
            const blob = await response.blob();
            const base64 = await new Promise((resolve) => {
              const reader = new FileReader();
              reader.onloadend = () => resolve(reader.result);
              reader.readAsDataURL(blob);
            });
            rowImages[tableBody.length] = base64;
          }
        } catch (e) {
          console.warn("Gagal load gambar PDF", e);
        }
      }

      // 4. MASUKKAN KE TABEL PDF
      tableBody.push([
        nomorUrut++,
        stripHtml(d[0]), // No. Laporan
        stripHtml(d[1]), // Tgl Laporan
        stripHtml(d[2]) || "-", // <--- TGL PERBAIKAN
        stripHtml(d[3]), // Nama Alat
        stripHtml(d[4]) || "-", // No. Inv
        stripHtml(d[5]) || "-", // Lokasi
        stripHtml(d[6]) || "-", // Unit
        statusFisik,
        stripHtml(d[9]) || "-", // Teknisi/Pelaksana
        validasiText,
        statusCek,
        stripHtml(d[7]) || "-", // Keluhan
        diagnosaText,
        "", // Kolom kosong untuk foto
      ]);
    }

    // Konfigurasi AutoTable PDF
    doc.autoTable({
      head: [
        [
          "No",
          "No. Laporan",
          "Tgl",
          "Tgl Perbaikan",
          "Nama Alat",
          "No. Inv",
          "Lokasi",
          "Unit",
          "Kondisi",
          "Teknisi",
          "Validasi",
          "Status",
          "Keluhan",
          "Hasil Perbaikan",
          "Foto",
        ],
      ],
      body: tableBody,
      startY: 30,
      theme: "grid",
      styles: {
        halign: "center",
        valign: "middle",
        fontSize: 8,
        textColor: [0, 0, 0],
        lineColor: [0, 0, 0],
        lineWidth: 0.1,
        cellPadding: 2,
        overflow: "linebreak",
        minCellHeight: 15,
      },
      headStyles: {
        fillColor: [13, 110, 253],
        textColor: [255, 255, 255],
        fontStyle: "bold",
        lineWidth: 0.1,
      },
      columnStyles: {
        0: { cellWidth: 8 },
        1: { cellWidth: 18 }, // Perkecil sedikit agar muat
        2: { cellWidth: 16 }, // Tgl Laporan
        3: { cellWidth: 16 }, // Tgl Perbaikan
        // Sesuaikan sisa indeks jika Anda mengatur lebar spesifik
        12: { cellWidth: 25, halign: "left" }, // Keluhan (Indeks bergeser)
        13: { cellWidth: 25, halign: "left" }, // Hasil Perbaikan (Indeks bergeser)
        14: { cellWidth: 15 }, // Foto (Indeks bergeser dari 13 ke 14)
      },
      didParseCell: function (data) {
        if (data.section === "body") {
          // Kolom Status Perbaikan sekarang ada di index 11 (dulu 10)
          if (data.column.index === 11) {
            const text = data.cell.raw.toString();
            if (text.includes("RUSAK")) {
              data.cell.styles.textColor = [255, 0, 0];
              data.cell.styles.fontStyle = "bold";
            } else if (text.includes("SELESAI")) {
              data.cell.styles.textColor = [0, 128, 0];
              data.cell.styles.fontStyle = "bold";
            } else if (text.includes("MENUNGGU")) {
              data.cell.styles.textColor = [128, 128, 128];
              data.cell.styles.fontStyle = "italic";
            }
          }
        }
      },
      didDrawCell: function (data) {
        if (data.section === "body" && data.column.index === 14) {
          const rowIndex = data.row.index;
          if (rowImages[rowIndex]) {
            try {
              const dim = 12; // Ukuran kotak foto di PDF
              const x = data.cell.x + (data.cell.width - dim) / 2;
              const y = data.cell.y + (data.cell.height - dim) / 2;
              doc.addImage(rowImages[rowIndex], "JPEG", x, y, dim, dim);
            } catch (err) {}
          }
        }
      },
      didDrawPage: function (data) {
        let str = "Halaman " + doc.internal.getNumberOfPages();
        doc.setFontSize(8);
        doc.text(
          str,
          data.settings.margin.left,
          doc.internal.pageSize.height - 10
        );
      },
    });

    doc.save(`Rekap_Riwayat_${new Date().toISOString().slice(0, 10)}.pdf`);
  } catch (error) {
    console.error(error);
    Swal.fire({
      icon: "error",
      title: "Gagal",
      text: "Gagal export PDF: " + error.message,
    });
  } finally {
    btn.disabled = false;
    btn.innerHTML = originalText;
  }
};

// --- LOGIKA REALTIME STATISTIK ---
function updateDashboardStats() {
  const daterange = $("#filter_daterange").val() || "";
  const unit = $("#filter_unit").val() || "";
  const bulan = $("#cetak_bulan").val() || "";
  const tahun = $("#cetak_tahun").val() || "";

  const params = new URLSearchParams({
    daterange: daterange,
    unit: unit,
    bulan: bulan,
    tahun: tahun,
  });

  fetch(`${BASE_URL}teknisi/get_statistik_json?${params.toString()}`)
    .then((response) => {
      if (!response.ok) throw new Error("Gagal koneksi statistik");
      return response.json();
    })
    .then((data) => {
      $("#stat_total").text(data.total ?? 0);
      $("#stat_ringan").text(data.ringan ?? 0);
      $("#stat_sedang").text(data.sedang ?? 0);
      $("#stat_berat").text(data.berat ?? 0);
    })
    .catch((error) => {
      console.error("Error Statistik:", error);
    });
}

$(document).ready(function () {
  updateDashboardStats();
  $("#tableLaporan").on("draw.dt", function () {
    updateDashboardStats();
  });
});

document.addEventListener("DOMContentLoaded", () => {
  updateDashboardStats();
  setInterval(updateDashboardStats, 5000);
});

// =============================================
// INISIALISASI FILTER & DATATABLES
// =============================================
$(document).ready(function () {
  var table = $("#tableLaporan").DataTable({
    processing: true,
    serverSide: true,
    autoWidth: false,
    order: [],
    pageLength: 10,
    dom: '<"d-none"Bf>t<"dt-footer d-flex justify-content-between align-items-center mt-2"ip>',
    lengthMenu: [
      [10, 25, 50, 100, -1],
      [10, 25, 50, 100, "Semua"],
    ],
    buttons: [
      {
        extend: "pageLength",
        className: "btn btn-secondary btn-sm buttons-page-length",
        text: '<i class="fas fa-list me-1"></i> Show Rows</i>',
        align: "button-center",
      },
      {
        extend: "colvis",
        className: "btn btn-success btn-sm buttons-colvis",
        text: '<i class="fas fa-columns me-1"></i> Kolom Ditampilkan</i>',
        columns: ":not(:last-child)",
        postfixButtons: ["colvisRestore"],
        align: "button-center",
      },
    ],
    ajax: {
      url: BASE_URL + "teknisi/get_riwayat_datatable",
      type: "GET",
      data: function (data) {
        data.daterange = $("#filter_daterange").val();
        data.status = $("#filterStatus").val();
        data.unit = $("#filter_unit").val();
        data.bulan = $("#cetak_bulan").val();
        data.tahun = $("#cetak_tahun").val();
      },
    },
    language: {
      search: "",
      searchPlaceholder: "Cari data...",
      processing:
        "<div class='spinner-border text-primary' role='status'><span class='visually-hidden'>Loading...</span></div>",
      zeroRecords: "Tidak ada data ditemukan",
      info: "Showing _START_ to _END_ of _TOTAL_ entries",
      paginate: {
        first: "First",
        last: "Last",
        next: "Next",
        previous: "Previous",
      },
      buttons: { colvis: "Kolom Ditampilkan", colvisRestore: "Reset Kolom" },
    },
    initComplete: function (settings, json) {
      var wrapper = $(this).closest(".dataTables_wrapper");
      var searchContainer = $("#search-container");
      setTimeout(function () {
        var btnPageLength = wrapper.find(".buttons-page-length");
        if (btnPageLength.length) {
          btnPageLength.prependTo(searchContainer);
          btnPageLength
            .removeClass("mb-3 dt-button")
            .addClass("btn btn-success btn-sm")
            .css({
              display: "inline-block",
              margin: "0",
              "border-radius": "0",
            });
        }
        var btnColVis = wrapper.find(".buttons-colvis");
        if (btnColVis.length) {
          if (btnPageLength.length) btnColVis.insertAfter(btnPageLength);
          else btnColVis.prependTo(searchContainer);
          btnColVis
            .removeClass("dt-button")
            .addClass("btn btn-success btn-sm")
            .css({
              display: "inline-block",
              margin: "0",
              "border-radius": "0",
            });
        }
        var filterBox = wrapper.find(".dataTables_filter");
        if (filterBox.length) {
          searchContainer.append(filterBox);
          filterBox.css("margin-bottom", "0");
          filterBox.find("input").addClass("form-control form-control-sm").css({
            display: "inline-block",
            width: "200px",
            "margin-left": "0",
          });
        }
      }, 100);
    },
    drawCallback: function (settings) {
      var wrapper = $(this).closest(".dataTables_wrapper");
      var scrollableDiv = $(this).closest(".table-responsive");
      var footer = wrapper.find(".dt-footer");
      if (scrollableDiv.length && footer.length) {
        footer.insertAfter(scrollableDiv);
      }
    },
    columnDefs: [
      {
        targets: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
        className: "align-middle",
      },
      // === KODE BARU UNTUK KOLOM VALIDASI KEPALA (MENUNGGU & DISETUJUI SAJA) ===
      {
        targets: 12,
        className: "text-center align-middle",
        orderable: false,
        render: function (data, type, row) {
          // 'data' menerima teks dari Database PHP
          let validasi = data ? data.toString().trim() : "Menunggu";

          if (validasi === "Disetujui") {
            // Jika disetujui -> Tampil Icon Centang Hijau
            return '<span class="badge rounded-circle bg-success p-2 shadow-sm" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;" title="Disetujui"><i class="fas fa-check text-white"></i></span>';
          } else {
            // Jika selain Disetujui (yaitu Menunggu) -> Tampil Icon Jam Abu-abu
            return '<span class="badge rounded-circle bg-danger p-2 shadow-sm" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;" title="Menunggu Validasi"><i class="fas fa-times text-white"></i></span>';
          }
        },
      },

      { targets: 13, className: "text-center align-middle", orderable: false },
      {
        targets: 14,
        orderable: false,
        width: "1%",
        className: "text-center align-middle",
        data: null,
        defaultContent: `
            <div class="btn-group-vertical btn-group-sm" role="group" aria-label="Aksi">
                <button class="btn btn-outline-warning btn-edit" title="Edit"> <i class="fas fa-edit"></i></button>
                <button class="btn btn-outline-primary btn-sm btn-cetak-pdf" title="Cetak PDF">
                    <i class="fas fa-print"></i>
                </button>
                <button class="btn btn-outline-danger btn-hapus" title="Hapus"><i class="fas fa-trash"></i></button>
            </div>`,
      },
    ],
  });

  flatpickr("#filter_daterange", {
    mode: "range",
    dateFormat: "Y-m-d",
    altInput: true,
    altFormat: "j F Y",
    locale: { rangeSeparator: " s/d " },
    onReady: function (selectedDates, dateStr, instance) {
      const footer = document.createElement("div");
      footer.classList.add(
        "d-flex",
        "justify-content-between",
        "p-2",
        "border-top",
        "bg-white"
      );
      const clearBtn = document.createElement("button");
      clearBtn.type = "button";
      clearBtn.className =
        "btn btn-sm btn-link text-danger fw-bold text-decoration-none";
      clearBtn.innerText = "Clear";
      clearBtn.onclick = () => {
        instance.clear();
        instance.close();
      };
      const todayBtn = document.createElement("button");
      todayBtn.type = "button";
      todayBtn.className =
        "btn btn-sm btn-link text-primary fw-bold text-decoration-none";
      todayBtn.innerText = "Hari Ini";
      todayBtn.onclick = () => {
        instance.setDate([new Date(), new Date()], true);
        instance.close();
      };
      footer.appendChild(clearBtn);
      footer.appendChild(todayBtn);
      instance.calendarContainer.appendChild(footer);
    },
    onClose: function (selectedDates, dateStr, instance) {
      if (selectedDates.length === 1) {
        var date = selectedDates[0];
        instance.setDate([date, date], true);
      }
    },
    onChange: function (selectedDates, dateStr, instance) {
      if (selectedDates.length === 2 || selectedDates.length === 0)
        table.draw();
    },
  });

  $("#filterStatus, #cetak_bulan, #cetak_tahun").change(function () {
    table.draw();
  });

  $("#filter_unit")
    .select2({
      placeholder: "Pilih/Cari Unit...",
      allowClear: true,
      width: "100%",
    })
    .on("select2:select select2:unselect", function (e) {
      table.draw();
    });

  $("#btnReload").click(function () {
    table.ajax.reload(null, false);
  });
});

const BASE_URL_TAMBAH = BASE_URL + "teknisi/tambah_data";
const BASE_URL_GET_NOMOR = BASE_URL + "teknisi/get_nomor_otomatis";
let modalTambahInstance;

document.addEventListener("DOMContentLoaded", () => {
  const el = document.getElementById("ModalTambahLaporan");
  if (el) modalTambahInstance = new bootstrap.Modal(el);
  const form = document.getElementById("formTambahLaporan");
  if (form) form.addEventListener("submit", handleSimpanData);
  initLogikaFormTambah();
});

window.openModalTambah = async function () {
  document.getElementById("formTambahLaporan").reset();
  const selectLokasi = document.getElementById("tambah_lokasi_alat");
  if (selectLokasi) {
    selectLokasi.innerHTML =
      '<option value="">-- Pilih Jurusan/Unit di atas --</option>';
    selectLokasi.disabled = true;
  }
  const inputNomorView = document.getElementById("tambah_nomor_laporan_view");
  const inputNomorHidden = document.getElementById("tambah_nomor_laporan");
  if (inputNomorView) inputNomorView.value = "Sedang mengambil nomor...";
  try {
    const response = await fetch(BASE_URL_GET_NOMOR, {
      method: "GET",
      headers: { "X-Requested-With": "XMLHttpRequest" },
    });
    if (!response.ok) throw new Error(`HTTP Error: ${response.status}`);
    const data = await response.json();
    if (data.nomor) {
      if (inputNomorView) inputNomorView.value = data.nomor;
      if (inputNomorHidden) inputNomorHidden.value = data.nomor;
    } else {
      if (inputNomorView)
        inputNomorView.value = "Gagal: " + (data.status || "Unknown");
    }
  } catch (error) {
    console.error("Error get nomor:", error);
    if (inputNomorView) inputNomorView.value = "Error Koneksi (Cek Console)";
    const fallback =
      "LPR-" + new Date().toISOString().slice(0, 10).replace(/-/g, "") + "XXX";
    if (inputNomorHidden) inputNomorHidden.value = fallback;
  }
  if (modalTambahInstance) modalTambahInstance.show();
  else
    new bootstrap.Modal(document.getElementById("ModalTambahLaporan")).show();
};

function initLogikaFormTambah() {
  const selectAlat = document.getElementById("tambah_nama_alat");
  const inputInventaris = document.getElementById("tambah_nomor_inventaris");
  const inputPelaksana = document.getElementById("tambah_pelaksana");
  const selectJurusan = document.getElementById("tambah_unit");
  const selectLokasi = document.getElementById("tambah_lokasi_alat");

  if (selectAlat) {
    selectAlat.addEventListener("change", function () {
      const selectedOption = this.options[this.selectedIndex];
      const namaAlat = this.value;
      const noInv = selectedOption.getAttribute("data-inventaris");
      if (inputInventaris)
        inputInventaris.value = noInv && noInv !== "null" ? noInv : "-";
      if (inputPelaksana) {
        if (pelaksanaMap && pelaksanaMap[namaAlat])
          inputPelaksana.value = pelaksanaMap[namaAlat];
        else inputPelaksana.value = "";
      }
    });
  }

  if (selectJurusan && selectLokasi) {
    selectJurusan.addEventListener("change", function () {
      const unit = this.value;
      selectLokasi.innerHTML = '<option value="">-- Pilih Lokasi --</option>';
      if (unit && dataMasterLokasi[unit]) {
        selectLokasi.disabled = false;
        dataMasterLokasi[unit].forEach((ruangan) => {
          const opt = document.createElement("option");
          opt.value = ruangan;
          opt.textContent = ruangan;
          selectLokasi.appendChild(opt);
        });
      } else if (["Lainnya", "KPA", "Pos Satpam"].includes(unit)) {
        selectLokasi.disabled = false;
        const opt = document.createElement("option");
        opt.value = "Lokasi Umum " + unit;
        opt.textContent = "Lokasi Umum " + unit;
        selectLokasi.appendChild(opt);
      } else {
        selectLokasi.innerHTML =
          '<option value="">-- Pilih Jurusan/Unit di atas --</option>';
        selectLokasi.disabled = true;
      }
    });
  }
}

async function handleSimpanData(e) {
  e.preventDefault();
  const btn = document.getElementById("btnSimpanTambah");
  const textAsli = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML =
    '<span class="spinner-border spinner-border-sm me-2"></span> Menyimpan...';
  const formData = new FormData(this);
  try {
    const response = await fetch(BASE_URL_TAMBAH, {
      method: "POST",
      body: formData,
      headers: { "X-Requested-With": "XMLHttpRequest" },
    });
    const result = await response.json();
    if (result.status === "success") {
      Swal.fire({
        icon: "success",
        title: "Berhasil!",
        text: result.message,
        timer: 1500,
        showConfirmButton: false,
      }).then(() => {
        modalTambahInstance.hide();
        location.reload();
      });
    } else {
      Swal.fire({ icon: "error", title: "Gagal", text: result.message });
    }
  } catch (error) {
    console.error("Error Simpan:", error);
    Swal.fire({
      icon: "error",
      title: "Error",
      text: "Terjadi kesalahan koneksi.",
    });
  } finally {
    btn.disabled = false;
    btn.innerHTML = textAsli;
  }
}

$(document).on("click", ".btn-hapus", function (e) {
  e.preventDefault();
  var row = $(this).closest("tr");
  var rowData = $("#tableLaporan").DataTable().row(row).data();
  hapusLaporan(rowData[0]);
});

function hapusLaporan(id) {
  Swal.fire({
    title: "Yakin hapus data?",
    text: "Data Laporan " + id + " akan dihapus permanen!",
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
      const formData = new FormData();
      formData.append("nomor_laporan", id);
      const url = BASE_URL + "teknisi/hapus_laporan";
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
              title: "Berhasil!",
              text: data.message,
              icon: "success",
              timer: 1500,
              showConfirmButton: false,
            }).then(() => {
              $("#tableLaporan").DataTable().ajax.reload(null, false);
              if (typeof updateDashboardStats === "function")
                updateDashboardStats();
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
}

$(document).on("click", ".btn-edit", function (e) {
  e.preventDefault();
  var row = $(this).closest("tr");
  var rowData = $("#tableLaporan").DataTable().row(row).data();
  openModalEdit(rowData[0]);
});

async function openModalEdit(nomorLaporan) {
  if (!nomorLaporan) return;
  document.getElementById("formEditLaporan").reset();
  if (window.statusModalEdit) window.statusModalEdit.show();
  else new bootstrap.Modal(document.getElementById("ModalEditLaporan")).show();

  try {
    const response = await fetch(
      `${BASE_URL}teknisi/get_data_by_nomor/${nomorLaporan}`,
      { headers: { "X-Requested-With": "XMLHttpRequest" } }
    );
    if (!response.ok) throw new Error("Gagal mengambil data");
    const result = await response.json();
    if (result.status === "success") {
      fillEditForm(result.data);
    } else {
      Swal.fire({
        icon: "error",
        title: "Gagal",
        text: "Gagal memuat data: " + result.message,
      });
    }
  } catch (error) {
    console.error(error);
    Swal.fire({
      icon: "error",
      title: "Koneksi Error",
      text: "Terjadi kesalahan koneksi saat mengambil data edit.",
    });
  }
}

function fillEditForm(data) {
  const map = {
    edit_nomor_laporan: data.nomor_laporan,
    edit_nomor_laporan_view: data.nomor_laporan,
    edit_nama_alat: data.nama_alat,
    edit_nomor_inventaris: data.nomor_inventaris,
    edit_lokasi_alat: data.lokasi || data.lokasi_alat,
    edit_unit: data.unit,
    edit_status_kerusakan: data.status_kerusakan,
    edit_pelaksana: data.pelaksana_nama || data.pelaksana,
    edit_pelapor: data.nama_pelapor,
    edit_kerusakan_keluhan: data.kerusakan || data.kerusakan_keluhan,
    edit_media_laporan: data.media_laporan,
    edit_uraian_pekerjaan: data.uraian_pekerjaan || "",
    edit_nama_barang: data.nama_barang || "",
    edit_jumlah_barang: data.jumlah_barang || "",
    edit_cetak_identitas_alat: data.cetak_identitas_alat || "",
  };
  for (const [id, value] of Object.entries(map)) {
    const el = document.getElementById(id);
    if (el) el.value = value || "";
  }
  if (data.tanggal_laporan) {
    const tgl = data.tanggal_laporan.split(" ")[0];
    const elTgl = document.getElementById("edit_tanggal");
    if (elTgl) elTgl.value = tgl;
  }
}

async function handleSubmitEdit(e) {
  e.preventDefault();
  const btn = document.getElementById("btnSimpanEdit");
  const textAsli = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML =
    '<span class="spinner-border spinner-border-sm me-2"></span> Menyimpan...';
  const formData = new FormData(this);
  try {
    const response = await fetch(`${BASE_URL}teknisi/update_data`, {
      method: "POST",
      body: formData,
      headers: { "X-Requested-With": "XMLHttpRequest" },
    });
    const result = await response.json();
    if (result.status === "success") {
      Swal.fire({
        icon: "success",
        title: "Berhasil Disimpan!",
        text: result.message,
        timer: 1500,
        showConfirmButton: false,
      }).then(() => {
        if (window.statusModalEdit) window.statusModalEdit.hide();
        $("#tableLaporan").DataTable().ajax.reload(null, false);
      });
    } else {
      Swal.fire({ icon: "error", title: "Gagal", text: result.message });
    }
  } catch (error) {
    console.error("Error Edit:", error);
    Swal.fire({
      icon: "error",
      title: "Error",
      text: "Terjadi kesalahan koneksi atau server.",
    });
  } finally {
    btn.disabled = false;
    btn.innerHTML = textAsli;
  }
}

// LISTENER TOMBOL CETAK PDF (VIA TAB BARU)
$("#tableLaporan tbody").on("click", ".btn-cetak-pdf", function (e) {
  e.preventDefault();
  var table = $("#tableLaporan").DataTable();
  var tr = $(this).closest("tr");
  if (tr.hasClass("child")) tr = tr.prev();
  var data = table.row(tr).data();
  var nomorLaporan = data[0];
  if (!nomorLaporan) {
    Swal.fire("Error", "Gagal membaca Nomor Laporan.", "error");
    return;
  }
  var url = BASE_URL + "cetak/cetak_laporan/" + nomorLaporan;
  window.open(url, "_blank");
});

// =========================================================
// LISTENER TOMBOL CETAK PDF (PER BARIS DATATABLES)
// =========================================================
$(document).on("click", ".btn-cetak-pdf", function (e) {
  e.preventDefault();

  // 1. Ambil instance DataTables
  var table = $("#tableLaporan").DataTable();

  // 2. Cari baris (tr) yang sedang diklik
  var tr = $(this).closest("tr");
  if (tr.hasClass("child")) {
    tr = tr.prev(); // Antisipasi jika DataTables dalam mode responsive/collapsible
  }

  // 3. Ambil data dari baris tersebut
  var data = table.row(tr).data();

  // 4. Asumsi Nomor Laporan ada di kolom indeks ke-0
  var nomorLaporan = data[0];

  if (!nomorLaporan) {
    Swal.fire("Error", "Gagal membaca Nomor Laporan dari tabel.", "error");
    return;
  }

  // 5. Susun URL menggunakan BASE_URL dan Nomor Laporan
  var url = BASE_URL + "cetak/cetak_laporan/" + nomorLaporan;

  // 6. Buka PDF di Tab Baru
  window.open(url, "_blank");
}); // <---- PERHATIKAN: KURUNG TUTUP HARUS ADA DI SINI BUNG!

// =========================================================
// LISTENER TOMBOL CETAK PDF (GLOBAL REKAP) - TERPISAH
// =========================================================
document.addEventListener("DOMContentLoaded", () => {
  const btnCetakGlobal = document.getElementById("btnCetakGlobal");

  if (btnCetakGlobal) {
    btnCetakGlobal.addEventListener("click", function (e) {
      e.preventDefault(); // Mencegah form reload

      const daterange = document.getElementById("filter_daterange").value || "";
      const bulan = document.getElementById("cetak_bulan").value || "";
      const tahun = document.getElementById("cetak_tahun").value || "";
      const status = document.getElementById("filterStatus").value || "";
      const unit = document.getElementById("filter_unit").value || "";

      const params = new URLSearchParams({
        daterange: daterange,
        bulan: bulan,
        tahun: tahun,
        status: status,
        unit: unit,
      });

      // PASTIKAN URL INI SESUAI DENGAN ROUTE BUNG DI app/Config/Routes.php
      const url = BASE_URL + "cetak/cetak_filter_pdf?" + params.toString();
      window.open(url, "_blank");
    });
  }
});
