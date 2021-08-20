<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResetDriverCountiesRequest;
use App\Repositories\DriverCountyRepository;
use App\Traits\Responses\ResponseMaker;
use Illuminate\Http\Request;

class DriverCountyController extends Controller
{
    use ResponseMaker;

    /**
     * @param ResetDriverCountiesRequest $request
     * @param DriverCountyRepository $repository
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function resetDriverCounties(ResetDriverCountiesRequest $request, DriverCountyRepository $repository)
    {
        $user = $request->user();
        $inputs = $request->validated();

        $stateIds = $inputs['state_ids'] ?? [];
        $countyIds = $inputs['county_ids'] ?? [];
        return $this->success($repository->resetCounties($user, $countyIds, $stateIds));
    }
}
