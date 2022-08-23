<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\v1\Car\CarController;
use Illuminate\Console\Command;

class SyncCars extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'car:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import cars to database!';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $controller = new CarController();
        $controller->saveCars();
        print('Cars has been imported to database!');
        return 0;
    }
}
