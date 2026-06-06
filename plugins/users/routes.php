<?php

use Illuminate\Support\Facades\Route;
use Plugins\users\Controllers\UserController;
use Plugins\users\Controllers\RoleController;

Route::prefix('users')->name('users.')->group(function () {
    Route::get('/',                  [UserController::class, 'index'])->name('index');
    Route::get('/create',            [UserController::class, 'create'])->name('create');
    Route::post('/',                 [UserController::class, 'store'])->name('store');
    Route::get('/{user}/edit',       [UserController::class, 'edit'])->name('edit');
    Route::put('/{user}',            [UserController::class, 'update'])->name('update');
    Route::delete('/{user}',         [UserController::class, 'destroy'])->name('destroy');

    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/',              [RoleController::class, 'index'])->name('index');
        Route::get('/create',        [RoleController::class, 'create'])->name('create');
        Route::post('/',             [RoleController::class, 'store'])->name('store');
        Route::get('/{role}/edit',   [RoleController::class, 'edit'])->name('edit');
        Route::put('/{role}',        [RoleController::class, 'update'])->name('update');
        Route::delete('/{role}',     [RoleController::class, 'destroy'])->name('destroy');
    });
});
