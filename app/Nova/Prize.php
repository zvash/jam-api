<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Khalin\Nova\Field\Link;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Prize extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Prize::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'full_title';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
        'title',
        'subtitle',
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
        return __('nova.prizes');
    }

    /**
     * @return array|null|string
     */
    public static function singularLabel()
    {
        return __('nova.prize');
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

            Text::make(__('nova.title'), 'title')
                ->rules('required'),

            Text::make(__('nova.subtitle'), 'subtitle')
                ->rules('nullable'),

            Image::make(__('nova.image'), 'image')
                ->disk('public')
                ->path('prizes')
                ->prunable()
                ->deletable()
                ->nullable()
                ->rules('nullable', 'mimes:jpeg,jpg,png'),
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
