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
    
    <link rel="stylesheet" href="{{ asset('signage/assets/css/stel.css') }}">

    <style>
    /* Styling Tambahan untuk Agenda Loop */
    .agenda-item {
        padding: 15px;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        margin-bottom: 5px;
    }
    .agenda-time {
        font-weight: 800;
        color: #ffc107; /* Warna kuning */
        font-size: 1.1rem;
        display: block;
    }
    .agenda-title {
        color: white;
        font-size: 1rem;
        font-weight: 500;
    }
    .agenda-loc {
        color: #ddd;
        font-size: 0.8rem;
        font-style: italic;
    }

    /* Styling Tabel (Bawaan Anda) */
    #table-apbd-body td:nth-child(2) { text-align: left !important; padding-left: 15px; }
    #table-apbd-body td:nth-child(3) { text-align: right !important; padding-right: 0px; }
    #table-apbd-body td:nth-child(5) { text-align: right !important; padding-right: 0px; }
    #table-apbd-body td:nth-child(4), #table-apbd-body td:nth-child(6) { text-align: center !important; }
    
    /* Agar video fit penuh */
    video { width: 100%; height: 100%; object-fit: cover; }
    iframe { width: 100%; height: 100%; border: none; }
    </style>

</head>

<body>
    <div class="main-layout-container d-flex flex-column">
        <header class="header-section d-flex align-items-center px-4 text-white flex-shrink-0">
            <div class="col-auto pe-3 d-flex align-items-center h-100">
                <div class="logo-circle">
                    <img src="{{ asset('signage/assets/img/logo1.png') }}" alt="Logo">
                </div>
            </div>
            <div class="col h-100 d-flex flex-column justify-content-center">
                <h5 class="fw-bold text-uppercase m-0 lh-1" style="font-size: 1.25rem;">Sekretariat Daerah Provinsi</h5>
                <h6 class="fw-medium m-0 lh-1 pt-1 opacity-75" style="font-size: 0.9rem; letter-spacing: 1px;">JAWA TENGAH, INDONESIA</h6>
            </div>
            <div class="col-auto pe-3 d-flex align-items-center h-100">
                <div class="slogan-circle">
                    <img src="{{ asset('signage/assets/img/slogan.png') }}" alt="Logo">
                </div>
            </div>
        </header>

        <div class="content-wrapper d-flex flex-grow-1 overflow-hidden">
            <div class="col-left d-flex flex-column" style="width: 50%;">
                
                <div class="video-section bg-black position-relative" style="height: 50%;">
                    <div id="dynamic-content" class="w-100 h-100 d-flex justify-content-center align-items-center overflow-hidden">
                        @if($video)
                            @if(Str::contains($video->file_path, 'youtube.com') || Str::contains($video->file_path, 'youtu.be'))
                                <iframe 
                                    src="https://www.youtube.com/embed/{{ \Str::afterLast($video->file_path, '/') }}?autoplay=1&mute=1&controls=0&loop=1&playlist={{ \Str::afterLast($video->file_path, '/') }}&modestbranding=1&rel=0&iv_load_policy=3&fs=0" 
                                    frameborder="0" 
                                    allow="autoplay; encrypted-media">
                                </iframe>
                            @else
                                <video autoplay muted loop playsinline>
                                    <source src="{{ asset('storage/' . $video->file_path) }}" type="video/mp4">
                                </video>
                            @endif
                        @else
                            <h3 class="text-white">Tidak ada video</h3>
                        @endif
                    </div>
                </div>

                <div class="agenda-section bg-dark-green d-flex flex-column px-4 pb-1 pt-2 position-relative" style="height: 50%;">
                    <div class="section-title mb-3 flex-shrink-0">
                        <h5 class="text-warning fw-medium m-0 small">JADWAL AGENDA</h5>
                        <h2 class="text-warning fw-bold m-0">PERTEMUAN HARI INI</h2>
                    </div>

                    <div class="marquee-vertical-container flex-grow-1">
                        <div class="marquee-vertical-content" id="agenda-loop">
                            @forelse($agendas as $agenda)
                                <div class="agenda-item">
                                    <div class="agenda-time">
                                        {{ \Carbon\Carbon::parse($agenda->start_time)->format('H:i') }} - 
                                        {{ \Carbon\Carbon::parse($agenda->end_time)->format('H:i') }}
                                    </div>
                                    <div class="agenda-title">{{ $agenda->title }}</div>
                                    <div class="agenda-loc"><i class="fa fa-map-marker-alt me-1"></i> {{ $agenda->location }}</div>
                                </div>
                            @empty
                                <div class="text-white text-center mt-5">
                                    <h4>Tidak ada agenda hari ini.</h4>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-right d-flex flex-column bg-soft-green" style="width: 50%;">
                
                <div class="charts-section p-3 d-flex justify-content-center gap-4" style="padding-top: 11px !important; height: 30.4%;">
                    <div class="gauge-wrapper text-center position-relative d-flex flex-column align-items-center">
                        <div style="width: 240px; height: 150px; position: relative;">
                            <canvas id="gaugePendapatan"></canvas>
                            <div class="gauge-value" style="bottom: 36%; font-size: 1.1rem;">0 %</div>
                        </div>
                        <h6 class="fw-bold text-dark-green mt-1" style="font-size: 0.8rem;">Realisasi Pendapatan<br>
                            Rp. 0
                        </h6>
                    </div>

                    <div class="gauge-wrapper text-center position-relative d-flex flex-column align-items-center">
                        <div style="width: 240px; height: 150px; position: relative;">
                            <canvas id="gaugeBelanja"></canvas>
                            <div class="gauge-value" style="bottom: 36%; font-size: 1.1rem;">
                                {{ count($apbds) > 0 ? number_format($apbds->avg('persen'), 0) : 0 }} %
                            </div>
                        </div>
                        <h6 class="fw-bold text-dark-green mt-1" style="font-size: 0.8rem;">Realisasi Belanja<br>
                            Rp. {{ number_format($apbds->sum('realisasi'), 0, ',', '.') }}
                        </h6>
                    </div>
                </div>

                <div class="table-section p-4 d-flex flex-column" style="height: 100%;">
                    <h5 class="text-center text-dark-grey fw-bold mb-3 flex-shrink-0 text-uppercase">REALISASI APBD PROV. JATENG TA. {{ date('Y') }}<br>
                        <small class="fw-normal">Per Tanggal: {{ $apbdDate ?? '-' }}</small>
                    </h5>
                    
                    <div class="text-dark-grey mb-1 flex-shrink-0 text-uppercase" style="padding-right: 5px; text-align: right; font-size: 0.64rem;">E-PENATAUSAHAAN GRMS</div>
                    <div class="table-layout-wrapper d-flex flex-column h-100 shadow-sm rounded overflow-hidden bg-white">
                        
                        <div class="table-header-custom d-flex text-center flex-shrink-0">
                            <div class="d-flex align-items-center justify-content-center" style="width:5%">NO.</div>
                            <div class="d-flex align-items-center justify-content-center" style="width:29%">PERANGKAT DAERAH</div>
                            <div class="d-flex align-items-center justify-content-center" style="width:24%">ANGGARAN<br> PENDAPATAN(Rp)</div>
                            <div class="d-flex align-items-center justify-content-center" style="width:9%">REAL (%)</div>
                            <div class="d-flex align-items-center justify-content-center" style="width:24%">ANGGARAN<br> BELANJA(Rp)</div>
                            <div class="d-flex align-items-center justify-content-center" style="width:9%">REAL (%)</div>
                        </div>

                        <div class="table-total-row d-flex align-items-center flex-shrink-0 border-bottom text-dark-green fw-bold" style="background-color: #85bbb3ff;">
                            <div style="width:5%"></div>
                            <div style="width:29%; text-align: center; font-size: 0.7rem;">PROVINSI JAWA TENGAH</div>
                            <div id="total-anggaran-pend" style="width:24%; text-align: center; font-size: 0.7rem;">Rp 0</div>
                            <div id="total-realisasi-pend" style="width:9%; text-align: center; font-size: 0.7rem;">0%</div>
                            <div id="total-anggaran-bel" style="width:24%; text-align: center; font-size: 0.7rem;">
                                Rp {{ number_format($apbds->sum('anggaran'), 0, ',', '.') }}
                            </div>
                            <div id="total-realisasi-bel" style="width:9%; text-align: center; font-size: 0.7rem;">
                                {{ count($apbds) > 0 ? number_format($apbds->avg('persen'), 1) : 0 }}%
                            </div>
                        </div>

                        <div class="marquee-vertical-container flex-grow-1 bg-white position-relative">
                            <div class="marquee-vertical-content">
                                <table class="table table-sm table-hover mb-0 small w-100 custom-table">
                                    <tbody id="table-apbd-body">
                                        @foreach($apbds as $index => $item)
                                        <tr>
                                            <td style="width:5%; text-align:center;">{{ $index + 1 }}</td>
                                            <td style="width:29%;">{{ $item->uraian }}</td>
                                            
                                            <td style="width:24%;">Rp 0</td>
                                            <td style="width:9%;">0%</td>
                                            
                                            <td style="width:24%;">Rp {{ number_format($item->anggaran, 0, ',', '.') }}</td>
                                            <td style="width:9%;">
                                                <span class="badge {{ $item->persen > 80 ? 'bg-success' : ($item->persen > 50 ? 'bg-warning' : 'bg-danger') }}">
                                                    {{ $item->persen }}%
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
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
                    {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
                </div>
            </div>
            <div class="ticker-wrap">
                <div class="ticker">
                    {{ $ticker ?? 'Selamat datang di Sekretariat Daerah Provinsi Jawa Tengah...' }}
                </div>
            </div>
        </footer>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script src="{{ asset('signage/assets/js/chart_data.js') }}"></script>
    <script src="{{ asset('signage/assets/js/skrip.js') }}"></script>
    
    <script>
        // Update Jam Real-time
        function updateClock() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            document.getElementById('jam').textContent = timeString;
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
</body>
</html>