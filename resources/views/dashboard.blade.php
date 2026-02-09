<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - Digital Signage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .menu-card { transition: transform 0.2s; cursor: pointer; }
        .menu-card:hover { transform: translateY(-5px); }
    </style>
</head>
<body class="bg-light">

    <nav class="navbar navbar-dark bg-dark mb-5">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">
                <i class="fa-solid fa-tv me-2"></i> ADMIN PANEL SIGNAGE
            </a>
            <a href="{{ url('http://localhost/digital_signage/') }}" target="_blank" class="btn btn-success btn-sm">
                <i class="fa-solid fa-eye me-1"></i> Lihat Tampilan TV
            </a>
        </div>
    </nav>

    <div class="container">
        <div class="row g-4 justify-content-center">
            
            <div class="col-md-4 col-lg-3">
                <a href="{{ route('agendas.index') }}" class="text-decoration-none">
                    <div class="card menu-card shadow-sm h-100 border-0">
                        <div class="card-body text-center py-5">
                            <i class="fa-regular fa-calendar-check fa-3x text-primary mb-3"></i>
                            <h5 class="card-title fw-bold text-dark">Jadwal Agenda</h5>
                            <p class="text-muted small">Input jadwal pertemuan harian</p>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-4 col-lg-3">
                <a href="{{ route('apbd.index') }}" class="text-decoration-none">
                    <div class="card menu-card shadow-sm h-100 border-0">
                        <div class="card-body text-center py-5">
                            <i class="fa-solid fa-chart-pie fa-3x text-success mb-3"></i>
                            <h5 class="card-title fw-bold text-dark">Data APBD</h5>
                            <p class="text-muted small">Upload Excel & Update Tanggal</p>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-4 col-lg-3">
                <a href="{{ route('video.index') }}" class="text-decoration-none">
                    <div class="card menu-card shadow-sm h-100 border-0">
                        <div class="card-body text-center py-5">
                            <i class="fa-solid fa-film fa-3x text-danger mb-3"></i>
                            <h5 class="card-title fw-bold text-dark">Video Layar</h5>
                            <p class="text-muted small">Upload file MP4 atau Link Youtube</p>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-4 col-lg-3">
                <a href="{{ route('settings.index') }}" class="text-decoration-none">
                    <div class="card menu-card shadow-sm h-100 border-0">
                        <div class="card-body text-center py-5">
                            <i class="fa-solid fa-bullhorn fa-3x text-warning mb-3"></i>
                            <h5 class="card-title fw-bold text-dark">Running Text</h5>
                            <p class="text-muted small">Ubah teks berjalan di bawah</p>
                        </div>
                    </div>
                </a>
            </div>

        </div>
    </div>

</body>
</html>