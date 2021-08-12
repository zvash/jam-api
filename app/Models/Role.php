<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     * @param string $name
     * @return Model|null|Role
     */
    public static function getByName(string $name)
    {
        $role = Role::query()
            ->where('name', $name)
            ->first();
        return $role;
    }

    public function users()
    {
        return $this->belongsToMany(User::class, UserRole::class);
    }
}
