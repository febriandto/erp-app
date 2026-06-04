<?php

use Illuminate\Support\Facades\Route;
use Plugins\inventory\Controllers\ProductController;

Route::prefix('inventory')->name('inventory.')->group(function () {
    Route::resource('products', ProductController::class);
});