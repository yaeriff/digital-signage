<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Apbd;
use App\Imports\ApbdImport;
use Maatwebsite\Excel\Facades\Excel; // Library Excel
use App\Models\Setting;

class ApbdController extends Controller
{
    // Tampilkan Halaman Daftar APBD
    public function index()
    {
        $apbds = Apbd::all();
        $currentDate = Setting::where('key', 'apbd_date')->first()->value ?? '';
        return view('apbd.index', compact('apbds', 'currentDate'));
    }


    // Proses Import Excel
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
            'apbd_date' => 'required',
        ]);

        // KOSONGKAN DATA LAMA
        Apbd::truncate(); 

        Setting::updateOrCreate(
            ['key' => 'apbd_date'],
            ['value' => $request->apbd_date]
        );

        // IMPORT DATA DARI EXCEL
        $file = $request->file('file');
        \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\ApbdImport, $file);

        // AUTO CLEANUP
        // Hapus Baris yang Namanya Kosong / Aneh
        Apbd::whereNull('perangkat_daerah')->delete();
        Apbd::where('perangkat_daerah', '')->delete();
        Apbd::whereRaw('LENGTH(perangkat_daerah) < 3')->delete(); // Hapus "-", "No", dll

        $blacklist = [
            'SURPLUS', 
            'DEFISIT', 
            'PEMBIAYAAN', 
            'PENERIMAAN', 
            'PENGELUARAN', 
            'NETTO', 
            'SISA', 
            'JUMLAH', 
            'TOTAL',
            'SILPA' // Sisa Lebih Pembiayaan Anggaran
        ];

        foreach ($blacklist as $kata) {
            // Hapus data jika ada nama mengandung kata tersebut (Case Insensitive)
            Apbd::where('perangkat_daerah', 'LIKE', '%' . $kata . '%')->delete();
        }

        // Hapus Baris yang Anggaran Pendapatan DAN Belanjanya 0 (Baris Kosong Total)
        Apbd::where('anggaran_pendapatan', 0)
            ->where('anggaran_belanja', 0)
            ->delete();

        return redirect()->back()->with('success', 'Data Berhasil Diperbarui!');
    }

    // Hapus Semua Data
    public function destroy()
    {
        Apbd::truncate();
        return redirect()->back()->with('success', 'Semua Data APBD Berhasil Dihapus!');
    }
}