<?php

namespace App\Providers;

use App\Models\Subscription;
use App\Observers\SubscriptionObserver;
use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\ExampleEvent' => [
            'App\Listeners\ExampleListener',
        ],
    ];

    public function dispatch(DispatcherContract $events)
    {
        parent::boot($events);

        // In this case we have a User model that we want to observe
        // We tell Laravel that the observer for the user model is the UserObserver
        Subscription::observe(new SubscriptionObserver());
    }
}
