<?php

namespace App\Console\Commands;

use App\Core\PluginManager;
use Illuminate\Console\Command;

class PluginDeactivate extends Command
{
    protected $signature   = 'plugin:deactivate {slug}';
    protected $description = 'Nonaktifkan plugin';

    public function handle(PluginManager $manager): void
    {
        $result = $manager->deactivate($this->argument('slug'));
        $result['success'] ? $this->info($result['message']) : $this->error($result['message']);
    }
}