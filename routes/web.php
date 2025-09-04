<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\IndicatorController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\AuthController;


Route::middleware(['auth:sanctum'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::prefix('indicator')->name('indicator.')->group(function () {
        Route::get('/dashboard-page', [IndicatorController::class, 'index'])->name('dashboard');

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

    // Route::prefix('test')->group(function () {
    //     Route::get('/', [IndicatorController::class, 'index'])->name('dashboard');
    //     Route::get('/create', [IndicatorController::class, 'create'])->name('create');

    //     // route actions for indicators
    //     Route::post('/', [IndicatorController::class, 'store'])->name('store');
    //     Route::get('/{id}', [IndicatorController::class, 'show'])->name('show');
    //     Route::get('/{id}/edit', [IndicatorController::class, 'edit'])->name('edit');
    //     Route::put('/{id}', [IndicatorController::class, 'update'])->name('update');
    //     Route::delete('/{id}', [IndicatorController::class, 'destroy'])->name('destroy');
    // });

    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/',        [UserController::class, 'index'])->name('index');
        Route::get('/create',  [UserController::class, 'create'])->name('create');
        Route::get('/{id}/edit', [UserController::class, 'edit'])->name('edit');   // ใช้ {id}
        Route::post('/',       [UserController::class, 'store'])->name('store');
        Route::put('/{id}',    [UserController::class, 'update'])->name('update');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
    });
});

Route::middleware('guest')->group(function () {
    // 1. ถ้าเข้า path "/" และยังไม่ล็อกอิน ให้ไปที่หน้า login
    Route::get('/', function () {
        return redirect()->route('login');
    });

    // 2. ย้าย Route ของ AuthController มาไว้ในกลุ่มนี้เพื่อความเป็นระเบียบ
    Route::controller(AuthController::class)->group(function () {
        Route::get('/login', 'showLoginForm')->name('login');
        Route::post('/login', 'login');
    });
});

Route::middleware('auth')->controller(AuthController::class)->group(function () {
    Route::post('/logout', 'logout')->name('logout');
});
