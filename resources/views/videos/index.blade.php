<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kelola Video - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <a href="{{ route('dashboard') }}" class="btn btn-danger">Kembali</a>
        <div class="card shadow-sm">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0">Kelola Video Tampilan</h5>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="mb-4 p-3 border rounded bg-light text-center">
                    <label class="fw-bold d-block mb-2">Video Aktif Saat Ini:</label>
                    @if($video)
                        @if($video->type == 'local')
                            <span class="badge bg-primary mb-2">File Lokal</span>
                            <div class="ratio ratio-16x9 w-50 mx-auto">
                                <video controls src="{{ asset('storage/' . $video->url) }}"></video>
                            </div>
                        @else
                            <span class="badge bg-danger mb-2">YouTube</span>
                            <p class="text-muted small">{{ $video->url }}</p>
                        @endif
                    @else
                        <p class="text-muted">Belum ada video diatur.</p>
                    @endif
                </div>

                <hr>

                <form id="uploadForm" action="{{ route('video.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="fw-bold">Pilih Sumber Video:</label>
                        <div class="d-flex gap-3 mt-1">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="type" id="typeLocal" value="local" checked onclick="toggleInput()">
                                <label class="form-check-label" for="typeLocal">Upload File (Local)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="type" id="typeYoutube" value="youtube" onclick="toggleInput()">
                                <label class="form-check-label" for="typeYoutube">Link YouTube</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3" id="inputLocal">
                        <label class="form-label">Upload Video (.mp4)</label>
                        <input type="file" name="video_file" id="video_file" class="form-control" accept="video/mp4,video/x-m4v,video/*">
                        <small class="text-muted">Maksimal ukuran file 2GB.</small>
                    </div>

                    <div class="mb-3 d-none" id="inputYoutube">
                        <label class="form-label">Masukkan Link YouTube</label>
                        <input type="text" name="video_url" class="form-control" placeholder="Contoh: https://www.youtube.com/watch?v=dQw4w9WgXcQ">
                    </div>

                    <div id="progressArea" class="mb-4 d-none">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-bold small text-primary" id="progressStatus">Mengunggah...</span>
                            <span class="fw-bold small text-dark" id="uploadSpeed">0 MB/s</span>
                        </div>
                        <div class="progress" style="height: 25px;">
                            <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%">0%</div>
                        </div>
                        <small class="text-danger mt-1 d-none" id="errorMessage"></small>
                    </div>

                    <button type="submit" id="btnSubmit" class="btn btn-danger">Simpan Video</button>
                    <a href="{{ route('agendas.index') }}" class="btn btn-outline-secondary ms-2">Kembali</a>
                </form>

            </div>
        </div>
    </div>

    <script>
        function toggleInput() {
            const isYoutube = document.getElementById('typeYoutube').checked;
            if (isYoutube) {
                document.getElementById('inputLocal').classList.add('d-none');
                document.getElementById('inputYoutube').classList.remove('d-none');
            } else {
                document.getElementById('inputLocal').classList.remove('d-none');
                document.getElementById('inputYoutube').classList.add('d-none');
            }
        }

        // --- SCRIPT BARU UNTUK UPLOAD PROGRESS ---
    document.getElementById('uploadForm').addEventListener('submit', function(e) {
        
        // Cek apakah mode Local File dipilih?
        const isLocal = document.getElementById('typeLocal').checked;
        const fileInput = document.getElementById('video_file');

        // Jika mode Youtube, biarkan submit biasa (karena tidak butuh upload besar)
        if (!isLocal || fileInput.files.length === 0) {
            return; // Lanjut submit biasa
        }

        // Jika mode Local, kita ambil alih dengan AJAX
        e.preventDefault(); 

        const formData = new FormData(this);
        const progressBar = document.getElementById('progressBar');
        const progressArea = document.getElementById('progressArea');
        const progressStatus = document.getElementById('progressStatus');
        const uploadSpeed = document.getElementById('uploadSpeed');
        const btnSubmit = document.getElementById('btnSubmit');
        const errorMessage = document.getElementById('errorMessage');

        // Tampilkan Progress Bar
        progressArea.classList.remove('d-none');
        btnSubmit.disabled = true; // Matikan tombol submit biar ga didouble klik
        btnSubmit.innerText = "Sedang Mengunggah...";
        errorMessage.classList.add('d-none');

        // Variabel untuk hitung kecepatan
        let lastLoaded = 0;
        let lastTime = Date.now();

        axios.post("{{ route('video.update') }}", formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            },
            onUploadProgress: function(progressEvent) {
                // 1. Hitung Persentase
                const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                
                // Update Bar
                progressBar.style.width = percentCompleted + '%';
                progressBar.innerText = percentCompleted + '%';

                // 2. Hitung Kecepatan Upload
                const currentTime = Date.now();
                const timeDiff = (currentTime - lastTime) / 1000; // dalam detik

                // Hitung speed tiap 0.5 detik agar angka tidak berkedip terlalu cepat
                if (timeDiff >= 0.5) {
                    const loadedDiff = progressEvent.loaded - lastLoaded; // byte yang terupload dalam selang waktu
                    const speedBytes = loadedDiff / timeDiff; // byte per detik
                    const speedMB = (speedBytes / (1024 * 1024)).toFixed(2); // Convert ke MB/s

                    uploadSpeed.innerText = speedMB + " MB/s";

                    // Reset variabel last
                    lastLoaded = progressEvent.loaded;
                    lastTime = currentTime;
                }

                // Ubah status teks
                if(percentCompleted < 100) {
                    progressStatus.innerText = `Terupload: ${(progressEvent.loaded / (1024*1024)).toFixed(1)} MB / ${(progressEvent.total / (1024*1024)).toFixed(1)} MB`;
                } else {
                    progressStatus.innerText = "Finishing... Tunggu respon server.";
                    uploadSpeed.innerText = "";
                    progressBar.classList.add('bg-success');
                    progressBar.classList.remove('bg-primary');
                }
            }
        })
        .then(function (response) {
            // Sukses
            window.location.reload(); // Reload halaman untuk memunculkan flash message sukses dari Laravel
        })
        .catch(function (error) {
            // Gagal
            console.error(error);
            btnSubmit.disabled = false;
            btnSubmit.innerText = "Simpan Video";
            progressArea.classList.add('d-none');
            
            // Tampilkan Pesan Error
            errorMessage.classList.remove('d-none');
            if (error.response && error.response.data && error.response.data.message) {
                 errorMessage.innerText = "Gagal: " + error.response.data.message;
            } else if (error.response && error.response.status === 413) {
                 errorMessage.innerText = "Gagal: File terlalu besar (Melebihi batas server). Cek settingan Nginx/PHP.";
            } else {
                 errorMessage.innerText = "Terjadi kesalahan saat mengupload.";
            }
            
            alert("Upload Gagal! Cek pesan error.");
        });
    });

    </script>
</body>
</html>