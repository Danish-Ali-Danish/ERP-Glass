<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

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
use App\Http\Controllers\PermissionController;


// =========================
// 🔓 Public Routes
// =========================
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/errors/auth-404', function () {
    return view('errors.auth-404');
})->name('auth-404');

// artisan helper routes (optional: in production ye hata dena)
Route::get('/clear-cache', fn() => tap(Artisan::call('cache:clear'), fn() => print "Cache cleared"));
Route::get('/linkstorage', fn() => tap(Artisan::call('storage:link'), fn() => print "Storage link created"));
Route::get('/migrate', fn() => tap(Artisan::call('migrate', ['--force' => true]), fn() => print "Migration completed"));
Route::get('/seed', fn() => tap(Artisan::call('db:seed', ['--force' => true]), fn() => print "Seeding completed"));
Route::get('/optimize', fn() => tap(Artisan::call('optimize'), fn() => print "Optimization completed"));
Route::get('/route-cache', fn() => tap(Artisan::call('route:cache'), fn() => print "Route cache created"));
Route::get('/route-clear', fn() => tap(Artisan::call('route:clear'), fn() => print "Route cache cleared"));
Route::get('/view-clear', fn() => tap(Artisan::call('view:clear'), fn() => print "View cache cleared"));
Route::get('/config-cache', fn() => tap(Artisan::call('config:cache'), fn() => print "Config cache created"));
Route::get('/config-clear', fn() => tap(Artisan::call('config:clear'), fn() => print "Config cache cleared"));
Route::get('/cache-clear', fn() => tap(Artisan::call('cache:clear'), fn() => print "App cache cleared"));


// =========================
// 🔒 Protected Routes (auth + permission)
// =========================
Route::middleware(['auth', 'permission'])->group(function () {

    // dashboard
    Route::get('/', function () {
        return view('layouts.dashboard');
    })->name('dashboard');

    // Departments
    Route::resource('departments', DepartmentController::class);

    // Requisitions
    Route::get('/requisitions/add-new', [RequisitionController::class, 'create'])->name('requisitions.create');
    Route::get('/requisitions/data', [RequisitionController::class, 'data'])->name('requisitions.data');
    Route::get('requisitions/items/search', [RequisitionController::class,'searchItems'])->name('requisitions.items.search');
    Route::resource('requisitions', RequisitionController::class)->except(['create']);

    // LPOs
    Route::get('/lpos/search-items', [LpoController::class, 'searchItems'])->name('lpos.items.search');
    Route::resource('lpos', LpoController::class);

    // Items
    Route::resource('items', ItemController::class);

    // GRNs
    Route::get('lpo/{id}/details', [GrnController::class, 'getLpoDetails'])->name('grns.lpo.details');
    Route::resource('grns', GrnController::class);

    // SIFs
    Route::resource('sifs', SifController::class);

    // Work Orders
    Route::get('/workorders/{id}/preview', [WorkOrderController::class, 'preview'])->name('workorders.preview');
    Route::resource('workorders', WorkOrderController::class);

    // Quotations
    Route::resource('quotations', QuotationController::class);

    // Users
    Route::resource('users', UserController::class);
    Route::patch('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    Route::get('/users/search', [UserController::class, 'search'])->name('users.search');

    // User Roles
    Route::get('/user-role/search-users', [UserRoleController::class, 'searchUsers'])->name('user-role.search');
    Route::resource('user-roles', UserRoleController::class)->except(['create','edit']);

    // Roles
    Route::resource('roles', RoleController::class);
    Route::post('/roles/assign-users', [RoleController::class, 'assignUsers'])->name('roles.assignUsers');
    Route::post('/roles/assign-role-to-user', [RoleController::class, 'assignRoleToUser'])->name('roles.assignRoleToUser');
    Route::post('/roles/remove-user', [RoleController::class, 'removeUserFromRole'])->name('roles.removeUser');
    Route::get('/roles/{role}/permissions', [RoleController::class, 'getRolePermissions'])->name('roles.permissions');
    Route::post('/roles/{role}/permissions', [RoleController::class, 'updateRolePermissions'])->name('roles.updatePermissions');

    // Permissions
    Route::resource('permissions', PermissionController::class)->except(['show', 'create', 'edit']);
    Route::post('/permissions/{permission}/add-router', [PermissionController::class, 'addRouter'])->name('permissions.addRouter');
    Route::delete('/permission-routers/{permissionRouter}', [PermissionController::class, 'removeRouter'])->name('permissions.removeRouter');
});

