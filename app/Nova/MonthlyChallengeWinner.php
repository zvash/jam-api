<?php

namespace App\Nova;

use App\Nova\Actions\HandOverMonthlyChallengePrize;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Http\Requests\ActionRequest;
use Laravel\Nova\Http\Requests\NovaRequest;
use Signifly\Nova\Fields\ProgressBar\ProgressBar;
use Titasgailius\SearchRelations\SearchesRelations;

class MonthlyChallengeWinner extends Resource
{
    use SearchesRelations;

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\MonthlyChallengeWinner::class;

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
        'monthly_challenge_id',
        'user_id',
    ];

    /**
     * Default ordering for index query.
     *
     * @var array
     */
    public static $indexDefaultOrder = [
        'monthly_challenge_id' => 'desc'
    ];

    /**
     * The relationship columns that should be searched.
     *
     * @var array
     */
    public static $searchRelations = [
        'user' => ['first_name', 'last_name', 'phone'],
    ];

    public static $displayInNavigation = false;

    /**
     * Build an "index" query for the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function indexQuery(NovaRequest $request, $query)
    {
        if (empty($request->get('orderBy'))) {
            $query->getQuery()->orders = [];
            return $query->orderBy(key(static::$indexDefaultOrder), reset(static::$indexDefaultOrder));
        }
        return $query;
    }

    /**
     * @return array|null|string
     */
    public static function label()
    {
        return __('nova.participants_stats');
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

    public function authorizedToView(Request $request)
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

            BelongsTo::make(__('nova.monthly_challenge'), 'challenge', MonthlyChallenge::class),

            BelongsTo::make(__('nova.user'), 'user', User::class),

            Number::make(__('nova.points'), 'points')
                ->displayUsing(function ($value) {
                    return float_number_format($value);
                }),

            Number::make(__('nova.points_needed'), 'points_needed')
                ->displayUsing(function ($value) {
                    return float_number_format($value);
                }),

            BelongsTo::make(__('nova.prize'), 'prize', Prize::class)
                ->required(),

            Boolean::make(__('nova.has_won'), 'has_won'),

            ProgressBar::make(__('nova.progress'), 'progress')
                ->options([
                    'fromColor' => '#FFEA82',
                    'toColor' => '#40BF55',
                    'animateColor' => true,
                ])
                ->exceptOnForms(),

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
                    return $this->resource instanceof \App\Models\MonthlyChallengeWinner
                        && $this->resource->points >= $this->resource->points_needed
                        && $this->resource->handed_over == false;
                }),
        ];
    }
}
