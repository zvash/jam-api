<?php

namespace App\Nova;

use App\Nova\Actions\ChooseUserType;
use App\Nova\UserCampaignLevelPrize;
use App\Nova\Actions\PromoteToAdmin;
use Illuminate\Http\Request;
use Illuminate\Testing\Fluent\Concerns\Has;
use Laravel\Nova\Fields\Avatar;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Text;

class User extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\User::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
        'first_name',
        'last_name',
        'email',
        'national_code',
        'phone',
    ];

    /**
     * @return array|null|string
     */
    public static function group()
    {
        return __('nova.users');
    }

    /**
     * @return array|null|string
     */
    public static function label()
    {
        return __('nova.users');
    }

    /**
     * @return array|null|string
     */
    public static function singularLabel()
    {
        return __('nova.user');
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function authorizedToDelete(Request $request)
    {
        return false;
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        $fields = [
            ID::make(__('nova.id'), 'id')->sortable(),

            Avatar::make(__('nova.image'), 'avatar')
                ->disk('public')
                ->rounded()
                ->path('users/avatars')
                ->prunable()
                ->deletable()
                ->nullable()
                ->rules('nullable', 'mimes:jpeg,jpg,png'),

            Text::make(__('nova.name'), 'name')
                ->sortable()
                ->exceptOnForms(),

            Text::make(__('nova.company'), 'company')
                ->sortable(),

            Text::make(__('nova.first_name'), 'first_name')
                ->sortable()
                ->rules('required', 'max:255')
                ->onlyOnForms(),

            Text::make(__('nova.last_name'), 'last_name')
                ->sortable()
                ->rules('required', 'max:255')
                ->onlyOnForms(),

            Text::make(__('nova.phone'), 'phone')
                ->sortable()
                ->rules('required', 'max:254')
                ->creationRules('unique:users,phone')
                ->updateRules('unique:users,phone,{{resourceId}}'),

            Text::make(__('nova.email'), 'email')
                ->sortable()
                ->rules('email', 'max:254', 'nullable')
                ->creationRules('unique:users,email')
                ->updateRules('unique:users,email,{{resourceId}}')
                ->showOnIndex(false),

            Text::make(__('nova.national_code'), 'national_code')
                ->sortable()
                ->rules('max:10', 'nullable', 'min:10')
                ->showOnIndex(false),

            Number::make(__('nova.price_change_percent'), 'price_change_percent')
                ->step(0.1)
                ->default(0)
                ->min(-100)
                ->max(100)
                ->rules('numeric', 'min:-100', 'max:100')
                ->displayUsing(function ($value) {
                    return $value . '%';
                }),

            Text::make(__('nova.seller_campaign_level'), 'seller_campaign_level')
                ->exceptOnForms()
                ->showOnIndex(false),

            Text::make(__('nova.driver_campaign_level'), 'driver_campaign_level')
                ->exceptOnForms()
                ->showOnIndex(false),

            Text::make(__('nova.user_rank'), 'rank')
                ->sortable()
                ->exceptOnForms(),

            Password::make(__('nova.password'), 'password')
                ->onlyOnForms()
                ->creationRules('required', 'string', 'min:8')
                ->updateRules('nullable', 'string', 'min:8'),

            Badge::make(__('nova.is_courier'), 'is_courier')
                ->displayUsing(function ($value) {
                    return $value ? 'YES' : 'NO';
                })
                ->map([
                    'YES' => 'success',
                    'NO' => 'danger'
                ])
                ->exceptOnForms(),

            Badge::make(__('nova.is_admin'), 'is_admin')
                ->displayUsing(function ($value) {
                    return $value ? 'YES' : 'NO';
                })
                ->map([
                    'YES' => 'success',
                    'NO' => 'danger'
                ])
                ->exceptOnForms(),

            Badge::make(__('nova.phone_is_verified'), 'phone_verified_at')
                ->displayUsing(function ($value) {
                    return $value ? 'YES' : 'NO';
                })
                ->map([
                    'YES' => 'success',
                    'NO' => 'danger'
                ])
                ->exceptOnForms(),

            Badge::make(__('nova.email_is_verified'), 'email_verified_at')
                ->displayUsing(function ($value) {
                    return $value ? 'YES' : 'NO';
                })
                ->map([
                    'YES' => 'success',
                    'NO' => 'danger'
                ])
                ->exceptOnForms()
                ->showOnIndex(false),

            HasMany::make(__('nova.documents'), 'documents', Document::class),

            HasMany::make(__('nova.user_sold_orders'), 'soldOrders', Order::class),

            HasMany::make(__('nova.user_driven_orders'), 'drivenOrders', Order::class),

            HasMany::make(__('nova.user_campaign_levels'), 'campaignPrizes', UserCampaignLevelPrize::class),

            HasMany::make(__('nova.state_in_current_month_challenge'), 'monthlyChallengeStats', MonthlyChallengeStat::class),

            HasMany::make(__('nova.monthly_challenges_stats'), 'monthlyChallengesWinnings', MonthlyChallengeWinner::class),
        ];

        if ($this->resource->isCourier() && $this->resource->isSeller()) {
            //pass
        } else if ($this->resource->isCourier()) {
            $fields[18]->hideFromDetail();
        } else if ($this->resource->isSeller()){
            $fields[17]->hideFromDetail();
            $fields[19]->hideFromDetail();
        } else {
            $fields[17]->hideFromDetail();
            $fields[18]->hideFromDetail();
            $fields[19]->hideFromDetail();
            $fields[20]->hideFromDetail();
            $fields[21]->hideFromDetail();
            $fields[22]->hideFromDetail();
        }

        return $fields;
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [
            ChooseUserType::make()
                ->setModel($this->resource)
                ->showOnTableRow()
        ];
    }
}
