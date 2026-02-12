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

                <form id="uploadForm" method="POST" enctype="multipart/form-data">
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

                    <div class="mb-3" id="containerLocal">
                        <label class="form-label">Upload Video (.mp4)</label>
                        <input type="file" id="video_file" name="video_file" class="form-control">
                        <small class="text-muted">Maksimal ukuran file besar 500MB (Chunk Upload).</small>
                    </div>

                    <div class="mb-3 d-none" id="containerYoutube">
                        <label class="form-label">Masukkan Link YouTube</label>
                        <input type="text" id="video_url" name="video_url" class="form-control" placeholder="Contoh: https://www.youtube.com/watch?v=dQw4w9WgXcQ">
                    </div>

                    <div class="mb-4">
                        <div class="progress" style="height: 25px;">
                            <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                                role="progressbar" style="width: 0%">0%</div>
                        </div>
                    </div>

                    <button type="button" id="startUpload" class="btn btn-danger">Simpan Video</button>
                    <div class="mt-2 small text-muted" id="fileInfo"></div>
                    
                    <div class="mt-3 text-center">
                        <video id="previewVideo" class="w-50 d-none" controls></video>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        // 1. Toggle Input Logic
        function toggleInput() {
            const isYoutube = document.getElementById('typeYoutube').checked;
            const containerLocal = document.getElementById('containerLocal');
            const containerYoutube = document.getElementById('containerYoutube');
            const previewVideo = document.getElementById('previewVideo');

            if (isYoutube) {
                containerLocal.classList.add('d-none');
                containerYoutube.classList.remove('d-none');
                previewVideo.classList.add('d-none'); // Sembunyikan preview lokal jika pindah ke YT
            } else {
                containerLocal.classList.remove('d-none');
                containerYoutube.classList.add('d-none');
            }
        }

        // 2. Resumable.js Logic (Local File)
        let r = new Resumable({
            target: "{{ route('upload.chunk') }}",
            headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
            fileParameterName: 'video_file',
            chunkSize: 5 * 1024 * 1024,
            testChunks: false
        });

        r.assignBrowse(document.getElementById('video_file'));

        // 3. Tombol Upload (Handel 2 Kondisi)
        document.getElementById('startUpload').addEventListener('click', function() {
            const isYoutube = document.getElementById('typeYoutube').checked;

            if (isYoutube) {
                // Logika Simpan Link YouTube
                const url = document.getElementById('video_url').value;
                if (!url) {
                    alert("Masukkan link YouTube terlebih dahulu!");
                    return;
                }
                
                // Kirim ke route yang sama atau route khusus update link
                axios.post("{{ route('video.update') }}", { // Pastikan route ini sesuai di Controller Anda
                    type: 'youtube',
                    video_url: url,
                    _token: "{{ csrf_token() }}"
                })
                .then(res => {
                    alert("Link YouTube berhasil disimpan!");
                    location.reload();
                })
                .catch(err => {
                    alert("Gagal menyimpan link.");
                });

            } else {
                // Logika Upload File Lokal
                if (r.files.length === 0) {
                    alert("Pilih file video terlebih dahulu!");
                    return;
                }
                r.upload();
            }
        });

        // Progress Bar & Success File Lokal
        r.on('fileProgress', function(file) {
            let percent = Math.floor(file.progress() * 100);
            let pb = document.getElementById('progressBar');
            pb.style.width = percent + "%";
            pb.innerText = percent + "%";
        });

        r.on('fileSuccess', function(file) {
            alert("Upload file lokal selesai!");
            location.reload();
        });

        // Preview Video Lokal
        document.getElementById('video_file').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            // Nama file otomatis muncul di input karena kita menggunakan class "form-control" pada input type file
            const preview = document.getElementById('previewVideo');
            const fileInfo = document.getElementById('fileInfo');
            
            preview.src = URL.createObjectURL(file);
            preview.classList.remove('d-none');
            fileInfo.innerHTML = `File terpilih: <b>${file.name}</b> (${(file.size / (1024*1024)).toFixed(2)} MB)`;
        });
    </script>

</body>
</html>