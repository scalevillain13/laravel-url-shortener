<?php

use App\Http\Controllers\Api\LinkController;
use App\Http\Controllers\Api\TokenController;
use Illuminate\Support\Facades\Route;

Route::post('/tokens', [TokenController::class, 'store'])->middleware('throttle:api-tokens');

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('/links', [LinkController::class, 'index']);
    Route::post('/links', [LinkController::class, 'store']);
    Route::get('/links/{link}', [LinkController::class, 'show']);
    Route::patch('/links/{link}', [LinkController::class, 'update']);
    Route::delete('/links/{link}', [LinkController::class, 'destroy']);
});
