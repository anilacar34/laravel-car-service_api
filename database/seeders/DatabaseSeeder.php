<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->createFirstAdmin();
        $this->createFirstUser();
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }

    private function createFirstAdmin(){
        // admin user create
        $user = User::where(['email'=>'admin@carservice.com'])->first();
        if (!$user) {
            $user = new User;
            $user->id = 1;
            $user->email = 'admin@carservice.com';
            $user->name = 'CarService Admin';
            $user->password = bcrypt('hPvBbQf7697q');
            $user->created_by = 1;
            $user->updated_by = 1;
            $user->saveQuietly();
        }

        $role = Role::where([
            'name'       => 'admin',
            'guard_name' => 'sanctum',
        ])->first();

        // spatie user role "admin" create
        if (!$role) {
            $role = new Role;
            $role->name = 'admin';
            $role->guard_name = 'sanctum';
            $role->saveQuietly();
        }

        // Admin user's assigned to admin role.
        $user->assignRole('admin');
    }

    private function createFirstUser(){
        // user create
        $user = User::where(['email'=>'john_doe@gmail.com'])->first();
        if (!$user) {
            $user = new User;
            $user->id = 2;
            $user->email = 'john_doe@gmail.com';
            $user->name = 'John Doe';
            $user->password = bcrypt('N9Vq94nAmuTH');
            $user->created_by = 2;
            $user->updated_by = 2;
            $user->saveQuietly();
        }

        $role = Role::where([
            'name'       => 'user',
            'guard_name' => 'sanctum',
        ])->first();

        // spatie user role "user" create
        if (!$role) {
            $role = new Role;
            $role->name = 'user';
            $role->guard_name = 'sanctum';
            $role->saveQuietly();
        }

        // Admin user's assigned to user role.
        $user->assignRole('user');
    }
}
