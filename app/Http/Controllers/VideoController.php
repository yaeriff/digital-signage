<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    public function index()
    {
        // Ambil video terakhir yang diupload/diset
        $video = Video::latest()->first();
        return view('videos.index', compact('video'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'type' => 'required|in:local,youtube',
            // Validasi kondisional: Kalau type=local wajib ada file, kalau youtube wajib url
            'video_file' => 'required_if:type,local|mimes:mp4,mov,avi|max:2097152', // Max 2GB
            'video_url'  => 'required_if:type,youtube|nullable|url',
        ]);

        // Hapus data lama (Opsional: jika ingin hanya menyimpan 1 record di DB)
        // Video::truncate(); 

        if ($request->type == 'local') {
            // Hapus file lama jika ada (opsional)
            // Storage::disk('public')->delete($oldFile);

            // Proses Upload File
            $path = $request->file('video_file')->store('videos', 'public');

            Video::create([
                'type' => 'local',
                'url'  => $path
            ]);

        } else {
            // Simpan URL YouTube
            Video::create([
                'type' => 'youtube',
                'url'  => $request->video_url
            ]);
        }

        return redirect()->back()->with('success', 'Video berhasil diperbarui!');
    }
}