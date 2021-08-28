<?php

namespace App\Nova;

use App\Nova\Actions\UpdatePriceList;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Item extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Item::class;

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
     * @return array|null|string
     */
    public static function group()
    {
        return __('nova.Orders');
    }

    /**
     * @return array|null|string
     */
    public static function label()
    {
        return __('nova.items');
    }

    /**
     * @return array|null|string
     */
    public static function singularLabel()
    {
        return __('nova.item');
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
            ID::make(__('ID'), 'id')->sortable(),

            Text::make(__('nova.name'), 'name')
                ->rules('required')
                ->creationRules('unique:items,name')
                ->updateRules('unique:items,name,{{resourceId}}'),

            Image::make(__('nova.image'), 'image')
                ->disk('public')
                ->path('items')
                ->prunable()
                ->deletable()
                ->nullable()
                ->rules('nullable', 'mimes:jpeg,jpg,png'),

            Currency::make(__('nova.price'), 'price')
                ->displayUsing(function($amount){
                    return number_format($amount);
                })
                ->step(1),

            Currency::make(__('nova.next_price'), 'next_price')
                ->displayUsing(function($amount){
                    return $amount ? number_format($amount) : null;
                })
                ->step(1),
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
        return [
            UpdatePriceList::make()
                ->standalone(),
        ];
    }
}
