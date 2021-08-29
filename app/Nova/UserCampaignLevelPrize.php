<?php

namespace App\Nova;

use App\Nova\Actions\HandOverMonthlyChallengePrize;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\ActionRequest;
use Laravel\Nova\Http\Requests\NovaRequest;
use Titasgailius\SearchRelations\SearchesRelations;

class UserCampaignLevelPrize extends Resource
{
    use SearchesRelations;

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\UserCampaignLevelPrize::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
    ];

    /**
     * The relationship columns that should be searched.
     *
     * @var array
     */
    public static $searchRelations = [
        'user' => ['first_name', 'last_name', 'phone'],
        'prize' => ['title', 'subtitle'],
    ];

    /**
     * @return array|null|string
     */
    public static function group()
    {
        return __('nova.campaigns_and_challenges');
    }

    /**
     * @return array|null|string
     */
    public static function label()
    {
        return __('nova.user_campaign_levels');
    }

    /**
     * @return array|null|string
     */
    public static function singularLabel()
    {
        return __('nova.user_campaign_level');
    }

    /**
     * @param Request $request
     * @return bool
     */
    public static function authorizedToCreate(Request $request)
    {
        return false;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function authorizedToUpdate(Request $request)
    {
        return $request instanceof ActionRequest;
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
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make(__('nova.id'), 'id')->sortable(),

            BelongsTo::make(__('nova.user'), 'user', User::class),
            BelongsTo::make(__('nova.campaign_level'), 'campaignLevel', CampaignLevel::class),
            BelongsTo::make(__('nova.prize'), 'prize', Prize::class),
            Boolean::make(__('nova.handed_over'), 'handed_over'),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [
            HandOverMonthlyChallengePrize::make()
                ->confirmButtonText(__('nova.yes'))
                ->cancelButtonText(__('nova.cancel'))
                ->confirmText(__('nova.is_prize_handed_over'))
                ->showOnTableRow()
                ->canSee(function ($request) {
                    if ($request instanceof ActionRequest) {
                        return true;
                    }
                    return $this->resource instanceof \App\Models\UserCampaignLevelPrize
                        && $this->resource->handed_over == false;
                }),
        ];
    }
}
