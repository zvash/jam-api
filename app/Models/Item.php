<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image',
        'price',
        'last_price',
    ];

    protected $appends = [
        'user_price',
        'price_fluctuation_amount',
        'price_fluctuation_percent',
    ];
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function orders()
    {
        return $this->belongsToMany(Order::class, OrderItem::class)->withPivot('weight');
    }

    /**
     * @return float|int|mixed
     */
    public function getUserPriceAttribute()
    {
        $user = request()->user();
        if (! $user) {
            return $this->price;
        }
        $priceChangePercent = $user->price_change_percent;
        $diff = $this->price * $priceChangePercent / 100;
        return $this->price + $diff;
    }

    /**
     * @return int|mixed
     */
    public function getPriceFluctuationAmountAttribute()
    {
        if (! $this->last_price) {
            return 0;
        }

        return $this->price - $this->last_price;
    }

    /**
     * @return int|mixed
     */
    public function getPriceFluctuationPercentAttribute()
    {
        if (! $this->price) {
            return 0;
        }
        if (! $this->last_price) {
            return 0;
        }

        return (($this->price - $this->last_price) / $this->price) * 100;
    }

}
