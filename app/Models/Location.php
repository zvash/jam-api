<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'state_id',
        'county_id',
        'address',
        'postal_code',
        'is_default',
        'longitude',
        'latitude',
    ];

    protected $appends = [
        'full_address',
        'state_city',
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
    public function state()
    {
        return $this->belongsTo(State::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function county()
    {
        return $this->belongsTo(County::class);
    }

    /**
     * @return string
     */
    public function getFullAddressAttribute()
    {
        $address[] = $this->state->name;
        $address[] = $this->county->name;
        $address[] = $this->address;
        return implode(' - ', $address);
    }

    /**
     * @return string
     */
    public function getStateCityAttribute()
    {
        $address[] = $this->state->name;
        $address[] = $this->county->name;
        return implode(' - ', $address);
    }
}
