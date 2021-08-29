<?php

namespace App\Nova;

use App\Nova\Actions\AddOrEditDriversMonthlyChallenge;
use App\Nova\Actions\AddOrEditSellersMonthlyChallenge;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Line;
use Laravel\Nova\Fields\Stack;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\ActionRequest;
use Laravel\Nova\Http\Requests\NovaRequest;
use Titasgailius\SearchRelations\SearchesRelations;

class MonthlyChallenge extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\MonthlyChallenge::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name_with_period';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
    ];

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
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function indexQuery(NovaRequest $request, $query)
    {
        if (empty($request->get('orderBy'))) {
            $query->getQuery()->orders = [];
            return $query->orderBy(key(static::$indexDefaultOrder), reset(static::$indexDefaultOrder))
                ->orderBy('starts_at', 'desc');
        }
        return $query;
    }


    /**
     * @return array|null|string
     */
    public static function label()
    {
        return __('nova.monthly_challenges');
    }

    /**
     * @return array|null|string
     */
    public static function singularLabel()
    {
        return __('nova.monthly_challenge');
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

            Text::make(__('nova.year'), 'year')->sortable(),

            Text::make(__('nova.month'), 'month')
                ->displayUsing(function ($value) {
                    return $this->months($value);
                })
                ->sortable(),

            Text::make(__('nova.challenge'), 'description'),

            Text::make(__('nova.goal'), 'goal'),

            BelongsTo::make(__('nova.prize'), 'prize', Prize::class)
                ->required(),

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

            HasMany::make(__('nova.participants_stats'), 'winners', MonthlyChallengeWinner::class),
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
            AddOrEditDriversMonthlyChallenge::make()
                ->confirmButtonText(__('nova.create_challenge'))
                ->standalone(),

            AddOrEditSellersMonthlyChallenge::make()
                ->confirmButtonText(__('nova.create_challenge'))
                ->standalone(),
        ];
    }

    /**
     * @param int $value
     * @return mixed|string
     */
    private function months(int $value)
    {
        $months = [
            'فروردین',
            'اردیبهشت',
            'خرداد',
            'تیر',
            'مرداد',
            'شهریور',
            'مهر',
            'آبان',
            'آذر',
            'دی',
            'بهمن',
            'اسفند',
        ];
        if (array_key_exists($value - 1, $months)) {
            return $months[$value - 1];
        }
        return ' - ';
    }
}
