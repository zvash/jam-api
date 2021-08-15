<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'user_type',
        'goal_type',
        'is_active',
    ];

    /**
     * @param string $title
     * @param string $userType
     * @param string $goalType
     */
    public static function createIfNotExists(string $title, string $userType, string $goalType)
    {
        $campaign = Campaign::query()->where('title', $title)->first();
        if(! $campaign) {
            Campaign::query()
                ->create([
                    'title' => $title,
                    'user_type' => $userType,
                    'goal_type' => $goalType
                ]);
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function levels()
    {
        return $this->hasMany(CampaignLevel::class);
    }
}
