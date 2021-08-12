<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

/**
 * @property string first_name
 * @property string last_name
 * @property string phone
 * @property string email
 * @property mixed phone_verified_at
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'phone_verified_at' => 'datetime',
        'email_verified_at' => 'datetime',
    ];

    protected $appends = [
        'name',
        'is_admin',
        'is_courier',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function monthlyChallengeStats()
    {
        return $this->hasOne(MonthlyChallengeStat::class);
    }

    /**
     * @return HasMany
     */
    public function monthlyChallengesWinnings()
    {
        return $this->hasMany(MonthlyChallengeWinner::class);
    }

    /**
     * @return HasMany
     */
    public function locations()
    {
        return $this->hasMany(Location::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, UserRole::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function driverCounty()
    {
        return $this->belongsToMany(County::class, DriverCounty::class, 'driver_id', 'county_id');
    }

    /**
     * @return HasMany
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Override the field which is used for username in the authentication
     * @param string $username
     * @return User
     */
    public function findForPassport(string $username)
    {
        return $this->where('phone', $username)->first();
    }

    /**
     * @param string $username
     * @return mixed
     */
    public static function findByUserName(string $username)
    {
        return static::where('phone', $username)->first();
    }

    /**
     * @return mixed|null
     */
    public function getMaskedPhoneAttribute()
    {
        return substr_replace($this->phone, '......', -8, 6);
    }

    /**
     * @return mixed
     */
    public function isAdmin()
    {
        return $this->roles->contains('name', 'admin');
    }

    /**
     * @return mixed
     */
    public function isCourier()
    {
        return $this->roles->contains('name', 'courier');
    }

    /**
     * @return mixed
     */
    public function isSeller()
    {
        return $this->roles->contains('name', 'seller');
    }

    /**
     * @param string $role
     * @return mixed
     */
    public function hasRole(string $role)
    {
        return $this->roles->contains('name', $role);
    }

    /**
     * @return mixed
     */
    public function getIsAdminAttribute()
    {
        return $this->isAdmin();
    }

    /**
     * @return null|string
     */
    public function getNameAttribute()
    {
        $firstName = $this->first_name ?? '';
        $lastName = $this->last_name ? ' ' . $this->last_name : '';
        $name = $firstName . $lastName ?? null;
        return $name;
    }

    /**
     * @return mixed
     */
    public function getIsCourierAttribute()
    {
        return $this->isCourier();
    }
}
