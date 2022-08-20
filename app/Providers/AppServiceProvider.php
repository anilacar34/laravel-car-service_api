<?php

namespace App\Providers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if(env('APP_ENV') != 'production'){
            Artisan::call('cache:clear');
            Artisan::call('route:clear');
            Artisan::call('config:clear');
        }

        Schema::defaultStringLength(125); // wamp mysql 8.0.27
    }
}
