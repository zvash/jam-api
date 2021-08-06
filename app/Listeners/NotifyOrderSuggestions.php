<?php

namespace App\Listeners;

use App\Events\NewOrderSuggestionsForDriversCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class NotifyOrderSuggestions
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  NewOrderSuggestionsForDriversCreated  $event
     * @return void
     */
    public function handle(NewOrderSuggestionsForDriversCreated $event)
    {
        $order = $event->order;
        $orderToken = $order->driver_token;
        $driverIds = $order->possibleDrivers()
            ->where('order_token', $orderToken)
            ->pluck('driver_id')
            ->all();
        foreach ($driverIds as $driverId) {
            Log::info("Order#{$order->id} been Suggested to Driver#{$driverId}");
        }
    }
}
