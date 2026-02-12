<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\ApbdController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\VideoController;
use App\Models\Video;
use App\Models\Setting;
use App\Models\Agenda; // <--- Wajib ada
use App\Models\Apbd;   // <--- Wajib ada

// ====================================================
//  1. HALAMAN TV / DISPLAY (UTAMA)
// ====================================================
Route::get('/', function () {
    // A. Ambil Video
    $video = null;
    if (Schema::hasTable('videos')) {
        $video = Video::latest()->first();
    }

    // B. Ambil Running Text
    $ticker = 'Selamat Datang di Dinas Kami...';
    if (Schema::hasTable('settings')) {
        $ticker = Setting::where('key', 'ticker_text')->value('value') ?? $ticker;
    }

    // C. Ambil Tanggal APBD
    $apbdDate = 'DATA TERBARU';
    if (Schema::hasTable('settings')) {
        $apbdDate = Setting::where('key', 'apbd_date')->value('value') ?? $apbdDate;
    }

    // D. AMBIL DATA AGENDA (Ini yang tadi ketinggalan)
    $agendas = [];
    if (Schema::hasTable('agendas')) {
        $agendas = Agenda::latest()->get(); 
    }

    // E. AMBIL DATA APBD
    $apbds = [];
    if (Schema::hasTable('apbds')) {
        $apbds = Apbd::all();
    }

    // Kirim SEMUA variable ke view (Video, Ticker, Tanggal, Agenda, APBD)
    return view('welcome', compact('video', 'ticker', 'apbdDate', 'agendas', 'apbds'));
});

// ====================================================
//  2. STORAGE LINK (Opsional)
// ====================================================
Route::get('/fix-storage', function () {
    Artisan::call('storage:link');
    return 'Storage Link Berhasil Dibuat!';
});

// ====================================================
//  3. DASHBOARD ADMIN
// ====================================================

Route::get('/dashboard', function () {
    return view('dashboard'); 
})->name('dashboard');

// Route Resource
Route::resource('agendas', AgendaController::class);

Route::get('/apbd', [ApbdController::class, 'index'])->name('apbd.index');
Route::post('/apbd/import', [ApbdController::class, 'import'])->name('apbd.import');
Route::delete('/apbd/hapus', [ApbdController::class, 'destroy'])->name('apbd.destroy');

Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

Route::get('/video', [VideoController::class, 'index'])->name('video.index');
// Route::post('/video', [VideoController::class, 'update'])->name('video.update');

Route::post('/upload-chunk', [VideoController::class, 'uploadChunk'])
    ->name('upload.chunk');
Route::post('/video/update', [VideoController::class, 'update'])->name('video.update');