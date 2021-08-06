<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ContentWasNotFountException;
use App\Http\Controllers\Controller;
use App\Http\Requests\RemoveLocationRequest;
use App\Http\Requests\StoreLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Models\Location;
use App\Models\State;
use App\Repositories\LocationRepository;
use App\Traits\Responses\ResponseMaker;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    use ResponseMaker;

    /**
     * @param StoreLocationRequest $request
     * @param LocationRepository $repository
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \App\Exceptions\UserReachedMaxLocationsException
     */
    public function store(StoreLocationRequest $request, LocationRepository $repository)
    {
        $inputs = $request->validated();
        $user = $request->user();
        return $this->success($repository->create($user, $inputs));
    }

    /**
     * @param RemoveLocationRequest $request
     * @param LocationRepository $repository
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \App\Exceptions\ContentWasNotFountException
     */
    public function destroy(RemoveLocationRequest $request, LocationRepository $repository)
    {
        $user = $request->user();
        $inputs = $request->validated();
        if ($repository->removeLocation($user, $inputs['location_id'])) {
            return $this->success([]);
        }
    }

    /**
     * @param UpdateLocationRequest $request
     * @param Location $location
     * @param LocationRepository $repository
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws ContentWasNotFountException
     */
    public function update(UpdateLocationRequest $request, Location $location, LocationRepository $repository)
    {
        $user = $request->user();
        if ($user->id != $location->user->id) {
            throw new ContentWasNotFountException(__('messages.error.content_was_not_found'));
        }
        $inputs = $request->validated();
        return $this->success($repository->update($location, $inputs));
    }

    /**
     * @param Request $request
     * @param Location $location
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws ContentWasNotFountException
     */
    public function get(Request $request, Location $location)
    {
        $user = $request->user();
        if ($user->id != $location->user->id) {
            throw new ContentWasNotFountException(__('messages.error.content_was_not_found'));
        }
        return $this->success($location);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function getAllForUser(Request $request)
    {
        return $this->success($request->user()->locations);
    }

    /**
     * @param Request $request
     * @param Location $location
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws ContentWasNotFountException
     */
    public function makeDefaultForUser(Request $request, Location $location)
    {
        $user = $request->user();
        if ($user->id != $location->user->id) {
            throw new ContentWasNotFountException(__('messages.error.content_was_not_found'));
        }
        Location::query()->where('user_id', $user->id)
            ->update(['is_default' => false]);
        $location->setAttribute('is_default', true)->save();
        return $this->success($location);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function unsetDefaultForUser(Request $request)
    {
        $user = $request->user();
        Location::query()->where('user_id', $user->id)
            ->update(['is_default' => false]);
        return $this->success([]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function getStates(Request $request)
    {
        return $this->success(State::all(['id', 'name']));
    }

    /**
     * @param Request $request
     * @param State $state
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function getCounties(Request $request, State $state)
    {
        return $this->success($state->counties()->get(['id', 'state_id', 'name']));
    }
}
