<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Seed semua permissions dari plugin.json yang ada
        $allPermissions = [
            ['name' => 'accounting.view',   'label' => 'View Accounting'],
            ['name' => 'accounting.manage', 'label' => 'Manage Accounting'],
            ['name' => 'inventory.view',    'label' => 'View Inventory'],
            ['name' => 'inventory.manage',  'label' => 'Manage Inventory'],
            ['name' => 'users.manage',      'label' => 'Manage Users & Roles'],
            ['name' => 'master-data.manage','label' => 'Manage Master Data'],
        ];

        foreach ($allPermissions as $perm) {
            Permission::firstOrCreate(['name' => $perm['name'], 'guard_name' => 'web']);
        }

        // Buat role admin dengan semua permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(Permission::all());

        // Buat user admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@erp.local'],
            [
                'name'     => 'Administrator',
                'username' => 'admin',
                'password' => bcrypt('password'),
            ]
        );

        $admin->syncRoles($adminRole);
    }
}
