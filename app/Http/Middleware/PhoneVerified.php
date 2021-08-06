<?php

namespace App\Http\Middleware;

use App\Enums\ErrorCodes;
use App\Traits\Responses\ResponseMaker;
use Closure;
use Illuminate\Http\Request;

class PhoneVerified
{
    use ResponseMaker;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if (!$user->phone_verified_at) {
            return $this->failWithCode(__('messages.error.phone_not_verified'), ErrorCodes::PHONE_NOT_VERIFIED, 403);
        }
        return $next($request);
    }
}
