<style>
    /* TAMBAHKAN INI DI DALAM TAG <STYLE> DI FILE HEADER.PHP */
    #toggleSidebar {
        border: none;
        /* Menghilangkan garis pinggir kotak */
        background: transparent;
        /* Membuat background bening */
        font-size: 22px;
        /* Ukuran ikon */
        cursor: pointer;
        margin-right: 10px;
        color: #003366 !important;
        /* Memastikan warna biru tua */
        padding: 0;
        /* Menghilangkan jarak dalam tombol */
    }

    #toggleSidebar:focus {
        outline: none;
        /* Menghilangkan garis biru saat diklik */
        box-shadow: none;
    }

    header {
        background-color: #b3d9ff;
        padding: 10px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        height: 60px;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1100;
    }

    header .left {
        display: flex;
        align-items: center;
    }

    .brand-logo {
        height: 40px;
        margin-right: 10px;
    }

    header,
    header .left span,
    header .right i,
    header .right button {
        color: #003366 !important;
    }

    header .right button {
        border-color: #003366 !important;
    }

    header .right button:hover {
        background-color: #003366 !important;
        color: #ffffff !important;
    }
</style>

<header>
    <div class="left">
        <button id="toggleSidebar"><i class="fas fa-bars"></i></button>
        <img src="<?= base_url('images/polsri.png') ?>" alt="Logo" class="brand-logo">
        <span class="fw-bold">UNIT PENUNJANG AKADEMIK PERAWATAN DAN PERBAIKAN</span>
    </div>
    <div class="right">
        <div class="dropdown d-inline-block me-3">
            <button class="btn btn-sm border-0 position-relative p-0" type="button" id="notifDropdown"
                data-bs-toggle="dropdown" aria-expanded="false" style="color: #003366;">
                <i class="fas fa-bell fa-lg mt-1"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none"
                    id="notifBadge" style="font-size: 0.6rem;">
                    0
                </span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="notifDropdown"
                style="width: 320px; max-height: 400px; overflow-y: auto;">
                <li>
                    <h6 class="dropdown-header text-primary fw-bold">Notifikasi Terbaru</h6>
                </li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <div id="notifItems">
                    <li><a class="dropdown-item text-center text-muted py-3" href="#">Memuat data...</a></li>
                </div>
            </ul>
        </div>
        <button type="button" class="btn btn-sm border-0 me-3" id="btnFullscreen" title="Layar Penuh">
            <i class="fas fa-expand"></i>
        </button>
        <button type="button" class="btn btn-outline-dark btn-sm" onclick="confirmLogout()">
            <i class="fas fa-sign-out-alt me-1"></i> Logout
        </button>
    </div>
</header>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    window.alert = function (message) {
        if (window.Swal && typeof window.Swal.fire === 'function') {
            Swal.fire({
                icon: 'info',
                title: 'Info',
                html: String(message).replace(/\n/g, '<br>'),
                confirmButtonText: 'OK'
            });
        } else {
            console.error('Alert:', message);
        }
    };

    function confirmLogout() {
        Swal.fire({
            title: 'Apakah anda yakin.',
            text: "Anda ingin keluar?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect ke route logout
                window.location.href = "<?= base_url('logout') ?>";
            }
        });
    }

    const btnFullscreen = document.getElementById('btnFullscreen');
    const iconFullscreen = btnFullscreen.querySelector('i');

    btnFullscreen.addEventListener('click', () => {
        if (!document.fullscreenElement) {
            // JIKA BELUM FULLSCREEN -> AKTIFKAN
            document.documentElement.requestFullscreen().catch((err) => {

            });

            // Ubah ikon jadi compress (kecilkan)
            iconFullscreen.classList.remove('fa-expand');
            iconFullscreen.classList.add('fa-compress');

        } else {
            // JIKA SUDAH FULLSCREEN -> KELUAR
            if (document.exitFullscreen) {
                document.exitFullscreen();
            }

            // Ubah ikon balik jadi expand
            iconFullscreen.classList.remove('fa-compress');
            iconFullscreen.classList.add('fa-expand');
        }
    });

    // Event Listener untuk mendeteksi jika user keluar fullscreen pakai tombol 'ESC' di keyboard
    document.addEventListener('fullscreenchange', () => {
        if (!document.fullscreenElement) {
            iconFullscreen.classList.remove('fa-compress');
            iconFullscreen.classList.add('fa-expand');
        }
    });

    // ==========================================
    // LOGIKA NOTIFIKASI REALTIME (AUTO-POLLING)
    // ==========================================
    const currentRole = "<?= session()->get('role') ?>";

    function getNotifTargetLink(item) {

        if (currentRole == "admin") {
            return "<?= base_url('admin/antrian_perbaikan') ?>";
        } else if (currentRole == "teknisi") {
            return "<?= base_url('teknisi/jadwal') ?>";
        } else if (currentRole == "pelapor") {
            return "<?= base_url('pelapor/riwayat') ?>";
        }
        return "<?= base_url('/') ?>";

    }

    const notifSeenKey = `notif_last_seen_${currentRole}`;

    function getNotifAllLink() {

        if (currentRole == "admin") {
            return "<?= base_url('admin/antrian_perbaikan') ?>";
        } else if (currentRole == "teknisi") {
            return "<?= base_url('teknisi/jadwal') ?>";
        } else if (currentRole == "pelapor") {
            return "<?= base_url('pelapor/riwayat') ?>";
        }
        return "<?= base_url('/') ?>";
    }

    function getNotifLastSeenTimestamp() {
        const stored = localStorage.getItem(notifSeenKey);
        return stored ? parseInt(stored, 10) : 0;
    }

    function markNotifikasiSeen() {
        localStorage.setItem(notifSeenKey, Date.now().toString());
        const badge = document.getElementById('notifBadge');
        if (badge) {
            badge.classList.add('d-none');
        }
    }

    function isNotificationNew(item, lastSeenTs) {

        const waktu = item.updated_at ?? item.tanggal_laporan;
        if (!waktu) {
            return true;
        }

        const when = Date.parse(waktu.replace(' ', 'T'));
        return Number.isFinite(when)
            ? when > lastSeenTs
            : true;

    }

    function formatWaktuNotifikasi(tanggal) {

        if (!tanggal) {
            return "-";
        }

        const waktu = new Date(tanggal.replace(' ', 'T'));

        const sekarang = new Date();

        const selisih = Math.floor((sekarang - waktu) / 1000);

        if (selisih < 60) {
            return "Baru saja";
        }

        if (selisih < 3600) {
            return Math.floor(selisih / 60) + " menit yang lalu";
        }

        if (selisih < 86400) {
            return Math.floor(selisih / 3600) + " jam yang lalu";
        }

        if (selisih < 172800) {
            return "Kemarin";
        }

        const opsi = {
            day: "2-digit",
            month: "short",
            year: "numeric",
            hour: "2-digit",
            minute: "2-digit"
        };

        return waktu.toLocaleDateString("id-ID", opsi);

    }

    function cekNotifikasi() {

        fetch("<?= base_url(session()->get('role') . '/get_notifikasi') ?>", {
            headers: {
                "X-Requested-With": "XMLHttpRequest"
            }
        })

            .then(response => response.json())

            .then(res => {

                const badge = document.getElementById("notifBadge");
                const notifItems = document.getElementById("notifItems");

                notifItems.innerHTML = "";

                if (res.status != "success") {

                    notifItems.innerHTML = `
                <li>
                    <a class="dropdown-item text-danger text-center py-3">
                        Gagal memuat notifikasi
                    </a>
                </li>
            `;

                    return;

                }

                const lastSeenTs = getNotifLastSeenTimestamp();

                const newItems = res.data.filter(item => isNotificationNew(item, lastSeenTs));

                if (newItems.length > 0) {

                    badge.innerText = newItems.length > 99 ? "99+" : newItems.length;

                    badge.classList.remove("d-none");

                } else {

                    badge.classList.add("d-none");

                }

                if (newItems.length == 0) {

                    notifItems.innerHTML = `
                <li>
                    <a class="dropdown-item text-center text-muted py-3">
                        Semua notifikasi sudah dibaca
                    </a>
                </li>
            `;

                    return;

                }

                newItems.forEach(function (item) {

                    let warna = "secondary";

                    if (item.status_laporan == "BARU") {

                        warna = "primary";

                    } else if (item.status_laporan == "DIJADWALKAN") {

                        warna = "warning";

                    } else if (item.status_laporan == "DIPROSES") {

                        warna = "info";

                    } else if (item.status_laporan == "PENDING") {

                        warna = "warning";

                    } else if (item.status_laporan == "MENUNGGU KONFIRMASI") {

                        warna = "success";

                    } else if (item.status_laporan == "SELESAI") {

                        warna = "dark";

                    }

                    const targetLink = getNotifTargetLink(item);

                    notifItems.innerHTML += `

                <li>

                    <a class="dropdown-item py-2 border-bottom"

                        href="${targetLink}"

                        onclick="markNotifikasiSeen()">

                        <div class="d-flex justify-content-between align-items-center">

                            <span class="badge bg-${warna}">
                                ${item.status_laporan}
                            </span>

                            <small class="text-muted">
                                ${formatWaktuNotifikasi(item.updated_at ?? item.tanggal_laporan)}
                            </small>

                        </div>

                        <div class="fw-bold mt-2">
                            ${item.nomor_laporan}
                        </div>

                        <div class="small text-muted">
                            ${item.nama_alat}
                        </div>

                        <div class="small">
                            ${item.pesan}
                        </div>

                        <div class="small text-muted">
                            Teknisi :
                            ${item.teknisi ?? "-"}
                        </div>
                    </a>
                </li>
            `;

                });
                notifItems.innerHTML += `
            <li>

                <a
                    class="dropdown-item text-center fw-bold text-primary"
                    href="${getNotifAllLink()}"
                    onclick="markNotifikasiSeen()">
                    Lihat Semua
                </a>

            </li>

        `;

            })
            .catch(function (err) {
                console.log(err);
            });
    }

    // Jalankan pertama kali saat header dimuat
    document.addEventListener("DOMContentLoaded", () => {
        cekNotifikasi();
        // Cek secara diam-diam setiap 10 detik (10000 ms)
        setInterval(cekNotifikasi, 10000);
    });
</script>