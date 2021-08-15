<?php

namespace App\Providers;

use App\Events\NewOrderSuggestionsForDriversCreated;
use App\Events\OrderStatusUpdated;
use App\Events\OrderWasCreated;
use App\Listeners\CheckCampaignMilestones;
use App\Listeners\LogOrderStatus;
use App\Listeners\NotifyDrivers;
use App\Listeners\NotifyOrderSuggestions;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        OrderWasCreated::class => [
            LogOrderStatus::class,
        ],
        OrderStatusUpdated::class => [
            LogOrderStatus::class,
            NotifyDrivers::class,
            CheckCampaignMilestones::class,
        ],
        NewOrderSuggestionsForDriversCreated::class => [
            NotifyOrderSuggestions::class
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
