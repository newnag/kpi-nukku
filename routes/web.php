<?php

use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DashboardExportController;
use App\Http\Controllers\DashboardKpiUserController;
use App\Http\Controllers\EvidenceController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StandardController;
use App\Http\Controllers\UserController;
use App\Models\Category;
use App\Models\Department;
use App\Models\Standard;
use Faker\Guesser\Name;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\IndicatorController;

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

Route::prefix('indicator')->name('indicator.')->group(function () {
    Route::get('/', [IndicatorController::class, 'index'])->name('dashboard');

    // create
    Route::get('/create-page', [IndicatorController::class, 'create'])->name('create');
    Route::post('/store', [IndicatorController::class, 'store'])->name('store');

    // read
    Route::get('/{id}/detail', [IndicatorController::class, 'show'])->name('detail');

    // edit + update + delete
    Route::get('/{id}/edit', [IndicatorController::class, 'edit'])->name('edit');
    Route::put('/{id}', [IndicatorController::class, 'update'])->name('update');
    Route::delete('/{id}', [IndicatorController::class, 'delete'])->name('delete');
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
    Route::get('/create/{criteria}',  [EvidenceController::class, 'create'])->name('create');
    Route::post('/store', [EvidenceController::class, 'store'])->name('store');
    // Route::get('/create/{criteria}', [EvidenceController::class, 'create'])->name('create');



    Route::put('/{id}', [EvidenceController::class, 'update'])->name('update');


    Route::delete('/{id}', [EvidenceController::class, 'destroy'])->name('destroy');
    Route::get('/{id}/download', [EvidenceController::class, 'download'])
        ->name('download')->whereNumber('id');
    Route::get('criteria/{criteriaId}/evidences', [EvidenceController::class, 'getByCriteria']);
    Route::patch('/{id}/toggle-status', [EvidenceController::class, 'toggleStatus']);
});

Route::prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::get('/result', [DashboardController::class, 'getData'])->name('getData');
    Route::get('/export', [DashboardExportController::class, 'export'])->name('export');
});
Route::prefix('dashboardKpiUser')->name('dashboardKpiUser.')->group(function () {
    Route::get('/', [DashboardKpiUserController::class, 'index'])->name('index');
    Route::get('/dashboardKpiUser/{id}', [DashboardKpiUserController::class, 'show'])->name('show');
});


// Route::get('/indicator/dashboard', [\App\Http\Controllers\IndicatorController::class, 'index'])->name('indicator.dashboard');
