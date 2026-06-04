<?php

namespace App\Modules\Inventory;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class InventoryServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'inventory');

        Route::middleware('web')
            ->group(__DIR__ . '/routes.php');
    }
}