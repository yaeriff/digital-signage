<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\ApbdController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\VideoController;
use App\Models\Video;
use App\Models\Setting;

// ====================================================
//  1. HALAMAN TV / DISPLAY (Tampilan Utama untuk Layar TV)
// ====================================================
Route::get('/', function () {
    // A. Ambil Video Aktif
    $video = null;
    if (Schema::hasTable('videos')) {
        $video = Video::where('is_active', true)->latest()->first() ?? Video::latest()->first();
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

    return view('welcome', compact('video', 'ticker', 'apbdDate'));
});


// ====================================================
//  2. HALAMAN DASHBOARD ADMIN (TANPA LOGIN / GRATIS MASUK)
// ====================================================

// Perhatikan: Tidak ada lagi middleware(['auth']) disini.

Route::get('/dashboard', function () {
    return view('dashboard'); 
})->name('dashboard');

// Route Resource Agenda
Route::resource('agendas', AgendaController::class);

// Route APBD
Route::get('/apbd', [ApbdController::class, 'index'])->name('apbd.index');
Route::post('/apbd/import', [ApbdController::class, 'import'])->name('apbd.import');
Route::delete('/apbd/hapus', [ApbdController::class, 'destroy'])->name('apbd.destroy');

// Route Settings
Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

// Route Video
Route::get('/video', [VideoController::class, 'index'])->name('video.index');
Route::post('/video', [VideoController::class, 'update'])->name('video.update');