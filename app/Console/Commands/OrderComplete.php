<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\v1\Order\OrderController;
use Illuminate\Console\Command;

class OrderComplete extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:complete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Completed orders!'; // order_status, ongoing => completed

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $controller = new OrderController();
        $count = $controller->orderComplete();
        print($count .' ongoing order(s) completed!');
        return 0;
    }
}
