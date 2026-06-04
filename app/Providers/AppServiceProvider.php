<?php

namespace App\Providers;

use App\Core\MenuManager;
use App\Core\PluginManager;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PluginManager::class, fn() => new PluginManager());
        $this->app->singleton(MenuManager::class, fn() => new MenuManager());
    }

    public function boot(): void
    {
        // Load semua plugin aktif
        $this->app->make(PluginManager::class)->loadActive();

        // View Composer — dipanggil saat view dirender, bukan saat boot
        // Jadi semua plugin sudah selesai register menu duluan
        View::composer('layouts.app', function ($view) {
            $view->with('menuItems', app(MenuManager::class)->all());
        });
    }
}