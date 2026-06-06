<?php

namespace Plugins\masterdata;

use App\Core\MenuManager;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class Plugin extends ServiceProvider
{
    public function boot(): void
    {
        if (app()->runningInConsole()) return;

        $this->loadViewsFrom(__DIR__ . '/resources/views', 'masterdata');
        $this->loadMigrationsFrom(__DIR__ . '/migrations');

        Route::middleware(['web', 'auth'])->group(__DIR__ . '/routes.php');

        $this->app->booted(function () {
            app()->make(MenuManager::class)->add([
                'title'      => 'Master Data',
                'url'        => route('masterdata.currencies.index'),
                'icon'       => 'ti ti-database',
                'order'      => 10,
                'active'     => 'masterdata*',
                'permission' => 'master-data.manage',
                'children'   => [
                    [
                        'title'      => 'Currency',
                        'url'        => route('masterdata.currencies.index'),
                        'icon'       => 'ti ti-currency-dollar',
                        'active'     => 'masterdata/currencies*',
                        'permission' => 'master-data.manage',
                    ],
                    [
                        'title'      => 'Unit of Measure',
                        'url'        => route('masterdata.uom.index'),
                        'icon'       => 'ti ti-ruler',
                        'active'     => 'masterdata/uom*',
                        'permission' => 'master-data.manage',
                    ],
                    [
                        'title'      => 'Tax',
                        'url'        => route('masterdata.taxes.index'),
                        'icon'       => 'ti ti-receipt-tax',
                        'active'     => 'masterdata/taxes*',
                        'permission' => 'master-data.manage',
                    ],
                    [
                        'title'      => 'Company Profile',
                        'url'        => route('masterdata.company.edit'),
                        'icon'       => 'ti ti-building',
                        'active'     => 'masterdata/company*',
                        'permission' => 'master-data.manage',
                    ],
                ],
            ]);
        });
    }
}
