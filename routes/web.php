<?php

use App\Http\Controllers\PasienController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::resource('pasien', PasienController::class);

Route::middleware(['auth'])->group(function () {
    Route::resource('pasien', PasienController::class);
});
