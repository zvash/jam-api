<?php

namespace App\Jobs;

use App\Enums\DriverOrderStatus;
use App\Enums\OrderStatus;
use App\Models\DriverAcceptOrder;
use App\Models\DriverOrder;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessDriverAcceptRequests implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $currentRequests = DriverAcceptOrder::query()
            ->where('is_processed', false)
            ->orderBy('created_at')
            ->get();
        $driverTokens = [];
        foreach ($currentRequests as $request) {
            $request->is_processed = 1;
            $request->save();
            $driverTokens[$request['driver_token']] = 1;
        }
        $allDriverTokens = array_keys($driverTokens);
        $needDriverTokens = Order::query()
            ->whereIn('driver_token', $allDriverTokens)
            ->where('status', OrderStatus::ACCEPTED_WAITING_FOR_DRIVER)
            ->where('driver_id', null)
            ->pluck('driver_token')
            ->all();
        foreach ($needDriverTokens as $driverToken) {
            $firstRequest = DriverAcceptOrder::query()
                ->where('driver_token', $driverToken)
                ->orderBy('created_at')
                ->first();
            if ($firstRequest) {
                $order = Order::find($firstRequest->order_id);
                if ($order->stateTransitionIsPermitted(OrderStatus::ACCEPTED_BY_DRIVER)) {
                    $order->driver_id = $firstRequest->driver_id;
                    $order->save();
                    $order->updateOrderStatus(OrderStatus::ACCEPTED_BY_DRIVER);
                    DriverOrder::query()
                        ->where('order_id', $firstRequest->order_id)
                        ->where('driver_id', $firstRequest->driver_id)
                        ->where('order_token', $firstRequest->order_token)
                        ->update(['status' => DriverOrderStatus::ACCEPTED_BY_DRIVER]);
                    DriverOrder::query()
                        ->where('order_id', $firstRequest->order_id)
                        ->where('driver_id', '<>', $firstRequest->driver_id)
                        ->where('order_token', $firstRequest->order_token)
                        ->update(['status' => DriverOrderStatus::TAKEN]);
                    //fire event to notify users
                    Log::info("Order#{$order->id} been accepted by Driver#{$firstRequest->driver_id}");
                }

            }
        }
    }
}
