<?php

namespace App\Repositories;


use App\Models\User;

class DriverCountyRepository extends BaseRepository
{

    /**
     * @param User $user
     * @param array $countyIds
     * @return mixed
     */
    public function resetCounties(User $user, array $countyIds)
    {
        $user->driverCounty()->sync($countyIds);
        return $user->driverCounty;
    }
}