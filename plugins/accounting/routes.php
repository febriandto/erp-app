<?php

use Illuminate\Support\Facades\Route;
use Plugins\accounting\Controllers\InvoiceController;

Route::prefix('accounting')->name('accounting.')->group(function () {
    Route::resource('invoices', InvoiceController::class);
    Route::patch('invoices/{invoice}/status', [InvoiceController::class, 'updateStatus'])
         ->name('invoices.update-status');
});