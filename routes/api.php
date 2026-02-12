<?php

use App\Http\Controllers\ApiController;

Route::get('/display-content', [ApiController::class, 'getDisplayData']);

Route::post('/upload-chunk', [VideoController::class, 'uploadChunk'])
    ->name('upload.chunk');
