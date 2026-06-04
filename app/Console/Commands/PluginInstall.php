<?php

namespace App\Console\Commands;

use App\Core\PluginManager;
use Illuminate\Console\Command;

class PluginInstall extends Command
{
    protected $signature   = 'plugin:install {github_url}';
    protected $description = 'Install plugin dari GitHub';

    public function handle(PluginManager $manager): void
    {
        $url    = $this->argument('github_url');
        $result = $manager->installFromGithub($url);

        if ($result['success']) {
            $this->info($result['message']);
            $this->info('Jalankan: php artisan plugin:activate <slug>');
        } else {
            $this->error($result['message']);
        }
    }
}