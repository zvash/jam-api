<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\RouteRegistrar;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        $this->registerPassport();
    }

    /**
     * Setup Laravel Passport.
     *
     * @return void
     */
    protected function registerPassport()
    {
        Passport::routes(function (RouteRegistrar $router) {
            // Handle Client Credential and Password Grants Routes
            $router->forAccessTokens();

            // Handle Refresh Tokens Routes
            $router->forTransientTokens();
        });

        Passport::tokensExpireIn(now()->addYear());
        Passport::refreshTokensExpireIn(now()->addYear()->addWeek());
    }
}
