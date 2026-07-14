<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body style="background:#f4f4f4;padding:30px;font-family:Arial,sans-serif;">

    <table width="650" align="center" cellpadding="0" cellspacing="0"
        style="background:#ffffff;border-radius:10px;overflow:hidden;">

        <tr>
            <td style="background:<?= esc($warna ?? '#0d6efd') ?>;padding:20px;color:#fff;text-align:center;">
                <h2 style="margin:0;">UPA PERAWATAN DAN PERBAIKAN</h2>
                <small>Politeknik Negeri Sriwijaya</small>
            </td>
        </tr>

        <tr>
            <td style="padding:30px;">

                <h3>
                    <?= esc($judul) ?>
                    <br>
                    <small style="color:#666">
                        Nomor Laporan<br>
                        <strong><?= esc($nomor_laporan) ?></strong>
                    </small>
                </h3>
                <p style="font-size:16px;">
                    Halo <strong><?= esc($nama_pelapor) ?></strong>,
                </p>

                <p><?= esc($pesan) ?></p>

                <table width="100%" cellpadding="8" style="border-collapse:collapse">

                    <tr>
                        <td width="180"><b>Nomor Laporan</b></td>
                        <td><?= esc($nomor_laporan) ?></td>
                    </tr>

                    <tr>
                        <td><b>Tanggal</b></td>
                        <td><?= esc($tanggal) ?></td>
                    </tr>

                    <tr>
                        <td><b>Nama Alat</b></td>
                        <td><?= esc($nama_alat) ?></td>
                    </tr>

                    <tr>
                        <td><b>Lokasi</b></td>
                        <td><?= esc($lokasi) ?></td>
                    </tr>

                    <tr>
                        <td><b>Lokasi</b></td>
                        <td><?= esc($lokasi) ?></td>
                    </tr>

                    <?php if (!empty($pelapor)): ?>
                        <tr>
                            <td><b>Pelapor</b></td>
                            <td><?= esc($pelapor) ?></td>
                        </tr>
                    <?php endif; ?>

                    <?php if (!empty($tanggal_perbaikan)): ?>
                        <tr>
                            <td><b>Tanggal Perbaikan</b></td>
                            <td><?= esc($tanggal_perbaikan) ?></td>
                        </tr>
                    <?php endif; ?>

                    <?php if (!empty($keluhan)): ?>
                        <tr>
                            <td><b>Keluhan</b></td>
                            <td><?= esc($keluhan) ?></td>
                        </tr>
                    <?php endif; ?>

                    <?php if (!empty($diagnosa)): ?>
                        <tr>
                            <td><b>Diagnosa Teknisi</b></td>
                            <td><?= esc($diagnosa) ?></td>
                        </tr>
                    <?php endif; ?>

                    <?php if (!empty($alasan_pending)): ?>
                        <tr>
                            <td><b>Alasan Pending</b></td>
                            <td>
                                <?= esc($alasan_pending) ?>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <tr>
                        <td><b>Status</b></td>
                        <td>
                            <span style="
                                display:inline-block;
                                background:<?= esc($warna) ?>;
                                color:#fff;
                                padding:6px 12px;
                                border-radius:20px;
                                font-weight:bold;
                            ">
                                <?= esc($status) ?>
                            </span>
                        </td>
                    </tr>

                </table>

                <hr>

                <h4 style="margin-bottom:15px;color:#0d6efd;">
                    Riwayat Perkembangan
                </h4>

                <table width="100%" cellpadding="8" style="border-collapse:collapse;">
                    <?php if (!empty($timeline)): ?>
                        <?php foreach ($timeline as $item): ?>
                            <tr style="border-bottom:1px solid #eee;">
                                <td width="170" style="border-left:4px solid #0d6efd;
                                padding-left:12px;
                                color:#666;">
                                    <?= esc($item['waktu']) ?>
                                </td>

                                <td>
                                    <strong><?= esc($item['status']) ?></strong>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    <?php endif; ?>
                </table>
                <br>

                <p>
                    Terima kasih telah menggunakan Sistem UPA Perawatan dan Perbaikan.
                </p>

            </td>
        </tr>

        <tr>
            <td style="background:#f7f7f7;padding:20px;">

                <hr style="border:none;border-top:1px solid #ddd;">

                <p style="font-size:13px;color:#666;margin:0;text-align:center;">
                    Mohon tidak membalas email ini karena dikirim secara otomatis.
                    <br><br>
                    <strong>Sistem UPA Perawatan dan Perbaikan</strong><br>
                    Politeknik Negeri Sriwijaya
                </p>
            </td>
        </tr>

    </table>

</body>

</html>