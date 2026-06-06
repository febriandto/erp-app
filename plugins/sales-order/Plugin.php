<?php

namespace Plugins\SalesOrder;

use App\Core\MenuManager;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class Plugin extends ServiceProvider
{
    public function boot(): void
    {
        if (app()->runningInConsole()) return;

        $this->loadViewsFrom(__DIR__ . '/resources/views', 'sales-order');
        $this->loadMigrationsFrom(__DIR__ . '/migrations');

        Route::middleware(['web', 'auth'])->group(__DIR__ . '/routes.php');

        $this->app->booted(function () {
            app()->make(MenuManager::class)->add([
                'title'      => 'Sales Order',
                'url'        => route('sales-order.index'),
                'icon'       => 'ti ti-puzzle',
                'order'      => 100,
                'active'     => 'sales-order*',
                'permission' => 'sales-order.view',
                'children'   => [
                    [
                        'title'      => 'Sales Order',
                        'icon'       => 'ti ti-puzzle',
                        'active'     => 'sales-order/list*',
                        'permission' => 'sales-order.view',
                        'children'   => [
                            ['title' => 'All Records',    'url' => route('sales-order.index'),  'active' => 'sales-order/list'],
                            ['title' => 'Create',         'url' => route('sales-order.create'), 'active' => 'sales-order/list/create', 'permission' => 'sales-order.manage'],
                        ],
                    ],
                ],
            ]);
        });
    }
}