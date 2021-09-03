<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Techouse\SelectAutoComplete\SelectAutoComplete as Select;
use Titasgailius\SearchRelations\SearchesRelations;

class Vehicle extends Resource
{
    use SearchesRelations;

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
        'owner_full_name',
        'owner_phone',
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
    public static function group()
    {
        return __('nova.drivers');
    }

    /**
     * @return array|null|string
     */
    public static function label()
    {
        return __('nova.vehicles');
    }

    /**
     * @return array|null|string
     */
    public static function singularLabel()
    {
        return __('nova.vehicle');
    }

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
            ID::make(__('nova.id'), 'id')->sortable(),

            BelongsTo::make(__('nova.type'), 'vehicleType', VehicleType::class)
                ->exceptOnForms(),
            Select::make(__('nova.type'), 'vehicle_type_id')->options($vehicleTypes)
                ->onlyOnForms()
                ->displayUsingLabels()
                ->creationRules('required'),

            BelongsTo::make(__('nova.driver'), 'driver', User::class)
                ->exceptOnForms(),
            Select::make(__('nova.driver'), 'user_id')->options($drivers)
                ->onlyOnForms()
                ->displayUsingLabels()
                ->creationRules('required'),


            BelongsTo::make(__('nova.county'), 'county', County::class)
                ->exceptOnForms(),
            Select::make(__('nova.county'), 'county_id')->options($counties)
                ->onlyOnForms()
                ->displayUsingLabels()
                ->creationRules('required'),

            Text::make(__('nova.plate_number'), 'plate_number')
                ->sortable()
                ->rules('required', 'max:9', 'min:8')
                ->creationRules('unique:vehicles,plate_number')
                ->updateRules('unique:vehicles,plate_number,{{resourceId}}'),

            Text::make(__('nova.chassis_number'), 'chassis_number')
                ->sortable()
                ->rules('required'),

            Text::make(__('nova.engine_number'), 'engine_number')
                ->sortable()
                ->rules('required'),

//            Text::make('Chassis Number')
//                ->sortable()
//                ->rules('required'),

            Text::make(__('nova.owner_full_name'), 'owner_full_name')
                ->sortable()
                ->rules('required'),

            Text::make(__('nova.owner_phone'), 'owner_phone')
                ->sortable()
                ->rules('required'),

            Text::make(__('nova.owner_national_code'), 'owner_national_code')
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
