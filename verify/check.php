<?php

include '../config/koneksi.php';

$id = (int)$_GET['id'];

$result = mysqli_query(
    $conn,
    "SELECT * FROM documents
    WHERE id='$id'"
);

$data = mysqli_fetch_assoc($result);

?>

<!DOCTYPE html>
<html>

<head>

    <title>Verifikasi Dokumen</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>

        body{
            background:#f4f6f9;
        }

        .card{
            border-radius:20px;
            overflow:hidden;
        }

        .card-header{
            font-size:20px;
            font-weight:bold;
        }

        .info-box{
            background:#f8f9fa;
            border-radius:12px;
            padding:15px;
            border-left:5px solid #0d6efd;
        }

        .btn{
            border-radius:10px;
        }

        .doc-icon{
            font-size:60px;
        }

    </style>

</head>

<body>

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-lg-8">

            <div class="card shadow-lg border-0">

                <div class="card-header bg-primary text-white">

                    Verifikasi Dokumen

                </div>

                <div class="card-body p-4">

                    <div class="text-center mb-4">

                        <div class="doc-icon">
                            📄
                        </div>

                        <h4 class="mt-2">
                            Verifikasi Keaslian Dokumen
                        </h4>

                        <p class="text-muted">
                            Upload dokumen PDF yang ingin diverifikasi
                        </p>

                    </div>

                    <div class="info-box mb-4">

                        <div class="row">

                            <div class="col-md-3">

                                <strong>ID Dokumen</strong>

                            </div>

                            <div class="col-md-9">

                                <?= $data['id']; ?>

                            </div>

                        </div>

                        <hr>

                        <div class="row">

                            <div class="col-md-3">

                                <strong>Nama File</strong>

                            </div>

                            <div class="col-md-9">

                                <?= $data['file_name']; ?>

                            </div>

                        </div>

                    </div>

                    <form
                        action="verify_process.php?id=<?= $id ?>"
                        method="POST"
                        enctype="multipart/form-data">

                        <div class="mb-4">

                            <label class="form-label fw-bold">

                                Pilih PDF Untuk Verifikasi

                            </label>

                            <input
                                type="file"
                                name="pdf"
                                accept=".pdf"
                                class="form-control form-control-lg"
                                required>

                        </div>

                        <div class="d-grid">

                            <button
                                type="submit"
                                class="btn btn-success btn-lg">

                                Verifikasi Dokumen

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>