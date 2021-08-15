<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCampaignLevelPrize extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'campaign_level_id',
        'prize_id',
        'milestone',
    ];

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
    public function campaignLevel()
    {
        return $this->belongsTo(CampaignLevel::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function prize()
    {
        return $this->belongsTo(Prize::class);
    }
}
