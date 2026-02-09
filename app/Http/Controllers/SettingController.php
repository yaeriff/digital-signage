<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        // Ambil data dari database untuk ditampilkan di form
        $ticker = Setting::where('key', 'ticker_text')->first()->value ?? '';
        return view('settings.index', compact('ticker'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'ticker_text' => 'required',
        ]);

        Setting::updateOrCreate(
            ['key' => 'ticker_text'],
            ['value' => $request->ticker_text]
        );

        return redirect()->back()->with('success', 'Pengaturan berhasil disimpan!');
    }
}