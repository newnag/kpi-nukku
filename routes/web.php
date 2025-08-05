<?php

use App\Http\Controllers\DepartmentController;
use Faker\Guesser\Name;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('/layouts/app');
// });

Route::prefix('departments')->name('departments.')->group(function () {
    Route::get('/', [DepartmentController::class, 'index'])->name('index');
    Route::post('/store', [DepartmentController::class, 'store'])->name('store');
    Route::put('/{id}',[DepartmentController::class,'update'])->name('updete');
    // Add other routes for departments here
});
