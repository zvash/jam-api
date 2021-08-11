<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Techouse\SelectAutoComplete\SelectAutoComplete as Select;

class Vehicle extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Vehicle::class;

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
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        $counties = \App\Models\County::all()->pluck('name', 'id');
        $drivers = \App\Models\User::query()
            ->whereHas('roles', function($roles) {
                return $roles->where('roles.name', 'courier');
            })->get()
            ->pluck('name', 'id');
        $vehicleTypes = \App\Models\VehicleType::all()->pluck('name', 'id');

        return [
            ID::make(__('ID'), 'id')->sortable(),

            BelongsTo::make('Type', 'vehicleType', VehicleType::class)
                ->exceptOnForms(),
            Select::make('Type', 'vehicle_type_id')->options($vehicleTypes)
                ->onlyOnForms()
                ->displayUsingLabels()
                ->creationRules('required'),

            BelongsTo::make('Driver', 'driver', User::class)
                ->exceptOnForms(),
            Select::make('Driver', 'user_id')->options($drivers)
                ->onlyOnForms()
                ->displayUsingLabels()
                ->creationRules('required'),


            BelongsTo::make('County', 'county', County::class)
                ->exceptOnForms(),
            Select::make('County', 'county_id')->options($counties)
                ->onlyOnForms()
                ->displayUsingLabels()
                ->creationRules('required'),

            Text::make('Plate Number')
                ->sortable()
                ->rules('required', 'max:8', 'min:8')
                ->creationRules('unique:vehicles,plate_number')
                ->updateRules('unique:vehicles,plate_number,{{resourceId}}'),

            Text::make('Chassis Number')
                ->sortable()
                ->rules('required'),

            Text::make('Engine Number')
                ->sortable()
                ->rules('required'),

//            Text::make('Chassis Number')
//                ->sortable()
//                ->rules('required'),

            Text::make('Owner Full Name')
                ->sortable()
                ->rules('required'),

            Text::make('Owner Phone')
                ->sortable()
                ->rules('required'),

            Text::make('Owner National Code')
                ->sortable()
                ->rules('required'),
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
