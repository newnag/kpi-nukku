<?php

use App\Http\Controllers\DepartmentController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('/layouts/app');
// });

Route::prefix('departments')->name('departments.')->group(function () {
    Route::get('/', [DepartmentController::class, 'index'])->name('index');
    Route::post('/store', [DepartmentController::class, 'store'])->name('store');
    // Add other routes for departments here
});
