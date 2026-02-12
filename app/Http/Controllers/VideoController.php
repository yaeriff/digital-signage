<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    public function uploadChunk(Request $request)
    {
        $receiver = new FileReceiver(
            "video_file",
            $request,
            HandlerFactory::classFromRequest($request)
        );

        if (!$receiver->isUploaded()) {
            return response()->json(['error' => 'Upload gagal'], 400);
        }

        $save = $receiver->receive();

        if ($save->isFinished()) {

            $file = $save->getFile();

            // 🔥 Buat nama unik (anti tabrakan)
            $fileName = uniqid() . '.' . $file->getClientOriginalExtension();

            // 🔥 Simpan manual supaya lebih stabil
            Storage::disk('public')->putFileAs(
                'videos',
                $file,
                $fileName
            );

            // Hapus file temporary chunk
            unlink($file->getPathname());

            // Simpan ke database
            $path = 'videos/' . $fileName;

            Video::create([
                'type' => 'local',
                'url'  => $path
            ]);

            return response()->json([
                'success' => true,
                'path' => $path
            ]);
        }

        // Kalau belum selesai
        $handler = $save->handler();

        return response()->json([
            "done" => $handler->getPercentageDone(),
        ]);
    }

}