<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kelola Video - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="{{ asset('signage/assets/js/axios.js') }}"></script>
    <script src="{{ asset('signage/assets/js/resumable.js') }}"></script>

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
        // 1. Fungsi toggle tetap di luar agar bisa dipanggil atribut onclick HTML
        function toggleInput() {
            const isYoutube = document.getElementById('typeYoutube').checked;
            const containerLocal = document.getElementById('containerLocal');
            const containerYoutube = document.getElementById('containerYoutube');
            
            if (isYoutube) {
                containerLocal.classList.add('d-none');
                containerYoutube.classList.remove('d-none');
            } else {
                containerLocal.classList.remove('d-none');
                containerYoutube.classList.add('d-none');
            }
        }

        // 2. Inisialisasi variabel r di scope utama script
        let r;

        document.addEventListener("DOMContentLoaded", function() {
            // Inisialisasi Resumable jika library berhasil dimuat
            if (typeof Resumable !== 'undefined') {
                r = new Resumable({
                    target: "{{ route('upload.chunk') }}",
                    headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                    fileParameterName: 'video_file',
                    chunkSize: 5 * 1024 * 1024,
                    testChunks: false
                });

                r.assignBrowse(document.getElementById('video_file'));

                // Tambahkan event handling dasar untuk progress
                r.on('fileProgress', function(file) {
                    let percent = Math.floor(file.progress() * 100);
                    let pb = document.getElementById('progressBar');
                    pb.style.width = percent + "%";
                    pb.innerText = percent + "%";
                });

                r.on('fileSuccess', function(file, response) {
                    alert("Upload file berhasil!");
                    location.reload();
                });

                r.on('fileError', function(file, message) {
                    alert("Upload gagal: " + message);
                });
            }

            // 3. Logika Tombol Simpan
            document.getElementById('startUpload').addEventListener('click', function() {
                const isYoutube = document.getElementById('typeYoutube').checked;

                if (isYoutube) {
                    const url = document.getElementById('video_url').value;
                    if (!url) return alert("Masukkan link YouTube dulu!");

                    // Gunakan Axios untuk simpan link (Pastikan route video.update sudah ada di web.php)
                    axios.post("{{ route('video.update') }}", {
                        type: 'youtube',
                        video_url: url
                    })
                    .then(response => {
                        alert("Link YouTube berhasil disimpan!");
                        location.reload();
                    })
                    .catch(error => {
                        alert("Gagal menyimpan link. Cek controller atau route.");
                    });

                } else {
                    // Pastikan r sudah terdefinisi dan ada file yang dipilih
                    if (r && r.files.length > 0) {
                        r.upload();
                    } else {
                        alert("Sistem belum siap atau file belum dipilih!");
                    }
                }
            });
        });
    </script>

</body>
</html>