<?php

namespace App\Models;

use App\Enums\GoalType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampaignLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'prize_id',
        'milestone',
        'is_active',
    ];

    protected $appends = [
        'with_unit',
        'passed',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
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
    public function getWithUnitAttribute()
    {
        if ($this->campaign->goal_type == GoalType::ORDER_COUNT) {
            return float_number_format($this->milestone) . ' عدد';
        } else {
            return float_number_format($this->milestone) . ' کیلوگرم';
        }
    }

    /**
     * @return bool
     */
    public function getPassedAttribute()
    {
        $user = request()->user();
        if (! $user) {
            return false;
        }
        return $user->campaignPrizes()
            ->where('campaign_level_id', $this->id)
            ->count() > 0;
    }
}
