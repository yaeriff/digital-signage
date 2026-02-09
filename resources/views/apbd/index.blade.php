<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Data APBD - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container mt-5 mb-5">
        <a href="{{ route('dashboard') }}" class="btn btn-danger">Kembali</a>
        
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Import Data APBD Baru</h5>
                
                <form action="{{ route('apbd.destroy') }}" method="POST" onsubmit="return confirm('PERINGATAN: Apakah Anda yakin ingin MENGHAPUS SEMUA data APBD? Data yang sudah dihapus tidak bisa dikembalikan.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm fw-bold shadow-sm border-white">
                        X Kosongkan Data
                    </button>
                </form>
            </div>
            
            <div class="card-body">
                <p class="text-muted small">
                    Upload file Excel (.xlsx) untuk memperbarui data. <br>
                    <span class="text-danger fw-bold">Peringatan:</span> Data lama akan otomatis dihapus dan diganti dengan data dari file ini.
                </p>

                <form action="{{ route('apbd.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row align-items-start">
                        <div class="col-md-5">
                            <label class="form-label fw-bold small text-uppercase text-secondary">Update Label Tanggal</label>
                            <input type="text" name="apbd_date" class="form-control" 
                                value="{{ $currentDate ?? '' }}" 
                                placeholder="Contoh: 8 Desember 2026" required>
                            <div class="form-text small text-muted fst-italic">
                                *Label ini muncul di bawah judul "REALISASI APBD"
                            </div>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label fw-bold small text-uppercase text-secondary">File Excel APBD</label>
                            <div class="input-group">
                                <input type="file" name="file" class="form-control" required accept=".xlsx, .xls">
                                <button type="submit" class="btn btn-primary fw-bold px-4">
                                    Upload & Update
                                </button>
                            </div>
                            <div class="form-text small text-muted fst-italic">
                                *Pastikan format file .xlsx dan header tabel sesuai format
                            </div>
                        </div>
                    </div>
                </form>

                @if(session('success'))
                    <div class="alert alert-success mt-3 mb-0">
                        {{ session('success') }}
                    </div>
                @endif
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Preview Data Saat Ini</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-0">

                        <thead class="table-dark text-center small">
                            <tr>
                                <th>No</th>
                                <th>Perangkat Daerah</th>
                                <th>Anggaran Pendapatan (Rp)</th>
                                <th style="width: 10%;">Real (%)</th> <th>Anggaran Belanja (Rp)</th>
                                <th style="width: 10%;">Real (%)</th> </tr>
                        </thead>

                        <tbody>
                            @forelse($apbds as $data)
                            <tr class="small">
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $data->perangkat_daerah }}</td>
                                
                                <td class="text-end">Rp {{ number_format($data->anggaran_pendapatan, 0, ',', '.') }}</td>
                                
                                <td class="text-center fw-bold text-success">{{ $data->realisasi_pendapatan }}%</td>
                                
                                <td class="text-end">Rp {{ number_format($data->anggaran_belanja, 0, ',', '.') }}</td>
                                
                                <td class="text-center fw-bold text-success">{{ $data->realisasi_belanja }}%</td>
                            </tr>
                            @empty
                            @endforelse
                        </tbody>

                    </table>
                </div>
            </div>
        </div>

    </div>

</body>
</html>