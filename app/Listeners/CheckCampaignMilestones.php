<?php

namespace App\Listeners;

use App\Enums\GoalType;
use App\Enums\OrderStatus;
use App\Enums\UserType;
use App\Events\OrderStatusUpdated;
use App\Models\Campaign;
use App\Models\CampaignLevel;
use App\Models\Order;
use App\Models\UserCampaignLevelPrize;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CheckCampaignMilestones
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
     * @param  OrderStatusUpdated $event
     * @return void
     */
    public function handle(OrderStatusUpdated $event)
    {
        $order = $event->order;
        if ($order->status == OrderStatus::FINISHED) {
            $this->userCampaign($order);
            $this->driverCampaign($order);
        }

    }

    /**
     * @param Order $order
     */
    private function userCampaign(Order $order)
    {
        $user = $order->user;
        $previousOrdersWeights = Order::query()
            ->where('user_id', $user->id)
            ->whereNotNull('final_weight')
            ->where('status', OrderStatus::FINISHED)
            ->where('id', '<>', $order->id)
            ->sum('final_weight');
        $allOrdersWeights = $previousOrdersWeights + $order->final_weight;
        $campaign = Campaign::query()
            ->where('user_type', UserType::SELLER)
            ->where('goal_type', GoalType::WEIGHT)
            ->where('is_active', true)
            ->first();
        if (!$campaign) {
            return;
        }

        $passedLevels = CampaignLevel::query()
            ->where('campaign_id', $campaign->id)
            ->where('is_active', true)
            ->where('milestone', '>', $previousOrdersWeights)
            ->where('milestone', '<=', $allOrdersWeights)
            ->orderBy('milestone', 'asc')
            ->get();

        foreach ($passedLevels as $level) {
            if (
            UserCampaignLevelPrize::query()
                ->where('user_id', $user->id)
                ->where('campaign_level_id', $level->id)
                ->first()
            ) {
                continue;
            }
            UserCampaignLevelPrize::query()
                ->create([
                    'user_id' => $user->id,
                    'campaign_level_id' => $level->id,
                    'prize_id' => $level->prize_id,
                    'milestone' => $level->milestone,
                ]);
        }

    }

    /**
     * @param Order $order
     */
    private function driverCampaign(Order $order)
    {
        $user = $order->driver;
        if (!$user) {
            return;
        }
        $ordersCount = Order::query()
            ->where('driver_id', $user->id)
            ->where('status', OrderStatus::FINISHED)
            ->count();

        $campaign = Campaign::query()
            ->where('user_type', UserType::DRIVER)
            ->where('goal_type', GoalType::ORDER_COUNT)
            ->where('is_active', true)
            ->first();
        if (!$campaign) {
            return;
        }

        $level = CampaignLevel::query()
            ->where('campaign_id', $campaign->id)
            ->where('is_active', true)
            ->where('milestone', $ordersCount)
            ->first();

        if (! $level) {
            return;
        }

        if (
        UserCampaignLevelPrize::query()
            ->where('user_id', $user->id)
            ->where('campaign_level_id', $level->id)
            ->first()
        ) {
            return;
        }
        UserCampaignLevelPrize::query()
            ->create([
                'user_id' => $user->id,
                'campaign_level_id' => $level->id,
                'prize_id' => $level->prize_id,
                'milestone' => $level->milestone,
            ]);

    }
}
