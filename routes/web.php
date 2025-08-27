<?php

use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EvidenceController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StandardController;
use App\Http\Controllers\UserController;
use App\Models\Category;
use App\Models\Department;
use App\Models\Standard;
use Faker\Guesser\Name;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('/layouts/app');
// });

Route::prefix('departments')->name('departments.')->group(function () {
    Route::get('/', [DepartmentController::class, 'index'])->name('index');
    Route::post('/store', [DepartmentController::class, 'store'])->name('store');
    Route::put('/{id}', [DepartmentController::class, 'update'])->name('update');
    Route::delete('/{id}', [DepartmentController::class, 'destroy'])->name('destroy');
    // Add other routes for departments here
});

Route::prefix('categories')->name('categories.')->group(function () {
    Route::get('/', [CategorieController::class, 'index'])->name('index');
    Route::post('/store', [CategorieController::class, 'store'])->name('store');
    Route::put('/{id}', [CategorieController::class, 'update'])->name('update');
    Route::delete('/{id}', [CategorieController::class, 'destroy'])->name('destroy');
});

Route::prefix('standards')->name('standards.')->group(function () {
    Route::get('/', [StandardController::class, 'index'])->name('index');
    Route::post('/store', [StandardController::class, 'store'])->name('store');
    Route::put('/{id}', [StandardController::class, 'update'])->name('update');
    Route::delete('/{id}', [StandardController::class, 'destroy'])->name('destroy');
});

Route::prefix('settings')->name('settings.')->group(function () {
    Route::get('/', [SettingController::class, 'index'])->name('index');
    Route::post('/store', [SettingController::class, 'store'])->name('store');
    Route::put('/{id}', [SettingController::class, 'update'])->name('update');
   
});

Route::prefix('users')->name('users.')->group(function () {
    Route::get('/',        [UserController::class, 'index'])->name('index');
    Route::get('/create',  [UserController::class, 'create'])->name('create');
    Route::get('/{id}/edit', [UserController::class, 'edit'])->name('edit');   // ใช้ {id}
    Route::post('/',       [UserController::class, 'store'])->name('store');
    Route::put('/{id}',    [UserController::class, 'update'])->name('update');
    Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
});
Route::prefix('evidences')->name('evidences.')->group(function () {
    Route::get('/', [EvidenceController::class, 'index'])->name('index');
     Route::get('/create',  [EvidenceController::class, 'create'])->name('create');
    Route::post('/store', [EvidenceController::class, 'store'])->name('store');
    Route::put('/{id}', [EvidenceController::class, 'update'])->name('update');
    Route::delete('/{id}', [EvidenceController::class, 'destroy'])->name('destroy');
    Route::get('/{id}/download', [EvidenceController::class, 'download']);
    Route::get('criteria/{criteriaId}/evidences', [EvidenceController::class, 'getByCriteria']);
    Route::patch('/{id}/toggle-status', [EvidenceController::class, 'toggleStatus']);
});

Route::prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::get('/result', [DashboardController::class, 'getData'])->name('getData');

});


Route::get('/indicator/dashboard', [\App\Http\Controllers\IndicatorController::class, 'index'])->name('indicator.dashboard');
