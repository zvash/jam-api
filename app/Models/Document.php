<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $appends = [
        'is_valid',
        'image_url',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'expire_date' => 'datetime',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return bool
     */
    public function getIsValidAttribute()
    {
        return $this->expire_date->timestamp > \Carbon\Carbon::now()->timestamp;
    }

    /**
     * @return null|string
     */
    public function getImageUrlAttribute()
    {
        if (! $this->image) {
            return null;
        }
        return rtrim(env('APP_URL'), '/') . '/storage/' . $this->image;
    }

}
