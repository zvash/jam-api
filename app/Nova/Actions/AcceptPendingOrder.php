<?php

namespace App\Nova\Actions;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Currency;

class AcceptPendingOrder extends Action
{
    use InteractsWithQueue, Queueable;

    public function name()
    {
        return __('nova.accept_pending_order');
    }
    
    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        if (count($models) !== 1) {
            return Action::danger(__('messages.error.just_one_resource_error', ['resource' => __('nova.Order')]));
        }
        $value = $fields->price;
        foreach ($models as $model) {
            if ($model instanceof Order) {
                $newState = OrderStatus::ACCEPTED_WAITING_CUSTOMER_APPROVAL;
                if (! $model->final_price_needed) {
                    $newState = OrderStatus::ACCEPTED_WAITING_FOR_DRIVER;
                    if (! $model->requires_driver) {
                        $newState = OrderStatus::ACCEPTED_DRIVER_NOT_NEEDED;
                    }
                }
                if ($model->stateTransitionIsPermitted($newState)) {
                    try {
                        if ($value && $newState == OrderStatus::ACCEPTED_WAITING_CUSTOMER_APPROVAL) {
                            $model->approximate_price = $value;
                        }
                        $model->statePrerequisitesIsMet($newState);
                    } catch (\Exception $exception) {
                        $message = $exception->getMessage();
                        return Action::danger($message);
                    }
                    $model->updateOrderStatus($newState);
                    return Action::message(__('messages.success.successful_operation'));
                } else {

                }
            }
            return Action::danger(__('messages.error.not_possible_operation'));
        }
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields()
    {
        return [
            Currency::make(__('nova.suggested_price'), 'price')
                ->help(__('nova.suggested_price_help'))
                ->min(0)
                ->step(100)
                ->rules(['nullable', 'int', 'min:0']),
        ];
    }
}
