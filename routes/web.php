<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\RequisitionController;
use App\Http\Controllers\LpoController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\GrnController;
use App\Http\Controllers\SifController;
use App\Http\Controllers\WorkOrderController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserRoleController;

use Illuminate\Support\Facades\Artisan; 


// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

// Public routes (no permission check)
Route::get('/errors/auth-404', function () {
return view('errors.auth-404');
})->name('auth-404');

Route::get('/', function () {
    return view('layouts.dashboard');
})->name('dashboard')->middleware('auth'); // Add auth middleware here

// Apply permission middleware to all protected routes
Route::middleware(['auth', 'permission'])->group(function () {
    
    // Departments
    Route::get('/departments', [DepartmentController::class, 'index'])->name('departments.index');
    Route::post('/departments', [DepartmentController::class, 'store'])->name('departments.store');
    Route::get('/departments/{id}', [DepartmentController::class, 'show'])->name('departments.show');
    Route::put('/departments/{id}', [DepartmentController::class, 'update'])->name('departments.update');
    Route::delete('/departments/{id}', [DepartmentController::class, 'destroy'])->name('departments.destroy');

    // Requisitions
    Route::get('/requisitions/add-new', [RequisitionController::class, 'create'])->name('requisitions.create');
    Route::get('/requisitions', [RequisitionController::class, 'index'])->name('requisitions.index');
    Route::get('/requisitions/data', [RequisitionController::class, 'data'])->name('requisitions.data');
    Route::post('/requisitions', [RequisitionController::class, 'store'])->name('requisitions.store');
    Route::get('/requisitions/{id}', [RequisitionController::class, 'show'])->name('requisitions.show');
    Route::get('/requisitions/{id}/edit', [RequisitionController::class, 'edit'])->name('requisitions.edit');
    Route::put('/requisitions/{id}', [RequisitionController::class, 'update'])->name('requisitions.update');
    Route::delete('/requisitions/{id}', [RequisitionController::class, 'destroy'])->name('requisitions.destroy');
    Route::get('requisitions/items/search', [RequisitionController::class,'searchItems'])->name('requisitions.items.search');

    // LPOs
    Route::resource('lpos', LpoController::class);
    Route::get('/lpos/search-items', [LpoController::class, 'searchItems'])->name('lpos.items.search');

    // Items
    Route::resource('items', ItemController::class);

    // GRNs
    Route::resource('grns', GrnController::class);
    Route::get('lpo/{id}/details', [GrnController::class, 'getLpoDetails'])->name('grns.lpo.details');

    // SIFs
    Route::prefix('sifs')->group(function () {
        Route::get('/', [SifController::class, 'index'])->name('sifs.index');
        Route::get('/add-new', [SifController::class, 'create'])->name('sifs.create');
        Route::post('/', [SifController::class, 'store'])->name('sifs.store');
        Route::get('/{id}', [SifController::class, 'show'])->name('sifs.show');
        Route::get('/{id}/edit', [SifController::class, 'edit'])->name('sifs.edit');
        Route::put('/{id}', [SifController::class, 'update'])->name('sifs.update');
        Route::delete('/{id}', [SifController::class, 'destroy'])->name('sifs.destroy');
    });
        Route::get('/sifs/add-new', [SifController::class, 'create'])->name('sifs.create');


    // Work Orders
    Route::prefix('workorders')->name('workorders.')->group(function () {
        Route::get('/', [WorkOrderController::class, 'index'])->name('index');
        Route::get('/create', [WorkOrderController::class, 'create'])->name('create');
        Route::post('/', [WorkOrderController::class, 'store'])->name('store');
        Route::get('/{workorder}', [WorkOrderController::class, 'show'])->name('show');
        Route::get('/{workorder}/edit', [WorkOrderController::class, 'edit'])->name('edit');
        Route::put('/{workorder}', [WorkOrderController::class, 'update'])->name('update');
        Route::delete('/{workorder}', [WorkOrderController::class, 'destroy'])->name('destroy');
    });
    Route::get('/workorders/{id}/preview', [WorkOrderController::class, 'preview'])->name('workorders.preview');

    // Quotations
    Route::prefix('quotations')->name('quotations.')->group(function () {
        Route::get('/', [QuotationController::class, 'index'])->name('index');
        Route::get('/create', [QuotationController::class, 'create'])->name('create');
        Route::post('/', [QuotationController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [QuotationController::class, 'edit'])->name('edit');
        Route::put('/{id}', [QuotationController::class, 'update'])->name('update');
        Route::delete('/{id}', [QuotationController::class, 'destroy'])->name('destroy');
        Route::get('/{id}', [QuotationController::class, 'show'])->name('show');
    });
});
// Fallback route for undefined routes

Route::get('/clear-cache', function() {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    return "Cache is cleared";
});
Route::get('/linkstorage', function () {
    Artisan::call('storage:link');
    return 'Storage link created';
});
Route::get('/migrate', function () {
    Artisan::call('migrate', ['--force' => true]);
    return 'Migration completed';
});
Route::get('/seed', function () {
    Artisan::call('db:seed', ['--force' => true]);
    return 'Seeding completed';
});
Route::get('/optimize', function() {
    Artisan::call('optimize');
    return 'Optimization completed';
});
Route::get('/route-cache', function() {
    Artisan::call('route:cache');
    return 'Route cache created';
});
Route::get('/route-clear', function() {
    Artisan::call('route:clear');
    return 'Route cache cleared';
});
Route::get('/view-clear', function() {
    Artisan::call('view:clear');
    return 'View cache cleared';
}); 
Route::get('/config-cache', function() {
    Artisan::call('config:cache');
    return 'Configuration cache created';
});
Route::get('/config-clear', function() {
    Artisan::call('config:clear');
    return 'Configuration cache cleared';
});
Route::get('/cache-clear', function() {
    Artisan::call('cache:clear');
    return 'Application cache cleared';
});

Route::resource('users', UserController::class);
Route::patch('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

Route::get('/user-roles', [UserRoleController::class, 'index'])->name('user.roles.index');
Route::get('/user-roles/{id}', [UserRoleController::class, 'show']);
Route::post('/user-roles', [UserRoleController::class, 'store'])->name('user.roles.store');
Route::put('/user-roles/{id}', [UserRoleController::class, 'update'])->name('user.roles.update');
Route::delete('/user-roles/{id}', [UserRoleController::class, 'destroy'])->name('user.roles.destroy');
Route::get('/user-role/search-users', [UserRoleController::class, 'searchUsers'])->name('user-role.search');
Route::get('/users/search', [App\Http\Controllers\UserController::class, 'search'])->name('users.search');

// Roles Resource

// Role Routes
Route::resource('roles', RoleController::class);
Route::post('/roles/assign-users', [RoleController::class, 'assignUsers'])->name('roles.assignUsers');
Route::post('/roles/assign-role-to-user', [RoleController::class, 'assignRoleToUser'])->name('roles.assignRoleToUser');
Route::post('/roles/remove-user', [RoleController::class, 'removeUserFromRole'])->name('roles.removeUser');
Route::get('/roles/{role}/permissions', [RoleController::class, 'getRolePermissions'])->name('roles.permissions');
Route::post('/roles/{role}/permissions', [RoleController::class, 'updateRolePermissions'])->name('roles.updatePermissions');

// Permission Routes
Route::resource('permissions', PermissionController::class)->except(['show', 'create', 'edit']);
Route::post('/permissions/{permission}/add-router', [PermissionController::class, 'addRouter'])->name('permissions.addRouter');
Route::delete('/permission-routers/{permissionRouter}', [PermissionController::class, 'removeRouter'])->name('permissions.removeRouter');
