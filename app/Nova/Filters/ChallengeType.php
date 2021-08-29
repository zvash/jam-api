<?php

namespace App\Nova\Filters;

use App\Enums\UserType;
use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class ChallengeType extends Filter
{
    /**
     * The filter's component.
     *
     * @var string
     */
    public $component = 'select-filter';

    public function name()
    {
        return __('nova.challenge_type');
    }

    /**
     * Apply the filter to the given query.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Request $request, $query, $value)
    {
        return $query->whereHas('challenge', function ($challenge) use ($value) {
            return $challenge->where('user_type', $value);
        });
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function options(Request $request)
    {
        return [
            __('nova.sellers') => UserType::SELLER,
            __('nova.drivers') => UserType::DRIVER,
        ];
    }
}
