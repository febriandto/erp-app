<?php

namespace Database\Seeders;

use App\Models\Plugin;
use Illuminate\Database\Seeder;

class CorePluginSeeder extends Seeder
{
    public function run(): void
    {
        Plugin::updateOrCreate(
            ['slug' => 'users'],
            [
                'name'           => 'User Management',
                'version'        => '1.0.0',
                'description'    => 'Kelola user, role, dan permission',
                'author'         => 'febriandto',
                'is_active'      => true,
                'is_core'        => true,
                'installed_path' => 'plugins/users',
                'installed_at'   => now(),
            ]
        );

        Plugin::updateOrCreate(
            ['slug' => 'masterdata'],
            [
                'name'           => 'Master Data',
                'version'        => '1.0.0',
                'description'    => 'Data master lintas modul: mata uang, satuan ukur, pajak, dan profil perusahaan.',
                'author'         => 'febriandto',
                'is_active'      => true,
                'is_core'        => true,
                'installed_path' => 'plugins/masterdata',
                'installed_at'   => now(),
            ]
        );
    }
}
