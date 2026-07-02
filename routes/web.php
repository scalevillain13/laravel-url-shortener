<?php

use App\Http\Controllers\RedirectController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/{code}', RedirectController::class)
    ->where('code', '[A-Za-z0-9]+')
    ->name('short-link.redirect');
