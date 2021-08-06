<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\Avatar;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\Gravatar;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Text;

class User extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\User::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'first_name', 'last_name', 'email', 'national_code',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),

            Avatar::make('Avatar')
                ->disk('public')
                ->rounded()
                ->path('users/avatars')
                ->prunable()
                ->deletable()
                ->nullable()
                ->rules('nullable', 'mimes:jpeg,jpg,png'),

            Text::make('Name')
                ->sortable()
                ->exceptOnForms(),

            Text::make('First Name')
                ->sortable()
                ->rules('required', 'max:255')
                ->onlyOnForms(),

            Text::make('Last Name')
                ->sortable()
                ->rules('required', 'max:255')
                ->onlyOnForms(),

            Text::make('Phone')
                ->sortable()
                ->rules('required', 'phone', 'max:254')
                ->creationRules('unique:users,phone')
                ->updateRules('unique:users,phone,{{resourceId}}'),

            Text::make('Email')
                ->sortable()
                ->rules('email', 'max:254')
                ->creationRules('unique:users,email')
                ->updateRules('unique:users,email,{{resourceId}}'),

            Text::make('National Code')
                ->sortable()
                ->rules('max:10'),

            Password::make('Password')
                ->onlyOnForms()
                ->creationRules('required', 'string', 'min:8')
                ->updateRules('nullable', 'string', 'min:8'),

            Badge::make('Is Courier')
                ->displayUsing(function ($value) {
                    return $value ? 'YES' : 'NO';
                })
                ->map([
                    'YES' => 'success',
                    'NO' => 'danger'
                ])
                ->exceptOnForms(),

            Badge::make('Is Admin')
                ->displayUsing(function ($value) {
                    return $value ? 'YES' : 'NO';
                })
                ->map([
                    'YES' => 'success',
                    'NO' => 'danger'
                ])
                ->exceptOnForms(),

            Badge::make('Phone Is Verified', 'phone_verified_at')
                ->displayUsing(function ($value) {
                    return $value ? 'YES' : 'NO';
                })
                ->map([
                    'YES' => 'success',
                    'NO' => 'danger'
                ])
                ->exceptOnForms(),

            Badge::make('Email Is Verified', 'email_verified_at')
                ->displayUsing(function ($value) {
                    return $value ? 'YES' : 'NO';
                })
                ->map([
                    'YES' => 'success',
                    'NO' => 'danger'
                ])
                ->exceptOnForms(),
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
