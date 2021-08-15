<?php

namespace App\Repositories;


use App\Enums\OrderStatus;
use App\Enums\UserType;
use App\Exceptions\ContentWasNotFountException;
use App\Http\Requests\StoreTicketRequest;
use App\Models\Campaign;
use App\Models\MonthlyChallenge;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\User;

class MonthlyChallengeRepository extends BaseRepository
{

    /**
     * @param string $userType
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|null|object
     * @throws ContentWasNotFountException
     */
    public function getCurrentChallenge(string $userType)
    {
        $now = \Carbon\Carbon::now();
        $challenge = MonthlyChallenge::query()
            ->where('user_type', $userType)
            ->where('starts_at', '<=', $now)
            ->where('ends_at', '>=', $now)
            ->where('is_active', true)
            ->with('prize')
            ->first();
        if (! $challenge) {
            throw new ContentWasNotFountException(__('messages.error.content_was_not_found'));
        }
        return $challenge;
    }

    /**
     * @param User $user
     * @param MonthlyChallenge $challenge
     * @return int|mixed
     */
    public function getChallengeScore(User $user, MonthlyChallenge $challenge)
    {
        if ($challenge->user_type == UserType::SELLER) {
            $currentPoints = Order::query()
                ->whereNotNull('final_weight')
                ->where('status', OrderStatus::FINISHED)
                ->where('user_id', $user->id)
                ->where('finished_at', '>=', $challenge->starts_at)
                ->where('finished_at', '<=', $challenge->ends_at)
                ->sum('final_weight');
        } else {
            $currentPoints = Order::query()
                ->where('status', OrderStatus::FINISHED)
                ->where('driver_id', $user->id)
                ->where('finished_at', '>=', $challenge->starts_at)
                ->where('finished_at', '<=', $challenge->ends_at)
                ->count();
        }
        return $currentPoints;
    }
}