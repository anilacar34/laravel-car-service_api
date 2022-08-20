<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;

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
            $user->password = bcrypt('car1969');
            $user->created_by = 1;
            $user->updated_by = 1;
            $user->saveQuietly();
        }
    }
}
