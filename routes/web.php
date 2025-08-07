<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/indicator/dashboard', [\App\Http\Controllers\IndicatorController::class, 'index'])->name('indicator.dashboard');
