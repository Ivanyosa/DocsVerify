<!DOCTYPE html>
<html>
<head>
    <title>DOCVERIFY</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>

        body{
            background:#f4f6f9;
        }

        .hero-card{
            border:none;
            border-radius:20px;
        }

        .logo-box{
            width:90px;
            height:90px;
            border-radius:50%;
            background:#0d6efd;
            color:white;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:40px;
            margin:auto;
        }

        .btn-custom{
            min-width:170px;
        }

    </style>

</head>

<body>

<nav class="navbar navbar-dark bg-dark shadow-sm">
    <div class="container">

        <span class="navbar-brand fw-bold">
            DOCVERIFY
        </span>

    </div>
</nav>

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-lg-8">

            <div class="card hero-card shadow">

                <div class="card-body p-5">

                    <div class="text-center mb-4">

                        <div class="logo-box">
                            🔒
                        </div>

                        <h2 class="mt-3 fw-bold">
                            Sistem Verifikasi Dokumen Digital
                        </h2>

                        <p class="text-muted">
                            Upload dokumen PDF, buat QR Code otomatis,
                            dan verifikasi keaslian dokumen menggunakan
                            algoritma SHA-256.
                        </p>

                    </div>

                    <form
                        action="upload.php"
                        method="POST"
                        enctype="multipart/form-data">

                        <div class="mb-4">

                            <label class="form-label fw-semibold">
                                Pilih Dokumen PDF
                            </label>

                            <input
                                type="file"
                                name="pdf"
                                accept=".pdf"
                                class="form-control form-control-lg"
                                required>

                        </div>

                        <div class="d-flex flex-wrap gap-2">

                            <button
                                type="submit"
                                class="btn btn-success btn-lg btn-custom">

                                Upload Dokumen

                            </button>

                            <a
                                href="list.php"
                                class="btn btn-primary btn-lg btn-custom">

                                Daftar Dokumen

                            </a>

                            <a
                                href="logs.php"
                                class="btn btn-dark btn-lg btn-custom">

                                Riwayat Verifikasi

                            </a>

                        </div>

                    </form>

                </div>

            </div>

            <div class="row mt-4">

                <div class="col-md-4">

                    <div class="card border-0 shadow-sm">

                        <div class="card-body text-center">

                            <h1>
                                🔐
                            </h1>

                            <h6 class="fw-bold">
                                SHA-256
                            </h6>

                            <small class="text-muted">
                                Menjamin integritas dokumen.
                            </small>

                        </div>

                    </div>

                </div>

                <div class="col-md-4">

                    <div class="card border-0 shadow-sm">

                        <div class="card-body text-center">

                            <h1>
                                📱
                            </h1>

                            <h6 class="fw-bold">
                                QR Code
                            </h6>

                            <small class="text-muted">
                                Verifikasi cepat melalui scan QR.
                            </small>

                        </div>

                    </div>

                </div>

                <div class="col-md-4">

                    <div class="card border-0 shadow-sm">

                        <div class="card-body text-center">

                            <h1>
                                ✔️
                            </h1>

                            <h6 class="fw-bold">
                                Validasi
                            </h6>

                            <small class="text-muted">
                                Mendeteksi perubahan dokumen.
                            </small>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>