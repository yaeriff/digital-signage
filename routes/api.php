<?php

use App\Http\Controllers\ApiController;

Route::get('/display-content', [ApiController::class, 'getDisplayData']);
