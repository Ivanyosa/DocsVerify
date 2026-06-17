<?php

include '../config/koneksi.php';

$result = mysqli_query(
    $conn,
    "SELECT * FROM documents ORDER BY id DESC"
);

if(!$result)
{
    die(mysqli_error($conn));
}

?>
<!DOCTYPE html>
<html>

<head>

    <title>Daftar Dokumen</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>

        body{
            background:#f4f6f9;
        }

        .card{
            border:none;
            border-radius:15px;
        }

        .hash-box{
            max-width:350px;
            overflow:hidden;
            text-overflow:ellipsis;
            white-space:nowrap;
            font-size:12px;
            font-family:monospace;
        }

        .qr-img{
            width:100px;
            transition:.3s;
        }

        .qr-img:hover{
            transform:scale(1.2);
        }

    </style>

</head>

<body>

<nav class="navbar navbar-dark bg-dark shadow">

    <div class="container">

        <span class="navbar-brand fw-bold">
            DOCVERIFY ADMIN
        </span>

        <a href="index.php"
            class="btn btn-light btn-sm">

            Upload Dokumen

        </a>

    </div>

</nav>

<div class="container mt-4">

    <div class="row mb-4">

        <div class="col-md-4">

            <div class="card shadow-sm">

                <div class="card-body">

                    <h6 class="text-muted">
                        Total Dokumen
                    </h6>

                    <h2 class="fw-bold">
                        <?= mysqli_num_rows($result); ?>
                    </h2>

                </div>

            </div>

        </div>

        <div class="col-md-4">

            <div class="card shadow-sm">

                <div class="card-body">

                    <h6 class="text-muted">
                        Algoritma
                    </h6>

                    <h5 class="fw-bold">
                        SHA-256
                    </h5>

                </div>

            </div>

        </div>

        <div class="col-md-4">

            <div class="card shadow-sm">

                <div class="card-body">

                    <h6 class="text-muted">
                        Verifikasi
                    </h6>

                    <h5 class="fw-bold text-success">
                        QR Code Enabled
                    </h5>

                </div>

            </div>

        </div>

    </div>

    <div class="card shadow">

        <div class="card-header bg-success text-white">

            <h5 class="mb-0">
                Daftar Dokumen Terdaftar
            </h5>

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-dark">

                    <tr>

                        <th>ID</th>

                        <th>Nama File</th>

                        <th>Hash SHA-256</th>

                        <th>QR Code</th>

                        <th>PDF Final</th>

                    </tr>

                    </thead>

                    <tbody>

                    <?php
                    mysqli_data_seek($result,0);

                    while($row = mysqli_fetch_assoc($result)):
                    ?>

                    <tr>

                        <td>
                            <?= $row['id']; ?>
                        </td>

                        <td>
                            <?= $row['file_name']; ?>
                        </td>

                        <td>

                            <div
                                class="hash-box"
                                title="<?= $row['hash_value']; ?>">

                                <?= $row['hash_value']; ?>

                            </div>

                        </td>

                        <td>

                            <?php if($row['qr_path']) : ?>

                            <img
                                src="../qrcodes/<?= $row['qr_path']; ?>"
                                class="img-thumbnail qr-img">

                            <?php endif; ?>

                        </td>

                        <td>

                            <?php if(!empty($row['verified_pdf'])): ?>

                            <a
                                href="../verified/<?= $row['verified_pdf']; ?>"
                                target="_blank"
                                class="btn btn-success btn-sm">

                                Download PDF

                            </a>

                            <?php else: ?>

                            <span class="badge bg-secondary">
                                Belum Ada
                            </span>

                            <?php endif; ?>

                        </td>

                    </tr>

                    <?php endwhile; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

</body>
</html>