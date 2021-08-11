<?php

namespace App\Nova;

use App\Nova\Filters\TicketStatus;
use Illuminate\Http\Request;
use Khalin\Nova\Field\Link;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\ActionRequest;
use Laravel\Nova\Http\Requests\NovaRequest;
use Titasgailius\SearchRelations\SearchesRelations;

class Ticket extends Resource
{
    use SearchesRelations;
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Ticket::class;

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
    ];

    /**
     * @return array|null|string
     */
    public static function label()
    {
        return __('nova.Tickets');
    }

    /**
     * @return array|null|string
     */
    public static function singularLabel()
    {
        return __('nova.Ticket');
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

            Text::make(__('nova.phone'), 'phone'),

            Text::make(__('nova.comment'), 'comment')->onlyOnIndex(),
            Textarea::make(__('nova.comment'), 'comment')->onlyOnDetail(),

            Link::make(__('nova.image'), 'image_url')
                ->url(function () {
                    return "{$this->image_url}";
                })->text(__('nova.display'))->blank(),

            Image::make(__('nova.image'), 'image')
                ->disk('public')
            ->onlyOnDetail(),

            Badge::make(__('nova.status'), 'is_open')
                ->displayUsing(function ($value) {
                    return $value ? __('nova.open') : __('nova.closed');
                })
            ->map([
                __('nova.open') => 'info',
                __('nova.closed') => 'success',
            ])->exceptOnForms()
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
        return [
            new TicketStatus(),
        ];
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
