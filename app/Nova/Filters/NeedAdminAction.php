<?php

namespace App\Nova\Filters;

use App\Enums\OrderStatus;
use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class NeedAdminAction extends Filter
{
    /**
     * The filter's component.
     *
     * @var string
     */
    public $component = 'select-filter';

    public function name()
    {
        return __('nova.status');
    }

    public function default()
    {
        return 'true';
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
        if ($value == 'true') {
            return $query->whereIn('status', [
                OrderStatus::DELIVERED,
                OrderStatus::PENDING,
                OrderStatus::ACCEPTED_DRIVER_NOT_NEEDED,
            ]);
        }
        return $query;
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
            __('nova.need_admin_action') => true
        ];
    }
}
