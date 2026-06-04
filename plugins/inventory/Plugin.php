<?php

namespace Plugins\inventory;

use App\Core\MenuManager;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class Plugin extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'inventory');
        $this->loadMigrationsFrom(__DIR__ . '/migrations');

        Route::middleware('web')->group(__DIR__ . '/routes.php');

        // Daftarkan menu
        $this->app->make(MenuManager::class)->add([
            'title'  => 'Inventory',
            'url'    => route('inventory.products.index'),
            'icon'   => 'ti ti-package',
            'order'  => 10,
            'active' => 'inventory*',
        ]);
    }
}