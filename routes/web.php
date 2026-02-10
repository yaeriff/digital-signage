<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\ApbdController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\VideoController;
use App\Models\Video;
use App\Models\Setting;

// ====================================================
//  LOGIN DARURAT (EMERGENCY LOGIN) - SINGLE FILE
// ====================================================

// 1. Halaman Form Login (Langsung HTML di sini biar praktis)
Route::get('/login', function () {
    // Kalau sudah login, langsung lempar ke Dashboard/Home
    if (Auth::check()) {
        return redirect('/'); 
    }

    // Tampilan Form Login Sederhana
    return '
    <!DOCTYPE html>
    <html>
    <head>
        <title>Login Darurat</title>
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


// 2. Proses Logika Login (Menerima data dari form di atas)
Route::post('/login', function (Request $request) {
    // Coba login dengan data yang dikirim
    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        // Jika Berhasil:
        $request->session()->regenerate();
        return redirect()->intended('/'); // Masuk ke halaman utama/dashboard
    }

    // Jika Gagal:
    return back()->with('error', 'Email atau Password Salah!');
});


// 3. Tombol Logout (Biar bisa keluar)
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout');
//                                  

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
    $video = null;
    if (Schema::hasTable('videos')) {
        $video = Video::latest()->first();
    }

    // 2. Ambil Running Text (Ticker)
    $ticker = 'Selamat Datang...';
    if (Schema::hasTable('settings')) {
        $ticker = Setting::where('key', 'ticker_text')->value('value') ?? $ticker;
    }

    // 3. Ambil Tanggal APBD
    $apbdDate = 'DATA TERBARU';
    if (Schema::hasTable('settings')) {
        $apbdDate = Setting::where('key', 'apbd_date')->value('value') ?? $apbdDate;
    }

    return view('welcome', compact('video', 'ticker', 'apbdDate'));
});