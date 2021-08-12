<?php

namespace App\Models;

use App\Enums\GoalType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string goal_type
 */
class MonthlyChallenge extends Model
{
    use HasFactory;

    protected $appends = [
        'goal',
    ];

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
}
