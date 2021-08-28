<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyChallengeStat extends Model
{
    use HasFactory;

    protected $appends = [
        'progress',
    ];
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function challenge()
    {
        return $this->belongsTo(MonthlyChallenge::class, 'monthly_challenge_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return float|int
     */
    public function getProgressAttribute()
    {
        $challenge = $this->challenge;
        if ($challenge->goal_amount) {
            return min($this->amount / $challenge->goal_amount, 1);
        }
        return 0.0;
    }
}
