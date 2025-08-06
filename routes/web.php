<?php

use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\CategorieController;
use App\Models\Category;
use App\Models\Department;
use Faker\Guesser\Name;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('/layouts/app');
// });

Route::prefix('departments')->name('departments.')->group(function () {
    Route::get('/', [DepartmentController::class, 'index'])->name('index');
    Route::post('/store', [DepartmentController::class, 'store'])->name('store');
    Route::put('/{id}',[DepartmentController::class,'update'])->name('update');
    Route::delete('/{id}',[DepartmentController::class,'destroy'])->name('destroy');
    // Add other routes for departments here
});

Route::prefix('categories')->name('categories.')->group(function(){
    Route::get('/',[CategorieController::class,'index'])->name('index');
    Route::post('/store', [CategorieController::class, 'store'])->name('store');
    Route::put('/{id}',[CategorieController::class,'update'])->name('update');
    Route::delete('/{id}',[CategorieController::class,'destroy'])->name('destroy');
});
