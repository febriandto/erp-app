<?php

namespace Database\Seeders;

use App\Models\Plugin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class CorePluginSeeder extends Seeder
{
    protected array $corePlugins = [
        [
            'slug'        => 'users',
            'name'        => 'User Management',
            'version'     => '1.0.0',
            'description' => 'Kelola user, role, dan permission',
            'author'      => 'febriandto',
        ],
        [
            'slug'        => 'masterdata',
            'name'        => 'Master Data',
            'version'     => '1.0.0',
            'description' => 'Data master lintas modul: mata uang, satuan ukur, pajak, dan profil perusahaan.',
            'author'      => 'febriandto',
        ],
    ];

    public function run(): void
    {
        foreach ($this->corePlugins as $plugin) {
            Plugin::updateOrCreate(
                ['slug' => $plugin['slug']],
                [
                    'name'           => $plugin['name'],
                    'version'        => $plugin['version'],
                    'description'    => $plugin['description'],
                    'author'         => $plugin['author'],
                    'is_active'      => true,
                    'is_core'        => true,
                    'installed_path' => "plugins/{$plugin['slug']}",
                    'installed_at'   => now(),
                ]
            );

            // Jalankan migration plugin setelah diaktifkan
            foreach (['migrations', 'database/migrations'] as $dir) {
                $path = base_path("plugins/{$plugin['slug']}/{$dir}");
                if (is_dir($path)) {
                    $relative = "plugins/{$plugin['slug']}/{$dir}";
                    Artisan::call('migrate', ['--path' => $relative, '--force' => true]);
                    break;
                }
            }
        }
    }
}
