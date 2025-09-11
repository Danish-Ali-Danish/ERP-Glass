<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;

use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\MaterialRequisitionController;
use App\Http\Controllers\RequisitionController;

use App\Http\Controllers\LpoController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\GrnController;
use App\Http\Controllers\SifController;


Route::get('/', function () {
    return view('layouts.dashboard');
})->name('dashboard'); // <-- yahan name add kiya

Route::get('/add-new', function () {
    return view('material.add-new');
})->name('add-new'); // <-- yahan name add kiya
Route::get('/departments', [DepartmentController::class, 'index'])->name('departments.index');
Route::post('/departments', [DepartmentController::class, 'store'])->name('departments.store');
Route::get('/departments/{id}', [DepartmentController::class, 'show'])->name('departments.show');
Route::put('/departments/{id}', [DepartmentController::class, 'update'])->name('departments.update');
Route::delete('/departments/{id}', [DepartmentController::class, 'destroy'])->name('departments.destroy');


Route::get('/requisitions/add-new', [RequisitionController::class, 'create'])->name('requisitions.add-new');
Route::get('/requisitions', [RequisitionController::class, 'index'])->name('requisitions.index');
Route::get('/requisitions/data', [RequisitionController::class, 'data'])->name('requisitions.data');

Route::post('/requisitions', [RequisitionController::class, 'store'])->name('requisitions.store');
Route::get('/requisitions/{id}', [RequisitionController::class, 'show'])->name('requisitions.show');
Route::get('/requisitions/{id}/edit', [RequisitionController::class, 'edit'])->name('requisitions.edit');

Route::put('/requisitions/{id}', [RequisitionController::class, 'update'])->name('requisitions.update');
Route::delete('/requisitions/{id}', [RequisitionController::class, 'destroy'])->name('requisitions.destroy');
Route::get('requisitions/items/search', [RequisitionController::class,'searchItems'])->name('requisitions.items.search');

Route::resource('lpos', LpoController::class);
Route::resource('items', ItemController::class);
Route::get('/lpos/search-items', [LpoController::class, 'searchItems'])->name('lpos.items.search');


Route::prefix('grns')->group(function(){
    Route::get('/lpo-details/{id}', [GrnController::class,'getLpoDetails']);
});
Route::delete('/grns/{grn}', [GrnController::class, 'destroy'])->name('grns.destroy');
Route::resource('grns', GrnController::class);
Route::prefix('sifs')->group(function () {
    Route::get('/', [SifController::class, 'index'])->name('sifs.index');
    Route::get('/create', [SifController::class, 'create'])->name('sifs.create');
    Route::post('/', [SifController::class, 'store'])->name('sifs.store');
    Route::get('/{sif}', [SifController::class, 'show'])->name('sifs.show');
    Route::delete('/{sif}', [SifController::class, 'destroy'])->name('sifs.destroy');
});
Route::get('/items/get-code', [ItemController::class, 'getCode'])->name('items.get-code');
Route::get('/sifs/items/search', [SifController::class, 'searchItems'])->name('sifs.items.search');
