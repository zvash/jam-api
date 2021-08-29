<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyChallengeWinner extends Model
{
    use HasFactory;

    protected $fillable = [
        'monthly_challenge_id',
        'user_id',
        'points',
        'points_needed',
        'prize_id',
        'has_won',
    ];

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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function prize()
    {
        return $this->belongsTo(Prize::class);
    }

    /**
     * @return float|int
     */
    public function getProgressAttribute()
    {
        if (! $this->points_needed) {
            return 1;
        }
        return min($this->points / $this->points_needed, 1);
    }
}
