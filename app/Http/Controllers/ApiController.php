<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;
use App\Models\Setting;
use App\Models\Agenda;
use App\Models\Apbd;

class ApiController extends Controller
{
    public function getDisplayData()
    {
        $video = Video::latest()->first();
        if ($video && $video->type == 'local') {
            $video->url = asset('storage/' . $video->url);
        }
        $ticker = Setting::where('key', 'ticker_text')->value('value') ?? '-';
        $apbdDate = Setting::where('key', 'apbd_date')->value('value') ?? '-';
        $agendas = Agenda::orderBy('start_time', 'asc')->get();

        // AMBIL DATA APBD (Hanya Dinas, tanpa totalan/provinsi bawaan excel)
        $apbds = Apbd::where('perangkat_daerah', '!=', '')
            ->where(function ($q) {
                $q->where('anggaran_pendapatan', '>', 0)
                ->orWhere('anggaran_belanja', '>', 0);
            })
            ->get();


        // HITUNG TOTAL (SESUAI RUMUS EXCEL =H7/G7)
        // PENDAPATAN
        // Total Anggaran Pendapatan
        $totalAnggaranPend = $apbds->sum('anggaran_pendapatan');
        $totalRealisasiRpPend = $apbds->sum('realisasi_pendapatan_rp'); 

        // Hitung Persen Global
        $persenGlobalPend = $totalAnggaranPend > 0 
            ? round(($totalRealisasiRpPend / $totalAnggaranPend) * 100, 2) 
            : 0;


        // BELANJA
        $totalAnggaranBel = $apbds->sum('anggaran_belanja');
        $totalRealisasiRpBel = $apbds->sum('realisasi_belanja_rp');

        // Hitung Persen Global
        $persenGlobalBel = $totalAnggaranBel > 0 
            ? round(($totalRealisasiRpBel / $totalAnggaranBel) * 100, 2) 
            : 0;


        // KIRIM DATA KE FRONTEND
        return response()->json([
            'video' => $video,
            'settings' => [
                'ticker' => $ticker,
                'apbd_date' => $apbdDate
            ],
            'agendas' => $agendas,
            'apbd' => [
                'data' => $apbds,
                
                // Data Total untuk Baris Atas & Gauge Chart
                'total' => [
                    // Pendapatan
                    'anggaran_pendapatan' => $totalAnggaranPend,
                    'realisasi_pendapatan_persen' => $persenGlobalPend, // Hasil Rumus Baru
                    
                    // Belanja
                    'anggaran_belanja' => $totalAnggaranBel,
                    'realisasi_belanja_persen' => $persenGlobalBel,     // Hasil Rumus Baru
                    
                    // kirim nominal Rp Realisasi kalau butuh
                    'realisasi_pendapatan_rp' => $totalRealisasiRpPend,
                    'realisasi_belanja_rp' => $totalRealisasiRpBel
                ]
            ]
        ]);
    }
}