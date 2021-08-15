<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\ActionRequest;
use Laravel\Nova\Http\Requests\NovaRequest;

class CampaignLevel extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\CampaignLevel::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'with_unit';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
    ];

    public static $displayInNavigation = false;

    /**
     * Default ordering for index query.
     *
     * @var array
     */
    public static $indexDefaultOrder = [
        'is_active' => 'desc'
    ];

    /**
     * @return array|null|string
     */
    public static function group()
    {
        return __('nova.campaigns_and_challenges');
    }

    /**
     * Build an "index" query for the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest $request
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function indexQuery(NovaRequest $request, $query)
    {
        if (empty($request->get('orderBy'))) {
            $query->getQuery()->orders = [];
            return $query->orderBy(key(static::$indexDefaultOrder), reset(static::$indexDefaultOrder))
                ->orderBy('milestone', 'asc');
        }
        return $query;
    }

    /**
     * @return array|null|string
     */
    public static function label()
    {
        return __('nova.campaign_levels');
    }

    /**
     * @return array|null|string
     */
    public static function singularLabel()
    {
        return __('nova.campaign_level');
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
        return [
            ID::make(__('nova.id'), 'id')->sortable(),

            BelongsTo::make(__('nova.campaign'), 'campaign', Campaign::class)
                ->required(),

            BelongsTo::make(__('nova.prize'), 'prize', Prize::class)
                ->required(),

            Text::make(__('nova.milestone'), 'with_unit')
                ->exceptOnForms(),

            Number::make(__('nova.milestone'), 'milestone')
                ->required()
                ->showOnIndex(false)
                ->showOnDetail(false)
                ->showOnUpdating(false),

            Text::make(__('nova.milestone'), 'milestone')
                ->showOnCreating(false)
                ->showOnIndex(false)
                ->showOnDetail(false)
                ->displayUsing(function ($value) {
                    return float_number_format($value);
                })
                ->withMeta(['extraAttributes' => [
                    'readonly' => true
                ]]),

            Badge::make(__('nova.status'), 'is_active')
                ->displayUsing(function ($value) {
                    return $value ? __('nova.active') : __('nova.inactive');
                })
                ->map([
                    __('nova.active') => 'success',
                    __('nova.inactive') => 'warning',
                ]),
        ];
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
        return [];
    }
}
