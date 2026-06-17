<?php

include '../config/koneksi.php';
require '../vendor/autoload.php';

use setasign\Fpdi\Fpdi;
include '../phpqrcode/qrlib.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $file = $_FILES['pdf'];

    $fileName = time() . '_' . basename($file['name']);

    $targetPath = '../uploads/' . $fileName;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {

        // Generate SHA256
$hash = hash_file('sha256', $targetPath);

// Cek apakah hash sudah ada
$check = mysqli_query(
$conn,
"SELECT id, file_name
FROM documents
WHERE hash_value='$hash'"
);

if(mysqli_num_rows($check) > 0)
{
$existing = mysqli_fetch_assoc($check);

// Hapus file yang baru diupload
unlink($targetPath);

die(
    "<h2>Upload Ditolak</h2>
    <p>Dokumen identik sudah pernah diupload.</p>
    <p>ID Dokumen : ".$existing['id']."</p>
    <p>Nama File : ".$existing['file_name']."</p>"
);

}

// Simpan ke database
$sql = "INSERT INTO documents (
file_name,
hash_value
)
VALUES (
'$fileName',
'$hash'
)";


        if (mysqli_query($conn, $sql)) {

            // Ambil ID dokumen yang baru dibuat
            $documentId = mysqli_insert_id($conn);

            // URL verifikasi
            $fullVerifyUrl =
                "http://localhost/docverify/verify/check.php?id=" .
                $documentId;

            // Nama file QR
            $qrFileName =
                'qr_' . $documentId . '.png';

            $qrPath =
                '../qrcodes/' . $qrFileName;

            // Generate QR
            QRcode::png(
                $fullVerifyUrl,
                $qrPath,
                QR_ECLEVEL_L,
                5
            );

            // Simpan nama file QR
            // Simpan nama file QR
mysqli_query(
    $conn,
    "UPDATE documents
    SET qr_path='$qrFileName'
    WHERE id=$documentId"
);

/*
|--------------------------------------------------------------------------
| Tempel QR ke halaman terakhir PDF
|--------------------------------------------------------------------------
*/

$pdf = new Fpdi();

$pageCount = $pdf->setSourceFile($targetPath);

for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++)
{
    $template = $pdf->importPage($pageNo);

    $size = $pdf->getTemplateSize($template);

    $pdf->AddPage(
        $size['orientation'],
        [$size['width'], $size['height']]
    );

    $pdf->useTemplate($template);

    // Halaman terakhir
    if($pageNo == $pageCount)
    {
        $pdf->Image(
            $qrPath,
            $size['width'] - 40,
            $size['height'] - 40,
            25,
            25
        );
    }
}

/*
|--------------------------------------------------------------------------
| Simpan PDF hasil
|--------------------------------------------------------------------------
*/

$verifiedPdfName =
    'verified_' . $fileName;

$verifiedPdfPath =
    '../verified/' . $verifiedPdfName;

$pdf->Output(
    'F',
    $verifiedPdfPath
);

/*
|--------------------------------------------------------------------------
| Simpan nama PDF hasil ke database
|--------------------------------------------------------------------------
*/

mysqli_query(
    $conn,
    "UPDATE documents
    SET verified_pdf='$verifiedPdfName'
    WHERE id=$documentId"
);

echo "
<!DOCTYPE html>
<html>
<head>

<title>Upload Berhasil</title>

<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css' rel='stylesheet'>

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

.file-box{
    background:white;
    border:1px solid #dee2e6;
    border-radius:10px;
    padding:12px;
}

.hash-box{
    font-family:monospace;
}

.qr-image{
    max-width:220px;
    border-radius:12px;
    padding:10px;
    background:white;
    box-shadow:0 0 15px rgba(0,0,0,.15);
}

.btn{
    border-radius:10px;
}

.info-card{
    background:#f8f9fa;
    border-radius:10px;
    padding:15px;
}

</style>

</head>

<body>

<nav class='navbar navbar-dark bg-dark shadow'>
    <div class='container'>
        <span class='navbar-brand fw-bold'>
            DOCVERIFY
        </span>
    </div>
</nav>

<div class='container mt-5 mb-5'>

    <div class='row justify-content-center'>

        <div class='col-lg-10'>

            <div class='card shadow-lg border-0'>

                <div class='card-header bg-success text-white'>
                    Upload Berhasil
                </div>

                <div class='card-body p-4'>

                    <div class='alert alert-success'>
                        <strong>Berhasil!</strong>
                        Dokumen telah diproses, hash SHA-256 dibuat,
                        QR Code berhasil digenerate, dan PDF final berhasil dibuat.
                    </div>

                    <div class='mb-3'>

                        <label class='fw-bold mb-2'>
                            Nama File
                        </label>

                        <div class='file-box'>
                            $fileName
                        </div>

                    </div>

                    <div class='mb-4'>

                        <label class='fw-bold mb-2'>
                            SHA-256 Hash
                        </label>

                        <textarea
                            class='form-control hash-box'
                            rows='4'
                            readonly>$hash</textarea>

                    </div>

                    <div class='row'>

                        <div class='col-md-4 text-center'>

                            <div class='info-card'>

                                <h5 class='mb-3'>
                                    QR Code
                                </h5>

                                <img
                                    src='../qrcodes/$qrFileName'
                                    class='img-fluid qr-image'>

                            </div>

                        </div>

                        <div class='col-md-8'>

                            <div class='info-card h-100'>

                                <h5 class='mb-4'>
                                    Aksi Dokumen
                                </h5>

                                <div class='d-flex flex-wrap gap-2'>

                                    <a
                                        href='../verified/$verifiedPdfName'
                                        target='_blank'
                                        class='btn btn-success'>

                                        Download PDF Final

                                    </a>

                                    <a
                                        href='list.php'
                                        class='btn btn-primary'>

                                        Daftar Dokumen

                                    </a>

                                    <a
                                        href='logs.php'
                                        class='btn btn-dark'>

                                        Riwayat Verifikasi

                                    </a>

                                    <a
                                        href='index.php'
                                        class='btn btn-secondary'>

                                        Upload Lagi

                                    </a>

                                </div>

                                <hr>

                                <p class='text-muted mb-0'>
                                    Dokumen final telah memiliki QR Code
                                    yang mengarah ke halaman verifikasi.
                                    Jika isi dokumen diubah, sistem akan
                                    mendeteksi perubahan melalui perbandingan
                                    hash SHA-256.
                                </p>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>
";
          

        } else {

            echo "Gagal simpan database";

        }

    } else {

        echo "Upload gagal";

    }

}

?>