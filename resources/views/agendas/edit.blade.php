<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Agenda - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0 fw-bold">Edit Agenda</h5>
                    </div>
                    <div class="card-body">
                        
                        <form action="{{ route('agendas.update', $agenda->id) }}" method="POST">
                            @csrf
                            @method('PUT') <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Jam Mulai</label>
                                    <input type="time" name="start_time" class="form-control" value="{{ $agenda->start_time }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Jam Selesai</label>
                                    <input type="time" name="end_time" class="form-control" value="{{ $agenda->end_time }}" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Nama Kegiatan / Agenda</label>
                                <input type="text" name="title" class="form-control" value="{{ $agenda->title }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Lokasi</label>
                                <input type="text" name="location" class="form-control" value="{{ $agenda->location }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Deskripsi Singkat (Opsional)</label>
                                <textarea name="description" class="form-control" rows="3">{{ $agenda->description }}</textarea>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('agendas.index') }}" class="btn btn-secondary">Batal</a>
                                <button type="submit" class="btn btn-primary">Update Agenda</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>