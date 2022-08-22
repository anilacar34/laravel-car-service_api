<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\CarService;
use Illuminate\Database\Seeder;

class CarServiceSeeder extends Seeder
{
    use WithoutModelEvents;
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $serviceList = [
            [
                'name'  => 'Clean windshield',
                'price' => 9.99
            ],
            [
                'name'  => 'Check tire pressure',
                'price' => 4.99
            ],
            [
                'name'  => 'Check oil level and top off as needed',
                'price' => 19.99
            ],
            [
                'name'  => 'Change air filter',
                'price' => 3.99
            ],
            [
                'name'  => 'Rotate tires',
                'price' => 4.99
            ],
            [
                'name'  => 'Wash',
                'price' => 7.99
            ],
        ];

        foreach ($serviceList as $service){
            CarService::updateOrCreate(
                [
                    'name' => $service['name']
                ],
                $service
            );
        }
    }
}
