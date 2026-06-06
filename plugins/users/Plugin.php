<?php

namespace Plugins\users;

use App\Core\MenuManager;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class Plugin extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'users');

        Route::middleware(['web', 'auth'])->group(__DIR__ . '/routes.php');

        if (app()->runningInConsole()) return;

        $this->app->make(MenuManager::class)->add([
            'title'    => 'Users',
            'url'      => route('users.index'),
            'icon'     => 'ti ti-users',
            'order'    => 5,
            'active'   => 'users*',
            'children' => [
                ['title' => 'All Users', 'url' => route('users.index'),       'icon' => 'ti ti-user',   'active' => 'users'],
                ['title' => 'Roles',     'url' => route('users.roles.index'), 'icon' => 'ti ti-shield', 'active' => 'users/roles*'],
            ],
        ]);
    }
}
