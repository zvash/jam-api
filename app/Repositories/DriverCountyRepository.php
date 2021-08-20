<?php

namespace App\Repositories;


use App\Models\County;
use App\Models\User;

class DriverCountyRepository extends BaseRepository
{

    /**
     * @param User $user
     * @param array $countyIds
     * @param array $stateIds
     * @return mixed
     */
    public function resetCounties(User $user, array $countyIds, array $stateIds = [])
    {
        $query = County::query();

        if ($countyIds) {
            $query = $query->whereIn('id', $countyIds);
        }
        if ($stateIds) {
            $query = $query->orWhereIn('state_id', $stateIds);
        }
        $ids = $query->pluck('id')->all();
        $user->driverCounty()->sync($ids);
        return $user->driverCounty;
    }
}