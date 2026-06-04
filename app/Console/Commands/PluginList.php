<?php

namespace App\Console\Commands;

use App\Models\Plugin;
use Illuminate\Console\Command;

class PluginList extends Command
{
    protected $signature   = 'plugin:list';
    protected $description = 'Lihat daftar plugin terinstall';

    public function handle(): void
    {
        $plugins = Plugin::all(['slug', 'name', 'version', 'is_active', 'installed_at']);

        if ($plugins->isEmpty()) {
            $this->info('Belum ada plugin terinstall.');
            return;
        }

        $this->table(
            ['Slug', 'Name', 'Version', 'Status', 'Installed'],
            $plugins->map(fn($p) => [
                $p->slug,
                $p->name,
                $p->version,
                $p->is_active ? '<info>Active</info>' : '<comment>Inactive</comment>',
                $p->installed_at?->format('d M Y'),
            ])
        );
    }
}