<?php

use App\Http\Api\V1\Controllers\ApiController;
use App\Http\Api\V1\Controllers\EventController;
use App\Http\Api\V1\Controllers\HostController;
use App\Http\Api\V1\Controllers\PingController;
use Illuminate\Support\Facades\Route;

Route::get('/', ApiController::class);

Route::prefix('v1')->middleware('auth:api')->name('v1.')->group(function () {
    Route::get('ping', PingController::class);
    Route::post('ping', PingController::class);
    Route::apiResource('hosts', HostController::class);
    Route::get('events', EventController::class);

    Route::any('{catchall}', function () {
        return response()->json([
            'error' => 'Not allowed',
        ], 405);
    })->where('catchall', '.*')->fallback();
});
