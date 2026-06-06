<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        $admin = User::firstOrCreate(
            ['email' => 'admin@erp.local'],
            [
                'name'     => 'Administrator',
                'password' => bcrypt('password'),
            ]
        );

        $admin->assignRole($adminRole);
    }
}
