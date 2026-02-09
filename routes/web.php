<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\ApbdController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\VideoController;
use App\Models\Video;
use App\Models\Setting;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('agendas', AgendaController::class);

// Group route untuk APBD
Route::get('/apbd', [ApbdController::class, 'index'])->name('apbd.index');
Route::post('/apbd/import', [ApbdController::class, 'import'])->name('apbd.import');
// Route untuk hapus data
Route::delete('/apbd/hapus', [ApbdController::class, 'destroy'])->name('apbd.destroy');

Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

Route::get('/video', [VideoController::class, 'index'])->name('video.index');
Route::post('/video', [VideoController::class, 'update'])->name('video.update');

Route::get('/', function () {
    // Ambil data-data yang diperlukan
    $video = Video::latest()->first(); 

    return view('welcome', compact('video'));
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');


Route::get('/', function () {
    // 1. Ambil Video Aktif
    $video = Video::latest()->first();
    
    if (!$video) {
    $video = null;
    }

    // 2. Ambil Running Text (Ticker)
    $ticker = Setting::where('key', 'ticker_text')->value('value') ?? 'Selamat Datang...';

    // 3. Ambil Tanggal APBD
    $apbdDate = Setting::where('key', 'apbd_date')->value('value') ?? 'DATA TERBARU';

    // Kirim semua ke view 'welcome'
    return view('welcome', compact('video', 'ticker', 'apbdDate'));
});