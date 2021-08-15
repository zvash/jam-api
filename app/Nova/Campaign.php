<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\ActionRequest;
use Laravel\Nova\Http\Requests\NovaRequest;

class Campaign extends Resource
{

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Campaign::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'title';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
        'title',
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
        return __('nova.campaigns');
    }

    /**
     * @return array|null|string
     */
    public static function singularLabel()
    {
        return __('nova.campaign');
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

            Text::make(__('nova.title'), 'title'),

            Badge::make(__('nova.related_to'), 'user_type')
                ->displayUsing(function ($value) {
                    $value = Str::plural($value);
                    $key = 'nova.' . $value;
                    return __($key);
                })
                ->map([
                    __('nova.drivers') => 'info',
                    __('nova.sellers') => 'info',
                ]),

            Badge::make(__('nova.status'), 'is_active')
                ->displayUsing(function ($value) {
                    return $value ? __('nova.active') : __('nova.inactive');
                })
                ->map([
                    __('nova.active') => 'success',
                    __('nova.inactive') => 'warning',
                ]),

            HasMany::make(__('nova.campaign_levels'), 'levels', CampaignLevel::class),
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
        return [];
    }
}
