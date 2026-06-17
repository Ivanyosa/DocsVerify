<?php

include '../config/koneksi.php';

$id = $_GET['id'];

$result = mysqli_query(
    $conn,
    "SELECT * FROM documents
    WHERE id='$id'"
);

$data = mysqli_fetch_assoc($result);

if(isset($_FILES['pdf']))
{
    $tmpFile =
    $_FILES['pdf']['tmp_name'];

    $uploadedHash =
    hash_file(
        'sha256',
        $tmpFile
    );

    $originalHash =
    $data['hash_value'];

    if(
    $uploadedHash
    ==
    $originalHash
)
{
    $status = "VALID";

    $message =
    "Dokumen asli dan belum diubah.";

    mysqli_query(
        $conn,
        "INSERT INTO verification_logs
        (document_id,status)
        VALUES
        ('$id','VALID')"
    );
}
else
{
    $status = "INVALID";

    $message =
    "Dokumen telah dimodifikasi.";

    mysqli_query(
        $conn,
        "INSERT INTO verification_logs
        (document_id,status)
        VALUES
        ('$id','INVALID')"
    );
}
}
?>

<!DOCTYPE html>

<html>
<head>
    <title>Hasil Verifikasi</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-dark">
    <div class="container">
        <span class="navbar-brand fw-bold">
            DOCVERIFY
        </span>
    </div>
</nav>

<div class="container mt-5">

<div class="card shadow">

    <div class="card-header bg-dark text-white">
        Hasil Verifikasi
    </div>

    <div class="card-body">

        <?php if($status == "VALID"): ?>

            <div class="alert alert-success">

                <h4 class="alert-heading">
                    ✓ VALID
                </h4>

                <p class="mb-0">
                    <?= $message ?>
                </p>

            </div>

        <?php else: ?>

            <div class="alert alert-danger">

                <h4 class="alert-heading">
                    ✗ INVALID
                </h4>

                <p class="mb-0">
                    <?= $message ?>
                </p>

            </div>

        <?php endif; ?>

        <div class="mb-3">

            <label class="form-label fw-bold">
                Hash Database
            </label>

            <textarea
                class="form-control"
                rows="4"
                readonly><?= $originalHash ?></textarea>

        </div>

        <div class="mb-3">

            <label class="form-label fw-bold">
                Hash Upload
            </label>

            <textarea
                class="form-control"
                rows="4"
                readonly><?= $uploadedHash ?></textarea>

        </div>

        <a
            href="javascript:history.back()"
            class="btn btn-primary">

            Kembali

        </a>

    </div>

</div>
</div>

</body>
</html>
