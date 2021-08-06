<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;

class AuthenticatedDriver
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param array $guards
     * @return mixed
     * @throws AuthenticationException
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $user = $request->user();
        if ($user->isCourier() || $user->isAdmin()) {
            return $next($request);
        }
        throw new AuthenticationException(
            'Unauthenticated.', $guards
        );
    }
}
