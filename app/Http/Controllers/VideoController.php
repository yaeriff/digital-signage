<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    public function index()
    {
        $video = Video::latest()->first();
        return view('videos.index', compact('video'));
    }

    public function uploadChunk(Request $request)
    {
        $receiver = new FileReceiver(
            "video_file",
            $request,
            HandlerFactory::classFromRequest($request)
        );

        if (!$receiver->isUploaded()) {
            return response()->json([
                'error' => 'File tidak terdeteksi',
                'all_request' => $request->all(),
                'files' => $request->files->all()
            ], 400);
        }


        $save = $receiver->receive();

        if ($save->isFinished()) {
            $file = $save->getFile();

            $path = $file->store('videos', 'public');

            Video::create([
                'type' => 'local',
                'url'  => $path
            ]);

            return response()->json([
                'success' => true,
                'path' => $path
            ]);
        }

        $handler = $save->handler();

        return response()->json([
            "done" => $handler->getPercentageDone(),
        ]);
    }
}
