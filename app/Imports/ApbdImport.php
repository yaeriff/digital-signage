<?php

namespace App\Imports;

use App\Models\Apbd;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class ApbdImport implements ToModel, WithStartRow, WithCalculatedFormulas
{
    private $jumlahData = 0;
    private $absensiSlug = [];

    public function startRow(): int
    {
        return 7;
    }

    public function model(array $row)
    {
        // 1. LIMIT 50 DATA
        if ($this->jumlahData >= 50) return null;

        // 2. AUTO DETECT KOLOM NAMA
        $idxNama = 2; 
        if (isset($row[2]) && is_numeric(str_replace('.', '', $row[2])) && strlen($row[2]) > 5) {
            $idxNama = 1;
        }

        $rawNama = $row[$idxNama] ?? '';
        $namaSKPD = trim(str_replace("\xc2\xa0", ' ', $rawNama));

        if (empty($namaSKPD) || strlen($namaSKPD) < 3 || is_numeric($namaSKPD)) return null;
        
        $blacklist = ['PROV', 'HALAMAN', 'DICETAK', 'TANGGAL', 'SUMBER', 'TOTAL', 'JUMLAH', 'SURPLUS', 'DEFISIT', 'PEMBIAYAAN', 'NETTO'];
        foreach ($blacklist as $kata) {
            if (str_contains(strtoupper($namaSKPD), $kata)) return null;
        }

        $slug = preg_replace('/[^a-z0-9]/', '', strtolower($namaSKPD));
        if (in_array($slug, $this->absensiSlug)) return null;
        $this->absensiSlug[] = $slug;
        $this->jumlahData++;

        // 3. MAPPING KOLOM (8 Kolom)
        $idxAnggaranPend   = $idxNama + 1;
        $idxRealRpPend     = $idxNama + 2; 
        $idxRealPersenPend = $idxNama + 3; 
        
        $idxAnggaranBel    = $idxNama + 4; 
        $idxRealRpBel      = $idxNama + 5; 
        $idxRealPersenBel  = $idxNama + 6; 

        $bersihkanUang = function($nilai) {
            if (!$nilai) return 0;
            
            // Jika datanya sudah berupa angka (integer/float), langsung ambil.
            // Jangan di-preg_replace krn akan menghilangkan koma desimalnya.
            if (is_numeric($nilai)) {
                return (float) $nilai;
            }
            
            // Hapus Rp, spasi, dan koma (thousand separator US)
            $clean = preg_replace('/[^0-9.]/', '', str_replace(',', '', $nilai));
            return (float) $clean;
        };

        $bersihkanPersen = function($nilai) {
            if (!$nilai) return 0;
            $val = (float) str_replace(['%', ','], ['', '.'], $nilai);
            if ($val <= 1 && $val != 0) $val = $val * 100;
            return round($val, 2);
        };

        // 4. AMBIL DATA
        $anggaranPend = $bersihkanUang($row[$idxAnggaranPend] ?? 0);
        $realRpPend   = $bersihkanUang($row[$idxRealRpPend] ?? 0); // Rupiah Asli (Desimal aman)
        
        $anggaranBel  = $bersihkanUang($row[$idxAnggaranBel] ?? 0);
        $realRpBel    = $bersihkanUang($row[$idxRealRpBel] ?? 0);  // Rupiah Asli (Desimal aman)
        
        // Ambil Persen
        $realPersenPend = ($anggaranPend == 0) ? 0 : $bersihkanPersen($row[$idxRealPersenPend] ?? 0);
        $realPersenBel  = ($anggaranBel == 0)  ? 0 : $bersihkanPersen($row[$idxRealPersenBel] ?? 0);

        return new Apbd([
            'perangkat_daerah'        => $namaSKPD,
            'anggaran_pendapatan'     => $anggaranPend,
            'realisasi_pendapatan_rp' => $realRpPend,     
            'realisasi_pendapatan'    => $realPersenPend, 
            
            'anggaran_belanja'        => $anggaranBel,
            'realisasi_belanja_rp'    => $realRpBel,      
            'realisasi_belanja'       => $realPersenBel,  
        ]);
    }
}