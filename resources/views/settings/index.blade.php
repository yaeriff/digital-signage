<!DOCTYPE html>
<html lang="id">
<head>
    <title>Pengaturan Tampilan - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <a href="{{ route('dashboard') }}" class="btn btn-danger">Kembali</a>
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">Pengaturan Tampilan Layar</h5>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form action="{{ route('settings.update') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="fw-bold">Teks Running Text (Ticker)</label>
                        <textarea name="ticker_text" class="form-control" rows="3">{{ $ticker }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="{{ route('apbd.index') }}" class="btn btn-outline-secondary ms-2">Ke Menu APBD</a>
                    <a href="{{ route('agendas.index') }}" class="btn btn-outline-secondary ms-2">Ke Menu Agenda</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>