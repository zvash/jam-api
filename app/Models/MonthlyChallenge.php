<?php

namespace App\Models;

use App\Enums\GoalType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Morilog\Jalali\Jalalian;

/**
 * @property string goal_type
 */
class MonthlyChallenge extends Model
{
    use HasFactory;

    protected $appends = [
        'goal',
        'name_with_period',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function stats()
    {
        return $this->hasMany(MonthlyChallengeStat::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function winners()
    {
        return $this->hasMany(MonthlyChallengeWinner::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function prize()
    {
        return $this->belongsTo(Prize::class);
    }

    /**
     * @return string
     */
    public function getGoalAttribute()
    {
        if ($this->goal_type == GoalType::ORDER_COUNT) {
            return 'رساندن ' . $this->goal_amount . ' عدد مرسوله';
        }
        if ($this->goal_type == GoalType::WEIGHT) {
            return 'فروش ' . $this->goal_amount . ' کیلوگرم';
        }
        return "{$this->goal_amount}";
    }

    /**
     * @return string
     */
    public function getNameWithPeriodAttribute()
    {
        $monthName = getMonthName($this->month);
        return "{$this->description} ({$monthName} {$this->year})";
    }
}
