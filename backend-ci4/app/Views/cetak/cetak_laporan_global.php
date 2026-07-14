<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Hasil Perbaikan dan Kerusakan</title>

    <style>
        /* ========================================================
           1. PENGATURAN KERTAS GLOBAL (F4 / LEGAL INDONESIA)
           ======================================================== */
        @page {
            /* Ukuran F4 standar */
            size: 215mm 330mm portrait;
            /* Margin: Atas (25mm), Kanan (15mm), Bawah (15mm), Kiri (15mm) */
            margin: 25mm 15mm 15mm 15mm;
        }

        @page hal_dua {
            margin-top: 25mm;
            margin-bottom: 5mm;
        }

        body {
            font-family: "Times New Roman", serif;
            font-size: 11pt;
            margin: 0;
            padding: 0;
        }

        .new-page {
            page-break-before: always;
            page: hal_dua;
        }

        /* Tambahan untuk memisahkan setiap laporan agar mulai di kertas baru */
        .laporan-baru {
            page-break-before: always;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        /* ========================================================
           2. STYLE KOP & TABEL HALAMAN 1
           ======================================================== */
        .kop-table td {
            border: 1px solid #000;
            vertical-align: middle;
            padding: 6px;
            border-bottom: none !important;
        }

        .logo-box {
            width: 180px;
            text-align: center;
        }

        .kop-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
        }

        .kop-right {
            font-size: 14px;
            padding-left: 10px;
            width: 100px;
        }

        .detail-asset-table {
            border: 1px solid black;
        }

        .detail-asset-table td {
            border-left: none !important;
            border-right: none !important;
            padding: 5px 8px;
            vertical-align: top;
        }

        .detail-asset-table td.label {
            width: 30%;
            padding-right: 10px;
            white-space: nowrap;
        }

        .detail-asset-table td.colon {
            width: 3%;
            text-align: center;
        }

        .pekerjaan-table {
            margin-top: 15px;
            border: 1px solid black;
        }

        .pekerjaan-table th,
        .pekerjaan-table td {
            border: 1px solid black;
            padding: 8px;
            vertical-align: top !important;
            text-align: left !important;
        }

        .pekerjaan-table th {
            text-align: center !important;
        }

        .sign-table th,
        .sign-table td {
            border: 1px solid black;
            padding: 8px;
            vertical-align: middle;
            font-size: 10pt;
        }

        /* ========================================================
           3. STYLE KOP & TABEL HALAMAN 2 (LAPORAN KERUSAKAN)
           ======================================================== */
        .kop-kerusakan-table td {
            border: 1px solid black;
            padding: 5px;
        }

        .detail-asset-table-kerusakan {
            border: 1px solid black;
            border-top: none;
        }

        .detail-asset-table-kerusakan td {
            padding: 4px 8px;
            vertical-align: middle;
        }

        /* ========================================================
           4. STYLE KOTAK KUPON (YANG DIPERBAIKI TOTAL)
           ======================================================== */

        /* Tabel pembungkus utama agar posisi pasti bersebelahan */

        /* 1. GARIS PEMISAH ATAS (Garis Gunting) */
        .coupon-wrapper-table {
            width: 100%;
            margin-top: 15px;
            /* 2px = Ketebalan, dashed = Putus-putus */
            border-top: 2px dashed black;
            padding-top: 15px;
            border-collapse: collapse;
        }

        /* 2. GARIS KOTAK BUKTI & IDENTITAS (Ubah ke Lurus/Solid) */
        .coupon-box-cell {
            width: 49%;
            /* 1px = Ketebalan tipis, solid = Garis Lurus (TIDAK PUTUS) */
            border: 1px solid black;
            vertical-align: top;
            padding: 15px;
        }

        /* Spasi tengah pengaman */
        .coupon-spacer-cell {
            width: 2%;
            border: none !important;
        }

        .coupon-header {
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
            text-decoration: underline;
            margin-bottom: 5px;
        }

        .coupon-subheader {
            text-align: center;
            font-size: 9pt;
            font-weight: bold;
            margin-bottom: 10px;
        }

        /* Tabel dalam untuk isi data kupon */
        .coupon-inner-table {
            width: 100%;
            font-size: 10pt;
            border: none;
            border-collapse: collapse;
        }

        /* Jarak antar baris isian dibuat rapat (compact) */
        .coupon-inner-table td {
            padding: 4px 0;
            /* Padding atas bawah kecil agar rapat */
            vertical-align: bottom;
            /* Teks mepet ke garis bawah */
            border: none;
        }

        .coupon-label {
            width: 28%;
            font-weight: bold;
            white-space: nowrap;
        }

        .coupon-colon {
            width: 4%;
            text-align: center;
        }

        /* 3. GARIS ISIAN TITIK DUA (Nama Alat, dll) */
        .coupon-line {
            width: 68%;
            /* 1px = Ketebalan, solid = Garis lurus bawah */
            border-bottom: 1px solid black;
        }
    </style>
</head>

<body>
    <!-- HALAMAN 1 -->
    <?php if (!empty($laporan_list)) : ?>
        <?php $i = 0;
        foreach ($laporan_list as $laporan) : ?>

            <div class="page <?= $i > 0 ? 'laporan-baru' : '' ?>">
                <table class="kop-table">
                    <tr>
                        <td class="logo-box">
                            <div style="font-size:14px; font-weight:bold; margin-top:5px;">Politeknik Negeri Sriwijaya</div>
                            <img src="<?= $logo_polsri ?>" alt="Logo Polsri" style="width: 50px;">
                            <div style="font-size:14px; font-weight:bold; margin-top:3px;">UPA PERAWATAN DAN PERBAIKAN</div>
                        </td>
                        <td>
                            <div class="kop-title">LAPORAN HASIL PERBAIKAN</div>
                        </td>
                        <td class="kop-right">
                            <b>No. Laporan Kerusakan :</b><br>
                            <?= esc($laporan['nomor_laporan'] ?? '') ?>
                        </td>
                    </tr>
                </table>

                <table class="detail-asset-table">
                    <tr>
                        <td class="label">Nama Alat/Mesin</td>
                        <td class="colon">:</td>
                        <td><?= esc($laporan['nama_alat'] ?? '') ?></td>
                    </tr>
                    <tr>
                        <td class="label">No. Inventaris</td>
                        <td class="colon">:</td>
                        <td><?= esc($laporan['nomor_inventaris'] ?? '') ?></td>
                    </tr>
                    <tr>
                        <td class="label">Lokasi Alat/Mesin</td>
                        <td class="colon">:</td>
                        <td><?= esc($laporan['lokasi'] ?? '') ?></td>
                    </tr>
                </table>

                <table class="pekerjaan-table">
                    <thead>
                        <tr>
                            <th rowspan="2" style="width: 100%;">Uraian Pekerjaan</th>
                            <th colspan="2">Material / Suku Cadang</th>
                        </tr>
                        <tr>
                            <th style="width: 25%;">Nama Barang</th>
                            <th style="width: 25%;">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="height: 280px;">
                                <?= nl2br(esc((string)($laporan['uraian_pekerjaan'] ?? ''))) ?>
                            </td>
                            <td>
                                <?= nl2br(esc((string)($laporan['nama_barang'] ?? ''))) ?>
                            </td>
                            <td style="text-align: center !important;">
                                <?= nl2br(esc(trim((string)($laporan['jumlah_barang'] ?? $laporan['jumlah'] ?? '')))) ?>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <?php
                // Ambil data teknisi dari Controller
                $nama_pelaksana = esc($laporan['nama_teknisi_polsri'] ?? '-');
                $jabatan_pelaksana = esc($laporan['jabatan_teknisi_polsri'] ?? 'Teknisi');
                ?>

                <table class="sign-table" style="margin-top: 15px; table-layout: fixed;">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Pelaksana</th>
                            <th>Pemeriksa</th>
                            <th>Kepala UPA Perawatan dan Perbaikan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="font-weight: bold;">Nama</td>
                            <td style="text-align: center;"><?= $nama_pelaksana ?></td>
                            <td style="text-align: center;"><?= esc($laporan['pemeriksa_nama'] ?? '........................') ?></u></td>
                            <td style="text-align: center;"><?= esc($laporan['kepala_nama'] ?? 'Harba Ario Sukha') ?></u></td>
                        </tr>
                        <tr>
                            <td>Jabatan</td>
                            <td style="text-align:center;"><?= $jabatan_pelaksana ?></td>
                            <td style="text-align:center;"><?= esc($laporan['pemeriksa_jabatan'] ?? 'Ketua Jurusan / Ka. Unit') ?></td>
                            <td style="text-align:center;"><?= esc($laporan['kepala_jabatan'] ?? 'Ka. UPA Perawatan dan Perbaikan') ?></td>
                        </tr>
                        <tr>
                            <td>Tanggal Selesai</td>
                            <td style="text-align:center;"><?= esc($laporan['tanggal_selesai'] ?? '........................') ?></td>
                            <td style="text-align:center;"><?= esc($laporan['tanggal_selesai'] ?? '........................') ?></td>
                            <td style="text-align:center;"><?= esc($laporan['tanggal_selesai'] ?? '........................') ?></td>
                        </tr>
                        <tr>
                            <td>Paraf</td>
                            <td style="height: 60px;"></td>
                            <td style="height: 60px;"></td>
                            <td style="height: 60px;"></td>
                        </tr>
                    </tbody>
                </table>

                <!-- HALAMAN 2 -->
                <div class="page new-page">
                    <table class="kop-kerusakan-table">
                        <tr>
                            <td rowspan="2" style="width: 30%; height: 90px; text-align: center; vertical-align: middle;">
                                <div style="font-weight: bold; margin-bottom: 4px;">Politeknik Negeri Sriwijaya</div>
                                <img src="<?= $logo_polsri ?>" alt="Logo Polsri" style="width: 50px; display: block; margin: 0 auto 5px auto;">
                                <div style="font-weight: bold; font-size: 11pt;">UPA PERAWATAN DAN PERBAIKAN</div>
                            </td>
                            <td style="width: 50%; text-align: center; vertical-align: middle; font-weight: bold; font-size: 14pt;">
                                LAPORAN KERUSAKAN
                            </td>
                            <td style="width: 20%; height: 60px; text-align: center; vertical-align: middle;">
                                <img src="<?= $logo_iso ?>" alt="Logo ISO" style="width: 95px; height: 50px; display: block; margin: 0 auto;">
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 50%; padding: 8px 10px; vertical-align: top;">
                                <table style="width: 100%; border: none;">
                                    <tr>
                                        <td style="width: 30%; border: none; padding: 2px 0;">Nomor</td>
                                        <td style="width: 5%; border: none; padding: 2px 0;">:</td>
                                        <td style="width: 65%; border: none; padding: 2px 0;"><?= esc($laporan['nomor_laporan'] ?? '') ?></td>
                                    </tr>
                                    <tr>
                                        <td style="border: none; padding: 2px 0;">Tanggal</td>
                                        <td style="border: none; padding: 2px 0; text-align: center;">:</td>
                                        <td style="border: none; padding: 2px 0;"><?= esc($laporan['tanggal_laporan'] ?? '') ?></td>
                                    </tr>
                                </table>
                            </td>
                            <td style="width: 20%; vertical-align: top;"></td>
                        </tr>
                    </table>

                    <table class="detail-asset-table-kerusakan">
                        <tbody>
                            <tr>
                                <td style="width: 30%; padding-left: 25px; white-space: nowrap;">Nama Alat</td>
                                <td style="width: 3%; text-align: center;">:</td>
                                <td style="width: 67%;">
                                    <div style="border-bottom: 1px solid black; width: 95%; padding-bottom: 2px;">
                                        <?= esc($laporan['nama_alat'] ?? '') ?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-left: 25px; white-space: nowrap;">No. Inventaris</td>
                                <td style="text-align: center;">:</td>
                                <td>
                                    <div style="border-bottom: 1px solid black; width: 95%; padding-bottom: 2px;">
                                        <?= esc($laporan['nomor_inventaris'] ?? '') ?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-left: 25px; white-space: nowrap;">Lokasi Alat</td>
                                <td style="text-align: center;">:</td>
                                <td>
                                    <div style="border-bottom: 1px solid black; width: 95%; padding-bottom: 2px;">
                                        <?= esc($laporan['lokasi'] ?? '') ?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-left: 25px; white-space: nowrap;">Jurusan/Unit</td>
                                <td style="text-align: center;">:</td>
                                <td>
                                    <div style="border-bottom: 1px solid black; width: 95%; padding-bottom: 2px;">
                                        <?= esc($laporan['unit'] ?? '') ?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-left: 25px; white-space: nowrap;">Status Kerusakan</td>
                                <td style="text-align: center;">:</td>
                                <td style="padding: 6px 8px; vertical-align: middle;">
                                    <?php
                                    // KUNCI SAKTI: Tambahkan TRIM agar spasi "ringan " menjadi "ringan"
                                    $status = trim(strtolower((string)($laporan['status_kerusakan'] ?? '')));
                                    ?>

                                    <span style="margin-right: 20px;">
                                        <span style="font-family: DejaVu Sans, sans-serif; font-size: 14pt;">
                                            <?= ($status == 'ringan') ? '☑' : '☐' ?>
                                        </span> Ringan
                                    </span>

                                    <span style="margin-right: 20px;">
                                        <span style="font-family: DejaVu Sans, sans-serif; font-size: 14pt;">
                                            <?= ($status == 'sedang') ? '☑' : '☐' ?>
                                        </span> Sedang
                                    </span>

                                    <span>
                                        <span style="font-family: DejaVu Sans, sans-serif; font-size: 14pt;">
                                            <?= ($status == 'berat') ? '☑' : '☐' ?>
                                        </span> Berat
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <table style="width: 100%; border: 1px solid black; border-top: none;">
                        <tbody>
                            <tr>
                                <td style="width: 30%; padding: 15px 8px 15px 25px; vertical-align: top; white-space: nowrap;">Kerusakan Keluhan</td>
                                <td style="width: 3%; padding: 15px 4px; vertical-align: top; text-align: center;">:</td>
                                <td style="width: 67%; padding: 15px 8px; vertical-align: top;">
                                    <div style="border-bottom: 1px solid black; width: 95%; padding-bottom: 2px; min-height: 20px; line-height: 1.5;">
                                        <?= nl2br(esc((string)($laporan['kerusakan'] ?? ''))) ?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" style="padding: 25px 8px 10px 25px;">
                                    <div style="border-bottom: 1px solid black; width: 96.65%;"></div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" style="padding: 25px 8px 10px 25px;">
                                    <div style="border-bottom: 1px solid black; width: 96.65%;"></div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" style="padding: 25px 8px 10px 25px;">
                                    <div style="border-bottom: 1px solid black; width: 96.65%;"></div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" style="padding: 25px 8px 15px 25px;">
                                    <div style="border-bottom: 1px solid black; width: 96.65%;"></div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <table style="width: 100%; border: 1px solid black; border-top: none; table-layout: fixed;">
                        <tbody>
                            <tr>
                                <td style="width: 50%; height: 180px; border-right: 1px solid black; vertical-align: bottom; padding: 10px; text-align: center;">
                                    <?= esc($laporan['nama_pelapor'] ?? $laporan['pelapor'] ?? '-') ?><br>Pelapor
                                </td>
                                <td style="width: 50%; height: 180px; vertical-align: bottom; padding: 10px; text-align: center;">
                                    <?= esc($nama_pelaksana) ?></u><br>Penerima Laporan
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- BUKTI PERBAIKAN & IDENTITAS ALAT -->
                    <table style="width: 100%; border-collapse: collapse; border: none; margin-top: 15px;">
                        <tr>
                            <td style="width: 48%; border-top: 1px dashed black; padding-top: 15px; padding-right: 5px; vertical-align: top;">
                                <div style="border: 1px solid black; padding: 15px;">
                                    <div style="text-align: center; font-weight: bold; font-size: 12pt; text-decoration: underline; margin-bottom: 15px;">BUKTI PERBAIKAN</div>
                                    <div style="text-align: center; font-size: 9pt; font-weight: bold; margin-bottom: 10px;">
                                        NO. LK : <?= esc($laporan['nomor_laporan'] ?? '...............') ?> &nbsp;&nbsp;&nbsp; TANGGAL : <?= esc(date('d-m-Y', strtotime($laporan['tanggal_laporan'] ?? date('Y-m-d')))) ?>
                                    </div>
                                    <div style="border-top: 1px solid black; margin-bottom: 15px;"></div>

                                    <table style="width: 100%; font-size: 10pt; border-collapse: collapse;">
                                        <tr>
                                            <td style="width: 25%; font-weight: bold; padding: 6px 0; border: none;">Nama Alat</td>
                                            <td style="width: 5%; text-align: center; padding: 6px 0; border: none;">:</td>
                                            <td style="width: 70%; border-bottom: 1px solid black; padding: 6px 0;">
                                                <?= esc($laporan['nama_alat'] ?? '') ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="font-weight: bold; padding: 6px 0; border: none;">No. Inv</td>
                                            <td style="text-align: center; padding: 6px 0; border: none;">:</td>
                                            <td style="border-bottom: 1px solid black; padding: 6px 0;">
                                                <?= esc($laporan['nomor_inventaris'] ?? '') ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="font-weight: bold; padding: 6px 0; border: none;">Lokasi Alat</td>
                                            <td style="text-align: center; padding: 6px 0; border: none;">:</td>
                                            <td style="border-bottom: 1px solid black; padding: 6px 0;">
                                                <?= esc($laporan['lokasi'] ?? '') ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="font-weight: bold; padding: 6px 0; border: none;">Jurusan/Unit</td>
                                            <td style="text-align: center; padding: 6px 0; border: none;">:</td>
                                            <td style="border-bottom: 1px solid black; padding: 6px 0;">
                                                <?= esc($laporan['unit'] ?? '') ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="font-weight: bold; padding: 20px 0 5px 0; border: none;">Paraf Teknisi</td>
                                            <td style="text-align: center; padding: 20px 0 5px 0; border: none;">:</td>
                                            <td style="border: none; padding: 20px 0 5px 0;"></td>
                                        </tr>
                                    </table>
                                </div>
                            </td>

                            <td style="width: 2%; border-top: 1px dashed black; border-right: 1px dashed black;"></td>

                            <td style="width: 2%; border-top: 1px dashed black; border-left: none;"></td>

                            <td style="width: 48%; border-top: 1px dashed black; padding-top: 15px; padding-left: 5px; vertical-align: top;">
                                <div style="border: 1px solid black; padding: 15px;">
                                    <div style="text-align: center; font-weight: bold; font-size: 12pt; text-decoration: underline; margin-bottom: 15px;">IDENTITAS ALAT</div>
                                    <div style="text-align: center; font-size: 9pt; font-weight: bold; margin-bottom: 10px;">
                                        NO. LK : <?= esc($laporan['nomor_laporan'] ?? '...............') ?> &nbsp;&nbsp;&nbsp; TANGGAL : <?= esc(date('d-m-Y', strtotime($laporan['tanggal_laporan'] ?? date('Y-m-d')))) ?>
                                    </div>
                                    <div style="border-top: 1px solid black; margin-bottom: 15px;"></div>

                                    <table style="width: 100%; font-size: 10pt; border-collapse: collapse;">
                                        <tr>
                                            <td style="width: 25%; font-weight: bold; padding: 6px 0; border: none;">Nama Alat</td>
                                            <td style="width: 5%; text-align: center; padding: 6px 0; border: none;">:</td>
                                            <td style="width: 70%; border-bottom: 1px solid black; padding: 6px 0;">
                                                <?= esc($laporan['nama_alat'] ?? '') ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="font-weight: bold; padding: 6px 0; border: none;">No. Inv</td>
                                            <td style="text-align: center; padding: 6px 0; border: none;">:</td>
                                            <td style="border-bottom: 1px solid black; padding: 6px 0;">
                                                <?= esc($laporan['nomor_inventaris'] ?? '') ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="font-weight: bold; padding: 6px 0; border: none;">Lokasi Alat</td>
                                            <td style="text-align: center; padding: 6px 0; border: none;">:</td>
                                            <td style="border-bottom: 1px solid black; padding: 6px 0;">
                                                <?= esc($laporan['lokasi'] ?? '') ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="font-weight: bold; padding: 6px 0; border: none;">Jurusan/Unit</td>
                                            <td style="text-align: center; padding: 6px 0; border: none;">:</td>
                                            <td style="border-bottom: 1px solid black; padding: 6px 0;">
                                                <?= esc($laporan['unit'] ?? '') ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="font-weight: bold; padding: 20px 0 5px 0; border: none;">Paraf Teknisi</td>
                                            <td style="text-align: center; padding: 20px 0 5px 0; border: none;">:</td>
                                            <td style="border: none; padding: 20px 0 5px 0;"></td>
                                        </tr>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

        <?php $i++;
        endforeach; ?>
    <?php else : ?>
        <div style="text-align: center; margin-top: 50px;">
            <h2>Data Laporan Tidak Ditemukan</h2>
            <p>Silakan sesuaikan filter pencarian Anda.</p>
        </div>
    <?php endif; ?>

    <?php if (isset($foto_list) && !empty($foto_list)) : ?>
        <?php foreach ($foto_list as $index => $foto) : ?>
            <div style="page: foto_page_<?= $index ?>; page-break-before: always; width: 100%; height: 100%;">
                <img src="<?= $foto['base64'] ?>" style="width: 100%; height: 100%; object-fit: fill;">
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</body>

</html>