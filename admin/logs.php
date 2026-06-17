<?php

include '../config/koneksi.php';

$result = mysqli_query(
    $conn,
    "SELECT
        verification_logs.id,
        documents.file_name,
        verification_logs.status,
        verification_logs.verified_at
    FROM verification_logs
    JOIN documents
    ON verification_logs.document_id = documents.id
    ORDER BY verification_logs.id DESC"
);

if(!$result)
{
    die(mysqli_error($conn));
}

?>
<!DOCTYPE html>
<html>

<head>

    <title>Riwayat Verifikasi</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>

        body{
            background:#f4f6f9;
        }

        .card{
            border:none;
            border-radius:15px;
        }

        .status-valid{
            background:#198754;
        }

        .status-invalid{
            background:#dc3545;
        }

        .table td,
        .table th{
            vertical-align:middle;
        }

    </style>

</head>

<body>

<nav class="navbar navbar-dark bg-dark shadow">

    <div class="container">

        <span class="navbar-brand fw-bold">
            DOCVERIFY ADMIN
        </span>

        <div>

            <a href="index.php"
                class="btn btn-light btn-sm">

                Upload

            </a>

            <a href="list.php"
                class="btn btn-success btn-sm">

                Dokumen

            </a>

        </div>

    </div>

</nav>

<div class="container mt-4">

    <?php
    $totalVerifikasi = mysqli_num_rows($result);

    mysqli_data_seek($result,0);
    ?>

    <div class="row mb-4">

        <div class="col-md-4">

            <div class="card shadow-sm">

                <div class="card-body">

                    <h6 class="text-muted">
                        Total Verifikasi
                    </h6>

                    <h2 class="fw-bold">
                        <?= $totalVerifikasi ?>
                    </h2>

                </div>

            </div>

        </div>

        <div class="col-md-4">

            <div class="card shadow-sm">

                <div class="card-body">

                    <h6 class="text-muted">
                        Monitoring
                    </h6>

                    <h5 class="fw-bold">
                        Real Time Log
                    </h5>

                </div>

            </div>

        </div>

        <div class="col-md-4">

            <div class="card shadow-sm">

                <div class="card-body">

                    <h6 class="text-muted">
                        Status
                    </h6>

                    <h5 class="fw-bold text-success">
                        Aktif
                    </h5>

                </div>

            </div>

        </div>

    </div>

    <div class="card shadow">

        <div class="card-header bg-warning">

            <h5 class="mb-0">
                Riwayat Verifikasi Dokumen
            </h5>

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover">

                    <thead class="table-dark">

                    <tr>

                        <th>No</th>

                        <th>Nama Dokumen</th>

                        <th>Status</th>

                        <th>Waktu Verifikasi</th>

                    </tr>

                    </thead>

                    <tbody>

                    <?php
                    $no = 1;

                    while($row = mysqli_fetch_assoc($result))
                    {
                    ?>

                    <tr>

                        <td>
                            <?= $no++; ?>
                        </td>

                        <td>
                            <?= $row['file_name']; ?>
                        </td>

                        <td>

                            <?php if($row['status']=="VALID"): ?>

                                <span class="badge status-valid">
                                    VALID
                                </span>

                            <?php else: ?>

                                <span class="badge status-invalid">
                                    INVALID
                                </span>

                            <?php endif; ?>

                        </td>

                        <td>
                            <?= $row['verified_at']; ?>
                        </td>

                    </tr>

                    <?php } ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

</body>
</html>