<?php

namespace App\Repositories;


use App\Exceptions\ContentWasNotFountException;
use App\Exceptions\UserReachedMaxLocationsException;
use App\Models\County;
use App\Models\Location;
use App\Models\User;

class LocationRepository extends BaseRepository
{
    /**
     * @param User $user
     * @param array $inputs
     * @return mixed
     * @throws UserReachedMaxLocationsException
     */
    public function create(User $user, array $inputs)
    {
        $maxLocations = env('USER_MAX_LOCATION', 4);
        if ($user->locations()->count() >= $maxLocations) {
            throw new UserReachedMaxLocationsException(__('messages.error.max_locations'), [
                'maxAddressCount' => $maxLocations
            ]);
        }
        $inputs['user_id'] = $user->id;
        $county = County::find($inputs['county_id']);
        $inputs['state_id'] = $county->state_id;
        return Location::create($inputs);
    }

    /**
     * @param User $user
     * @param int $locationId
     * @return bool
     * @throws ContentWasNotFountException
     */
    public function removeLocation(User $user, int $locationId)
    {
        if ($user->locations()->pluck('id')->contains($locationId)) {
            Location::query()->where('id', $locationId)->delete();
            return true;
        }
        throw new ContentWasNotFountException(__('messages.error.content_was_not_found'));
    }

    /**
     * @param Location $location
     * @param array $inputs
     * @return Location
     */
    public function update(Location $location, array $inputs)
    {
        foreach ($inputs as $key => $value) {
            if ($key == 'county_id') {
                $county = County::find($value);
                $location->setAttribute('state_id', $county->state_id);
            }
            $location->setAttribute($key, $value);
        }
        $location->save();
        return $location;
    }
}