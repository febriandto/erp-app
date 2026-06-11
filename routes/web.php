<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PluginController;

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// App — semua route dilindungi auth
Route::middleware('auth')->group(function () {

    Route::get('/', fn() => view('dashboard'));

    // Demo/preview halaman Laravel Licensing — bukan implementasi nyata
    Route::get('/laravel-licensing', fn() => view('demo.licensing'))->name('demo.licensing');

    Route::prefix('admin/plugins')->name('plugins.')->middleware('role:admin')->group(function () {
        Route::get('/',                          [PluginController::class, 'index'])->name('index');
        Route::post('/install',                  [PluginController::class, 'install'])->name('install');
        Route::post('/{plugin}/activate',        [PluginController::class, 'activate'])->name('activate');
        Route::post('/{plugin}/deactivate',      [PluginController::class, 'deactivate'])->name('deactivate');
        Route::post('/{plugin}/update',          [PluginController::class, 'update'])->name('update');
        Route::delete('/{plugin}/uninstall',     [PluginController::class, 'uninstall'])->name('uninstall');
    });

});
