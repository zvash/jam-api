<?php

namespace App\Nova;

use App\Enums\OrderStatus;
use App\Nova\Actions\AcceptPendingOrder;
use App\Nova\Actions\CloseOrder;
use App\Nova\Actions\DeliverOrder;
use App\Nova\Actions\RejectOrder;
use Dpsoft\NovaPersianDate\PersianDate;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\ActionRequest;
use Titasgailius\SearchRelations\SearchesRelations;

class Order extends Resource
{
    use SearchesRelations;
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Order::class;

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
    public static function group()
    {
        return __('nova.Orders');
    }

    /**
     * @return array|null|string
     */
    public static function label()
    {
        return __('nova.Orders');
    }

    /**
     * @return array|null|string
     */
    public static function singularLabel()
    {
        return __('nova.Order');
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
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make(__('nova.id'), 'id')->sortable(),

            BelongsTo::make(__('nova.user'), 'user', User::class),

            BelongsTo::make(__('nova.driver'), 'driver', User::class),

            BelongsTo::make(__('nova.location'), 'location', Location::class),

            Boolean::make(__('nova.requires_driver'), 'requires_driver'),

            Boolean::make(__('nova.final_price_needed'), 'final_price_needed'),

            PersianDate::make(__('nova.pickup_date'), 'pickup_date'),

            Text::make(__('nova.description'), 'description')
                ->showOnIndex(false),

            Badge::make(__('nova.status'), 'status')
                ->displayUsing(function ($value) {
                    $map = [
                        OrderStatus::PENDING => __('nova.' . OrderStatus::PENDING),
                        OrderStatus::REJECTED => __('nova.' . OrderStatus::REJECTED),
                        OrderStatus::CANCELED_BY_CUSTOMER => __('nova.' . OrderStatus::CANCELED_BY_CUSTOMER),
                        OrderStatus::ACCEPTED_WAITING_CUSTOMER_APPROVAL => __('nova.' . OrderStatus::ACCEPTED_WAITING_CUSTOMER_APPROVAL),
                        OrderStatus::ACCEPTED_WAITING_FOR_DRIVER => __('nova.' . OrderStatus::ACCEPTED_WAITING_FOR_DRIVER),
                        OrderStatus::ACCEPTED_BY_DRIVER => __('nova.' . OrderStatus::ACCEPTED_BY_DRIVER),
                        OrderStatus::DRIVER_HEADING_TO_LOCATION => __('nova.' . OrderStatus::DRIVER_HEADING_TO_LOCATION),
                        OrderStatus::PICKED_UP => __('nova.' . OrderStatus::PICKED_UP),
                        OrderStatus::ACCEPTED_DRIVER_NOT_NEEDED => __('nova.' . OrderStatus::ACCEPTED_DRIVER_NOT_NEEDED),
                        OrderStatus::DELIVERED => __('nova.' . OrderStatus::DELIVERED),
                        OrderStatus::FINISHED => __('nova.' . OrderStatus::FINISHED),
                    ];
                    return $map[$value];
                })
                ->map([
                    __('nova.' . OrderStatus::PENDING) => 'warning',
                    __('nova.' . OrderStatus::REJECTED) => 'danger',
                    __('nova.' . OrderStatus::CANCELED_BY_CUSTOMER) => 'danger',
                    __('nova.' . OrderStatus::ACCEPTED_WAITING_CUSTOMER_APPROVAL) => 'info',
                    __('nova.' . OrderStatus::ACCEPTED_WAITING_FOR_DRIVER) => 'info',
                    __('nova.' . OrderStatus::ACCEPTED_BY_DRIVER) => 'info',
                    __('nova.' . OrderStatus::DRIVER_HEADING_TO_LOCATION) => 'info',
                    __('nova.' . OrderStatus::PICKED_UP) => 'info',
                    __('nova.' . OrderStatus::ACCEPTED_DRIVER_NOT_NEEDED) => 'info',
                    __('nova.' . OrderStatus::DELIVERED) => 'success',
                    __('nova.' . OrderStatus::FINISHED) => 'success',
                ])->exceptOnForms(),

            Text::make(__('nova.approximate_weight') . ' (Kg)', 'approximate_weight')
                ->showOnIndex(false),
            Text::make(__('nova.final_weight') . ' (Kg)', 'final_weight')
                ->showOnIndex(false),

            Text::make(__('nova.approximate_price'), 'approximate_price')
                ->displayUsing(function ($value) {
                    return $value ? number_format($value) : $value;
                }),
            Text::make(__('nova.final_price'), 'final_price')
                ->displayUsing(function ($value) {
                    return $value ? number_format($value) : $value;
                }),

            Boolean::make(__('nova.driver_is_paid'), 'driver_is_paid'),

            Boolean::make(__('nova.user_is_paid'), 'user_is_paid'),

            Text::make(__('nova.final_driver_price'), 'final_driver_price')
                ->showOnIndex(false)
                ->displayUsing(function ($value) {
                    return $value ? number_format($value) : $value;
                }),

            Image::make(__('nova.waybill_image'), 'waybill_image')
                ->disk('public')
                ->showOnIndex(false),

            Text::make(__('nova.waybill_number'), 'waybill_number')
                ->showOnIndex(false),

            Image::make(__('nova.evacuation_permit_image'), 'evacuation_permit_image')
                ->disk('public')
                ->showOnIndex(false),

            Text::make(__('nova.evacuation_permit_number'), 'evacuation_permit_number')
                ->showOnIndex(false),

            HasMany::make(__('nova.images'), 'images', OrderImage::class),
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
            AcceptPendingOrder::make()
                ->confirmButtonText(__('nova.accept'))
                ->cancelButtonText(__('nova.cancel'))
                ->showOnTableRow()
                ->canSee(function ($request) {
                    if ($request instanceof ActionRequest) {
                        return true;
                    }
                    return $this->resource instanceof \App\Models\Order
                        && $this->resource->status == OrderStatus::PENDING;
                }),

            RejectOrder::make()
                ->confirmButtonText(__('nova.reject'))
                ->cancelButtonText(__('nova.cancel'))
                ->confirmText(__('nova.reject_order_confirmation'))
                ->showOnTableRow()
                ->canSee(function ($request) {
                    if ($request instanceof ActionRequest) {
                        return true;
                    }
                    return $this->resource instanceof \App\Models\Order
                        && $this->resource->status == OrderStatus::PENDING;
                }),

            DeliverOrder::make()
                ->confirmButtonText(__('nova.delivered'))
                ->cancelButtonText(__('nova.cancel'))
                ->confirmText(__('nova.was_order_delivered'))
                ->showOnTableRow()
                ->canSee(function ($request) {
                    if ($request instanceof ActionRequest) {
                        return true;
                    }
                    return $this->resource instanceof \App\Models\Order
                        && $this->resource->status == OrderStatus::ACCEPTED_DRIVER_NOT_NEEDED;
                }),

            CloseOrder::make()
                ->confirmButtonText(__('nova.close'))
                ->cancelButtonText(__('nova.cancel'))
                ->showOnTableRow()
                ->canSee(function ($request) {
                    if ($request instanceof ActionRequest) {
                        return true;
                    }
                    return $this->resource instanceof \App\Models\Order
                        && $this->resource->status == OrderStatus::DELIVERED;
                })
                ->setModel($this->resource),
        ];
    }
}
