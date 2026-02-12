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

                    <div class="mb-3">
                        <label class="form-label">Upload Video (.mp4)</label>
                        <input type="file" 
                                id="video_file" 
                                name="video_file"
                                class="form-control">
                        <small class="text-muted">Maksimal ukuran file besar (Chunk Upload).</small>
                    </div>

                    <div class="mb-3 d-none" id="inputYoutube">
                        <label class="form-label">Masukkan Link YouTube</label>
                        <input type="text" name="video_url" class="form-control" placeholder="Contoh: https://www.youtube.com/watch?v=dQw4w9WgXcQ">
                    </div>

                    <div class="mb-4">
                        <div class="progress" style="height: 25px;">
                            <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                                role="progressbar" style="width: 0%">0%</div>
                        </div>
                    </div>

                    <button type="button" id="startUpload" class="btn btn-danger">
                        Upload Video
                    </button>

                    <div class="mt-2 small text-muted" id="fileInfo"></div>

                    <div class="mt-3">
                        <video id="previewVideo" class="w-50 d-none" controls></video>
                    </div>

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

    
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/resumable.js/1.1.0/resumable.min.js"></script>

    <script>
        let r = new Resumable({
        target: "{{ route('upload.chunk') }}",
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        fileType: ['mp4','mov','avi'],
        chunkSize: 5 * 1024 * 1024,
        simultaneousUploads: 1,
        testChunks: false
    });

    let progressBar = document.getElementById('progressBar');

    r.assignBrowse(document.getElementById('video_file'));

    document.getElementById('startUpload').addEventListener('click', function(){
        if (r.files.length === 0) {
            alert("Pilih file dulu!");
            return;
        }
        r.upload();
    });

    r.on('fileProgress', function(file){
        let percent = Math.floor(file.progress() * 100);
        progressBar.style.width = percent + "%";
        progressBar.innerText = percent + "%";
    });

    r.on('fileSuccess', function(file, response){
        alert("Upload selesai!");
        location.reload();
    });

    r.on('fileError', function(file, message){
        console.error(message);
        alert("Upload gagal!");
    });

    let fileInput = document.getElementById('video_file');
    let preview = document.getElementById('previewVideo');
    let fileInfo = document.getElementById('fileInfo');

    fileInput.addEventListener('change', function(e){
        const file = e.target.files[0];
        if (!file) return;

        const url = URL.createObjectURL(file);
        preview.src = url;
        preview.classList.remove('d-none');

        fileInfo.innerHTML = `
            Nama File: <b>${file.name}</b><br>
            Ukuran: ${(file.size / (1024*1024)).toFixed(2)} MB<br>
            Tipe: ${file.type}
        `;
    });


    </script>

</body>
</html>