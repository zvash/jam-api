<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'comment',
        'image',
        'is_open',
    ];

    protected $appends = [
        'image_url',
        'phone',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
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

    /**
     * @return mixed
     */
    public function getPhoneAttribute()
    {
        return $this->user->phone;
    }
}
