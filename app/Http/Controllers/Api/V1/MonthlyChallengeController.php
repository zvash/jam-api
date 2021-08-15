<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\UserType;
use App\Exceptions\ContentWasNotFountException;
use App\Http\Controllers\Controller;
use App\Repositories\MonthlyChallengeRepository;
use App\Traits\Responses\ResponseMaker;
use Illuminate\Http\Request;

class MonthlyChallengeController extends Controller
{
    use ResponseMaker;

    /**
     * @param Request $request
     * @param string $type
     * @param MonthlyChallengeRepository $repository
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws ContentWasNotFountException
     */
    public function getCurrentChallengeStats(Request $request, string $type, MonthlyChallengeRepository $repository)
    {
        if (! in_array($type, [UserType::SELLER, UserType::DRIVER])) {
            throw new ContentWasNotFountException(__('messages.error.content_was_not_found'));
        }
        $user = $request->user();
        if (! $user->isCourier() && $type == UserType::DRIVER) {
            throw new ContentWasNotFountException(__('messages.error.content_was_not_found'));
        }
        $challenge = $repository->getCurrentChallenge($type);
        $score = $repository->getChallengeScore($user, $challenge);
        return $this->success([
            'challenge' => $challenge,
            'score' => $score
        ]);
    }
}
