<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Kelola Agenda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container mt-5">
        <a href="{{ route('dashboard') }}" class="btn btn-danger">Kembali</a>
        <div class="card shadow-sm">
            
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Agenda Harian</h5>
                <a href="{{ route('agendas.create') }}" class="btn btn-light btn-sm fw-bold">+ Tambah Agenda</a>
                
                
            </div>
            <div class="card-body">
                
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th width="15%" class="text-center">Waktu</th>
                                <th>Nama Agenda</th>
                                <th>Lokasi</th>
                                <th width="15%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($agendas as $agenda)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="text-center">
                                    {{ date('H:i', strtotime($agenda->start_time)) }} - 
                                    {{ date('H:i', strtotime($agenda->end_time)) }}
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $agenda->title }}</div>
                                    <small class="text-muted">{{ $agenda->description }}</small>
                                </td>
                                <td>{{ $agenda->location }}</td>
                                <td class="text-center">
                                    <form onsubmit="return confirm('Apakah Anda Yakin ?');" action="{{ route('agendas.destroy', $agenda->id) }}" method="POST">
                                        <a href="{{ route('agendas.edit', $agenda->id) }}" class="btn btn-sm btn-warning text-white">Edit</a>
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <em>Belum ada data agenda. Silakan tambah data.</em>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>