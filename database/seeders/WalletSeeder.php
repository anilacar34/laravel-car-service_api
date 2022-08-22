<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class WalletSeeder extends Seeder
{
    use WithoutModelEvents;
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Admin Wallet
        Wallet::firstOrCreate(
            [
                'user_id' => 1
            ],
            [
                'id'         => 1,
                'user_id'    => 1,
                'created_by' => 1,
                'created_at' => Carbon::now(),
            ]
        );

        // User Wallet
        Wallet::firstOrCreate(
            [
                'user_id' => 2
            ],
            [
                'id'         => 2,
                'user_id'    => 2,
                'created_by' => 2,
                'created_at' => Carbon::now(),
            ]
        );
    }
}
