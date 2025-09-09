<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DashboardExportController;
use App\Http\Controllers\DashboardKpiUserController;
use App\Http\Controllers\DashboardKpiAdminController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EvidenceController;
use App\Http\Controllers\IndicatorController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StandardController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

/*
|--------------------------------------------------------------------------
| Guest Routes (Public Access)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    // Redirect root to login
    Route::get('/', function () {
        return redirect()->route('login');
    });

    // Authentication routes
    Route::controller(AuthController::class)->group(function () {
        Route::get('/login', 'showLoginForm')->name('login');
        Route::post('/login', 'login');
    });
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Logout)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->controller(AuthController::class)->group(function () {
    Route::post('/logout', 'logout')->name('logout');
});

/*
|--------------------------------------------------------------------------
| Protected Routes (Authenticated + Permission-based)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum'])->group(function () {

    // ===== DASHBOARD ROUTES =====
    Route::prefix('dashboard')->name('dashboard.')->middleware('permission:view-dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('index');
        Route::get('/result', [DashboardController::class, 'getData'])->name('getData');
        Route::get('/export', [DashboardExportController::class, 'export'])
            ->name('export')
            ->middleware('permission:export-dashboard');
    });

    // ===== DASHBOARD KPI USER ROUTES =====
    Route::prefix('dashboardKpiUser')->name('dashboardKpiUser.')->middleware('permission:view-dashboard-kpi-user')->group(function () {
        Route::get('/', [DashboardKpiUserController::class, 'index'])->name('index');
        Route::get('/dashboardKpiUser/{id}', [DashboardKpiUserController::class, 'show'])

            ->name('show')
            ->middleware('permission:show-dashboard-kpi-user');
    });

    // ===== INDICATOR ROUTES =====
    Route::prefix('indicator')->name('indicator.')->group(function () {
        // Dashboard
        Route::get('/', [IndicatorController::class, 'index'])
            ->name('index')
            ->middleware('permission:view-indicator-dashboard');

        // View
        Route::get('/{id}/show', [IndicatorController::class, 'show'])
            ->name('show')
            ->middleware('permission:view-indicator');

        // Create
        Route::get('/create', [IndicatorController::class, 'create'])
            ->name('create')
            ->middleware('permission:create-indicator');
        Route::post('/store', [IndicatorController::class, 'store'])
            ->name('store')
            ->middleware('permission:create-indicator');

        // Edit & Update
        Route::get('/{id}/edit', [IndicatorController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:edit-indicator');
        Route::put('/{id}', [IndicatorController::class, 'update'])
            ->name('update')
            ->middleware('permission:edit-indicator');

        // Delete
        Route::delete('/{id}', [IndicatorController::class, 'delete'])
            ->name('delete')
            ->middleware('permission:delete-indicator');
    });

    // ===== USER MANAGEMENT ROUTES =====
    Route::prefix('users')->name('users.')->middleware('permission:view-users')->group(function () {
        // View
        Route::get('/', [UserController::class, 'index'])->name('index');

        // Create
        Route::get('/create', [UserController::class, 'create'])
            ->name('create')
            ->middleware('permission:create-users');
        Route::post('/', [UserController::class, 'store'])
            ->name('store')
            ->middleware('permission:create-users');

        // Edit & Update
        Route::get('/{id}/edit', [UserController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:edit-users');
        Route::put('/{id}', [UserController::class, 'update'])
            ->name('update')
            ->middleware('permission:edit-users');

        // Delete
        Route::delete('/{id}', [UserController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:delete-users');
    });

    // ===== DEPARTMENT ROUTES =====
    Route::prefix('departments')->name('departments.')->middleware('permission:view-departments')->group(function () {
        // View
        Route::get('/', [DepartmentController::class, 'index'])->name('index');

        // Create
        Route::post('/store', [DepartmentController::class, 'store'])
            ->name('store')
            ->middleware('permission:create-departments');

        // Update
        Route::put('/{id}', [DepartmentController::class, 'update'])
            ->name('update')
            ->middleware('permission:edit-departments');

        // Delete
        Route::delete('/{id}', [DepartmentController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:delete-departments');
    });

    // ===== CATEGORY ROUTES =====
    Route::prefix('categories')->name('categories.')->middleware('permission:view-categories')->group(function () {
        // View
        Route::get('/', [CategorieController::class, 'index'])->name('index');

        // Create
        Route::post('/store', [CategorieController::class, 'store'])
            ->name('store')
            ->middleware('permission:create-categories');

        // Update
        Route::put('/{id}', [CategorieController::class, 'update'])
            ->name('update')
            ->middleware('permission:edit-categories');

        // Delete
        Route::delete('/{id}', [CategorieController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:delete-categories');
    });

    // ===== STANDARD ROUTES =====
    Route::prefix('standards')->name('standards.')->middleware('permission:view-standards')->group(function () {
        // View
        Route::get('/', [StandardController::class, 'index'])->name('index');

        // Create
        Route::post('/store', [StandardController::class, 'store'])
            ->name('store')
            ->middleware('permission:create-standards');

        // Update
        Route::put('/{id}', [StandardController::class, 'update'])
            ->name('update')
            ->middleware('permission:edit-standards');

        // Delete
        Route::delete('/{id}', [StandardController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:delete-standards');
    });

    // ===== SETTINGS ROUTES =====
    Route::prefix('settings')->name('settings.')->middleware('permission:view-settings')->group(function () {
        // View
        Route::get('/', [SettingController::class, 'index'])->name('index');

        // Create
        Route::post('/store', [SettingController::class, 'store'])
            ->name('store')
            ->middleware('permission:create-settings');

        // Update
        Route::put('/{id}', [SettingController::class, 'update'])
            ->name('update')
            ->middleware('permission:edit-settings');
    });

    // ===== EVIDENCE ROUTES =====
    Route::prefix('evidences')->name('evidences.')->middleware('permission:view-evidence')->group(function () {
        // View
        Route::get('/', [EvidenceController::class, 'index'])->name('index');
        Route::get('criteria/{criteriaId}/evidences', [EvidenceController::class, 'getByCriteria']);

        // Create
        Route::get('/create/{criteria}', [EvidenceController::class, 'create'])
            ->name('create')
            ->middleware('permission:create-evidence');
        Route::post('/store', [EvidenceController::class, 'store'])
            ->name('store')
            ->middleware('permission:create-evidence');

        // Update
        Route::put('/{id}', [EvidenceController::class, 'update'])
            ->name('update')
            ->middleware('permission:edit-evidence');
        Route::patch('/{id}/toggle-status', [EvidenceController::class, 'toggleStatus'])
            ->middleware('permission:edit-evidence');

        // Delete
        Route::delete('/{id}', [EvidenceController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:delete-evidence');

        // Download
        Route::get('/{id}/download', [EvidenceController::class, 'download'])
            ->name('download')
            ->whereNumber('id')
            ->middleware('permission:download-evidence');
    });

    Route::prefix('dashboardKpiUser')->name('dashboardKpiUser.')->group(function () {
        Route::get('/', [DashboardKpiUserController::class, 'index'])->name('index');
        Route::get('/dashboardKpiUser/{id}', [DashboardKpiUserController::class, 'show'])->name('show');
        Route::put('/{id}/update-variables', [DashboardKpiUserController::class, 'saveVariables'])->name('saveVariables');
    });

    Route::prefix('dashboardKpiUser')->name('dashboardKpiUser.')->group(function () {
        Route::get('/', [DashboardKpiUserController::class, 'index'])->name('index');
        Route::get('/dashboardKpiUser/{id}', [DashboardKpiUserController::class, 'show'])->name('show');
        Route::put('/{id}/update-variables', [DashboardKpiUserController::class, 'saveVariables'])->name('saveVariables');
    });
});

Route::prefix('/test')->name('dashboardKpiAdmin.')->group(function () {
    Route::get('/', [DashboardKpiAdminController::class, 'index'])->name('index');
    Route::get('/dashboardKpiUser/{id}', [DashboardKpiAdminController::class, 'show'])->name('show');
});