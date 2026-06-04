<?php

namespace App\Modules\Accounting;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class AccountingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'accounting');

        Route::middleware('web')
            ->group(__DIR__ . '/routes.php');
    }
}