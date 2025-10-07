<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\RequisitionController;
use App\Http\Controllers\LpoController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\GrnController;
use App\Http\Controllers\SifController;
use App\Http\Controllers\WorkOrderController;
use App\Http\Controllers\QuotationController;



// Login & Logout
Route::get('/login', [AuthenticatedSessionController::class, 'create'])
    ->middleware('guest')
    ->name('login');

Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// Dashboard
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// ✅ Ye block properly close kiya
Route::middleware(['auth'])->group(function () {

    // Roles CRUD
    Route::resource('roles', RoleController::class);
    Route::get('roles/{role}/permissions', [RoleController::class, 'show'])->name('roles.permissions.show');

    // Departments CRUD
    Route::get('/departments', [DepartmentController::class, 'index'])->name('departments.index');
    Route::post('/departments', [DepartmentController::class, 'store'])->name('departments.store');
    Route::get('/departments/{id}', [DepartmentController::class, 'show'])->name('departments.show');
    Route::put('/departments/{id}', [DepartmentController::class, 'update'])->name('departments.update');
    Route::delete('/departments/{id}', [DepartmentController::class, 'destroy'])->name('departments.destroy');

    // Requisitions CRUD
    Route::prefix('requisitions')->group(function () {
        Route::get('/add-new', [RequisitionController::class, 'create'])->name('requisitions.add-new');
        Route::get('/', [RequisitionController::class, 'index'])->name('requisitions.index');
        Route::get('/data', [RequisitionController::class, 'data'])->name('requisitions.data');
        Route::post('/', [RequisitionController::class, 'store'])->name('requisitions.store');
        Route::get('/{id}', [RequisitionController::class, 'show'])->name('requisitions.show');
        Route::get('/{id}/edit', [RequisitionController::class, 'edit'])->name('requisitions.edit');
        Route::put('/{id}', [RequisitionController::class, 'update'])->name('requisitions.update');
        Route::delete('/{id}', [RequisitionController::class, 'destroy'])->name('requisitions.destroy');
    });
    Route::get('/requisitions/items/search', [RequisitionController::class,'searchItems'])->name('requisitions.items.search');

    // LPO + Items
    Route::resource('lpos', LpoController::class);
    Route::resource('items', ItemController::class);
    Route::get('/lpos/items/search', [LpoController::class, 'searchItems'])->name('lpos.items.search');

    // GRN
    Route::resource('grns', GrnController::class);
    Route::get('lpo/{id}/details', [GrnController::class, 'getLpoDetails'])->name('grns.lpo.details');

    // SIF
    Route::prefix('sifs')->group(function () {
        Route::get('/', [SifController::class, 'index'])->name('sifs.index');
        Route::get('/add-new', [SifController::class, 'create'])->name('sifs.add-new');
        Route::post('/', [SifController::class, 'store'])->name('sifs.store');
        Route::get('/{id}', [SifController::class, 'show'])->name('sifs.show');
        Route::get('/{id}/edit', [SifController::class, 'edit'])->name('sifs.edit');
        Route::put('/{id}', [SifController::class, 'update'])->name('sifs.update');
        Route::delete('/{id}', [SifController::class, 'destroy'])->name('sifs.destroy');
    });
    
    
    // Work Orders Routes
    Route::prefix('workorders')->name('workorders.')->group(function () {
        Route::get('/', [WorkOrderController::class, 'index'])->name('index');       // list all
        Route::get('/create', [WorkOrderController::class, 'create'])->name('create'); // create form
        Route::post('/', [WorkOrderController::class, 'store'])->name('store');      // save new
        Route::get('/{workorder}', [WorkOrderController::class, 'show'])->name('show'); // single record
        Route::get('/{workorder}/edit', [WorkOrderController::class, 'edit'])->name('edit'); // edit form
        Route::put('/{workorder}', [WorkOrderController::class, 'update'])->name('update'); // update record
        Route::delete('/{workorder}', [WorkOrderController::class, 'destroy'])->name('destroy'); // delete record
    });
    Route::get('/workorders/{id}/preview', [WorkOrderController::class, 'preview'])->name('workorders.preview');
    
    
    // Quotations Routes
    Route::prefix('quotations')->name('quotations.')->group(function () {
        Route::get('/', [QuotationController::class, 'index'])->name('index');
        Route::get('/create', [QuotationController::class, 'create'])->name('create');
        Route::post('/', [QuotationController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [QuotationController::class, 'edit'])->name('edit');
        Route::put('/{id}', [QuotationController::class, 'update'])->name('update');
        Route::delete('/{id}', [QuotationController::class, 'destroy'])->name('destroy');
        Route::get('/{id}', [QuotationController::class, 'show'])->name('show'); // for preview JSON
    });


});
