<?php

namespace App\Console\Commands;

use App\Core\PluginManager;
use Illuminate\Console\Command;

class PluginActivate extends Command
{
    protected $signature   = 'plugin:activate {slug}';
    protected $description = 'Aktifkan plugin';

    public function handle(PluginManager $manager): void
    {
        $result = $manager->activate($this->argument('slug'));
        $result['success'] ? $this->info($result['message']) : $this->error($result['message']);
    }
}