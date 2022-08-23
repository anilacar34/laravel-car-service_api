<?php

namespace App\Providers;

use App\Models\Car;
use App\Models\CarBrand;
use App\Models\CarModelYear;
use App\Models\CarService;
use App\Models\TransactionHistory;
use App\Models\User;
use App\Models\Wallet;
use App\Observers\GeneralObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        User::observe(GeneralObserver::class);
        Wallet::observe(GeneralObserver::class);
        TransactionHistory::observe(GeneralObserver::class);
        CarService::observe(GeneralObserver::class);
        Car::observe(GeneralObserver::class);
        CarBrand::observe(GeneralObserver::class);
        CarModelYear::observe(GeneralObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
