<?php

use Illuminate\Support\Facades\Route;
use Plugins\SalesOrder\Controllers\SalesOrderController;

Route::prefix('sales-order')->name('sales-order.')->group(function () {
    Route::get('list',               [SalesOrderController::class, 'index'])->name('index')->middleware('can:sales-order.view');
    Route::get('list/create',        [SalesOrderController::class, 'create'])->name('create')->middleware('can:sales-order.manage');
    Route::post('list',              [SalesOrderController::class, 'store'])->name('store')->middleware('can:sales-order.manage');
    Route::get('list/{record}',      [SalesOrderController::class, 'show'])->name('show')->middleware('can:sales-order.view');
    Route::get('list/{record}/edit', [SalesOrderController::class, 'edit'])->name('edit')->middleware('can:sales-order.manage');
    Route::put('list/{record}',      [SalesOrderController::class, 'update'])->name('update')->middleware('can:sales-order.manage');
    Route::delete('list/{record}',   [SalesOrderController::class, 'destroy'])->name('destroy')->middleware('can:sales-order.manage');
});