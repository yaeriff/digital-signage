<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <meta http-equiv="refresh" content="300">

    <title>Sekretariat Daerah - Digital Signage v3</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/stel.css">

    <style>
    /* Kolom 2: Perangkat Daerah (Rata Kiri) */
    #table-apbd-body td:nth-child(2) {
        text-align: left !important;
        padding-left: 15px; /* Biar lurus sama header */
    }

    /* Kolom 3: Anggaran Pendapatan (Rata Kanan) */
    #table-apbd-body td:nth-child(3) {
        text-align: right !important;
        padding-right: 0px; /* Biar lurus sama header */
    }

    /* Kolom 5: Anggaran Belanja (Rata Kanan) */
    #table-apbd-body td:nth-child(5) {
        text-align: right !important;
        padding-right: 0px; /* Biar lurus sama header */
    }

    /* Kolom 4 & 6: Realisasi (Tetap Tengah - Optional, buat jaga-jaga) */
    #table-apbd-body td:nth-child(4),
    #table-apbd-body td:nth-child(6) {
        text-align: center !important;
    }
    
</style>

</head>

<body>
    <div class="main-layout-container d-flex flex-column">
        <header class="header-section d-flex align-items-center px-4 text-white flex-shrink-0">
            <div class="col-auto pe-3 d-flex align-items-center h-100">
                <div class="logo-circle">
                    <img src="assets/img/logo1.png" alt="Logo">
                </div>
            </div>
            <div class="col h-100 d-flex flex-column justify-content-center">
                <h5 class="fw-bold text-uppercase m-0 lh-1" style="font-size: 1.25rem;">Sekretariat Daerah Provinsi</h5>
                <h6 class="fw-medium m-0 lh-1 pt-1 opacity-75" style="font-size: 0.9rem; letter-spacing: 1px;">JAWA TENGAH, INDONESIA</h6>
            </div>
            <div class="col-auto pe-3 d-flex align-items-center h-100">
                <div class="slogan-circle">
                    <img src="assets/img/slogan.png" alt="Logo">
                </div>
            </div>
        </header>

        <div class="content-wrapper d-flex flex-grow-1 overflow-hidden">
            <div class="col-left d-flex flex-column" style="width: 50%;">
                <div class="video-section bg-black position-relative" style="height: 50%;">
                    <div id="dynamic-content" class="w-100 h-100">
                        </div>
                </div>

                <div class="agenda-section bg-dark-green d-flex flex-column px-4 pb-1 pt-2 position-relative" style="height: 50%;">
                    <div class="section-title mb-3 flex-shrink-0">
                        <h5 class="text-warning fw-medium m-0 small">JADWAL AGENDA</h5>
                        <h2 class="text-warning fw-bold m-0">PERTEMUAN HARI INI</h2>
                    </div>

                    <div class="marquee-vertical-container flex-grow-1">
                        <div class="marquee-vertical-content" id="agenda-loop">
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-right d-flex flex-column bg-soft-green" style="width: 50%;">
                <div class="charts-section p-3 d-flex justify-content-center gap-4" style="padding-top: 11px !important; height: 30.4%;">
                    <div class="gauge-wrapper text-center position-relative d-flex flex-column align-items-center">
                        <div style="width: 240px; height: 150px; position: relative;">
                            <canvas id="gaugePendapatan"></canvas>
                            <div class="gauge-value" style="bottom: 36%; font-size: 1.1rem;">90 %</div>
                        </div>
                        <h6 class="fw-bold text-dark-green mt-1" style="font-size: 0.8rem;">Realisasi Pendapatan<br>Rp. 25.000.000.000.000</h6>
                    </div>

                    <div class="gauge-wrapper text-center position-relative d-flex flex-column align-items-center">
                        <div style="width: 240px; height: 150px; position: relative;">
                            <canvas id="gaugeBelanja"></canvas>
                            <div class="gauge-value" style="bottom: 36%; font-size: 1.1rem;">90 %</div>
                        </div>
                        <h6 class="fw-bold text-dark-green mt-1" style="font-size: 0.8rem;">Realisasi Belanja<br>Rp. 25.000.000.000.000</h6>
                    </div>
                </div>

                <div class="table-section p-4 d-flex flex-column" style="height: 100%;">
                    <h5 class="text-center text-dark-grey fw-bold mb-3 flex-shrink-0 text-uppercase">REALISASI APBD PROV. JATENG TA. 2026<br><small class="fw-normal">S.D 12 Januari 2026</small></h5>
                    
                    <div class="text-dark-grey mb-1 flex-shrink-0 text-uppercase" style="padding-right: 5px; text-align: right; font-size: 0.64rem;">E-PENATAUSAHAAN GRMS</div>
                    <div class="table-layout-wrapper d-flex flex-column h-100 shadow-sm rounded overflow-hidden bg-white">
                        <div class="table-header-custom d-flex text-center flex-shrink-0">
                            <div class="d-flex align-items-center justify-content-center" style="width:5%">
                                NO.
                            </div>
                            <div class="d-flex align-items-center justify-content-center" style="width:29%">
                                PERANGKAT DAERAH
                            </div>
                            <div class="d-flex align-items-center justify-content-center" style="width:24%">
                                ANGGARAN<br> PENDAPATAN(Rp)
                            </div>
                            <div class="d-flex align-items-center justify-content-center" style="width:9%">
                                REAL (%)
                            </div>
                            <div class="d-flex align-items-center justify-content-center" style="width:24%">
                                ANGGARAN<br> BELANJA(Rp)
                            </div>
                            <div class="d-flex align-items-center justify-content-center" style="width:9%">
                                REAL (%)
                            </div>
                        </div>

                        <div class="table-total-row d-flex align-items-center flex-shrink-0 border-bottom text-dark-green fw-bold" style="background-color: #85bbb3ff;">
                            <div style="width:5%"></div>
                            <div style="width:29%; text-align: center; font-size: 0.7rem;">
                                PROVINSI JAWA TENGAH
                            </div>
                            <div id="total-anggaran-pend" style="width:24%; text-align: center; font-size: 0.7rem;">
                                Rp 0
                            </div>
                            <div id="total-realisasi-pend" style="width:9%; text-align: center; font-size: 0.7rem;">
                                0%
                            </div>
                            <div id="total-anggaran-bel" style="width:24%; text-align: center; font-size: 0.7rem;">
                                Rp 0
                            </div>
                            <div id="total-realisasi-bel" style="width:9%; text-align: center; font-size: 0.7rem;">
                                0%
                            </div>
                        </div>

                        <div class="marquee-vertical-container flex-grow-1 bg-white position-relative">
                            <div class="marquee-vertical-content">
                                <table class="table table-sm table-hover mb-0 small w-100 custom-table">
                                    <tbody id="table-apbd-body">
                                        </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <footer class="footer-section flex-shrink-0">
            <div class="info-label d-flex flex-column align-items-center justify-content-center text-center">
                <div id="jam" class="fw-bold lh-1" style="font-size: 1.1rem; color: #164A41; letter-spacing: 1px;">
                    00:00
                </div>
                <div id="tanggal" class="fw-bold lh-1" style="font-size: 0.65rem; color: var(--dark-green); margin-top: 2px;">
                    1 Januari 2026
                </div>
            </div>
            <div class="ticker-wrap">
                <div class="ticker">
                    Selamat datang di Sekretariat Daerah Provinsi Jawa Tengah. Mari wujudkan Jawa Tengah yang Sejahtera dan Berdikari. --- Tetap patuhi protokol kesehatan di lingkungan kerja.
                </div>
            </div>
        </footer>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="assets/js/chart_data.js"></script>
    <script src="assets/js/skrip.js"></script>
</body>
</html>