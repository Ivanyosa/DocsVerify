<?php

include '../config/koneksi.php';
require '../vendor/autoload.php';

use setasign\Fpdi\Fpdi;

include '../phpqrcode/qrlib.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $successFiles = [];

    $totalFiles = count($_FILES['pdf']['name']);

    for ($i = 0; $i < $totalFiles; $i++) {

        if (empty($_FILES['pdf']['name'][$i])) {
            continue;
        }

        $fileName =
            time() . '_' . $i . '_' .
            basename($_FILES['pdf']['name'][$i]);

        $targetPath =
            '../uploads/' . $fileName;

        if (move_uploaded_file(
            $_FILES['pdf']['tmp_name'][$i],
            $targetPath
        )) {

            // Generate SHA256
            $hash = hash_file('sha256', $targetPath);

            // Cek apakah hash sudah ada
            $check = mysqli_query(
                $conn,
                "SELECT id, file_name
FROM documents
WHERE hash_value='$hash'"
            );

            if (mysqli_num_rows($check) > 0) {
                $existing = mysqli_fetch_assoc($check);

                // Hapus file yang baru diupload
                unlink($targetPath);

                die("<h2>Upload Ditolak</h2>
    <p>Dokumen identik sudah pernah diupload.</p>
    <p>ID Dokumen : " . $existing['id'] . "</p>
    <p>Nama File : " . $existing['file_name'] . "</p>");
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

                for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {

                    $template = $pdf->importPage($pageNo);

                    $size = $pdf->getTemplateSize($template);

                    $pdf->AddPage(
                        $size['orientation'],
                        [$size['width'], $size['height']]
                    );

                    $pdf->useTemplate($template);

                    // QR kanan atas semua halaman
                    $pdf->Image(
                        $qrPath,
                        $size['width'] - 35,
                        10,
                        20,
                        20
                    );
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
                $successFiles[] = [
                    'file' => $fileName,
                    'hash' => $hash,
                    'qr' => $qrFileName,
                    'pdf' => $verifiedPdfName
                ];
            } else {

                echo "Gagal simpan database";
            }
        } else {

            echo "Upload gagal";
        }
    }
?>
    <!DOCTYPE html>
    <html>

    <head>
        <title>Upload Berhasil</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

        <style>
            body {
                background: #f4f6f9;
            }

            .main-card {
                border: none;
                border-radius: 20px;
                overflow: hidden;
            }

            .table img {
                border-radius: 10px;
                background: white;
                padding: 5px;
                box-shadow: 0 0 10px rgba(0, 0, 0, .1);
            }

            .summary-box {
                background: #e8f5e9;
                border-left: 5px solid #198754;
                padding: 15px;
                border-radius: 10px;
            }

            .btn {
                border-radius: 10px;
            }

            .table th {
                background: #f8f9fa;
            }
        </style>
    </head>

    <body>

        <nav class="navbar navbar-dark bg-dark shadow">
            <div class="container">
                <span class="navbar-brand fw-bold">
                    DOCVERIFY
                </span>
            </div>
        </nav>

        <div class="container mt-5 mb-5">

            <div class="row justify-content-center">

                <div class="col-lg-11">

                    <div class="card main-card shadow-lg">

                        <div class="card-header bg-success text-white py-3">
                            <h4 class="mb-0">
                                Upload Berhasil
                            </h4>
                        </div>

                        <div class="card-body p-4">

                            <div class="summary-box mb-4">

                                <h5 class="mb-1">
                                    ✅ Proses Selesai
                                </h5>

                                <small>
                                    <?= count($successFiles) ?>
                                    dokumen berhasil diproses dan diberi QR Code.
                                </small>

                            </div>

                            <div class="table-responsive">

                                <table class="table table-hover align-middle">

                                    <thead>

                                        <tr>
                                            <th width="70">#</th>
                                            <th>Nama File</th>
                                            <th width="120">QR Code</th>
                                            <th width="180">PDF Final</th>
                                        </tr>

                                    </thead>

                                    <tbody>

                                        <?php
                                        $no = 1;

                                        foreach ($successFiles as $doc) {
                                        ?>

                                            <tr>

                                                <td>
                                                    <?= $no++ ?>
                                                </td>

                                                <td>
                                                    <?= $doc['file'] ?>
                                                </td>

                                                <td class="text-center">

                                                    <img
                                                        src="../qrcodes/<?= $doc['qr'] ?>"
                                                        width="70">

                                                </td>

                                                <td>

                                                    <a
                                                        href="../verified/<?= $doc['pdf'] ?>"
                                                        target="_blank"
                                                        class="btn btn-success btn-sm">

                                                        Download PDF

                                                    </a>

                                                </td>

                                            </tr>

                                        <?php } ?>

                                    </tbody>

                                </table>

                            </div>

                            <hr>

                            <div class="d-flex gap-2 flex-wrap">

                                <a
                                    href="index.php"
                                    class="btn btn-primary">

                                    Upload Lagi

                                </a>

                                <a
                                    href="list.php"
                                    class="btn btn-success">

                                    Daftar Dokumen

                                </a>

                                <a
                                    href="logs.php"
                                    class="btn btn-dark">

                                    Riwayat Verifikasi

                                </a>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </body>

    </html>
<?php
}
?>