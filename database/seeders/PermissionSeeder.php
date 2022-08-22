<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    use WithoutModelEvents;
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Role::firstOrCreate(
            [
                'name'       => 'admin',
                'guard_name' => 'sanctum',
            ],
            [
                'name'       => 'admin',
                'guard_name' => 'sanctum',
            ]
        );
        User::find(1)->assignRole('admin');

        Role::firstOrCreate(
            [
                'name'       => 'user',
                'guard_name' => 'sanctum',
            ],
            [
                'name'       => 'user',
                'guard_name' => 'sanctum',
            ]
        );
        User::find(2)->assignRole('user');
    }
}
