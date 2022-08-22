<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Admin User
        User::firstOrCreate(
            [
                'id' => 1
            ],
            [
                'id'         => 1,
                'email'      => 'admin@carservice.com',
                'name'       => 'CarService Admin',
                'password'   => bcrypt('hPvBbQf7697q'),
                'created_by' => 1,
                'created_at' => Carbon::now(),
            ]
        );

        // User
        User::firstOrCreate(
            [
                'id' => 2
            ],
            [
                'id'         => 2,
                'email'      => 'john_doe@gmail.com',
                'name'       => 'John Doe',
                'password'   => bcrypt('N9Vq94nAmuTH'),
                'created_by' => 2,
                'created_at' => Carbon::now(),
            ]
        );
    }
}
