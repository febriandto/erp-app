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

        View::composer('layouts.app', function ($view) {
            $allItems = app(MenuManager::class)->all();

            // Cari sidebar items dari modul yang sedang aktif
            $sidebarItems = [];
            foreach ($allItems as $module) {
                if (!empty($module['active']) && request()->is($module['active'])) {
                    $sidebarItems = $module['children'];
                    break;
                }
            }

            $view->with('menuItems', $allItems);
            $view->with('sidebarItems', $sidebarItems);
        });
    }
}