<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema; // Tambahan penting
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\ApbdController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\VideoController;
use App\Models\Video;
use App\Models\Setting;

// ====================================================
//  1. LOGIN & LOGOUT (DARURAT)
// ====================================================

Route::get('/login', function () {
    if (Auth::check()) {
        return redirect('/dashboard'); // Kalau sudah login, lempar ke Dashboard
    }

    return '
    <!DOCTYPE html>
    <html>
    <head>
        <title>Login Admin</title>
        <style>
            body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background: #f0f2f5; }
            .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 300px; }
            input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
            button { width: 100%; padding: 10px; background: #FF2D20; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
            button:hover { background: #e0281c; }
            .error { color: red; font-size: 14px; margin-bottom: 10px; text-align: center; }
        </style>
    </head>
    <body>
        <div class="card">
            <h2 style="text-align:center; margin-top:0;">Login Admin</h2>
            '. (session('error') ? '<div class="error">'. session('error') .'</div>' : '') .'
            <form method="POST" action="/login">
                <input type="hidden" name="_token" value="'. csrf_token() .'">
                <label>Email</label>
                <input type="email" name="email" required placeholder="admin@gmail.com">
                <label>Password</label>
                <input type="password" name="password" required placeholder="password123">
                <button type="submit">MASUK</button>
            </form>
        </div>
    </body>
    </html>
    ';
})->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended('/dashboard'); // <--- INI PERBAIKANNYA (Ke Dashboard)
    }

    return back()->with('error', 'Email atau Password Salah!');
});

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout');


// ====================================================
//  2. HALAMAN TV / DISPLAY (Halaman Depan)
// ====================================================
Route::get('/', function () {
    // Logika Mengambil Data untuk TV
    
    // A. Ambil Video
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

    // Pastikan Anda punya file resources/views/welcome.blade.php yang isinya Kodingan TV
    return view('welcome', compact('video', 'ticker', 'apbdDate'));
});


// ====================================================
//  3. HALAMAN DASHBOARD ADMIN (Harus Login)
// ====================================================

// Kita bungkus dalam grup "auth" supaya tidak bisa ditembak langsung tanpa login
Route::middleware(['auth'])->group(function () {
    
    Route::get('/dashboard', function () {
        // Pastikan Anda punya file resources/views/dashboard.blade.php
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
});