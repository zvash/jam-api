<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\UserType;
use App\Exceptions\ContentWasNotFountException;
use App\Http\Controllers\Controller;
use App\Repositories\UserCampaignRepository;
use App\Traits\Responses\ResponseMaker;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    use ResponseMaker;

    /**
     * @param Request $request
     * @param string $type
     * @param UserCampaignRepository $repository
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws ContentWasNotFountException
     */
    public function getAvailableCampaign(Request $request, string $type, UserCampaignRepository $repository)
    {
        if (! in_array($type, [UserType::SELLER, UserType::DRIVER])) {
            throw new ContentWasNotFountException(__('messages.error.content_was_not_found'));
        }
        $user = $request->user();
        if (! $user->isCourier() && $type == UserType::DRIVER) {
            throw new ContentWasNotFountException(__('messages.error.content_was_not_found'));
        }
        $campaign = $repository->getCampaignByUserType($type);
        $result['campaign'] = $campaign->toArray();
        $result['levels'] = $repository->getAvailableLevels($user, $campaign)->toArray();
        $result['score'] = $repository->getCurrentScore($user, $campaign);
        return $this->success($result);

    }
}
