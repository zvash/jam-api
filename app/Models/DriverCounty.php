<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverCounty extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'county_id',
    ];

    protected $appends = [
        'state',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function driver()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function county()
    {
        return $this->belongsTo(County::class);
    }

    /**
     * @return mixed
     */
    public function getStateAttribute()
    {
        return $this->county->state->name;
    }
}
