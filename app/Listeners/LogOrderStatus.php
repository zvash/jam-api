<?php

namespace App\Listeners;

use App\Events\OrderAwareEvent;
use App\Models\OrderStatusLog;

class LogOrderStatus
{
    /**
     * Handle the event.
     *
     * @param OrderAwareEvent $event
     * @return void
     */
    public function handle(OrderAwareEvent $event)
    {
        $order = $event->order;
        OrderStatusLog::query()
            ->create([
                'order_id' => $order->id,
                'status' => $order->status,
            ]);
    }
}
