<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Hasil Perbaikan dan Kerusakan</title>

    <style>
        body {
            font-family: "Times New Roman", serif;
        }

        .page {
            width: 190mm;
            margin: 15mm auto;
        }

        .new-page {
            page-break-before: always;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        /* KOP SURAT */
        .kop-table td {
            border: 1px solid #000;
            vertical-align: middle;
            padding: 6px;
            border-bottom: none !important;
        }

        .logo-box {
            width: 180px;
            height: 90px;
            text-align: center;
        }

        .brand-logo {
            width: 70px;
            height: auto;
            display: block;
            margin: 5px auto 0 auto;
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

        /* DETAIL ASSET */
        .detail-asset-table {
            margin-top: 0;
            border: 1px solid black;
        }

        .detail-asset-table td {
            border-left: none !important;
            border-right: none !important;
            padding: 5px 8px;
            vertical-align: top;
            font-size: 12pt;
        }

        .detail-asset-table td.label {
            text-align: left;
            padding-right: 10px;
            font-size: 12pt;
            white-space: nowrap;
        }

        .detail-asset-table td.colon {
            width: 10px;
            text-align: center;
        }

        /* PEKERJAAN TABLE */
        .pekerjaan-table {
            width: 100%;
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

        /* SIGN TABLE */
        .sign-table th,
        .sign-table td {
            border: 1px solid black;
            padding: 8px;
            vertical-align: middle;
            font-size: 10pt;
        }

        /* KOP KERUSAKAN HALAMAN 2 */
        .kop-kerusakan-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            border: 1px solid black;
            font-family: "Times New Roman";
            font-size: 12pt;
        }

        .kop-kerusakan-table td {
            vertical-align: top;
            padding: 5px;
        }

        .detail-asset-table-kerusakan {
            margin-top: 0;
            border: 1px solid black;
            border-top: none;
        }

        .new-page {
            width: 190mm;
            margin: 15mm auto;
        }
    </style>
</head>

<body>

    <!-- HALAMAN 1 -->
    <div class="page">

        <!-- KOP SURAT -->
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
                    <?= $laporan['nomor_laporan'] ?? '' ?>
                </td>
            </tr>
        </table>

        <!-- DETAIL ASSET -->
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

        <!-- TABEL PEKERJAAN -->
        <table class="pekerjaan-table">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 100%; text-align: center;">Uraian Pekerjaan</th>
                    <th colspan="2">Material / Suku Cadang</th>
                </tr>
                <tr>
                    <th style="width: 25%;">Nama Barang</th>
                    <th style="width: 25%;">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <!-- 1. Kolom Uraian Pekerjaan -->
                    <td style="height: 230px; vertical-align: top;">
                        <?= nl2br(esc((string)($laporan['uraian_pekerjaan'] ?? ''))) ?>
                    </td>

                    <!-- 2. Kolom Nama Barang -->
                    <td style="vertical-align: top;">
                        <?= nl2br(esc((string)($laporan['nama_barang'] ?? ''))) ?>
                    </td>

                    <td style="vertical-align: top; text-align: center !important;">
                        <?= nl2br(esc(trim((string)($laporan['jumlah_barang'] ?? '')))) ?>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- TANDA TANGAN -->
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
                <!-- LOGIKA PENENTUAN JABATAN OTOMATIS -->
                <?php
                // Kita ambil nama teknisi hasil JOIN tadi
                $nama_pelaksana_db = (string)($laporan['nama_teknisi_polsri'] ?? '');
                $jabatan_db = (string)($laporan['jabatan_teknisi_polsri'] ?? '');

                $nama_pelaksana = !empty($nama_pelaksana_db) ? $nama_pelaksana_db : '-';
                $jabatan_pelaksana = !empty($jabatan_db) ? $jabatan_db : 'Teknisi';

                // Logika cerdas: Jika di database jabatannya hanya 'teknisi', 
                // kita detailkan berdasarkan namanya (agar lebih keren di PDF)
                if (strtolower($jabatan_pelaksana) === 'teknisi' || empty($jabatan_db)) {
                    $nama_lower = strtolower($nama_pelaksana);
                    if (strpos($nama_lower, 'karison') !== false) $jabatan_pelaksana = 'Teknisi Komputer';
                    elseif (strpos($nama_lower, 'riadi') !== false) $jabatan_pelaksana = 'Teknisi Kelistrikan';
                    elseif (strpos($nama_lower, 'edial') !== false) $jabatan_pelaksana = 'Teknisi Elektronika';
                    elseif (strpos($nama_lower, 'cipto') !== false || strpos($nama_lower, 'sairespen') !== false) $jabatan_pelaksana = 'Teknisi AC';
                }
                ?>
                <tr>
                    <td style="border: 1px solid black; padding: 5px; font-weight: bold;">Nama</td>
                    <td style="border: 1px solid black; text-align: center; padding: 5px;">
                        <?= esc($nama_teknisi_polsri) ?>
                    </td>
                    <td style="border: 1px solid black; text-align: center; padding: 5px;">
                        <?= esc($laporan['pemeriksa_nama'] ?? '') ?>
                    </td>
                    <td style="border: 1px solid black; text-align: center; padding: 5px;">
                        <?= esc($laporan['kepala_nama'] ?? 'Harba Ario Sukha') ?>
                    </td>
                </tr>
                <tr>
                    <td>Jabatan</td>
                    <td style="text-align:center;">
                        <?= esc($jabatan_teknisi_polsri) ?>
                    </td>
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

    </div>

    <!-- HALAMAN 2 -->
    <div class="page new-page">
        <table class="kop-kerusakan-table" style="width: 100%; border-collapse: collapse; border: 1px solid black; font-family: 'Times New Roman', serif; font-size: 12pt;">
            <tr>
                <td rowspan="2"
                    style="width: 30%; height: 90px; text-align: center; vertical-align: middle; border-right: 1px solid black; border-bottom: 1px solid black; padding: 5px;">
                    <div style="font-weight: bold; font-size: 12pt; margin-bottom: 4px;">Politeknik Negeri Sriwijaya</div>
                    <img src="<?= $logo_polsri ?>" alt="Logo Polsri" style="width: 50px; display: block; margin: 0 auto 5px auto; height: auto;">
                    <div style="font-weight: bold; font-size: 11pt;">UPA PERAWATAN DAN PERBAIKAN</div>
                </td>
                <td style="width: 50%; border-right: 1px solid black; border-bottom: 1px solid black; text-align: center; vertical-align: middle; font-weight: bold; font-size: 14pt; padding: 5px;">
                    LAPORAN KERUSAKAN
                </td>
                <td style="width: 20%; height: 60px; border-bottom: 1px solid black; text-align: center; vertical-align: middle; padding: 5px;">
                    <img src="<?= $logo_iso ?>" alt="Logo ISO" style="width: 95px; height: 50px; display: block; margin: 0 auto;">
                </td>
            </tr>
            <tr>
                <td style="width: 50%; border-right: 1px solid black; padding: 8px 10px; vertical-align: top;">
                    <table style="width: 100%; border-collapse: collapse; font-family: 'Times New Roman', serif; font-size: 12pt;">
                        <tr>
                            <td style="width: 30%; padding: 3px 8px 3px 0; text-align: left;">Nomor</td>
                            <td style="width: 5%; padding: 3px 8px 3px 0; text-align: center;">:</td>
                            <td style="width: 65%; padding: 3px 0; text-align: left;"><?= esc($laporan['nomor_laporan'] ?? '') ?></td>
                        </tr>
                        <tr>
                            <td style="padding: 3px 8px 3px 0; text-align: left;">Tanggal</td>
                            <td style="text-align: center; padding: 3px 8px 3px 0;">:</td>
                            <td style="padding: 3px 0; text-align: left;"><?= esc($laporan['tanggal_laporan'] ?? '') ?></td>
                        </tr>
                    </table>
                </td>
                <td style="width: 20%; vertical-align: top;">
                </td>
            </tr>
        </table>

        <table class="detail-asset-table-kerusakan" style="width: 100%; border-collapse: collapse; font-family: 'Times New Roman', serif; font-size: 12pt;">
            <tbody>
                <tr>
                    <td style="width: 30%; padding: 4px 8px 4px 25px; vertical-align: middle; text-align: left; white-space: nowrap;">Nama Alat</td>
                    <td style="width: 3%; padding: 4px 4px; vertical-align: middle; text-align: center;">:</td>
                    <td style="width: 67%; padding: 4px 8px; vertical-align: middle;">
                        <div style="border-bottom: 1px solid black; width: 95%; margin-right: 0; padding-bottom: 5px;">
                            <?= esc($laporan['nama_alat'] ?? '') ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 30%; padding: 4px 8px 4px 25px; vertical-align: middle; text-align: left; white-space: nowrap;">No. Inventaris</td>
                    <td style="width: 3%; padding: 4px 4px; vertical-align: middle; text-align: center;">:</td>
                    <td style="width: 67%; padding: 4px 8px; vertical-align: middle;">
                        <div style="border-bottom: 1px solid black; width: 95%; margin-right: 0; padding-bottom: 5px;">
                            <?= esc($laporan['nomor_inventaris'] ?? '') ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 30%; padding: 4px 8px 4px 25px; vertical-align: middle; text-align: left; white-space: nowrap;">Lokasi Alat</td>
                    <td style="width: 3%; padding: 4px 4px; vertical-align: middle; text-align: center;">:</td>
                    <td style="width: 67%; padding: 4px 8px; vertical-align: middle;">
                        <div style="border-bottom: 1px solid black; width: 95%; margin-right: 0; padding-bottom: 5px;">
                            <?= esc($laporan['lokasi'] ?? '') ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 30%; padding: 4px 8px 4px 25px; vertical-align: middle; text-align: left; white-space: nowrap;">Jurusan/Unit</td>
                    <td style="width: 3%; padding: 4px 4px; vertical-align: middle; text-align: center;">:</td>
                    <td style="width: 67%; padding: 4px 8px; vertical-align: middle;">
                        <div style="border-bottom: 1px solid black; width: 95%; margin-right: 0; padding-bottom: 5px;">
                            <?= esc($laporan['unit'] ?? '') ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 4px 8px 4px 25px; vertical-align: middle; text-align: left; white-space: nowrap;">
                        Status Kerusakan
                    </td>
                    <td style="padding: 4px 4px; vertical-align: middle; text-align: center;">
                        :
                    </td>
                    <td style="padding: 6px 8px; vertical-align: middle;">
                        <?php $status = strtolower((string)($laporan['status_kerusakan'] ?? '')); ?>

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

        <table style="width: 100%; border-collapse: collapse; font-family: 'Times New Roman', serif; font-size: 12pt;">
            <tbody style="border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black;">

                <!-- BARIS 1: JUDUL & ISI KELUHAN UTAMA -->
                <tr>
                    <td style="width: 30%; padding: 15px 8px 15px 25px; vertical-align: top; white-space: nowrap;">
                        Kerusakan Keluhan
                    </td>
                    <td style="width: 3%; padding: 15px 4px; vertical-align: top; text-align: center;">:</td>
                    <td style="width: 67%; padding: 15px 8px; vertical-align: top;">
                        <!-- MENAMPILKAN DATA KELUHAN DISINI -->
                        <div style="border-bottom: 1px solid black; width: 95%; margin-right: 0; padding-bottom: 2px; min-height: 20px; line-height: 1.5;">
                            <?php
                            // Menggunakan nl2br agar jika ada Enter di textarea, di sini juga turun baris
                            // Ditambahkan (string) agar VS Code tidak cerewet masalah tipe data
                            echo nl2br(esc((string)($laporan['kerusakan'] ?? '')));
                            ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style="padding: 30px 8px 15px 25px;">
                        <div style="border-bottom: 1px solid black; width: 96.65%;">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style="padding: 30px 8px 15px 25px;">
                        <div style="border-bottom: 1px solid black; width: 96.65%;">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style="padding: 30px 8px 15px 25px;">
                        <div style="border-bottom: 1px solid black; width: 96.65%;">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style="padding: 30px 8px 15px 25px;">
                        <div style="border-bottom: 1px solid black; width: 96.65%;">
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        </table>

        <table style="
                width: 100%; 
                border-collapse: collapse; 
                font-family: 'Times New Roman', serif; 
                font-size: 12pt; 
                border: 1px solid black;
                border-top: none;
                table-layout: fixed;
            ">
            <tbody>
                <tr>
                    <td style="
                            width: 50%; 
                            height: 195px; 
                            border-right: 1px solid black;
                            vertical-align: bottom; 
                            padding: 10px; 
                            text-align: center;
                        ">
                        <!-- Mengambil nama dari data laporan -->
                        <?= esc($laporan['nama_pelapor'] ?? $laporan['pelapor'] ?? '-') ?><br />
                        Pelapor
                    </td>
                    <td style="
                        width: 50%; 
                        height: 90px; 
                        vertical-align: bottom; 
                        padding: 10px; 
                        text-align: center;
                    ">
                        <!-- Mengambil nama dari data laporan -->
                        <?= esc($nama_teknisi_polsri) ?><br />
                        Penerima Laporan
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- HALAMAN FOTO -->
    <?php if (!empty($foto_list)) : ?>

        <?php foreach ($foto_list as $index => $foto) : ?>

            <div style="
            page: foto_page_<?= $index ?>;         /* Menerapkan @page unik */
            page-break-before: always;      /* Memaksa halaman baru */
            width: 100%;                    
            height: 100%;                  
        ">
                <img src="<?= $foto['base64'] ?>" style="
                width: 100%; 
                height: 100%;
                object-fit: fill; /* 'fill' akan memaksa gambar pas 100% di halaman kustom */
            ">
            </div>

        <?php endforeach; ?>
    <?php endif; ?>
    <!-- AKHIR DARI BLOK FOTO -->

</body>

</html>