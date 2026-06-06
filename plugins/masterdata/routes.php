<?php

use Illuminate\Support\Facades\Route;
use Plugins\masterdata\Controllers\CurrencyController;
use Plugins\masterdata\Controllers\UomController;
use Plugins\masterdata\Controllers\TaxController;
use Plugins\masterdata\Controllers\CompanyController;

Route::prefix('masterdata')->name('masterdata.')->middleware('can:master-data.manage')->group(function () {

    // Currencies
    Route::get('currencies',                      [CurrencyController::class, 'index'])->name('currencies.index');
    Route::get('currencies/create',               [CurrencyController::class, 'create'])->name('currencies.create');
    Route::post('currencies',                     [CurrencyController::class, 'store'])->name('currencies.store');
    Route::get('currencies/{currency}/edit',      [CurrencyController::class, 'edit'])->name('currencies.edit');
    Route::put('currencies/{currency}',           [CurrencyController::class, 'update'])->name('currencies.update');
    Route::delete('currencies/{currency}',        [CurrencyController::class, 'destroy'])->name('currencies.destroy');
    Route::patch('currencies/{currency}/default', [CurrencyController::class, 'setDefault'])->name('currencies.set-default');

    // Unit of Measure
    Route::get('uom',                [UomController::class, 'index'])->name('uom.index');
    Route::get('uom/create',         [UomController::class, 'create'])->name('uom.create');
    Route::post('uom',               [UomController::class, 'store'])->name('uom.store');
    Route::get('uom/{uom}/edit',     [UomController::class, 'edit'])->name('uom.edit');
    Route::put('uom/{uom}',          [UomController::class, 'update'])->name('uom.update');
    Route::delete('uom/{uom}',       [UomController::class, 'destroy'])->name('uom.destroy');

    // Taxes
    Route::get('taxes',              [TaxController::class, 'index'])->name('taxes.index');
    Route::get('taxes/create',       [TaxController::class, 'create'])->name('taxes.create');
    Route::post('taxes',             [TaxController::class, 'store'])->name('taxes.store');
    Route::get('taxes/{tax}/edit',   [TaxController::class, 'edit'])->name('taxes.edit');
    Route::put('taxes/{tax}',        [TaxController::class, 'update'])->name('taxes.update');
    Route::delete('taxes/{tax}',     [TaxController::class, 'destroy'])->name('taxes.destroy');

    // Company Profile (singleton — no index/create/destroy)
    Route::get('company',            [CompanyController::class, 'edit'])->name('company.edit');
    Route::put('company',            [CompanyController::class, 'update'])->name('company.update');

});
