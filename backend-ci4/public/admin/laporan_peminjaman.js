// ==========================================
// VARIABLE GLOBAL
// ==========================================
let modalUnitInstance;

// ==========================================
// 1. SAAT HALAMAN DIMUAT
// ==========================================
window.addEventListener("DOMContentLoaded", () => {
  const modalEl = document.getElementById("modal_unit");
  if (modalEl) modalUnitInstance = new bootstrap.Modal(modalEl);

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
  setupSidebar();
  setupModalPeminjamanLogic();
  initFilter();
});

// =======================================================
// LOGIKA HAPUS PEMINJAMAN
// =======================================================
window.hapusPeminjaman = function (id) {
  Swal.fire({
    title: 'Yakin ingin menghapus?',
    text: 'Data peminjaman ini akan dihapus permanen.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Ya, Hapus!',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      fetch(`${BASE_URL}admin/hapus_peminjaman`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: `id_peminjaman=${id}`
      })
        .then(response => response.json())
        .then(data => {
          if (data.status === 'success') {
            Swal.fire({
              icon: 'success',
              title: 'Berhasil!',
              text: data.message,
              timer: 1500,
              showConfirmButton: false
            }).then(() => {
              location.reload();
            });
          } else {
            Swal.fire('Gagal', data.message, 'error');
          }
        })
        .catch(error => {
          console.error(error);
          Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
        });
    }
  });
};

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

// =======================================================
// LOGIKA MODAL TAMBAH PEMINJAMAN
// =======================================================
function setupModalPeminjamanLogic() {
  const modalPeminjaman = document.getElementById("modalTambahPeminjaman");
  if (modalPeminjaman) {
    modalPeminjaman.addEventListener("show.bs.modal", function () {
      generateNomorOtomatis();
    });
  }

  const selectUnit = document.getElementById("selectUnit");
  const selectLokasi = document.getElementById("selectLokasi");

  if (selectUnit && selectLokasi) {
    const allLokasiOptions = Array.from(
      selectLokasi.querySelectorAll('option:not([value=""])')
    );

    selectUnit.addEventListener("change", function () {
      const selectedUnitId = this.value;

      selectLokasi.value = "";
      selectLokasi.innerHTML = '<option value="">-- Pilih Lokasi --</option>';

      if (!selectedUnitId) {
        selectLokasi.disabled = true;
        selectLokasi.innerHTML =
          '<option value="">-- Pilih Unit Dulu --</option>';
        return;
      }

      selectLokasi.disabled = false;
      let countAvailable = 0;

      allLokasiOptions.forEach((option) => {
        const rawData = option.getAttribute("data-units");
        try {
          const owners = JSON.parse(rawData);
          if (owners.map(String).includes(String(selectedUnitId))) {
            selectLokasi.appendChild(option);
            countAvailable++;
          }
        } catch (e) {
          console.error("Gagal parse data lokasi:", rawData);
        }
      });

      if (countAvailable === 0) {
        selectLokasi.innerHTML +=
          '<option value="" disabled>Tidak ada lokasi untuk unit ini</option>';
      }
    });
  }
}

// Fungsi untuk membuka modal dan mengisi form untuk edit
window.editPeminjaman = async function (id) {
  try {
    const res = await fetch(`${BASE_URL}admin/get_peminjaman/${id}`);
    if (!res.ok) throw new Error('Gagal mengambil data');
    const data = await res.json();

    // Isi form
    document.getElementById('id_peminjaman').value = data.id_peminjaman || '';
    document.getElementById('nomor').value = data.nomor || '';

    const selectUnit = document.getElementById('selectUnit');
    const selectLokasi = document.getElementById('selectLokasi');
    if (selectUnit) {
      selectUnit.value = data.id_unit || '';
      // Trigger change agar lokasi di-populate
      selectUnit.dispatchEvent(new Event('change'));
    }

    // Setelah opsi lokasi ter-populate, set value lokasi
    setTimeout(() => {
      if (selectLokasi) selectLokasi.value = data.lokasi || '';
    }, 150);

    document.querySelector('[name="kegiatan"]').value = data.kegiatan || '';
    document.querySelector('[name="mulai"]').value = data.tanggal_mulai || '';
    document.querySelector('[name="selesai"]').value = data.tanggal_selesai || '';
    document.querySelector('[name="keterangan"]').value = data.keterangan || '';
    document.querySelector('[name="identitas"]').value = data.identitas || '';
    document.querySelector('[name="peminjam"]').value = data.peminjam || '';
    document.querySelector('[name="handphone"]').value = data.handphone || '';

    // Ubah judul modal jika ada
    const modalTitle = document.querySelector('#modalTambahPeminjaman .modal-title');
    if (modalTitle) modalTitle.innerText = 'EDIT DATA PEMINJAMAN';

    const modalEl = document.getElementById('modalTambahPeminjaman');
    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    modal.show();
  } catch (err) {
    console.error(err);
    Swal.fire('Error', 'Gagal memuat data untuk diedit.', 'error');
  }
}

// FUNGSI GENERATE NOMOR (Format: YYYYMMDDxxx)
function generateNomorOtomatis() {
  const today = new window.Date();
  const year = today.getFullYear();
  const month = String(today.getMonth() + 1).padStart(2, "0");
  const day = String(today.getDate()).padStart(2, "0");
  const datePrefix = `${year}${month}${day}`;

  fetch(`${BASE_URL}admin/get_last_nomor_peminjaman`)
    .then((response) => response.json())
    .then((data) => {
      let nextSequence = "001";

      if (data.last_nomor) {
        const lastDate = data.last_nomor.substring(0, 8);
        if (lastDate === datePrefix) {
          const lastSeq = parseInt(data.last_nomor.substring(8));
          nextSequence = String(lastSeq + 1).padStart(3, "0");
        }
      }
      document.getElementById("nomor").value = `${datePrefix}${nextSequence}`;
    })
    .catch((error) => {
      console.error("Error generating nomor:", error);
      document.getElementById("nomor").value = `${datePrefix}001`;
    });
}

// FUNGSI SIMPAN DATA PEMINJAMAN
window.savePeminjaman = function () {
  const form = document.getElementById("formTambahPeminjaman");

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

  fetch(`${BASE_URL}admin/simpan_peminjaman`, {
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
        const modalEl = document.getElementById("modalTambahPeminjaman");
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.hide();

        setTimeout(() => {
          Swal.fire({
            icon: "success",
            title: "Berhasil!",
            text: data.message,
            timer: 1500,
            showConfirmButton: false,
          }).then(() => {
            location.reload();
          });
        }, 300);
      } else {
        let errorHtml = data.message;
        if (data.errors) {
          errorHtml = '<ul style="text-align: left; margin-left: 20px;">';
          if (typeof data.errors === "object") {
            Object.values(data.errors).forEach(
              (msg) => (errorHtml += `<li>${msg}</li>`)
            );
          } else {
            errorHtml += `<li>${data.errors}</li>`;
          }
          errorHtml += "</ul>";
        }
        Swal.fire({ icon: "error", title: "Gagal Menyimpan", html: errorHtml });
      }
    })
    .catch((error) => {
      console.error(error);
      Swal.fire("Error", "Terjadi kesalahan sistem.", "error");
    });
};

window.hapusPeminjamanRow = function (id) {
  const row = document.getElementById("row-" + id);
  const container = document.getElementById("peminjaman-form-container");
  if (container.children.length > 1) {
    row.remove();
  } else {
    Swal.fire({
      icon: 'warning',
      title: 'Perhatian',
      text: 'Minimal satu data harus diisi.',
      confirmButtonText: 'OK'
    });
  }
};

// =======================================================
// FUNGSI EXPORT EXCEL KHUSUS PEMINJAMAN (FULL FEATURES)
// =======================================================
window.exportToExcelPeminjaman = async function (btn) {
  const originalText = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Proses...';

  try {
    const workbook = new window.ExcelJS.Workbook();
    const worksheet = workbook.addWorksheet("Data Peminjaman");

    const styleCenter = {
      alignment: { horizontal: "center", vertical: "middle" },
    };

    worksheet.columns = [
      { header: "No", key: "id", width: 5, style: styleCenter },
      { header: "Nomor Surat", key: "nomor", width: 20, style: styleCenter },
      {
        header: "Unit/Jurusan/UKM",
        key: "unit",
        width: 25,
        style: styleCenter,
      },
      {
        header: "Kegiatan/Acara",
        key: "kegiatan",
        width: 30,
        style: styleCenter,
      },
      { header: "Lokasi", key: "lokasi", width: 40, style: styleCenter },
      { header: "Tanggal", key: "tanggal", width: 25, style: styleCenter },
      {
        header: "Nomor Identitas",
        key: "identitas",
        width: 20,
        style: styleCenter,
      },
      { header: "Peminjam", key: "peminjam", width: 25, style: styleCenter },
      { header: "Handphone", key: "hp", width: 15, style: styleCenter },
      { header: "Lampiran", key: "lampiran", width: 20, style: styleCenter },
      {
        header: "Keterangan",
        key: "keterangan",
        width: 30,
        style: styleCenter,
      },
    ];

    worksheet.insertRow(1, []);
    worksheet.mergeCells("A1:K1");
    const titleCell = worksheet.getCell("A1");

    titleCell.value = "DATA REKAPITULASI PEMINJAMAN";
    titleCell.font = {
      name: "Arial",
      size: 14,
      bold: true,
      color: { argb: "FFFFFFFF" },
    };
    titleCell.alignment = styleCenter.alignment;
    titleCell.fill = {
      type: "pattern",
      pattern: "solid",
      fgColor: { argb: "FF0D6EFD" },
    };
    worksheet.getRow(1).height = 30;

    const headerRow = worksheet.getRow(2);
    headerRow.height = 30;

    for (let i = 1; i <= worksheet.columns.length; i++) {
      const cell = headerRow.getCell(i);
      cell.fill = {
        type: "pattern",
        pattern: "solid",
        fgColor: { argb: "FFdee2e6" },
      };
      cell.font = {
        name: "Calibri",
        size: 12,
        bold: true,
        color: { argb: "FF000000" },
      };
      cell.alignment = styleCenter.alignment;
      cell.border = {
        top: { style: "thin" },
        left: { style: "thin" },
        bottom: { style: "thin" },
        right: { style: "thin" },
      };
    }

    const table = $('#peminjamanTable').DataTable();

    const dataRows = table.rows({
      search: 'applied'
    }).nodes();

    let counter = 1;
    const statsUnit = {};
    const statsLokasi = {};

    dataRows.forEach((row) => {
      const cells = row.querySelectorAll("td");
      if (cells.length < 2) return;

      const nomorSurat = cells[1]?.innerText.trim() || "-";
      const unit = cells[2]?.innerText.trim() || "-";
      const kegiatan = cells[3]?.innerText.trim() || "-";
      const lokasi = cells[4]?.innerText.trim() || "-";
      const rawTanggal = cells[5]?.innerText || "-";
      const tanggal = rawTanggal.replace(/\n/g, " ").trim();
      const identitas = cells[6]?.innerText.trim() || "-";
      const peminjam = cells[7]?.innerText.trim() || "-";
      const hp = cells[8]?.innerText.trim() || "-";

      let lampiranText = "-";
      const linkLampiran = cells[9]?.querySelector("a");
      if (linkLampiran) {
        lampiranText = "Ada Lampiran";
      } else {
        lampiranText = cells[9]?.innerText.trim() || "-";
      }

      const keterangan = cells[10]?.innerText.trim() || "-";

      worksheet.addRow({
        id: counter++,
        nomor: nomorSurat,
        unit: unit,
        kegiatan: kegiatan,
        lokasi: lokasi,
        tanggal: tanggal,
        identitas: identitas,
        peminjam: peminjam,
        hp: hp,
        lampiran: lampiranText,
        keterangan: keterangan,
      });

      statsUnit[unit] = (statsUnit[unit] || 0) + 1;
      const cleanLokasi = lokasi.replace(/\n/g, ", ").trim();
      statsLokasi[cleanLokasi] = (statsLokasi[cleanLokasi] || 0) + 1;
    });

    worksheet.eachRow((row, rowNumber) => {
      if (rowNumber > 1) {
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

    // ---------------- SHEET 2: DASHBOARD STATISTIK ----------------
    const sheetGrafik = workbook.addWorksheet("Dashboard Statistik");

    sheetGrafik.mergeCells("B2:E2");
    sheetGrafik.getCell("B2").value = "STATISTIK PEMINJAMAN";
    sheetGrafik.getCell("B2").font = { size: 16, bold: true };

    sheetGrafik.getCell("B4").value = "Total Peminjaman";
    sheetGrafik.getCell("C4").value = counter - 1;
    sheetGrafik.getCell("B4").font = { bold: true };

    const sortedUnit = Object.entries(statsUnit)
      .sort((a, b) => b[1] - a[1])
      .slice(0, 10);
    const imgUnit = await generateGenericChart({
      type: "bar",
      labels: sortedUnit.map((i) => i[0]),
      data: sortedUnit.map((i) => i[1]),
      colors: "#36a2eb",
      title: "Top 10 Unit Peminjam Terbanyak",
    });

    const sortedLokasi = Object.entries(statsLokasi)
      .sort((a, b) => b[1] - a[1])
      .slice(0, 5);
    const imgLokasi = await generateGenericChart({
      type: "pie",
      labels: sortedLokasi.map((i) => i[0]),
      data: sortedLokasi.map((i) => i[1]),
      colors: ["#ff6384", "#36a2eb", "#ffce56", "#4bc0c0", "#9966ff"],
      title: "5 Lokasi Paling Sering Digunakan",
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

    await addChartToSheet(imgUnit, 1, 6, 10, 12);
    await addChartToSheet(imgLokasi, 12, 6, 8, 12);

    const buffer = await workbook.xlsx.writeBuffer();
    const fileName = `Laporan_Peminjaman_${new window.Date()
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

function generateGenericChart(config) {
  return new window.Promise((resolve) => {
    const canvas = document.getElementById("tempChartCanvas");
    if (!canvas) {
      console.error("Canvas #tempChartCanvas tidak ditemukan!");
      resolve(null);
      return;
    }
    const ctx = canvas.getContext("2d");
    if (window.tempChartInstance) window.tempChartInstance.destroy();

    let bgColors = Array.isArray(config.colors)
      ? config.colors
      : Array(config.data.length).fill(config.colors);

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
        indexAxis:
          config.type === "bar" && config.indexAxis ? config.indexAxis : "x",
        plugins: {
          title: { display: true, text: config.title, font: { size: 14 } },
          legend: { display: config.type !== "bar", position: "bottom" },
        },
      },
    });

    setTimeout(() => {
      resolve(canvas.toDataURL("image/png"));
    }, 300);
  });
}

// =======================================================
// FUNGSI EXPORT PDF (LANDSCAPE & DATA LENGKAP)
// =======================================================
window.exportToPDFPeminjaman = async function (btn) {
  const originalText = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Proses...';

  try {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF("l", "mm", "a4");

    doc.setFontSize(16);
    doc.setFont("helvetica", "bold");
    doc.text(
      "DATA REKAPITULASI PEMINJAMAN",
      doc.internal.pageSize.getWidth() / 2,
      15,
      { align: "center" }
    );

    doc.setFontSize(10);
    doc.setFont("helvetica", "normal");
    doc.text(
      `Dicetak pada: ${new window.Date().toLocaleString("id-ID")}`,
      doc.internal.pageSize.getWidth() / 2,
      22,
      { align: "center" }
    );

    const table = $('#peminjamanTable').DataTable();

    const dataRows = table.rows({
      search: 'applied'
    }).nodes();
    const tableBody = [];
    let counter = 1;

    dataRows.forEach((row) => {
      const cells = row.querySelectorAll("td");
      if (cells.length < 2) return;

      const nomorSurat = cells[1]?.innerText.trim() || "-";
      const unit = cells[2]?.innerText.trim() || "-";
      const kegiatan = cells[3]?.innerText.trim() || "-";
      const lokasi = cells[4]?.innerText.trim() || "-";
      const rawTanggal = cells[5]?.innerText || "-";
      const tanggal = rawTanggal.replace(/\n/g, " ").trim();
      const identitas = cells[6]?.innerText.trim() || "-";
      const peminjam = cells[7]?.innerText.trim() || "-";
      const hp = cells[8]?.innerText.trim() || "-";

      let lampiranText = "-";
      const linkLampiran = cells[9]?.querySelector("a");
      if (linkLampiran) {
        lampiranText = "Ada Lampiran";
      } else {
        lampiranText = cells[9]?.innerText.trim() || "-";
      }
      const keterangan = cells[10]?.innerText.trim() || "-";

      tableBody.push([
        counter++,
        nomorSurat,
        unit,
        kegiatan,
        lokasi,
        tanggal,
        identitas,
        peminjam,
        hp,
        lampiranText,
        keterangan,
      ]);
    });

    doc.autoTable({
      head: [
        [
          "No",
          "Nomor Surat",
          "Unit/Jurusan",
          "Kegiatan",
          "Lokasi",
          "Tanggal",
          "Identitas",
          "Peminjam",
          "HP",
          "Lampiran",
          "Keterangan",
        ],
      ],
      body: tableBody,
      startY: 30,
      theme: "grid",
      styles: {
        fontSize: 9,
        cellPadding: 2,
        valign: "middle",
        overflow: "linebreak",
      },
      headStyles: {
        fillColor: [13, 110, 253],
        textColor: [255, 255, 255],
        halign: "center",
        fontStyle: "bold",
        lineWidth: 0.1,
        lineColor: [0, 0, 0],
      },
      columnStyles: {
        0: { halign: "center", cellWidth: 10 },
        1: { halign: "center", cellWidth: 25 },
        2: { halign: "center", cellWidth: 25 },
        3: { halign: "left", cellWidth: 30 },
        4: { halign: "center", cellWidth: 25 },
        5: { halign: "center", cellWidth: 25 },
        6: { halign: "center", cellWidth: 20 },
        7: { halign: "left", cellWidth: 25 },
        8: { halign: "center", cellWidth: 20 },
        9: { halign: "center", cellWidth: 20 },
        10: { halign: "left", cellWidth: "auto" },
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

    const fileName = `Laporan_Peminjaman_${new window.Date()
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

function initFilter() {
  // Flatpickr untuk tanggal range
  flatpickr('#filter_daterange', {
    mode: 'range',
    dateFormat: 'Y-m-d',
    altInput: true,
    altFormat: 'j F Y',
    locale: { rangeSeparator: ' s/d ' },
    onReady: function (selectedDates, dateStr, instance) {
      // Tambahkan tombol Clear & Hari Ini
      const footer = document.createElement('div');
      footer.classList.add('d-flex', 'justify-content-between', 'p-2', 'border-top', 'bg-white');

      const clearBtn = document.createElement('button');
      clearBtn.type = 'button';
      clearBtn.className = 'btn btn-sm btn-link text-danger fw-bold text-decoration-none';
      clearBtn.innerText = 'Clear';
      clearBtn.onclick = () => { instance.clear(); instance.close(); applyFilter(); };

      const todayBtn = document.createElement('button');
      todayBtn.type = 'button';
      todayBtn.className = 'btn btn-sm btn-link text-primary fw-bold text-decoration-none';
      todayBtn.innerText = 'Hari Ini';
      todayBtn.onclick = () => { instance.setDate([new Date(), new Date()], true); instance.close(); };

      footer.appendChild(clearBtn);
      footer.appendChild(todayBtn);
      instance.calendarContainer.appendChild(footer);
    },
    onChange: function (selectedDates) {
      if (selectedDates.length === 2 || selectedDates.length === 0) applyFilter();
    }
  });

  // Select2 untuk filter unit
  $('#filter_unit').select2({
    placeholder: 'Pilih/Cari Unit...',
    allowClear: true,
    width: '100%'
  }).on('select2:select select2:unselect', function () {
    applyFilter();
  });

  // Filter bulan & tahun
  $('#cetak_bulan, #cetak_tahun').change(function () { applyFilter(); });
}

function applyFilter() {

  table.draw();

}

let table;

$.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {

  // hanya berlaku untuk tabel peminjaman
  if (settings.nTable.id !== "peminjamanTable") {
    return true;
  }

  const tanggal = document.getElementById("filter_daterange").value;
  const bulan = document.getElementById("cetak_bulan").value;
  const tahun = document.getElementById("cetak_tahun").value;
  const unit = document.getElementById("filter_unit").value;

  // ==========================
  // FILTER UNIT
  // ==========================
  const unitTable = data[2].trim();

  if (unit && unitTable !== unit) {
    return false;
  }

  // ==========================
  // AMBIL TANGGAL DARI TABEL
  // ==========================
  const htmlTanggal = data[5];

  const tanggalAwal = htmlTanggal
    .split("s/d")[0]
    .trim();

  const pecahTanggal = tanggalAwal.split("/");

  const bulanTable = pecahTanggal[1];
  const tahunTable = pecahTanggal[2].trim();

  // ==========================
  // FILTER DATE RANGE
  // ==========================
  if (tanggal) {

    const range = tanggal.split(" s/d ");

    const startFilter = new Date(range[0]);

    const endFilter = range.length > 1
      ? new Date(range[1])
      : startFilter;

    const tanggalTable = new Date(
      `${pecahTanggal[2]}-${pecahTanggal[1]}-${pecahTanggal[0]}`
    );

    if (tanggalTable < startFilter || tanggalTable > endFilter) {
      return false;
    }
  }

  // ==========================
  // FILTER BULAN
  // ==========================
  if (bulan && bulanTable !== bulan.padStart(2, "0")) {
    return false;
  }

  // ==========================
  // FILTER TAHUN
  // ==========================
  if (tahun && tahunTable !== tahun) {
    return false;
  }

  return true;
});

$(document).ready(function () {

  table = $('#peminjamanTable').DataTable({

    autoWidth: true,
    pageLength: 10,
    dom:
      "Bf" +
      "<'row'<'col-12'tr>>" +
      "<'row mt-2'<'col-md-5'i><'col-md-7'p>>",
    scrollX: true,
    autoWidth: false,
    searching: true,
    lengthChange: false,
    lengthMenu: [
      [5, 10, 25, 50, 100, -1],
      [5, 10, 25, 50, 100, 'Semua']
    ],
    buttons: [
      {
        extend: 'pageLength',
        className: 'btn btn-success btn-sm buttons-page-length',
        text: '<i class="fas fa-list me-1"></i> Show Rows'
      },
      {
        extend: 'colvis',
        className: 'btn btn-success btn-sm buttons-colvis',
        text: '<i class="fas fa-columns me-1"></i> Kolom Ditampilkan',
        columns: ':not(:last-child)',
        postfixButtons: ['colvisRestore'],
      }
    ],
    language: {
      search: '',
      searchPlaceholder: 'Cari data...',
      lengthMenu: 'Tampilkan _MENU_ baris',
      info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ entri',
      infoEmpty: 'Menampilkan 0 sampai 0 dari 0 entri',
      paginate: {
        previous: 'Sebelumnya',
        next: 'Berikutnya'
      }
    },
    initComplete: function () {

      const wrapper = $("#peminjamanTable_wrapper");

      const searchContainer = $("#search-container");

      searchContainer.empty();

      searchContainer
        .append(
          wrapper.find(".dt-buttons").detach()
        );

      searchContainer
        .append(
          wrapper.find(".dataTables_filter").detach()
        );

      searchContainer.find(".dt-buttons")
        .removeClass("btn-group")
        .addClass("d-flex gap-2");

      wrapper.find(".dataTables_filter input")
        .addClass("form-control form-control-sm")
        .css({
          width: "220px",
          display: "inline-block",
          marginLeft: "5px"
        });
    }
  }
  );
});