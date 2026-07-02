<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\LinkQrCodeController;
use App\Http\Controllers\RedirectController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/', [HomeController::class, 'store'])
    ->middleware('throttle:home-store')
    ->name('home.store');

Route::middleware('auth')->get('/links/{link}/qr', LinkQrCodeController::class)
    ->name('links.qr');

Route::get('/{code}', RedirectController::class)
    ->middleware('throttle:redirect')
    ->where('code', '[A-Za-z0-9]+')
    ->name('short-link.redirect');
