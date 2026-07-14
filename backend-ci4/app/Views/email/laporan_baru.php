<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Kerusakan</title>
</head>

<body style="
    margin:0;
    padding:30px;
    background:#f5f7fb;
    font-family:Arial, Helvetica, sans-serif;
">

    <table width="650" align="center" cellpadding="0" cellspacing="0" style="
            background:#ffffff;
            border-radius:10px;
            overflow:hidden;
            border:1px solid #ddd;
        ">

        <!-- HEADER -->

        <tr>
            <td style="
                background:#0d6efd;
                color:white;
                padding:25px;
                text-align:center;
            ">

                <h2 style="margin:0;">
                    UPA PERAWATAN DAN PERBAIKAN
                </h2>

                <div style="margin-top:8px;">
                    Politeknik Negeri Sriwijaya
                </div>

            </td>
        </tr>

        <!-- BODY -->

        <tr>

            <td style="padding:35px;">

                <h3>
                    Halo, <?= esc($nama_pelapor) ?>
                </h3>

                <p>
                    Laporan kerusakan yang Anda kirim telah berhasil diterima oleh sistem.
                </p>

                <table width="100%" cellpadding="10" style="
                        border-collapse:collapse;
                        margin-top:20px;
                    ">

                    <tr>
                        <td width="35%"><b>Nomor Laporan</b></td>
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
                        <td><b>Status</b></td>
                        <td>

                            <span style="
                                background:#ffc107;
                                color:#000;
                                padding:5px 12px;
                                border-radius:15px;
                            ">
                                <?= esc($status) ?>
                            </span>

                        </td>
                    </tr>

                </table>

                <br>

                <p>
                    Silakan login ke Sistem UPA PP untuk memantau perkembangan laporan Anda.
                </p>

                <center>

                    <a href="<?= base_url('login') ?>" style="
                            display:inline-block;
                            padding:12px 25px;
                            background:#0d6efd;
                            color:white;
                            text-decoration:none;
                            border-radius:6px;
                        ">

                        Login Sistem

                    </a>

                </center>

            </td>

        </tr>

        <!-- FOOTER -->

        <tr>

            <td style="
                background:#f3f3f3;
                text-align:center;
                padding:20px;
                color:#777;
                font-size:13px;
            ">

                Email ini dikirim otomatis oleh Sistem UPA PP.<br>

                © <?= date('Y') ?> Politeknik Negeri Sriwijaya

            </td>

        </tr>

    </table>

</body>

</html>