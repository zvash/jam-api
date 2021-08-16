<?php

namespace App\Repositories;


use App\Enums\OrderStatus;
use App\Enums\UserType;
use App\Exceptions\ContentWasNotFountException;
use App\Models\Campaign;
use App\Models\CampaignLevel;
use App\Models\Order;
use App\Models\User;

class UserCampaignRepository extends BaseRepository
{

    /**
     * @param User $user
     * @param Campaign $campaign
     * @return array
     */
    public function getAvailableLevels(User $user, Campaign $campaign)
    {
        $passedLevels = $user->campaignPrizes()->with('prize')->get()->toArray();
        $prizesByLevelId = [];
        foreach ($passedLevels as $level) {
            $prizesByLevelId[$level['id']] = $level['prize'];
        }
        $passedLevelsIds = $user->campaignPrizes()->pluck('campaign_level_id')->all();
        $currentPoints = $this->getCurrentScore($user, $campaign);
        $availableLevels = CampaignLevel::query()
            ->where('campaign_id', $campaign->id)
            ->where('milestone', '>', $currentPoints)
            ->orWhereIn('id', $passedLevelsIds)
            ->orderBy('milestone')
            ->with('prize')
            ->get()
            ->makeHidden('campaign')
            ->toArray();
        foreach ($availableLevels as $key => $level) {
            if (array_key_exists($level['id'], $prizesByLevelId)) {
                $availableLevels[$key]['prize'] = $prizesByLevelId[$level['id']];
            }
        }
        return $availableLevels;
    }

    /**
     * @param string $userType
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|null|object
     * @throws ContentWasNotFountException
     */
    public function getCampaignByUserType(string $userType)
    {
        $campaign = Campaign::query()
            ->where('is_active', true)
            ->where('user_type', $userType)
            ->first();
        if (! $campaign) {
            throw new ContentWasNotFountException(__('messages.error.content_was_not_found'));
        }
        return $campaign;
    }

    /**
     * @param User $user
     * @param Campaign $campaign
     * @return int|mixed
     */
    public function getCurrentScore(User $user, Campaign $campaign)
    {
        if ($campaign->user_type == UserType::SELLER) {
            $currentPoints = Order::query()
                ->whereNotNull('final_weight')
                ->where('status', OrderStatus::FINISHED)
                ->where('user_id', $user->id)
                ->sum('final_weight');
        } else {
            $currentPoints = Order::query()
                ->where('status', OrderStatus::FINISHED)
                ->where('driver_id', $user->id)
                ->count();
        }
        return $currentPoints;
    }
}