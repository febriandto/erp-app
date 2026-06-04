<?php

namespace Plugins\accounting;

use App\Core\MenuManager;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class Plugin extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'accounting');
        $this->loadMigrationsFrom(__DIR__ . '/migrations');

        Route::middleware('web')->group(__DIR__ . '/routes.php');

        // Daftarkan menu
        $this->app->make(MenuManager::class)->add([
            'title'  => 'Accounting',
            'url'    => route('accounting.invoices.index'),
            'icon'   => 'ti ti-file-invoice',
            'order'  => 20,
            'active' => 'accounting*',
        ]);
    }
}