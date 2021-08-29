<?php

namespace App\Nova;

use Dpsoft\NovaPersianDate\PersianDate;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Milanjam\ImageLink\ImageLink;

class Document extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Document::class;

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
        'type',
    ];

    public static $displayInNavigation = false;

    /**
     * @return array|null|string
     */
    public static function label()
    {
        return __('nova.documents');
    }

    /**
     * @return array|null|string
     */
    public static function singularLabel()
    {
        return __('nova.document');
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

            Text::make(__('nova.document_type'), 'type')
                ->required()
                ->rules('required', 'filled'),

            Text::make(__('nova.number'), 'number')
                ->required()
                ->rules('required', 'filled'),

            PersianDate::make(__('nova.expire_date'), 'expire_date')
                ->required(),

            Boolean::make(__('nova.is_valid'), 'is_valid')->exceptOnForms(),

            Image::make(__('nova.image'), 'image')
                ->disk('public')
                ->path('users/document')
                ->prunable()
                ->deletable()
                ->nullable()
                ->rules('nullable', 'mimes:jpeg,jpg,png')
                ->showOnIndex(false),

            ImageLink::make(__('nova.image'), 'image_url')
                ->url(function () {
                    return "{$this->image_url}";
                })->text(__('nova.display'))->blank()
                ->onlyOnIndex(),
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
