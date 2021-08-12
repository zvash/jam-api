<?php

namespace App\Events;

use App\Models\MonthlyChallenge;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChallengeWasClosed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var MonthlyChallenge
     */
    public $challenge;

    /**
     * Create a new event instance.
     *
     * @param MonthlyChallenge $challenge
     */
    public function __construct(MonthlyChallenge $challenge)
    {
        $this->challenge = $challenge;
    }

    /**
     * @return MonthlyChallenge
     */
    public function getChallenge()
    {
        return $this->challenge;
    }
}
