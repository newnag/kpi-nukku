<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\IndicatorController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('indicator')->name('indicator.')->group(function () {
    Route::get('/dashboard', [IndicatorController::class, 'index'])->name('dashboard');
    Route::get('/create', [IndicatorController::class, 'create'])->name('create');

    // route actions for indicators
    Route::post('/', [IndicatorController::class, 'store'])->name('store');
    Route::get('/{id}', [IndicatorController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [IndicatorController::class, 'edit'])->name('edit');
    Route::put('/{id}', [IndicatorController::class, 'update'])->name('update');
    Route::delete('/{id}', [IndicatorController::class, 'destroy'])->name('destroy');
});

Route::prefix('test')->group(function () {
    Route::get('/', [IndicatorController::class, 'index'])->name('dashboard');
    Route::get('/create', [IndicatorController::class, 'create'])->name('create');

    // route actions for indicators
    Route::post('/', [IndicatorController::class, 'store'])->name('store');
    Route::get('/{id}', [IndicatorController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [IndicatorController::class, 'edit'])->name('edit');
    Route::put('/{id}', [IndicatorController::class, 'update'])->name('update');
    Route::delete('/{id}', [IndicatorController::class, 'destroy'])->name('destroy');
});


Route::post('post', function (Request $request) {
    return response()->json(['message' => 'Test created successfully']);
});
